<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reply to Your Message</title>
</head>
<body style="margin:0;padding:0;background:#f6f7f9;font-family:Arial,Helvetica,sans-serif;color:#1f2937;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f6f7f9;padding:24px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#ffffff;border:1px solid #e5e7eb;border-radius:8px;">
                    <tr>
                        <td style="padding:24px;">
                            <h2 style="margin:0 0 16px 0;font-size:22px;color:#111827;">Hello {{ $recipient_name }},</h2>
                            <p style="margin:0 0 12px 0;">You have received a reply to your message.</p>

                            <h3 style="margin:0 0 8px 0;font-size:16px;color:#111827;">Reply</h3>
                            <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:6px;padding:12px;white-space:pre-wrap;">{{ $reply_message }}</div>

                            <p style="margin:16px 0 4px 0;"><strong>Replied by:</strong> {{ $admin_name }}</p>
                            <p style="margin:0 0 16px 0;"><strong>Replied at:</strong> {{ $replied_at }}</p>

                            <h3 style="margin:0 0 8px 0;font-size:16px;color:#111827;">Your Original Message</h3>
                            <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:6px;padding:12px;white-space:pre-wrap;">{{ $original_message }}</div>

                            <p style="margin:16px 0 0 0;">If you need further assistance, just reply to this email.</p>
                            <p style="margin:16px 0 0 0;">Thanks,<br>{{ $app_name }}</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
