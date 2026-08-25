<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Password Reset</title>
</head>
<body style="font-family: Arial, sans-serif; background:#f4f4f4; padding:20px;">
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center">
                <table width="600" style="background:#ffffff; padding:30px; border-radius:6px;">
                    <tr>
                        <td>
                            <h2>Hello Employing Bulls Management User,</h2>
                            <p>You requested to reset your password for the Management Panel. Click on below Reset Password Button to continue.</p>

                            <p style="margin:30px 0;">
                                <a href="{{ $resetLink }}"
                                   style="background:#3E4093;color:#fff;padding:12px 25px;
                                   text-decoration:none;border-radius:4px;">
                                    Reset Account Password
                                </a>
                            </p>

                            <p>If you didn’t request this, you can safely ignore this email.</p>

                            <p style="margin-top:40px;">
                                Regards,<br>
                                <strong>Employing Bulls Team</strong>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
