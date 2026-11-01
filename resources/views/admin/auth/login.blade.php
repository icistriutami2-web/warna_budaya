<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Pengelola | WARNA BUDAYA</title>
</head>
<body>
    <main>
        <h1>Login Pengelola</h1>
        <form method="POST" action="{{ route('admin.login.store') }}">
            @csrf
            <label for="email">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required maxlength="255" autocomplete="username">
            @error('email')
                <p role="alert">{{ $message }}</p>
            @enderror

            <label for="password">Password</label>
            <input id="password" name="password" type="password" required maxlength="255" autocomplete="current-password">
            @error('password')
                <p role="alert">{{ $message }}</p>
            @enderror

            <button type="submit">Masuk</button>
        </form>
    </main>
</body>
</html>
