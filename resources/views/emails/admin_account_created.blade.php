<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $app_name }} - Admin Account Created</title>
</head>
<body style="margin:0;padding:0;background-color:#f6f8fb;font-family:Arial,sans-serif;color:#1f2937;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f6f8fb;padding:24px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:600px;background:#ffffff;border:1px solid #e5e7eb;border-radius:10px;overflow:hidden;">
                    <tr>
                        <td style="background:#057C3C;padding:20px 24px;color:#ffffff;font-size:20px;font-weight:700;">
                            {{ $app_name }}
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:24px;">
                            <p style="margin:0 0 14px 0;font-size:16px;">Hello {{ $user_name }},</p>
                            <p style="margin:0 0 14px 0;line-height:1.6;">
                                An admin account has been created for you. Use the temporary password below to sign in.
                                You will be required to change your password after login.
                            </p>
                            <div style="margin:18px 0;padding:14px 16px;background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;">
                                <p style="margin:0 0 6px 0;font-size:13px;color:#6b7280;">Temporary Password</p>
                                <p style="margin:0;font-size:18px;font-weight:700;color:#111827;letter-spacing:0.2px;">{{ $temporary_password }}</p>
                            </div>
                            <p style="margin:0 0 18px 0;line-height:1.6;">
                                <a href="{{ $login_url }}" style="display:inline-block;background:#057C3C;color:#ffffff;text-decoration:none;padding:10px 16px;border-radius:8px;font-weight:600;">
                                    Sign In
                                </a>
                            </p>
                            <p style="margin:0 0 8px 0;line-height:1.6;">
                                If you did not expect this email, please contact support.
                            </p>
                            <p style="margin:0;line-height:1.6;">
                                Thanks,<br>{{ $app_name }}
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
