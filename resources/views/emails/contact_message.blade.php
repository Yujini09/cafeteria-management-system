<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Message</title>
</head>
<body style="margin:0;padding:0;background:#f6f7f9;font-family:Arial,Helvetica,sans-serif;color:#1f2937;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f6f7f9;padding:24px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#ffffff;border:1px solid #e5e7eb;border-radius:8px;">
                    <tr>
                        <td style="padding:24px;">
                            <h2 style="margin:0 0 16px 0;font-size:22px;color:#111827;">New Message</h2>
                            <p style="margin:0 0 8px 0;">You received a new message.</p>
                            <p style="margin:0 0 4px 0;"><strong>From:</strong> {{ $name }}</p>
                            <p style="margin:0 0 4px 0;"><strong>Email:</strong> {{ $email }}</p>
                            <p style="margin:0 0 16px 0;"><strong>Sent:</strong> {{ $sent_at }}</p>

                            <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:6px;padding:12px;white-space:pre-wrap;">{{ $user_message }}</div>

                            <p style="margin:16px 0 0 0;">Reply directly to this email to respond to the sender.</p>
                            <p style="margin:16px 0 0 0;">Thanks,<br>{{ $app_name }}</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
