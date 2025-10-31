@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1 class="mb-4 text-center">Edit User</h1>

    <div class="card shadow-sm p-4">
        <div class="card-body">
            <!-- Display validation errors -->
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('agents.update', $agent->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group mb-4">
                    <label for="name" class="form-label fs-5">Name</label>
                    <input type="text" class="form-control rounded-pill shadow-sm @error('name') is-invalid @enderror"
                           id="name" name="name" value="{{ old('name', $agent->name) }}" required>
                    @error('name')
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group mb-4">
                    <label for="email" class="form-label fs-5">Email Address</label>
                    <input type="email" class="form-control rounded-pill shadow-sm @error('email') is-invalid @enderror"
                           id="email" name="email" value="{{ old('email', $agent->email) }}" required>
                    @error('email')
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group mb-4">
                    <label for="phone_no" class="form-label fs-5">Phone No</label>
                    <input type="text" class="form-control rounded-pill shadow-sm @error('phone_no') is-invalid @enderror"
                           id="phone_no" name="phone_no" value="{{ old('phone_no', $agent->phone_no) }}" required>
                    @error('phone_no')
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('agents.index') }}" class="btn btn-danger rounded-pill px-4">
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">
                        Update User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
