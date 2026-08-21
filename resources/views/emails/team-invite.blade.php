<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Team Invite</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f6f8fa;
            margin: 0;
            padding: 40px 0;
        }
        .container {
            max-width: 580px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            padding: 48px 40px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 32px;
        }
        .logo {
            background: #6b8c5c;
            color: white;
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 16px;
        }
        h1 {
            font-size: 24px;
            color: #1f2937;
            margin: 0 0 8px 0;
        }
        .subtitle {
            color: #6b7280;
            font-size: 16px;
            margin: 0;
        }
        .content {
            color: #374151;
            line-height: 1.6;
            margin-bottom: 32px;
        }
        .role-badge {
            display: inline-block;
            background: #e8f0e5;
            color: #6b8c5c;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
        }
        .btn {
            display: inline-block;
            background: #6b8c5c;
            color: white !important;
            padding: 14px 32px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            transition: background 0.2s;
        }
        .btn:hover {
            background: #5a7a4a;
        }
        .footer {
            text-align: center;
            color: #9ca3af;
            font-size: 14px;
            margin-top: 32px;
            padding-top: 32px;
            border-top: 1px solid #e5e7eb;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">E</div>
            <h1>You're invited to join {{ $company }}</h1>
            <p class="subtitle">on EPMP - Enterprise Project Management</p>
        </div>

        <div class="content">
            <p>Hi there,</p>
            <p><strong>{{ $inviter }}</strong> has invited you to join <strong>{{ $company }}</strong> as a <span class="role-badge">{{ $role }}</span>.</p>
            <p>Click the button below to accept the invitation and get started.</p>
            <p style="text-align: center; margin: 32px 0;">
                <a href="{{ $acceptUrl }}" class="btn">Accept Invitation</a>
            </p>
            <p style="font-size: 14px; color: #6b7280;">
                If you don't have an account yet, you'll be able to create one for free.
            </p>
        </div>

        <div class="footer">
            <p style="margin: 0;">EPMP - Enterprise Project Management</p>
            <p style="margin: 4px 0 0 0; font-size: 12px;">© 2026 EPMP. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
