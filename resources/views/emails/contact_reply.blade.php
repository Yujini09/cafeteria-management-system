<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reply to Your Message</title>
</head>
<body style="margin:0;padding:0;background:#f3f4f6;font-family:Arial,Helvetica,sans-serif;color:#111827;">
    <div style="max-width:640px;margin:0 auto;padding:24px;">
        <div style="background:#ffffff;border-radius:16px;overflow:hidden;border:1px solid #e5e7eb;">
            <div style="background:#00462E;padding:20px 24px;color:#ffffff;">
                <h1 style="margin:0;font-size:20px;line-height:1.2;">{{ $app_name }}</h1>
                <p style="margin:6px 0 0;font-size:14px;opacity:0.9;">Reply to your message</p>
            </div>

            <div style="padding:24px;">
                <p style="margin:0 0 12px;font-size:15px;color:#111827;">Hi {{ $recipient_name }},</p>

                <div style="border:1px solid #e5e7eb;border-radius:12px;padding:16px;background:#f9fafb;margin-bottom:16px;">
                    <p style="margin:0 0 8px;font-size:14px;color:#6b7280;">Reply</p>
                    <p style="margin:0;font-size:15px;line-height:1.6;color:#111827;white-space:pre-line;">
                        {!! nl2br(e($reply_message)) !!}
                    </p>
                </div>

                <div style="margin-bottom:16px;">
                    <p style="margin:0;font-size:14px;color:#6b7280;">Replied by</p>
                    <p style="margin:6px 0 0;font-size:14px;color:#374151;">{{ $admin_name }}</p>
                    <p style="margin:4px 0 0;font-size:14px;color:#374151;">{{ $replied_at }}</p>
                </div>

                <div style="border:1px dashed #e5e7eb;border-radius:12px;padding:16px;background:#ffffff;">
                    <p style="margin:0 0 8px;font-size:13px;color:#6b7280;">Your original message</p>
                    <p style="margin:0;font-size:14px;line-height:1.6;color:#111827;white-space:pre-line;">{{ $original_message }}</p>
                </div>
            </div>

            <div style="padding:16px 24px;background:#f9fafb;border-top:1px solid #e5e7eb;">
                <p style="margin:0;font-size:12px;color:#6b7280;">If you need further assistance, just reply to this email.</p>
            </div>
        </div>
    </div>
</body>
</html>
