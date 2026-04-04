<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Under Maintenance – {{ config('app.name', 'CricZone') }}</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: system-ui, -apple-system, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
            color: #f1f5f9;
        }
        .card {
            text-align: center;
            padding: 3rem 2.5rem;
            max-width: 500px;
            width: 90%;
        }
        .icon {
            font-size: 4rem;
            margin-bottom: 1.5rem;
            animation: spin 6s linear infinite;
            display: inline-block;
        }
        @keyframes spin {
            0%   { transform: rotate(0deg); }
            25%  { transform: rotate(20deg); }
            50%  { transform: rotate(0deg); }
            75%  { transform: rotate(-20deg); }
            100% { transform: rotate(0deg); }
        }
        h1 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.75rem;
            color: #f97316;   /* orange accent — matches CricZone brand */
        }
        p {
            font-size: 1.05rem;
            color: #94a3b8;
            line-height: 1.7;
            margin-bottom: 1.5rem;
        }
        .badge {
            display: inline-block;
            background: rgba(249,115,22,0.15);
            border: 1px solid rgba(249,115,22,0.4);
            color: #f97316;
            padding: 0.35rem 1.1rem;
            border-radius: 9999px;
            font-size: 0.85rem;
            letter-spacing: 0.05em;
            font-weight: 600;
        }
        .login-link {
            margin-top: 2rem;
            display: block;
            font-size: 0.8rem;
            color: #475569;
            text-decoration: none;
        }
        .login-link:hover { color: #94a3b8; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">🏏</div>
        <h1>We'll be back soon!</h1>
        <p>
            {{ config('app.name', 'CricZone') }} is currently undergoing scheduled maintenance.<br>
            We're working hard to improve your experience. Please check back shortly.
        </p>
        <span class="badge">🔧 Under Maintenance</span>
    </div>
</body>
</html>
