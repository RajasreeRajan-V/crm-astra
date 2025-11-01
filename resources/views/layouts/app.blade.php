<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard')</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/map.css') }}">
    
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        /* Responsive sidebar and layout */
        @media (max-width: 844px) {
            #sidebar {
                position: fixed;
                top: 56px;
                left: -250px;
                height: 100%;
                width: 250px;
                background: linear-gradient(135deg, #2c3e50, #4ca1af);
                color: #e8eaf6;
                overflow: hidden;
                transition: left 0.3s ease-in-out;
                z-index: 999;
            }
            #sidebar.collapsed {
                left: 0;
            }
            #main-content {
                transition: margin-left 0.3s ease-in-out;
            }
            .sidebar-toggle {
                display: block;
                position: absolute;
                top: 23px;
                left: 82px;
                z-index: 1000;
                cursor: pointer;
            }
            #sidebar ul {
                display: none;
            }
            #sidebar.collapsed ul {
                display: block;
            }
        }

        @media (min-width: 845px) {
            #sidebar {
                position: fixed;
                top: 56px;
                left: 0;
                width: 250px;
                height: 100%;
                background: linear-gradient(135deg, #2c3e50, #4ca1af);
                color: #e8eaf6;
                z-index: 999;
                overflow-y: auto;
            }
            #main-content {
                margin-left: 250px;
            }
            .sidebar-toggle {
                display: none;
            }
        }

        body {
            background: linear-gradient(135deg, #e8eaf6, #f1f8e9);
            font-family: 'Roboto', sans-serif;
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
            background: linear-gradient(135deg, #6c5ce7, rgb(98, 233, 233));
            border: none;
            color: #ffffff;
            transition: transform 0.2s ease;
        }

        .btn-primary:hover {
            transform: scale(1.05);
            background: linear-gradient(135deg, #5a54e0, rgb(16, 17, 17));
        }

        .modal-content {
            border-radius: 10px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }

        .modal-backdrop {
            background-color: rgba(0, 0, 0, 0.7);
        }

        #sidebar .nav-link {
            padding: 6px 13px;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container-fluid">
            <!-- Admin name -->
            <span class="navbar-brand">{{ Auth::user()->name ?? 'Admin Dashboard' }}</span>

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

    <!-- Admin Details Modal -->
    <div class="modal fade" id="adminDetailsModal" tabindex="-1" aria-labelledby="adminDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="adminDetailsModalLabel">Admin Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Name:</strong> {{ Auth::user()->name ?? 'N/A' }}</p>
                    <p><strong>Username:</strong> {{ Auth::user()->username ?? 'N/A' }}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar + Content -->
    <div class="d-flex">
        <!-- Sidebar -->
        <div id="sidebar" class="bg-light">
            @include('layouts.sidebar')
        </div>

        <!-- Main Content -->
        <div class="container my-4" id="main-content">
            @yield('content')
        </div>
    </div>

    <!-- Bootstrap and Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('collapsed');
        }

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

    @stack('scripts')
</body>
</html>
