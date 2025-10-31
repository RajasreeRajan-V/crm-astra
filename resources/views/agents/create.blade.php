@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <div class="card mx-auto shadow-lg rounded-4" style="max-width: 600px; border: 2px solid #ddd;">
            <div class="card-body p-5">
                <h2 class="text-center text-dark mb-4">Add New User</h2>

                <!-- Display Validation Errors -->
                @if ($errors->any())
                    <div class="alert alert-danger fade show" role="alert">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('agents.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="name" class="form-label fw-bold" style="color: #343a40;">Name</label>
                        <input type="text" class="form-control rounded-3 @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="email" class="form-label fw-bold" style="color: #343a40;">Email</label>
                        <input type="email" class="form-control rounded-3 @error('email') is-invalid @enderror" 
                               id="email" name="email" value="{{ old('email') }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="phone_no" class="form-label fw-bold" style="color: #343a40;">Phone Number</label>
                        <input type="text" class="form-control rounded-3 @error('phone_no') is-invalid @enderror" 
                            id="phone_no" name="phone_no" value="{{ old('phone_no') }}" required>
                        @error('phone_no')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label fw-bold" style="color: #343a40;">Password</label>
                        <input type="password" class="form-control rounded-3 @error('password') is-invalid @enderror" 
                               id="password" name="password" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label fw-bold" style="color: #343a40;">Re-enter Password</label>
                        <input type="password" class="form-control rounded-3" id="password_confirmation" 
                               name="password_confirmation" required>
                    </div>

                    <button type="submit" class="btn w-100" style="background: linear-gradient(135deg, #343a40, #495057); color: white; border-radius: 25px; padding: 12px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); transition: transform 0.2s;">
                        <strong>Add User</strong>
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
