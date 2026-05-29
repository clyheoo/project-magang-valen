<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem Arsip Digital')</title>
    @vite(['resources/css/app.css', 'resources/js/dashboard.js'])
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary: #2c3e50;
            --secondary: #3498db;
            --success: #27ae60;
            --warning: #f39c12;
            --danger: #e74c3c;
            --light: #ecf0f1;
            --dark: #2c3e50;
        } 
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }
        
        .navbar {
            background-color: var(--primary);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .sidebar .nav-link {
            color: var(--dark);
            padding: 12px 20px;
            border-left: 3px solid transparent;
            transition: all 0.3s;
        }
        
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background-color: #f8f9fa;
            border-left-color: var(--secondary);
            color: var(--secondary);
        }
        
        .sidebar .nav-link i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            transition: transform 0.3s;
            margin-bottom: 20px;
        }
        
        .card:hover {
            transform: translateY(-5px);
        }
        
        .kpi-card {
            border-left: 4px solid;
        }
        
        .kpi-card.surat-masuk {
            border-left-color: var(--secondary);
        }
        
        .kpi-card.surat-keluar {
            border-left-color: var(--success);
        }
        
        .kpi-card.belum-ditindak {
            border-left-color: var(--warning);
        }
        
        .kpi-card.ratarata-waktu {
            border-left-color: var(--danger);
        }
        
        .table th {
            border-top: none;
            font-weight: 600;
            color: var(--dark);
        }
        
        .badge {
            font-size: 0.7rem;
            padding: 5px 8px;
        }
        
        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }
        
        .footer {
            background-color: var(--primary);
            color: white;
            padding: 15px 0;
            margin-top: 30px;
        }
        
        .dashboard-section {
            display: none;
        }
        
        .dashboard-section.active {
            display: block;
        }
        
        .divisi-tag {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            margin-right: 5px;
            margin-bottom: 5px;
        }
        
        .divisi-1 { background-color: #e3f2fd; color: #1565c0; }
        .divisi-2 { background-color: #e8f5e9; color: #2e7d32; }
        .divisi-3 { background-color: #fff3e0; color: #ef6c00; }
        .divisi-4 { background-color: #fce4ec; color: #c2185b; }
        .divisi-5 { background-color: #f3e5f5; color: #7b1fa2; }
        
        /* Timeline Styles for Detail Surat */
        .timeline {
            position: relative;
            padding-left: 30px;
            list-style: none;
        }
        
        .timeline:before {
            content: '';
            position: absolute;
            top: 0;
            left: 10px;
            height: 100%;
            width: 2px;
            background: #e9ecef;
        }
        
        .timeline-item {
            position: relative;
            margin-bottom: 20px;
        }
        
        .timeline-marker {
            position: absolute;
            left: -30px;
            top: 5px;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #3498db;
            border: 4px solid #fff;
            box-shadow: 0 0 0 2px #e9ecef;
        }
        
        .timeline-content {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        
        .timeline-title {
            margin: 0 0 5px;
            font-weight: 600;
        }
        
        .timeline-text {
            margin: 0 0 5px;
            font-size: 0.9rem;
        }
        
        /* Enhanced Dropdown Menu Styling for Profile and Settings */
        .dropdown-menu {
            background-color: white;
            border: 1px solid #dee2e6;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            padding: 8px;
            min-width: 220px;
            margin-top: 8px;
        }

        .dropdown-item {
            border-radius: 8px;
            margin-bottom: 6px;
            padding: 12px 16px;
            display: flex;
            align-items: center;
            border: 1px solid transparent;
            transition: all 0.3s ease;
            color: var(--dark);
            text-decoration: none;
            font-weight: 500;
        }

        .dropdown-item:hover,
        .dropdown-item:focus {
            background-color: #f8f9fa;
            border-color: #e9ecef;
            color: var(--secondary);
            transform: translateX(4px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .dropdown-item i {
            margin-right: 10px;
            width: 18px;
            text-align: center;
            font-size: 1.1rem;
        }

        .dropdown-divider {
            margin: 8px 0;
            border-top: 1px solid #e9ecef;
        }

        /* Ensure last item has no bottom margin */
        .dropdown-item:last-child {
            margin-bottom: 0;
        }

        /* --- Sidebar Toggle Styles --- */
        #sidebar {
            background-color: #f8f9fa; /* Warna latar belakang sidebar */
            width: 250px;
            transition: margin-left 0.3s ease-in-out;
            min-height: calc(100vh - 56px); /* Tinggi minimal agar footer tidak naik */
            overflow-y: auto; /* Allow scrolling if content overflows */
        }

        #sidebar.collapsed {
            margin-left: -250px; /* Sembunyikan dengan menggeser ke kiri */
        }

        #mainContent {
            flex-grow: 1; /* Ambil sisa ruang yang tersedia */
            padding: 1.5rem; /* Menggantikan py-4 px-4 */
            min-width: 0; /* Mencegah konten flex meluap */
            transition: margin-left 0.3s ease-in-out;
        }

        /* Sidebar Backdrop */
        .sidebar-backdrop {
            display: none;
        }

        .sidebar-backdrop.show {
            display: block;
        }

        .main-wrapper {
            display: flex;
        }

        /* Desktop: Make sidebar sticky to follow scroll */
        @media (min-width: 992px) {
            #sidebar {
                position: sticky;
                top: 56px; /* Below navbar */
                left: 0;
                height: calc(100vh - 56px);
                z-index: 1000;
            }

            #mainContent {
                margin-left: 250px;
            }

            #sidebar.collapsed ~ #mainContent {
                margin-left: 0;
            }
        }

        /* Responsive for smaller screens */
        @media (max-width: 991.98px) {
            /* Di layar kecil, kita akan menggunakan posisi absolut untuk overlay */
            .main-wrapper { display: block; } /* Hapus flexbox di mobile */
            #sidebar { position: absolute; z-index: 1000; height: 100%; }
            #mainContent { margin-left: 0 !important; }
            .sidebar-backdrop { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); z-index: 999; }
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <button class="btn btn-dark me-2" id="sidebarToggle" type="button">
                <i class="fas fa-bars"></i>
            </button>
            <a class="navbar-brand" href="{{ url('/') }}">
                <i class="fas fa-archive me-2"></i>
                <strong>Sistem Arsip Digital</strong>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fas fa-bell"></i></a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-1"></i> <span>{{ Auth::user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#profileModal"><i class="fas fa-user me-2"></i> Profil</a></li>
                            <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#settingsModal"><i class="fas fa-cog me-2"></i> Pengaturan</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="dropdown-item" id="logoutBtn"><i class="fas fa-sign-out-alt me-2"></i> Keluar</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="main-wrapper">
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <div class="pt-3">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link active" href="#" data-section="dashboard">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-section="surat-masuk">
                            <i class="fas fa-envelope"></i> Surat Masuk
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-section="surat-keluar">
                            <i class="fas fa-paper-plane"></i> Surat Keluar
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-section="beban-kerja">
                            <i class="fas fa-chart-bar"></i> Beban Kerja
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-section="arsip">
                            <i class="fas fa-archive"></i> Arsip
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-section="laporan">
                            <i class="fas fa-file-pdf"></i> Laporan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-section="pengguna">
                            <i class="fas fa-users"></i> Pengguna
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-section="pengaturan">
                            <i class="fas fa-cogs"></i> Pengaturan
                        </a>
                    </li>
                </ul>
                
                <div class="mt-4 p-3 bg-light rounded">
                    <h6>Status Sistem</h6>
                    <div class="d-flex justify-content-between small">
                        <span>Surat Masuk Hari Ini:</span>
                        <span class="text-primary">{{ $suratMasukHariIni }}</span>
                    </div>
                    <div class="d-flex justify-content-between small">
                        <span>Surat Keluar Hari Ini:</span>
                        <span class="text-success">{{ $suratKeluarHariIni }}</span>
                    </div>
                    <div class="d-flex justify-content-between small">
                        <span>Belum Ditindak:</span>
                        <span class="text-warning">{{ $belumDitindak }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <main id="mainContent">
            <div class="container-fluid">
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Sidebar Backdrop -->
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <!-- Footer -->
    <footer class="footer mt-auto"> <!-- Use mt-auto to push footer down -->
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-0">© 2025 Sistem Arsip Digital. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-end">
                    <p class="mb-0">Versi 2.1.0 | <a href="#" class="text-white">Bantuan</a></p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Profile Modal -->
    <div class="modal fade" id="profileModal" tabindex="-1" aria-labelledby="profileModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="profile-header text-center">
                    <div class="d-flex justify-content-center mb-3">
                        <img src="{{ Auth::user()->profile_picture ?: asset('images/default-profile.png') }}" alt="Profile" class="profile-img" id="profileImage">
                    </div>
                    <h4 id="profileName">{{ Auth::user()->name }}</h4>
                    <p id="profileRole">{{ ucfirst(Auth::user()->role) }}</p>
                </div>
                <div class="modal-body">
                    <form id="profileForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="fullName" class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" id="fullName" value="{{ Auth::user()->name }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" value="{{ Auth::user()->email }}" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Telepon</label>
                                <input type="tel" class="form-control" id="phone" value="{{ Auth::user()->phone }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="department" class="form-label">Divisi</label>
                                <select class="form-select" id="department">
                                    <option value="umum" {{ Auth::user()->department == 'umum' ? 'selected' : '' }}>Divisi Umum & Kepegawaian</option>
                                    <option value="keuangan" {{ Auth::user()->department == 'keuangan' ? 'selected' : '' }}>Divisi Keuangan</option>
                                    <option value="perencanaan" {{ Auth::user()->department == 'perencanaan' ? 'selected' : '' }}>Divisi Perencanaan</option>
                                    <option value="hukum" {{ Auth::user()->department == 'hukum' ? 'selected' : '' }}>Divisi Hukum & Kerjasama</option>
                                    <option value="ti" {{ Auth::user()->department == 'ti' ? 'selected' : '' }}>Divisi Teknologi Informasi</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="bio" class="form-label">Bio</label>
                            <textarea class="form-control" id="bio" rows="3" placeholder="Deskripsi singkat tentang diri Anda...">{{ Auth::user()->bio }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label for="profilePicture" class="form-label">Foto Profil</label>
                            <input type="file" class="form-control" id="profilePicture" accept="image/*">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary" id="saveProfile">Simpan Perubahan</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Settings Modal -->
    <div class="modal fade" id="settingsModal" tabindex="-1" aria-labelledby="settingsModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="settingsModalLabel">Pengaturan Akun</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="settings-option">
                        <h6>Ubah Password</h6>
                        <form id="passwordForm">
                            <div class="mb-3">
                                <label for="currentPassword" class="form-label">Password Saat Ini</label>
                                <input type="password" class="form-control" id="currentPassword">
                            </div>
                            <div class="mb-3">
                                <label for="newPassword" class="form-label">Password Baru</label>
                                <input type="password" class="form-control" id="newPassword">
                            </div>
                            <div class="mb-3">
                                <label for="confirmPassword" class="form-label">Konfirmasi Password Baru</label>
                                <input type="password" class="form-control" id="confirmPassword">
                            </div>
                            <button type="button" class="btn btn-warning btn-sm" id="changePassword">Ubah Password</button>
                        </form>
                    </div>
                    
                    <div class="settings-option">
                        <h6>Preferensi Notifikasi</h6>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="emailNotifications" {{ Auth::user()->email_notifications ? 'checked' : '' }}>
                            <label class="form-check-label" for="emailNotifications">
                                Notifikasi Email
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="smsNotifications" {{ Auth::user()->sms_notifications ? 'checked' : '' }}>
                            <label class="form-check-label" for="smsNotifications">
                                Notifikasi SMS
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="pushNotifications" {{ Auth::user()->push_notifications ? 'checked' : '' }}>
                            <label class="form-check-label" for="pushNotifications">
                                Notifikasi Browser
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary" id="saveSettings">Simpan Pengaturan</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sidebar = document.getElementById('sidebar');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebarBackdrop = document.getElementById('sidebarBackdrop');
            const sidebarLinks = document.querySelectorAll('#sidebar .nav-link');
            const mainContent = document.getElementById('mainContent');

            const toggleSidebar = () => {
                const isCollapsed = sidebar.classList.toggle('collapsed');
                const isMobile = window.innerWidth < 992;

                // Handle backdrop visibility on mobile
                if (isMobile) {
                    sidebarBackdrop.classList.toggle('show', !isCollapsed);
                }
            };

            const closeSidebar = () => {
                if (window.innerWidth < 992) {
                    sidebar.classList.add('collapsed');
                    sidebarBackdrop.classList.remove('show');
                }
            };

            const initializeSidebar = () => {
                if (window.innerWidth < 992) {
                    // Mobile: sidebar starts collapsed
                    if (!sidebar.classList.contains('collapsed')) sidebar.classList.add('collapsed');
                } else {
                    // Desktop: sidebar starts open
                    sidebar.classList.remove('collapsed');
                }
            };

            if (sidebarToggle) sidebarToggle.addEventListener('click', toggleSidebar);
            if (sidebarBackdrop) sidebarBackdrop.addEventListener('click', closeSidebar);
            sidebarLinks.forEach(link => link.addEventListener('click', closeSidebar));

            // Initialize sidebar on page load and on window resize
            initializeSidebar();
            window.addEventListener('resize', initializeSidebar);
        });
    </script>
    @stack('scripts')
</body>
</html>