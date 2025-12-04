<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register</title>
    <link href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Nunito', sans-serif; margin:0; background: #f6f8fa; }
        .container { display:flex; min-height:100vh; align-items:center; justify-content:center; }
        .card { background:white; max-width:420px; width:100%; padding:28px; border-radius:12px; box-shadow: 0 6px 20px rgba(0,0,0,0.08); }
        .logo { font-weight:700; font-size:20px; margin-bottom:8px; color:#101827; }
        label { display:block; margin-top:12px; font-size:13px; color:#334155; }
        input[type="text"], input[type="email"], input[type="password"] { width:100%; padding:12px 14px; margin-top:6px; border-radius:8px; border:1px solid #e6e9ef; background:#fff; }
        .btn { display:inline-block; margin-top:18px; width:100%; padding:12px; background:#111827; color:white; text-align:center; border-radius:8px; text-decoration:none; border:none; cursor:pointer }
        .muted { font-size:13px; color:#6b7280; margin-top:8px; }
        .error { color:#b91c1c; font-size:13px; margin-top:8px; }
        .footer { margin-top:18px; text-align:center; font-size:14px; color:#6b7280; }
        a.link { color:#111827; font-weight:600; text-decoration:none; }
    </style>
</head>
<body>
<div class="container">
    <div class="card">
        <div class="logo">Projek CELK — Cadastro</div>

        @if (
            $errors->any()
        )
        <div class="error">
            <ul style="margin:0;padding-left:18px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ url('/register') }}">
            @csrf
            <label for="name">Nome</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required>

            <label for="email">Email</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required>

            <label for="password">Senha</label>
            <input type="password" name="password" id="password" required>

            <label for="password_confirmation">Confirmar Senha</label>
            <input type="password" name="password_confirmation" id="password_confirmation" required>

            <button type="submit" class="btn">Criar conta</button>
        </form>

        <div class="footer">Já tem conta? <a class="link" href="{{ url('/') }}">Entrar</a></div>
    </div>
</div>
</body>
</html>
