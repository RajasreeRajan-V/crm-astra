@extends('agentlogin.layout') 
@section('content')
<style>

    .profile-container {
        max-width: 700px;
        margin: auto;
        background-color: #ffffff;
        border-radius: 15px;
        overflow: hidden;
        margin-top: 20px;
    }

    .profile-header {
        background: linear-gradient(135deg, #6c63ff, #a098ff);
        padding: 40px 20px;
        color: white;
        text-align: center;
    }

    .avatar {
        width: 120px;
        height: 120px;
        margin: auto;
        background-color: #ffffff;
        color: #6c63ff;
        font-size: 3rem;
        font-weight: bold;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 5px solid white;
        border-radius: 50%;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
    }

    .profile-info {
        text-align: center;
        margin-top: 20px;
    }

    .profile-info h3 {
        margin: 0;
        font-size: 2rem;
        font-weight: bold;
        color: #333333;
    }

    .profile-info p {
        margin: 5px 0;
        font-size: 1.2rem;
        color: #6c757d;
    }

    .profile-details {
        padding: 20px;
        text-align: left;
    }

    .profile-details p {
        font-size: 1rem;
        margin: 10px 0;
    }

    .profile-buttons {
        text-align: center;
        padding: 20px;
        background-color: #f4f7f9;
    }

    .btn-primary, .btn-secondary {
        padding: 10px 30px;
        font-size: 1.1rem;
        margin: 5px;
    }

    .btn-primary:hover {
        background-color: #5a54e0;
    }

    .btn-secondary:hover {
        background-color: #6c757d;
    }

    .btn i {
        margin-right: 5px;
    }

    /* Responsive Styles */
    @media (max-width: 768px) {
        .profile-container {
            margin: 20px 10px;
        }

        .profile-header {
            padding: 30px 15px;
        }

        .avatar {
            width: 100px;
            height: 100px;
            font-size: 2.5rem;
        }

        .profile-info h3 {
            font-size: 1.5rem;
        }

        .profile-info p {
            font-size: 1rem;
        }

        .profile-details p {
            font-size: 0.9rem;
        }

        .btn-primary, .btn-secondary {
            padding: 8px 20px;
            font-size: 1rem;
        }
    }

    @media (max-width: 576px) {
        .profile-container {
            margin: 10px;
        }

        .profile-header {
            padding: 20px 10px;
        }

        .avatar {
            width: 80px;
            height: 80px;
            font-size: 2rem;
        }

        .profile-info h3 {
            font-size: 1.2rem;
        }

        .profile-info p {
            font-size: 0.9rem;
        }

        .profile-details p {
            font-size: 0.8rem;
        }

        .btn-primary, .btn-secondary {
            padding: 6px 15px;
            font-size: 0.9rem;
        }
    }
</style>

<div class="d-flex justify-content-center">
    <!-- Main Content -->
    <div class="content mt-5">
        <div class="profile-container">
            <!-- Profile Header -->
            <div class="profile-header">
                <div class="avatar">
                    {{ strtoupper(substr($agent->name, 0, 1)) }}
                </div>
            </div>

            <!-- Profile Info -->
            <div class="profile-info">
                <h3>{{ $agent->name }}</h3>
                <p>{{ $agent->email }}</p>
            </div>

            <!-- Profile Details -->
            <div class="profile-details">
                <p><strong>User ID:</strong> {{ $agent->id }}</p>
                <p><strong>Phone No:</strong> {{ $agent->phone_no }} </p>
                <p><strong>Role:</strong> Sales Agent</p>
                <p><strong>Joined On:</strong> {{ $agent->created_at->format('M d, Y') }}</p>
            </div>

            <!-- Buttons -->
            <div class="profile-buttons">
                <a href="{{ route('agent.profile.edit') }}" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Edit Profile
                </a>
                <a href="{{ route('agent.dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-home"></i> Back to Dashboard
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
