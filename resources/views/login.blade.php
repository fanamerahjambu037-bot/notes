<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Login - IT Knowledge Base</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center p-4">
<form method="POST" action="{{ route('login.post') }}" class="bg-white rounded-lg shadow p-6 w-full max-w-sm space-y-4">
    @csrf
    <h1 class="text-xl font-bold text-center">IT Knowledge Base</h1>
    @if($errors->any())
        <div class="bg-red-100 text-red-700 rounded p-2 text-sm">{{ $errors->first() }}</div>
    @endif
    <input name="username" required autofocus placeholder="Username" class="w-full border rounded px-3 py-2 text-sm">
    <input name="password" type="password" required placeholder="Password" class="w-full border rounded px-3 py-2 text-sm">
    <button class="w-full bg-blue-600 hover:bg-blue-700 text-white rounded py-2 text-sm">Masuk</button>
</form>
</body>
</html>
