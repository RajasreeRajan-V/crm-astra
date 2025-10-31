<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agent Dashboard</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
/* Ensure sidebar is hidden by default on mobile screens */
@media (max-width: 844px) {
    /* Sidebar hidden by default */
    #sidebar {
        position: fixed;
        top: 56px;
        left: -250px; /* Start from off-screen */
        height: 100%;
        width: 250px;
        background-color: #f8f9fa;
        overflow: hidden;
        transition: left 0.3s ease-in-out;
        z-index: 999; /* Ensure sidebar is above content */
    }

    #sidebar.collapsed {
        left: 0; /* Sidebar appears from the left when toggled */
    }

    /* Adjust main content */
    #main-content {
        transition: margin-left 0.3s ease-in-out;
    }

    /* Hide the sidebar toggle icon on larger screens */
    .sidebar-toggle {
        display: block;
        position: absolute;
        top: 20px;
        left: 71px;
        z-index: 1000; /* Make sure the toggle button is on top */
        cursor: pointer;
    }

    /* Ensure sidebar links are visible when sidebar is expanded */
    #sidebar ul {
        display: none;
    }

    #sidebar.collapsed ul {
        display: block; /* Add some padding if needed */
    }

    /* Sidebar header visibility (Admin name) */
    #sidebar .navbar-brand {
        padding: 20px 0;
    }
}

@media (min-width: 845px) {
    /* Sidebar is always visible on larger screens */
    #sidebar {
        position: fixed;
        top: 56px; /* Adjust to avoid overlap with the navbar */
        left: 0;
        width: 250px;
        height: 100%;
        background-color: #f8f9fa;
        z-index: 999;
        overflow-y: auto;
    }

    /* Main content shift */
    #main-content {
        margin-left: 250px; /* Sidebar width */
    }

    /* Hide sidebar toggle on large screens */
    .sidebar-toggle {
        display: none;
    }
}

@media (min-width: 768px) and (max-width: 1040px) {
    .col-md-4 {
        flex: 0 0 33.333%; /* Three cards per row */
        max-width: 33.333%;
    }
}
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container-fluid">
            <!-- Admin name on the leftmost side -->
            <span class="navbar-brand">{{ Auth::user()->name }}</span>

            <!-- Sidebar toggle icon -->
            <i class="fas fa-bars sidebar-toggle text-white ms-3" onclick="toggleSidebar()"></i>

            <div class="ms-auto d-flex align-items-center">
                <!-- Logout button -->
                <form action="{{ route('agent.logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-light">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="d-flex">
        <!-- Sidebar -->
        <div id="sidebar" class="bg-light">
            @include('agentlogin.sidebar')
        </div>

        <!-- Main Content Area -->
        <div class="container my-4" id="main-content">
            @yield('content')
        </div>
    </div>

    <!-- Bootstrap JavaScript and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const statusSelect = document.getElementById('status');
            const extraFields = document.getElementById('extra-fields');
            const rateInput = document.getElementById('rate');
            const balanceInput = document.getElementById('balance');
            const balanceError = document.getElementById('balance-error');

            // Show/hide extra fields
            function toggleExtraFields() {
                if (statusSelect.value === 'closed') {
                    extraFields.style.display = 'block';
                } else {
                    extraFields.style.display = 'none';
                    rateInput.value = '';
                    balanceInput.value = '';
                    balanceError.style.display = 'none';
                }
            }

            // Ensure balance <= rate
            function validateBalance() {
                const rateValue = parseFloat(rateInput.value) || 0;
                const balanceValue = parseFloat(balanceInput.value) || 0;

                if (balanceValue > rateValue) {
                    balanceError.style.display = 'block';
                } else {
                    balanceError.style.display = 'none';
                }
            }

            // Event Listeners
            statusSelect.addEventListener('change', toggleExtraFields);
            rateInput.addEventListener('input', validateBalance);
            balanceInput.addEventListener('input', validateBalance);

            // Initialize on load
            toggleExtraFields();
        });

        document.getElementById('setNow').addEventListener('click', function () {
    var now = new Date();
    
    // Format to local time in YYYY-MM-DDTHH:MM
    var year = now.getFullYear();
    var month = String(now.getMonth() + 1).padStart(2, '0'); // Months are zero-indexed
    var day = String(now.getDate()).padStart(2, '0');
    var hours = String(now.getHours()).padStart(2, '0');
    var minutes = String(now.getMinutes()).padStart(2, '0');

    var formattedTime = `${year}-${month}-${day}T${hours}:${minutes}`;
    document.getElementById('call_time').value = formattedTime;
});

        document.getElementById('outcome').addEventListener('change', function () {
        const followUpContainer = document.getElementById('follow-up-date-container');
        if (this.value === 'follow-up' || this.value === 'interested') {
            followUpContainer.style.display = 'block';
        } else {
            followUpContainer.style.display = 'none';
        }
    });
    </script>
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('collapsed');
        }
        // Close sidebar when clicking outside
        document.addEventListener('click', (event) => {
            const sidebar = document.getElementById('sidebar');
            const toggleButton = document.querySelector('.sidebar-toggle');
            if (
                !sidebar.contains(event.target) &&
                !toggleButton.contains(event.target) &&
                sidebar.classList.contains('collapsed')
            ) {
                sidebar.classList.remove('collapsed');
            }
        });
        </script>
</body>
</html>
