@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1 class="mb-4">Users</h1>
    <a href="{{ route('agents.create') }}" class="btn btn-primary mb-3">Add New User</a>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone No</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($agents as $index => $agent)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $agent->name }}</td>
                        <td>{{ $agent->email }}</td>
                        <td>{{ $agent->phone_no }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                            <a href="{{ route('agents.show', $agent->id) }}" class="btn btn-info btn-sm">View</a>
                            <a href="{{ route('agents.edit', $agent->id) }}" class="btn btn-warning btn-sm">Edit</a>
                            <form action="{{ route('agents.destroy', $agent->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this agent?')">Delete</button>
                            </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
