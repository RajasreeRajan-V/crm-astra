<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Telecall CRM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f8f9fa;
        }
        .login-options {
            text-align: center;
        }
        .login-options h1 {
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <div class="login-options">
        <h1>Welcome to Telecall CRM</h1>
        <div class="d-grid gap-3 col-6 mx-auto">
            <a href="{{ route('login') }}" class="btn btn-primary btn-lg">Admin Login</a>
            <a href="{{ route('agent.login') }}" class="btn btn-secondary btn-lg">User Login</a>
        </div>
    </div>
</body>
</html>
