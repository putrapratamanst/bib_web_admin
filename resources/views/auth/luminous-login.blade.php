<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Luminous</title>
    <link rel="stylesheet" href="{{ asset('assets/bootstrap/css/bootstrap.min.css') }}">
</head>
<body class="bg-dark d-flex align-items-center min-vh-100">
    <div class="container">
        <div class="card mx-auto shadow" style="max-width: 420px">
            <div class="card-body p-4">
                <h3 class="mb-1">Luminous</h3>
                <p class="text-muted mb-4">Login pengelolaan tagihan sosialisasi</p>
                @if ($errors->any())
                    <div class="alert alert-danger">{{ $errors->first() }}</div>
                @endif
                <form method="POST" action="{{ route('luminous.login.store') }}">
                    @csrf
                    <input class="form-control mb-3" type="email" name="email" placeholder="Email" value="{{ old('email') }}" required autofocus>
                    <input class="form-control mb-3" type="password" name="password" placeholder="Password" required>
                    <button class="btn btn-primary w-100">Login</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
