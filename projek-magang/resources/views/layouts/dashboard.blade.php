<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="user-id" content="{{ Auth::id() }}">
    <meta name="user-role" content="{{ Auth::user()->role }}">
    <meta name="user-divisi-id" content="{{ Auth::user()->divisi_id }}">
    <title>@yield('title', 'Dashboard') - Sistem Arsip Digital</title>
    
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
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
        }
        
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f8f9fa; }
        
        .navbar { background-color: var(--primary); box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        
        .sidebar {
            background-color: white;
            min-height: calc(100vh - 56px);
            box-shadow: 1px 0 5px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            width: 250px;
            position: sticky;
            top: 56px;
            left: 0;
            z-index: 999;
            overflow-x: hidden;
        }
        
        .sidebar.collapsed { width: 0 !important; padding: 0 !important; margin: 0 !important; overflow: hidden; }
        .sidebar-logo { width: 80px; height: auto; transition: all 0.3s ease; }
        .sidebar-title { font-weight: bold; color: var(--primary); white-space: nowrap; font-size: 0.9rem; }
        
        .sidebar .nav-link {
            color: var(--primary); padding: 12px 20px;
            border-left: 3px solid transparent; transition: all 0.3s; display: flex; align-items: center;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background-color: #f8f9fa; border-left-color: var(--secondary); color: var(--secondary);
        }
        .sidebar .nav-link i { margin-right: 10px; width: 20px; text-align: center; min-width: 20px; }
        .sidebar.collapsed .nav-link i { margin-right: 0; }
        
        .card { border: none; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); transition: transform 0.3s; margin-bottom: 20px; }
        .card:hover { transform: translateY(-3px); }
        
        .kpi-card { border-left: 4px solid; }
        .kpi-card.surat-masuk { border-left-color: var(--secondary); }
        .kpi-card.surat-keluar { border-left-color: var(--success); }
        .kpi-card.belum-ditindak { border-left-color: var(--warning); }
        .kpi-card.ratarata-waktu { border-left-color: var(--danger); }
        
        .table th { border-top: none; font-weight: 600; color: var(--primary); }
        .badge { font-size: 0.7rem; padding: 5px 8px; }
        
        .chart-container { position: relative; height: 300px; width: 100%; }
        
        .footer { background-color: var(--primary); color: white; padding: 15px 0; margin-top: 30px; }
        
        .dashboard-section { display: none; }
        .dashboard-section.active { display: block; }
        
        .divisi-tag { display: inline-block; padding: 5px 10px; border-radius: 20px; font-size: 0.8rem; }
        .divisi-1 { background-color: #e3f2fd; color: #1565c0; }
        .divisi-2 { background-color: #e8f5e9; color: #2e7d32; }
        .divisi-3 { background-color: #fff3e0; color: #ef6c00; }
        .divisi-4 { background-color: #fce4ec; color: #c2185b; }
        .divisi-5 { background-color: #f3e5f5; color: #7b1fa2; }
        
        .modal-content { border-radius: 10px; border: none; }
        .profile-img { width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 3px solid var(--secondary); }
        .profile-header { background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%); color: white; padding: 20px; border-radius: 10px 10px 0 0; }
        
        .dropdown-menu { background-color: white; border: 1px solid #dee2e6; border-radius: 12px; box-shadow: 0 8px 25px rgba(0,0,0,0.15); padding: 8px; min-width: 220px; margin-top: 8px; }
        .dropdown-item { border-radius: 8px; margin-bottom: 6px; padding: 12px 16px; display: flex; align-items: center; color: var(--primary); text-decoration: none; font-weight: 500; transition: all 0.3s; }
        .dropdown-item:hover { background-color: #f8f9fa; color: var(--secondary); transform: translateX(4px); }
        .dropdown-item i { margin-right: 10px; width: 18px; text-align: center; }
        .dropdown-divider { margin: 8px 0; border-top: 1px solid #e9ecef; }
        
        .timeline { position: relative; padding-left: 30px; list-style: none; }
        .timeline:before { content: ''; position: absolute; top: 0; left: 10px; height: 100%; width: 2px; background: #e9ecef; }
        .timeline-item { position: relative; margin-bottom: 20px; }
        .timeline-marker { position: absolute; left: -30px; top: 5px; width: 20px; height: 20px; border-radius: 50%; background: #3498db; border: 4px solid #fff; box-shadow: 0 0 0 2px #e9ecef; }
        .timeline-content { background: #f8f9fa; padding: 15px; border-radius: 8px; }
        .timeline-title { margin: 0 0 5px; font-weight: 600; }
        .timeline-text { margin: 0 0 5px; font-size: 0.9rem; }
        
        .email-item { transition: background-color 0.2s; cursor: pointer; border-bottom: 1px solid #e9ecef; }
        .email-item:hover { background-color: #f8f9fa; }
        .email-subject { font-weight: 600; color: var(--primary); }
        .email-preview { line-height: 1.4; color: #6c757d; }
        .email-meta { font-size: 0.8rem; }
        
        .badge.bg-draft { background-color: #6c757d !important; }
        .badge.bg-dikirim { background-color: #17a2b8 !important; }
        .badge.bg-diterima { background-color: #28a745 !important; }
        
        .export-option { border: 1px solid #dee2e6; border-radius: 8px; padding: 15px; margin-bottom: 10px; cursor: pointer; transition: all 0.3s; }
        .export-option:hover { border-color: var(--secondary); background-color: #f8f9fa; }
        
        .main-content-wrapper { margin-left: 250px; transition: margin-left 0.3s ease; width: calc(100% - 250px); }
        .main-content-wrapper.expanded { margin-left: 0; width: 100% !important; }
        
        .filter-select { height: 38px; font-size: 0.875rem; padding: 0 0.75rem; margin: 0; }
        .btn-action { height: 38px; }
        
        .form-control:focus, .form-select:focus { border-color: var(--secondary); box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25); }
        
        @media (max-width: 767.98px) {
            .sidebar { margin-left: -250px; }
            .sidebar.show { margin-left: 0; }
            .main-content-wrapper { margin-left: 0; width: 100%; }
        }
        
        @stack('styles')
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ url('/') }}">
                <i class="fas fa-archive me-2"></i><strong>Sistem Arsip Digital</strong>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="#"><i class="fas fa-bell"></i></a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-1"></i> {{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#profileModal"><i class="fas fa-user"></i> Profil</a></li>
                            <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#settingsModal"><i class="fas fa-cog"></i> Pengaturan</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="dropdown-item"><i class="fas fa-sign-out-alt"></i> Keluar</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-3 col-md-4 d-md-block sidebar collapse" id="sidebar">
                <div class="position-sticky pt-3">
                    <div class="sidebar-header d-flex flex-column align-items-center px-3 py-2 mb-3">
                        <img src="https://z-cdn-media.chatglm.cn/files/4d8c4a95-b906-43a0-a624-ba43cb3d6ab4_logo%20cabdin.png?auth_key=1863697320-0546550f413245d6a2c0b7ff7c9f44d2-0-1cdfec829aefd5795c821dc2fedd8142" alt="Logo" class="sidebar-logo mb-2">
                        <span class="sidebar-title d-block">Cabdin Pendidikan</span>
                        <span class="sidebar-title d-block">Wilayah VII</span>
                    </div>
                    
                    <ul class="nav flex-column" id="sidebarNav">
                        <li class="nav-item">
                            <a class="nav-link active" href="#" data-section="dashboard"><i class="fas fa-tachometer-alt"></i> <span>Dashboard</span></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" data-section="surat-masuk"><i class="fas fa-envelope"></i> <span>Surat Masuk</span></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" data-section="surat-keluar"><i class="fas fa-paper-plane"></i> <span>Surat Keluar</span></a>
                        </li>
                        @if(Auth::user()->role === 'admin')
                        <li class="nav-item">
                            <a class="nav-link" href="#" data-section="beban-kerja"><i class="fas fa-chart-bar"></i> <span>Beban Kerja</span></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" data-section="arsip"><i class="fas fa-archive"></i> <span>Arsip</span></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" data-section="laporan"><i class="fas fa-file-pdf"></i> <span>Laporan</span></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" data-section="pengguna"><i class="fas fa-users"></i> <span>Pengguna</span></a>
                        </li>
                        @endif
                    </ul>
                    
                    <div class="mt-4 p-3 bg-light rounded">
                        <h6>Status Sistem</h6>
                        @if(Auth::user()->role === 'admin')
                        <div class="d-flex justify-content-between small mb-1">
                            <span>Surat Masuk Hari Ini:</span>
                            <span class="text-primary">{{ $suratMasukHariIni ?? 0 }}</span>
                        </div>
                        <div class="d-flex justify-content-between small mb-1">
                            <span>Surat Keluar Hari Ini:</span>
                            <span class="text-success">{{ $suratKeluarHariIni ?? 0 }}</span>
                        </div>
                        @endif
                        <div class="d-flex justify-content-between small">
                            <span>Belum Ditindak:</span>
                            <span class="text-warning">{{ $belumDitindakSidebarCount ?? 0 }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-lg-9 col-md-8 ms-sm-auto px-4 py-4 main-content-wrapper" id="mainContentWrapper">
                @yield('content')
            </div>
        </div>
    </div>

    @yield('modals')

    <!-- Footer -->
    <footer class="footer">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6"><p class="mb-0">© 2025 Sistem Arsip Digital.</p></div>
                <div class="col-md-6 text-end"><p class="mb-0">Versi {{ config('app.version', '2.1.0') }}</p></div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    @vite('resources/js/dashboard.js')
    @stack('scripts')

    <script>
        function getFileIconClass(formatFileId, size, formatName) {
            size = size || 'fa-lg';
            formatName = (formatName || '').toLowerCase();
            var formatId = parseInt(formatFileId);
            
            if (formatName.includes('pdf')) return 'fas fa-file-pdf text-danger ' + size;
            if (formatName.includes('word') || formatName.includes('doc')) return 'fas fa-file-word text-primary ' + size;
            if (formatName.includes('excel') || formatName.includes('xls')) return 'fas fa-file-excel text-success ' + size;
            
            switch (formatId) {
                case 1: return 'fas fa-file-pdf text-danger ' + size;
                case 2: case 3: return 'fas fa-file-word text-primary ' + size;
                case 5: return 'fas fa-file-excel text-success ' + size;
                default: return 'fas fa-file text-secondary ' + size;
            }
        }

        function togglePasswordVisibility(inputId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById('toggleIcon-' + inputId);
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Sidebar Toggle
            document.addEventListener('click', function(e) {
                if (e.target.closest('#sidebarToggle')) {
                    e.preventDefault();
                    if (window.innerWidth >= 768) {
                        document.getElementById('sidebar').classList.toggle('collapsed');
                        document.getElementById('mainContentWrapper').classList.toggle('expanded');
                        setTimeout(() => window.dispatchEvent(new Event('resize')), 310);
                    }
                }
            });

            // Section Navigation
            document.getElementById('sidebarNav')?.addEventListener('click', function(e) {
                const link = e.target.closest('[data-section]');
                if (!link) return;
                e.preventDefault();
                
                this.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
                link.classList.add('active');
                
                document.querySelectorAll('.dashboard-section').forEach(s => s.classList.remove('active'));
                document.getElementById(link.dataset.section)?.classList.add('active');
            });

            // Flash Messages
            @if (session('success'))
            if (typeof showSuccessModal === 'function') showSuccessModal("{{ session('success') }}");
            @endif
            @if (session('error'))
            if (typeof showErrorModal === 'function') showErrorModal("{{ session('error') }}");
            @endif
            @if ($errors->any())
            if (typeof showErrorModal === 'function') showErrorModal("{{ $errors->first() }}");
            @endif
        });
    </script>
</body>
</html>