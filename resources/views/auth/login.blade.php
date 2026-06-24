<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CorpChat - تسجيل الدخول</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --font-sans: 'Cairo', sans-serif;
            --color-bg: #000000;
            --color-card-bg: #09090b;
            --color-card-border: #27272a;
            --color-input-bg: #09090b;
            --color-input-border: #27272a;
            --color-input-focus: #ffffff;
            --color-text-primary: #ffffff;
            --color-text-secondary: #a1a1aa;
            --color-button-bg: #ffffff;
            --color-button-text: #000000;
            --color-button-hover-bg: #e4e4e7;
            --color-error-bg: rgba(239, 68, 68, 0.1);
            --color-error-border: #7f1d1d;
            --color-error-text: #fca5a5;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: var(--font-sans);
        }

        body {
            background-color: var(--color-bg);
            color: var(--color-text-primary);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-card {
            background-color: var(--color-card-bg);
            border: 1px solid var(--color-card-border);
            border-radius: 8px;
            padding: 32px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.5);
        }

        .login-header {
            margin-bottom: 24px;
            text-align: center;
        }

        .login-title {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 6px;
        }

        .login-subtitle {
            color: var(--color-text-secondary);
            font-size: 13px;
        }

        .alert-danger {
            background: var(--color-error-bg);
            border: 1px solid var(--color-error-border);
            color: var(--color-error-text);
            padding: 10px 14px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 13px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 6px;
            color: var(--color-text-secondary);
        }

        .form-input {
            width: 100%;
            height: 40px;
            background-color: var(--color-input-bg);
            border: 1px solid var(--color-input-border);
            border-radius: 6px;
            padding: 0 12px;
            color: var(--color-text-primary);
            font-size: 14px;
            outline: none;
            transition: border-color 0.15s;
        }

        .form-input:focus {
            border-color: var(--color-input-focus);
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: var(--color-text-secondary);
            cursor: pointer;
            margin-bottom: 20px;
        }

        .submit-btn {
            width: 100%;
            height: 40px;
            background-color: var(--color-button-bg);
            color: var(--color-button-text);
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.15s;
        }

        .submit-btn:hover {
            background-color: var(--color-button-hover-bg);
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="login-header">
        <h1 class="login-title">تسجيل الدخول</h1>
        <p class="login-subtitle">CorpChat - نظام المراسلة الداخلي</p>
    </div>

    @if ($errors->any())
        <div class="alert-danger">
            <span>{{ $errors->first() }}</span>
        </div>
    @endif

    <form action="{{ url('/login') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="email" class="form-label">البريد الإلكتروني</label>
            <input type="email" id="email" name="email" class="form-input" placeholder="name@company.com" value="{{ old('email') }}" required autocomplete="email" autofocus>
        </div>

        <div class="form-group">
            <label for="password" class="form-label">كلمة المرور</label>
            <input type="password" id="password" name="password" class="form-input" placeholder="••••••••" required autocomplete="current-password">
        </div>

        <label class="remember-me">
            <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
            <span>تذكرني على هذا الجهاز</span>
        </label>

        <button type="submit" class="submit-btn">تسجيل الدخول</button>
    </form>
</div>

</body>
</html>
