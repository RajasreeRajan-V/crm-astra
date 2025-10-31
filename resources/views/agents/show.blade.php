@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1>User Details</h1>
    <div class="card mt-3 mb-4">
        <div class="card-body">
            <h5 class="card-title">{{ $agent->name }}</h5>
            <br>
            <p><strong>Email:</strong> {{ $agent->email }}</p>
            <p><strong>Phone No:</strong> {{ $agent->phone_no }}</p>
        </div>
    </div>

    <h2>Assigned Leads</h2>
    @if($agent->leads->isEmpty())
        <p>No leads assigned to this User.</p>
    @else
        <table class="table table-bordered mt-3">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Contact</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($agent->leads as $lead)
                    <tr>
                        <td>{{ $lead->id }}</td>
                        <td>{{ $lead->name }}</td>
                        <td>{{ $lead->contact }}</td>
                        <td>{{ $lead->status }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
    <a href="{{ route('agents.index') }}" class="btn btn-secondary mt-3">Back to Users</a>
</div>
@endsection
