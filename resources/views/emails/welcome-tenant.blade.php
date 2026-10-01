<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
</head>
<body style="margin:0; padding:0; background-color:#FFF5F5; font-family: Arial, Helvetica, sans-serif;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#FFF5F5; padding:32px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px; background:#ffffff; border-radius:12px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,0.06); border:1px solid #F2D5D5;">

                    {{-- Header --}}
                    <tr>
                        <td style="background-color:#D17574; padding:24px 32px;">
                            <h1 style="margin:0; color:#ffffff; font-size:18px; font-weight:700;">
                                {{ $appName }}
                            </h1>
                        </td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td style="padding:32px;">
                            <h2 style="margin:0 0 16px; font-size:22px; color:#111827;">
                                Welcome, {{ $adminName }} 👋
                            </h2>

                            <p style="font-size:15px; line-height:1.6; color:#4b5563; margin:0 0 16px;">
                                Your organization <strong>{{ $companyName }}</strong> has been
                                successfully registered with <strong>{{ $appName }}</strong>.
                                You have been assigned as the primary administrator for your HR system.
                            </p>

                            <p style="font-size:15px; line-height:1.6; color:#4b5563; margin:0 0 24px;">
                                Your login email is: <strong>{{ $adminEmail }}</strong><br>
                                To get started, please set your password by clicking the button below.
                                This link expires in 60 minutes.
                            </p>

                            {{-- Button --}}
                            <table role="presentation" cellpadding="0" cellspacing="0" style="margin:0 auto 24px;">
                                <tr>
                                    <td align="center" style="border-radius:8px; background-color:#D17574;">
                                        <a href="{{ $resetUrl }}"
                                           style="display:inline-block; padding:12px 28px; color:#ffffff;
                                                  text-decoration:none; font-weight:600; font-size:14px;
                                                  border-radius:8px;">
                                            Set Your Password
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="font-size:13px; line-height:1.6; color:#6b7280; margin:0 0 12px;">
                                If the button doesn't work, copy this link into your browser:<br>
                                <a href="{{ $resetUrl }}" style="color:#B86564; word-break:break-all;">
                                    {{ $resetUrl }}
                                </a>
                            </p>

                            <p style="font-size:13px; line-height:1.6; color:#6b7280; margin:0;">
                                For security, please change your password after your first login.
                            </p>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="padding:20px 32px; background-color:#FFF5F5; border-top:1px solid #F2D5D5;">
                            <p style="font-size:12px; color:#9ca3af; line-height:1.5; text-align:center; margin:0;">
                                If you didn't request this, you can safely ignore this email.<br>
                                &copy; {{ date('Y') }} {{ $appName }}. All rights reserved.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>