<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $fullSubject }}</title>
</head>
<body style="margin:0;padding:0;background:#f3f4f6;font-family:Arial,Helvetica,sans-serif;color:#1f2937;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f3f4f6;padding:24px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:640px;background:#ffffff;border:1px solid #d1d5db;border-radius:14px;overflow:hidden;">
                    <tr>
                        <td style="background:#057c3c;padding:18px 24px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="color:#ffffff;font-size:20px;font-weight:700;">{{ $appName }}</td>
                                    <td align="right" style="color:#d1fae5;font-size:12px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;">{{ $headerLabel }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:32px 28px 24px 28px;">
                            <p style="margin:0 0 8px 0;font-size:16px;font-weight:600;color:#111827;">{{ $greeting }}</p>
                            <h1 style="margin:0 0 18px 0;font-size:26px;line-height:1.25;color:#111827;">{{ $title }}</h1>

                            @foreach ($introLines as $line)
                                <p style="margin:0 0 12px 0;font-size:15px;line-height:1.7;color:#374151;">{{ $line }}</p>
                            @endforeach

                            @if ($details)
                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:20px 0;border:1px solid #e5e7eb;border-radius:12px;background:#f9fafb;">
                                    @foreach ($details as $label => $value)
                                        <tr>
                                            <td style="padding:12px 16px;border-bottom:{{ $loop->last ? '0' : '1px solid #e5e7eb' }};font-size:13px;font-weight:700;color:#4b5563;width:34%;">{{ $label }}</td>
                                            <td style="padding:12px 16px;border-bottom:{{ $loop->last ? '0' : '1px solid #e5e7eb' }};font-size:14px;line-height:1.6;color:#111827;white-space:pre-line;">{{ $value }}</td>
                                        </tr>
                                    @endforeach
                                </table>
                            @endif

                            @foreach ($sections as $section)
                                <div style="margin:20px 0;padding:16px 18px;border:1px solid #e5e7eb;border-radius:12px;background:#ffffff;">
                                    <p style="margin:0 0 10px 0;font-size:13px;font-weight:700;letter-spacing:0.04em;text-transform:uppercase;color:#057c3c;">{{ $section['title'] }}</p>

                                    @if ($section['is_list'])
                                        <ul style="margin:0;padding-left:18px;color:#374151;">
                                            @foreach ($section['content'] as $item)
                                                <li style="margin:0 0 8px 0;font-size:14px;line-height:1.6;">{{ $item }}</li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <div style="font-size:14px;line-height:1.7;color:#374151;white-space:pre-line;">{{ $section['content'] }}</div>
                                    @endif
                                </div>
                            @endforeach

                            @if ($action)
                                <p style="margin:24px 0 20px 0;">
                                    <a href="{{ $action['url'] }}" style="display:inline-block;background:#057c3c;color:#ffffff;text-decoration:none;padding:12px 18px;border-radius:10px;font-size:14px;font-weight:700;">
                                        {{ $action['text'] }}
                                    </a>
                                </p>
                            @endif

                            @foreach ($outroLines as $line)
                                <p style="margin:0 0 12px 0;font-size:14px;line-height:1.7;color:#374151;">{{ $line }}</p>
                            @endforeach
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:0 28px 24px 28px;">
                            <div style="border-top:1px solid #e5e7eb;padding-top:16px;font-size:12px;line-height:1.6;color:#6b7280;">
                                This email was sent by {{ $appName }}. Please keep it for your records.
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
