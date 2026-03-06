<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StandardAppMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        protected string $topic,
        protected string $title,
        protected ?string $recipientName = null,
        protected array $introLines = [],
        protected array $details = [],
        protected array $sections = [],
        protected ?array $action = null,
        protected array $outroLines = [],
        protected ?string $headerLabel = null,
    ) {
    }

    public function build(): static
    {
        $appName = config('app.name', 'Smart Cafeteria');
        $fullSubject = "{$appName}: {$this->topic}";

        return $this->subject($fullSubject)
            ->view('emails.standard')
            ->text('emails.standard_plain')
            ->with([
                'appName' => $appName,
                'fullSubject' => $fullSubject,
                'headerLabel' => $this->headerLabel ?: $this->title,
                'greeting' => $this->greeting(),
                'title' => $this->title,
                'introLines' => $this->normalizeLines($this->introLines),
                'details' => $this->normalizeDetails($this->details),
                'sections' => $this->normalizeSections($this->sections),
                'action' => $this->normalizeAction($this->action),
                'outroLines' => $this->normalizeLines($this->outroLines),
            ]);
    }

    protected function greeting(): string
    {
        $name = trim((string) $this->recipientName);

        return $name !== '' ? "Hello {$name}," : 'Hello,';
    }

    protected function normalizeLines(array $lines): array
    {
        return array_values(array_filter(
            array_map(static fn ($line) => trim((string) $line), $lines),
            static fn (string $line) => $line !== ''
        ));
    }

    protected function normalizeDetails(array $details): array
    {
        $normalized = [];

        foreach ($details as $label => $value) {
            $detailLabel = trim((string) $label);
            $detailValue = trim((string) $value);

            if ($detailLabel === '' || $detailValue === '') {
                continue;
            }

            $normalized[$detailLabel] = $detailValue;
        }

        return $normalized;
    }

    protected function normalizeSections(array $sections): array
    {
        $normalized = [];

        foreach ($sections as $section) {
            $heading = trim((string) ($section['title'] ?? ''));
            $content = $section['content'] ?? null;

            if ($heading === '' || $content === null) {
                continue;
            }

            if (is_array($content)) {
                $items = $this->normalizeLines($content);

                if ($items === []) {
                    continue;
                }

                $normalized[] = [
                    'title' => $heading,
                    'content' => $items,
                    'is_list' => true,
                ];

                continue;
            }

            $contentText = trim((string) $content);

            if ($contentText === '') {
                continue;
            }

            $normalized[] = [
                'title' => $heading,
                'content' => $contentText,
                'is_list' => false,
            ];
        }

        return $normalized;
    }

    protected function normalizeAction(?array $action): ?array
    {
        if (! is_array($action)) {
            return null;
        }

        $text = trim((string) ($action['text'] ?? ''));
        $url = trim((string) ($action['url'] ?? ''));

        if ($text === '' || $url === '') {
            return null;
        }

        return [
            'text' => $text,
            'url' => $url,
        ];
    }
}
