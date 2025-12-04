<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Confirmar exclusão de tarefas</title>
    <link href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Nunito', sans-serif; margin:0; background: #f6f8fa; }
        .container { display:flex; min-height:100vh; align-items:center; justify-content:center; }
        .card { background:white; max-width:720px; width:100%; padding:28px; border-radius:12px; box-shadow: 0 6px 20px rgba(0,0,0,0.08); }
        .btn { background: #111827; color:white; padding:10px 16px; border-radius:8px; text-decoration:none; border:none; cursor:pointer }
        .danger { background:#ef4444 }
    </style>
</head>
<body>
<div class="container">
    <div class="card">
        <h1>Confirmar exclusão</h1>
        <p>Você tem certeza que deseja excluir <strong>todas</strong> as suas tarefas? Esta ação não pode ser desfeita.</p>

        <div style="display:flex;gap:12px;margin-top:18px;">
            <form method="POST" action="{{ route('tasks.clear') }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn danger">Sim, excluir todas</button>
            </form>

            <a href="{{ url('/dashboard') }}" class="btn">Cancelar</a>
        </div>
    </div>
</div>
</body>
</html>
