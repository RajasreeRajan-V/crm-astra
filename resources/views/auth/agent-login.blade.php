<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f7fa;
            font-family: 'Arial', sans-serif;
        }
        .login-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 30px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .login-container h2 {
            text-align: center;
            margin-bottom: 30px;
            font-size: 24px;
            font-weight: 600;
            color: #007bff;
        }
        .form-label {
            font-weight: 600;
        }
        .form-control {
            border-radius: 5px;
        }
        .btn-primary {
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            border: none;
            border-radius: 5px;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .form-check-label {
            font-weight: 500;
        }
        .alert-danger {
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>User Login</h2>

        <!-- Display any errors -->
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Agent Login Form -->
        <form action="{{ route('agent.login.submit') }}" method="POST"  autocomplete="off">
            @csrf
            <!--<div class="mb-3">-->
            <!--    <label for="phone_no" class="form-label">Phone No</label>-->
            <!--    <input type="number" name="phone_no" id="phone_no" class="form-control" required>-->
            <!--</div>-->
           <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" 
                       name="email" 
                       id="email" 
                       class="form-control" 
                       placeholder="Enter your email" 
                       required
                       autocomplete="off">
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary">Login</button>
        </form>
        <div class="mb-3 mt-1 text-center">
    <a href="{{ route('agent.password.request') }}" class="text-primary">Forgot Password?</a>
     </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
