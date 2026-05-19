<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="x-apple-disable-message-reformatting">
    <title>{{ config('app.name') }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&display=swap');
        html, body { margin: 0; padding: 0; width: 100% !important; font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; background-color: #f1f5f9; }
        * { box-sizing: border-box; }
        .email-wrapper { width: 100%; background-color: #f1f5f9; padding: 40px 20px; }
        .email-container { max-width: 600px; margin: 0 auto; }
        .header { background: #0f172a; border-radius: 24px 24px 0 0; padding: 36px 48px; text-align: center; }
        .logo-mark { display: inline-flex; align-items: center; gap: 10px; margin-bottom: 8px; }
        .logo-icon { width: 40px; height: 40px; background: #ef4444; border-radius: 12px; display: inline-flex; align-items: center; justify-content: center; }
        .logo-text { font-size: 22px; font-weight: 900; color: white; letter-spacing: -0.5px; }
        .logo-text span { color: #ef4444; }
        .header-sub { font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 3px; margin: 0; }
        .body-panel { background: white; padding: 52px 48px; }
        .status-badge { display: inline-block; background: #fef2f2; color: #ef4444; font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 3px; padding: 6px 16px; border-radius: 100px; border: 1px solid #fecaca; margin-bottom: 32px; }
        .title { font-size: 28px; font-weight: 900; color: #0f172a; margin: 0 0 16px; line-height: 1.2; letter-spacing: -0.5px; }
        .body-text { font-size: 15px; color: #64748b; line-height: 1.8; margin: 0 0 32px; }
        .action-btn { display: block; background: #ef4444; color: white !important; text-decoration: none; font-size: 13px; font-weight: 800; text-transform: uppercase; letter-spacing: 2px; padding: 18px 40px; border-radius: 16px; text-align: center; margin: 0 0 32px; box-shadow: 0 8px 24px rgba(239,68,68,0.3); }
        .divider { border: none; border-top: 1px solid #f1f5f9; margin: 32px 0; }
        .link-fallback { font-size: 12px; color: #94a3b8; line-height: 1.8; word-break: break-all; }
        .link-fallback a { color: #64748b; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin: 28px 0; }
        .info-cell { background: #f8fafc; border-radius: 12px; padding: 16px; border: 1px solid #f1f5f9; }
        .info-label { font-size: 9px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 2px; display: block; margin-bottom: 4px; }
        .info-value { font-size: 13px; font-weight: 700; color: #0f172a; }
        .footer { background: #0f172a; border-radius: 0 0 24px 24px; padding: 28px 48px; text-align: center; }
        .footer-text { font-size: 11px; color: #475569; margin: 0; line-height: 1.8; }
        .footer-text a { color: #64748b; text-decoration: none; }
        .security-note { background: #fff7ed; border: 1px solid #fed7aa; border-radius: 12px; padding: 16px 20px; margin-top: 28px; }
        .security-note p { font-size: 12px; color: #92400e; margin: 0; line-height: 1.6; }
        .security-note strong { font-weight: 700; }
    </style>
</head>
<body>
<div class="email-wrapper">
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <div class="logo-mark">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                <span class="logo-text">ResQ<span>Flow</span></span>
            </div>
            <p class="header-sub">Emergency Intelligence Network</p>
        </div>

        <!-- Body -->
        <div class="body-panel">
            <span class="status-badge">⚡ Secure Action Required</span>
            
            {{ $slot }}

            <div class="security-note">
                <p><strong>Security Notice:</strong> ResQFlow will never ask for your password via email. If you did not initiate this action, please secure your account immediately by contacting support.</p>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p class="footer-text">
                © {{ date('Y') }} ResQFlow Intelligence Network. All rights reserved.<br>
                <a href="#">Privacy Policy</a> · <a href="#">Terms of Service</a> · <a href="#">Support</a>
            </p>
        </div>
    </div>
</div>
</body>
</html>
