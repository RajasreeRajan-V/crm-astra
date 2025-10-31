    @extends('agentlogin.layout')
    @section('content')
    <style>

        .main-container {
            display: flex;
            justify-content: center;
            align-items: center;
            flex: 1;
            width: 100%;
        }

        .card {
            background-color: #fff;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            width: 500px;
            max-width: 600px;
        }

        .card-header {
            color: black;
            padding: 10px;
            border-radius: 10px;
            text-align: center;
            font-size: 1.5rem;
            font-weight: bold;
        }

        .form-group {
            margin-top: 20px;
            margin-bottom: 20px;
        }

        .form-control {
            border-radius: 10px;
            padding: 10px;
            font-size: 1rem;
        }

        .btn {
            font-size: 1rem;
        }

        .btn-success {
            background-color: #28a745;
            border: none;
            color: white;
        }

        .btn-secondary {
            background-color: #6c757d;
            border: none;
            color: white;
        }

        .btn:hover {
            opacity: 0.9;
        }

        .btn-success:hover {
            background-color: #218838;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
        }

        .text-danger {
            font-size: 0.85rem;
        }

        .footer {
            position: absolute;
            bottom: 10px;
            text-align: center;
            width: 100%;
        }

    </style>

<div class="d-flex">

    <!-- Main Content -->
    <div class="main-container mt-5">
        <div class="card">
            <div class="card-header">
                Edit Agent Profile
            </div>

            <form action="{{ route('agent.profile.update') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $agent->name) }}" required>
                    @error('name')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" id="email" name="email" class="form-control" value="{{ old('email', $agent->email) }}" required>
                    @error('email')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="phone_no" class="form-label">Phone No</label>
                    <input type="text" id="phone_no" name="phone_no" class="form-control" value="{{ old('phone_no', $agent->phone_no) }}" required>
                    @error('phone_no')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Enter a new password (optional)">
                    @error('password')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Re-enter new password">
                </div>

                <div class="form-group d-flex justify-content-between">
                    <button type="submit" class="btn btn-success">Update Profile</button>
                    <a href="{{ route('agent.profile') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection