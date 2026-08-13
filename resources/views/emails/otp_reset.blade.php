<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Your Verification Code</title>
  <style>
    body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f4f4f5; margin: 0; padding: 0; }
    .wrapper { max-width: 560px; margin: 40px auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,.08); }
    .header { background: #310566; padding: 32px 40px; text-align: center; }
    .header h1 { color: #ffffff; font-size: 22px; margin: 0; font-weight: 700; }
    .body { padding: 40px; text-align: center; }
    .body p { color: #374151; font-size: 15px; line-height: 1.6; margin: 0 0 16px; text-align: left; }
    .otp { display: inline-block; font-size: 42px; font-weight: 800; letter-spacing: 12px; color: #310566; background: #f3f0ff; border: 2px solid #310566; border-radius: 12px; padding: 16px 32px; margin: 16px 0 24px; }
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
      <p>Use the code below to reset your password. This code expires in <strong>15 minutes</strong>.</p>
      <div class="otp">{{ $otp }}</div>
      <p style="color:#6b7280; font-size:13px;">If you didn't request a password reset, ignore this email.</p>
    </div>
    <div class="footer">
      <p>© {{ date('Y') }} Soft Cactus. All rights reserved.</p>
    </div>
  </div>
</body>
</html>
