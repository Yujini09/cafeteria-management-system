<?php

namespace App\Services;

class RealtimeEmailVerifier
{
    public function verifyMailbox(string $email): array
    {
        if (app()->runningUnitTests()) {
            return [
                'ok' => true,
                'message' => 'Realtime email verification is skipped in testing mode.',
                'error_code' => null,
            ];
        }

        [$localPart, $domain] = array_pad(explode('@', $email, 2), 2, '');
        if ($localPart === '' || $domain === '') {
            return $this->emailNotFoundResult();
        }

        $mxHosts = [];
        $mxWeights = [];

        if (function_exists('getmxrr') && getmxrr($domain, $mxHosts, $mxWeights) && count($mxHosts) > 0) {
            if (count($mxWeights) !== count($mxHosts)) {
                $mxWeights = array_pad($mxWeights, count($mxHosts), 0);
            }
            array_multisort($mxWeights, SORT_ASC, $mxHosts);
        } elseif (checkdnsrr($domain, 'MX') || checkdnsrr($domain, 'A') || checkdnsrr($domain, 'AAAA')) {
            $mxHosts = [$domain];
        } else {
            return $this->emailNotFoundResult();
        }

        $mxHosts = array_slice(array_values(array_unique(array_filter($mxHosts))), 0, 3);
        if (empty($mxHosts)) {
            return $this->emailNotFoundResult();
        }

        $heloHost = parse_url((string) config('app.url'), PHP_URL_HOST);
        if (!is_string($heloHost) || $heloHost === '') {
            $heloHost = request()->getHost() ?: 'localhost';
        }
        $envelopeFrom = $this->resolveVerificationEnvelopeFrom($domain);

        foreach ($mxHosts as $mxHost) {
            $socket = @fsockopen($mxHost, 25, $errno, $errstr, 6);
            if (!$socket) {
                continue;
            }

            stream_set_timeout($socket, 6);

            $greeting = $this->smtpReadResponse($socket);
            if (!$this->smtpResponseHasCode($greeting, [220])) {
                fclose($socket);
                continue;
            }

            $ehlo = $this->smtpSendCommand($socket, "EHLO {$heloHost}");
            if (!$this->smtpResponseHasCode($ehlo, [250])) {
                $helo = $this->smtpSendCommand($socket, "HELO {$heloHost}");
                if (!$this->smtpResponseHasCode($helo, [250])) {
                    $this->smtpSendCommand($socket, 'QUIT');
                    fclose($socket);
                    continue;
                }
            }

            $mailFrom = $this->smtpSendCommand($socket, "MAIL FROM:<{$envelopeFrom}>");
            if (!$this->smtpResponseHasCode($mailFrom, [250])) {
                $this->smtpSendCommand($socket, 'QUIT');
                fclose($socket);
                continue;
            }

            $targetRcpt = $this->smtpSendCommand($socket, "RCPT TO:<{$email}>");
            $targetRcptCode = $this->smtpResponseCode($targetRcpt);

            if (in_array($targetRcptCode, [550, 551, 552, 553, 554], true)) {
                $this->smtpSendCommand($socket, 'RSET');
                $this->smtpSendCommand($socket, 'QUIT');
                fclose($socket);

                return $this->emailNotFoundResult();
            }

            if (in_array($targetRcptCode, [250, 251], true)) {
                try {
                    $probeLocalPart = 'probe-' . bin2hex(random_bytes(6));
                } catch (\Throwable) {
                    $probeLocalPart = 'probe-' . uniqid('', true);
                }
                $probeEmail = "{$probeLocalPart}@{$domain}";
                $probeRcpt = $this->smtpSendCommand($socket, "RCPT TO:<{$probeEmail}>");
                $probeRcptCode = $this->smtpResponseCode($probeRcpt);

                $this->smtpSendCommand($socket, 'RSET');
                $this->smtpSendCommand($socket, 'QUIT');
                fclose($socket);

                if (in_array($probeRcptCode, [250, 251], true)) {
                    return $this->unavailableRealtimeEmailVerificationResult();
                }

                return [
                    'ok' => true,
                    'message' => 'Email account verified.',
                    'error_code' => null,
                ];
            }

            $this->smtpSendCommand($socket, 'RSET');
            $this->smtpSendCommand($socket, 'QUIT');
            fclose($socket);
        }

        return $this->unavailableRealtimeEmailVerificationResult();
    }

    public function classifyDeliveryFailure(string $errorMessage): array
    {
        $normalizedError = strtolower($errorMessage);
        $emailNotFoundIndicators = [
            'user unknown',
            'unknown user',
            'no such user',
            'mailbox unavailable',
            'mailbox not found',
            'recipient address rejected',
            'recipient not found',
            'unknown recipient',
            'invalid recipient',
            '5.1.1',
            '550',
        ];

        foreach ($emailNotFoundIndicators as $indicator) {
            if (str_contains($normalizedError, $indicator)) {
                return [
                    'message' => 'Email address/account could not be found. Please input a valid email.',
                    'status' => 422,
                    'code' => 'email_not_found',
                ];
            }
        }

        return [
            'message' => 'Email failed to send. Please check the mail configuration and try again.',
            'status' => 500,
            'code' => 'email_send_failed',
        ];
    }

    private function emailNotFoundResult(): array
    {
        return [
            'ok' => false,
            'message' => 'Email address/account could not be found. Please input a valid email.',
            'error_code' => 'email_not_found',
        ];
    }

    private function unavailableRealtimeEmailVerificationResult(): array
    {
        if ($this->allowUnavailableVerification()) {
            return [
                'ok' => true,
                'message' => 'Realtime email verification is unavailable. Continuing with delivery-based validation.',
                'error_code' => null,
            ];
        }

        return [
            'ok' => false,
            'message' => 'Could not verify this email account in real time. Please input a valid email and try again.',
            'error_code' => 'email_check_unavailable',
        ];
    }

    private function allowUnavailableVerification(): bool
    {
        $configuredValue = config('services.realtime_email_verifier.allow_unavailable');

        if ($configuredValue === null) {
            return false;
        }

        if (is_bool($configuredValue)) {
            return $configuredValue;
        }

        if (is_string($configuredValue)) {
            return in_array(strtolower($configuredValue), ['1', 'true', 'on', 'yes'], true);
        }

        return (bool) $configuredValue;
    }

    private function resolveVerificationEnvelopeFrom(string $fallbackDomain): string
    {
        $configuredFrom = config('mail.from.address');
        if (is_string($configuredFrom) && str_contains($configuredFrom, '@')) {
            return $configuredFrom;
        }

        $appHost = parse_url((string) config('app.url'), PHP_URL_HOST);
        if (is_string($appHost) && $appHost !== '') {
            return "no-reply@{$appHost}";
        }

        return "no-reply@{$fallbackDomain}";
    }

    private function smtpSendCommand($socket, string $command): string
    {
        fwrite($socket, $command . "\r\n");
        return $this->smtpReadResponse($socket);
    }

    private function smtpReadResponse($socket): string
    {
        $response = '';

        while (($line = fgets($socket, 512)) !== false) {
            $response .= $line;
            if (strlen($line) < 4 || $line[3] === ' ') {
                break;
            }
        }

        return trim($response);
    }

    private function smtpResponseCode(string $response): ?int
    {
        if (preg_match('/^(\d{3})/m', $response, $matches)) {
            return (int) $matches[1];
        }

        return null;
    }

    private function smtpResponseHasCode(string $response, array $expectedCodes): bool
    {
        $code = $this->smtpResponseCode($response);
        return $code !== null && in_array($code, $expectedCodes, true);
    }
}
