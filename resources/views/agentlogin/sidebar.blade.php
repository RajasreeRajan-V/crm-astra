<div class="d-flex flex-column flex-shrink-0 p-3 bg-dark" style="width: 250px; height: 100vh; border-right: 1px solid #ddd;">
    <a href="{{ route('agent.dashboard') }}" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-light text-decoration-none">
        <span class="fs-4 fw-bold"><i class="fas fa-home me-2"></i> Dashboard</span>
    </a>
    <hr>
    <ul class="nav nav-pills flex-column mb-auto">
        <!-- Leads Section -->
        <li class="nav-item">
            <a href="{{ route('agent.leads') }}" class="nav-link link-light fs-5 fw-bold d-flex align-items-center">
                <i class="fas fa-user-friends me-2"></i> Leads
            </a>
        </li>
        <hr>

        <!-- Call Logs Section -->
        <li class="nav-item">
            <a href="{{ route('agent.callLogs') }}" class="nav-link link-light fs-5 fw-bold d-flex align-items-center">
                <i class="fas fa-phone-alt me-2"></i> Call Logs
            </a>
        </li>
        <hr>
        <li>
            <a href="{{ route('agent.transactions') }}"class="nav-link link-light fs-5 fw-bold d-flex align-items-center">
                <i class="fas fa-exchange-alt me-2"></i> Transactions
            </a>
        </li>
        <hr>

        <!-- Profile Section -->
        <li class="nav-item">
            <a href="{{ route('agent.profile') }}" class="nav-link link-light fs-5 fw-bold d-flex align-items-center">
                <i class="fas fa-user me-2"></i> Profile
            </a>
        </li>
    </ul>
    <hr>
</div>
