<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
/* Ensure sidebar is hidden by default on mobile screens */
@media (max-width: 844px) {
    /* Sidebar hidden by default */
    #sidebar {
        position:fixed;
        top: 56px;
        left: -250px; /* Start from off-screen */
        height: 100%;
        width: 250px;
        background: linear-gradient(135deg, #2c3e50, #4ca1af);
        color:#e8eaf6;
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
        top: 23px;
        left: 82px;
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
        position:fixed;
        top: 56px; /* Adjust to avoid overlap with the navbar */
        left: 0;
        width: 250px;
        height: 100%;
        background: linear-gradient(135deg, #2c3e50, #4ca1af);
        color:#e8eaf6;
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

body {
    background: linear-gradient(135deg, #e8eaf6, #f1f8e9);
    font-family: 'Roboto', sans-serif; /* Use a modern font */
}


.navbar {
    background: linear-gradient(135deg, #2c3e50, #4ca1af);
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}
.navbar-brand {
    font-weight: bold;
    font-size: 1.5rem;
}


.btn-primary {
    background: linear-gradient(135deg, #6c5ce7,rgb(98, 233, 233));
    border: none;
    color: #ffffff;
    transition: transform 0.2s ease;
}
.btn-primary:hover {
    transform: scale(1.05);
    background: linear-gradient(135deg, #5a54e0,rgb(16, 17, 17));
}

.modal-content {
    border-radius: 10px;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
}
.modal-backdrop {
    background-color: rgba(0, 0, 0, 0.7);
}
#sidebar .nav-link {
    padding: 6px 13px; /* Reduce padding for links */
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
                <!-- Admin user icon -->
                <span class="text-white me-3" data-bs-toggle="modal" data-bs-target="#adminDetailsModal">
                    <i class="fas fa-user-circle"></i>
                </span>
                <!-- Logout button -->
                <form action="{{ route('admin.logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-light">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="modal fade" id="adminDetailsModal" tabindex="-1" aria-labelledby="adminDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="adminDetailsModalLabel">Admin Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Name:</strong> {{ Auth::user()->name }}</p>
                    <p><strong>Username:</strong> {{ Auth::user()->username }}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex">
        <!-- Sidebar -->
        <div id="sidebar" class="bg-light">
            @include('layouts.sidebar')
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
