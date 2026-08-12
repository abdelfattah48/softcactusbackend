<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Reset Your Password</title>
  <style>
    body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f4f4f5; margin: 0; padding: 0; }
    .wrapper { max-width: 560px; margin: 40px auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,.08); }
    .header { background: #310566; padding: 32px 40px; text-align: center; }
    .header h1 { color: #ffffff; font-size: 22px; margin: 0; font-weight: 700; letter-spacing: -0.3px; }
    .body { padding: 40px; }
    .body p { color: #374151; font-size: 15px; line-height: 1.6; margin: 0 0 16px; }
    .btn { display: inline-block; margin: 8px 0 24px; padding: 14px 32px; background: #310566; color: #ffffff !important; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 15px; }
    .url { word-break: break-all; color: #6b7280; font-size: 13px; background: #f9fafb; padding: 12px; border-radius: 6px; border: 1px solid #e5e7eb; }
    .footer { padding: 24px 40px; border-top: 1px solid #e5e7eb; text-align: center; }
    .footer p { color: #9ca3af; font-size: 13px; margin: 0; }
  </style>
</head>
<body>
  <div class="wrapper">
    <div class="header">
      <h1>Soft Cactus Backoffice</h1>
    </div>
    <div class="body">
      <p>Hello {{ $name }},</p>
      <p>We received a request to reset the password for your backoffice account. Click the button below to set a new password:</p>
      <p style="text-align:center">
        <a href="{{ $resetUrl }}" class="btn">Reset My Password</a>
      </p>
      <p>This link will expire in <strong>60 minutes</strong>.</p>
      <p>If the button doesn't work, copy and paste this URL into your browser:</p>
      <div class="url">{{ $resetUrl }}</div>
      <p style="margin-top:24px; color:#6b7280; font-size:13px;">If you did not request a password reset, you can safely ignore this email — your password will not be changed.</p>
    </div>
    <div class="footer">
      <p>© {{ date('Y') }} Soft Cactus. All rights reserved.</p>
    </div>
  </div>
</body>
</html>
