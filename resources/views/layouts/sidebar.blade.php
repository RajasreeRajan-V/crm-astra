<!-- resources/views/layouts/sidebar.blade.php -->
<div class="d-flex flex-column flex-shrink-0 p-3" style="width: 250px;">
    <a href="{{ route('dashboard.index') }}" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-light text-decoration-none">
        <span class="fs-4">Dashboard</span>
    </a>
    <hr>
    <ul class="nav nav-pills flex-column mb-auto">
        <!-- Leads Section -->
        <li class="nav-item">
            <a href="#" class="nav-link link-light fs-5 fw-bold">
                Leads
            </a>
        </li>
        <li class="nav-item ms-3">
            <a href="{{ route('leads.create') }}" class="nav-link link-light">Add Lead</a>
        </li>
        <li class="nav-item ms-3">
            <a href="{{ route('leads.index') }}" class="nav-link link-light">View Leads</a>
        </li>
        <li class="nav-item ms-3">
            <a href="{{ route('leads.transfer') }}" class="nav-link link-light">Transfer Leads</a>
        </li>
        <li class="nav-item ms-3">
            <a href="{{ route('leads.updateStatusIndex') }}" class="nav-link link-light">Update Status</a>
        </li>
        <li class="nav-item ms-3">
            <a href="{{ route('leads.unassigned') }}" class="nav-link link-light">Unassigned Leads</a>
        </li>

        <!-- Divider -->
        <hr>

        <!-- Agents Section -->
        <li class="nav-item">
            <a href="#" class="nav-link link-light fs-5 fw-bold">
                Users
            </a>
        </li>
        <li class="nav-item ms-3">
            <a href="{{ route('agents.create') }}" class="nav-link link-light">Add User</a>
        </li>
        <li class="nav-item ms-3">
            <a href="{{ route('agents.index') }}" class="nav-link link-light">View Users</a>
        </li>
        <li class="nav-item ms-3">
            <a href="{{ route('agents.performance') }}" class="nav-link link-light">User Performance</a>
        </li>
        <li class="nav-item ms-3">
            <a href="{{ route('callLogs.index') }}" class="nav-link link-light">Call Logs</a>
        </li>
        <li class="nav-item ms-3">
            <a href="{{ route('track.agent') }}" class="nav-link link-light">Track Agent</a>
        </li>
        <li class="nav-item ms-3">
            <a href="{{ route('transactions.index') }}" class="nav-link link-light">Transactions</a>
        </li>
    </ul>
</div>