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

        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background-color: #f8f9fa; 
            overflow-x: hidden;
        }

        .navbar { 
            background-color: var(--primary); 
            box-shadow: 0 2px 4px rgba(0,0,0,0.1); 
            z-index: 1000;
        }

        /* ============================================
           SIDEBAR OVERLAY UNTUK MOBILE
           ============================================ */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 998;
            -webkit-backdrop-filter: blur(2px);
            backdrop-filter: blur(2px);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .sidebar-overlay.active {
            display: block;
            opacity: 1;
        }

        /* ============================================
           SIDEBAR STYLES
           ============================================ */
        .sidebar {
            background-color: white;
            min-height: calc(100vh - 56px);
            box-shadow: 1px 0 5px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            width: 100%;
            max-width: 250px;
            position: sticky;
            top: 56px;
            left: 0;
            z-index: 999;
            overflow-x: hidden;
            overflow-y: auto;
            flex-shrink: 0;
        }

        .sidebar.collapsed { 
            width: 0 !important; 
            padding: 0 !important; 
            margin: 0 !important; 
            overflow: hidden; 
        }
        
        .sidebar-logo { 
            width: 80px; 
            height: auto; 
            transition: all 0.3s ease; 
        }
        
        .sidebar-title { 
            font-weight: bold; 
            color: var(--primary); 
            white-space: nowrap; 
            font-size: 0.9rem; 
        }

        .sidebar .nav-link {
            color: var(--primary); 
            padding: 12px 20px;
            border-left: 3px solid transparent; 
            transition: all 0.3s; 
            display: flex; 
            align-items: center;
        }
        
        .sidebar .nav-link:hover, 
        .sidebar .nav-link.active {
            background-color: #f8f9fa; 
            border-left-color: var(--secondary); 
            color: var(--secondary);
        }
        
        .sidebar .nav-link i { 
            margin-right: 10px; 
            width: 20px; 
            text-align: center; 
            min-width: 20px; 
        }
        
        .sidebar.collapsed .nav-link i { 
            margin-right: 0; 
        }

        /* ============================================
           CARD STYLES
           ============================================ */
        .card { 
            border: none; 
            border-radius: 10px; 
            box-shadow: 0 4px 6px rgba(0,0,0,0.05); 
            transition: transform 0.3s; 
            margin-bottom: 20px; 
        }

        .kpi-card { border-left: 4px solid; }
        .kpi-card.surat-masuk { border-left-color: var(--secondary); }
        .kpi-card.surat-keluar { border-left-color: var(--success); }
        .kpi-card.belum-ditindak { border-left-color: var(--warning); }
        .kpi-card.ratarata-waktu { border-left-color: var(--danger); }

        /* ============================================
           TABLE STYLES
           ============================================ */
        .table th { 
            border-top: none; 
            font-weight: 600; 
            color: var(--primary); 
            white-space: nowrap;
        }
        
        .badge { 
            font-size: 0.7rem; 
            padding: 5px 8px; 
        }

        /* ============================================
           CHART STYLES
           ============================================ */
        .chart-container { 
            position: relative; 
            height: 300px; 
            width: 100%; 
        }

        /* ============================================
           FOOTER STYLES
           ============================================ */
        .footer { 
            background-color: var(--primary); 
            color: white; 
            padding: 15px 0; 
            margin-top: 30px; 
        }

        /* ============================================
           SECTION STYLES
           ============================================ */
        .dashboard-section { display: none; }
        .dashboard-section.active { display: block; }

        /* ============================================
           DIVISI TAG STYLES
           ============================================ */
        .divisi-tag { 
            display: inline-block; 
            padding: 5px 10px; 
            border-radius: 20px; 
            font-size: 0.8rem; 
        }
        .divisi-0 { background-color: #e0e0e0; color: #424242; }
        .divisi-1 { background-color: #e3f2fd; color: #1565c0; }
        .divisi-2 { background-color: #e8f5e9; color: #2e7d32; }
        .divisi-3 { background-color: #fff3e0; color: #ef6c00; }
        .divisi-4 { background-color: #fce4ec; color: #c2185b; }
        .divisi-5 { background-color: #f3e5f5; color: #7b1fa2; }

        /* ============================================
           MODAL STYLES
           ============================================ */
        .modal-content { 
            border-radius: 10px; 
            border: none; 
        }
        
        .profile-img { 
            width: 100px; 
            height: 100px; 
            border-radius: 50%; 
            object-fit: cover; 
            border: 3px solid var(--secondary); 
        }
        
        .profile-header { 
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%); 
            color: white; 
            padding: 20px; 
            border-radius: 10px 10px 0 0; 
        }

        /* ============================================
           DROPDOWN STYLES
           ============================================ */
        .dropdown-menu { 
            background-color: white; 
            border: 1px solid #dee2e6; 
            border-radius: 12px; 
            box-shadow: 0 8px 25px rgba(0,0,0,0.15); 
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
            color: var(--primary); 
            text-decoration: none; 
            font-weight: 500; 
            transition: all 0.3s; 
        }
        
        .dropdown-item:hover { 
            background-color: #f8f9fa; 
            color: var(--secondary); 
            transform: translateX(4px); 
        }
        
        .dropdown-item i { 
            margin-right: 10px; 
            width: 18px; 
            text-align: center; 
        }
        
        .dropdown-divider { 
            margin: 8px 0; 
            border-top: 1px solid #e9ecef; 
        }

        /* ============================================
           TIMELINE STYLES
           ============================================ */
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
        }
        
        .timeline-title { 
            margin: 0 0 5px; 
            font-weight: 600; 
        }
        
        .timeline-text { 
            margin: 0 0 5px; 
            font-size: 0.9rem; 
        }

        /* ============================================
           EMAIL/ITEM STYLES
           ============================================ */
        .email-item { 
            transition: background-color 0.2s; 
            cursor: pointer; 
            border-bottom: 1px solid #e9ecef; 
        }
        
        .email-item:hover { 
            background-color: #f8f9fa; 
        }
        
        .email-subject { 
            font-weight: 600; 
            color: var(--primary); 
        }
        
        .email-preview { 
            line-height: 1.4; 
            color: #6c757d; 
        }
        
        .email-meta { 
            font-size: 0.8rem; 
        }

        /* Email actions - desktop only hover */
        @media (min-width: 768px) {
            .email-item .email-actions {
                opacity: 0;
                transition: opacity 0.2s ease-in-out;
            }
            .email-item:hover .email-actions {
                opacity: 1;
            }
            
            .card:hover { 
                transform: translateY(-3px); 
            }
        }

        /* ============================================
           BADGE CUSTOM STYLES
           ============================================ */
        .badge.bg-draft { background-color: #6c757d !important; }
        .badge.bg-dikirim { background-color: #17a2b8 !important; }
        .badge.bg-diterima { background-color: #28a745 !important; }

        /* ============================================
           EXPORT OPTION STYLES
           ============================================ */
        .export-option { 
            border: 1px solid #dee2e6; 
            border-radius: 8px; 
            padding: 15px; 
            margin-bottom: 10px; 
            cursor: pointer; 
            transition: all 0.3s; 
        }
        
        .export-option:hover { 
            border-color: var(--secondary); 
            background-color: #f8f9fa; 
        }

        /* ============================================
           MAIN CONTENT WRAPPER
           ============================================ */
        .main-content-wrapper { 
            transition: all 0.3s ease; 
        }

        .main-content-wrapper.expanded { 
            margin-left: 0; 
        }

        .filter-select { 
            height: 38px; 
            font-size: 0.875rem; 
            padding: 0 0.75rem; 
            margin: 0; 
        }
        
        .btn-action { 
            height: 38px; 
        }

        .form-control:focus, .form-select:focus { 
            border-color: var(--secondary); 
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25); 
        }

        /* ============================================
           RESPONSIVE - TABLET & BELOW (max 767px)
           ============================================ */
        @media (max-width: 767.98px) {
            /* Sidebar - Fixed overlay on mobile */
            .sidebar {
                position: fixed !important;
                top: 56px !important;
                left: 0 !important;
                width: 280px !important;
                max-width: 280px !important;
                height: calc(100vh - 56px) !important;
                z-index: 999 !important;
                margin-left: -280px !important;
                padding: 0 !important;
                transition: margin-left 0.3s ease !important;
                box-shadow: 4px 0 15px rgba(0,0,0,0.2);
                display: block !important;
                overflow-x: hidden;
                overflow-y: auto;
                will-change: margin-left;
            }
            
            /* Override .collapsed class di mobile */
            .sidebar.collapsed {
                width: 280px !important;
                max-width: 280px !important;
                margin-left: -280px !important;
                padding: 0 !important;
                overflow: hidden;
            }
            
            /* Sidebar show state */
            .sidebar.show {
                margin-left: 0 !important;
            }
            
            /* Overlay yang diperbaiki */
            .sidebar-overlay {
                display: none !important;
                position: fixed !important;
                top: 0 !important;
                left: 0 !important;
                width: 100% !important;
                height: 100% !important;
                background: rgba(0,0,0,0.5) !important;
                z-index: 998 !important;
                -webkit-backdrop-filter: blur(2px);
                backdrop-filter: blur(2px);
                opacity: 0;
                transition: opacity 0.3s ease;
                pointer-events: none;
            }

            .sidebar-overlay.active {
                display: block !important;
                opacity: 1 !important;
                pointer-events: auto !important;
            }
            
            /* Main content - full width dengan padding minimal */
            .main-content-wrapper {
                margin-left: 0 !important;
                width: 100% !important;
                padding-left: 10px !important;
                padding-right: 10px !important;
                padding-top: 8px !important;
                padding-bottom: 8px !important;
            }
            
            /* Section headers */
            .dashboard-section > .d-flex.justify-content-between,
            .dashboard-section > .d-flex.justify-content-between.flex-wrap {
                flex-direction: column !important;
                align-items: stretch !important;
                gap: 8px;
                margin-bottom: 12px !important;
                padding-bottom: 8px !important;
            }
            
            .dashboard-section > .d-flex.justify-content-between > .d-flex.align-items-center {
                flex-shrink: 0;
            }
            
            /* Filter controls */
            .dashboard-section .d-flex.gap-2,
            .dashboard-section .d-flex[style*="gap: 10px"],
            .dashboard-section .d-flex[style*="gap:10px"] {
                flex-direction: column !important;
                width: 100% !important;
                gap: 6px !important;
            }
            
            .input-group[style*="width:280px"],
            .input-group[style*="width: 280px"],
            .input-group[style*="width:320px"],
            .input-group[style*="width: 320px"] {
                width: 100% !important;
            }
            
            .form-select[style*="width:160px"],
            .form-select[style*="width: 160px"],
            .form-select[style*="width:150px"],
            .form-select[style*="width: 150px"],
            .form-select[style*="width:170px"],
            .form-select[style*="width: 170px"],
            .form-select[style*="width:130px"],
            .form-select[style*="width: 130px"] {
                width: 100% !important;
            }
            
            .btn-toolbar {
                width: 100%;
            }
            
            .btn-toolbar .btn,
            .btn-action {
                width: 100%;
                display: flex;
                justify-content: center;
            }
            
            /* ============================================
               KPI CARDS - FORMAT 2x2 COMPACT
               ============================================ */
            .row.g-3.mb-4,
            .row.g-3.mb-3 {
                --bs-gutter-x: 8px;
                --bs-gutter-y: 8px;
                margin-bottom: 10px !important;
            }
            
            /* Force semua kolom KPI jadi 50% */
            .row.g-3 > [class*="col-xl-3"],
            .row.g-3 > [class*="col-md-3"],
            .row.g-3 > [class*="col-xl-4"],
            .row.g-3 > [class*="col-md-4"],
            .row.g-3 > [class*="col-md-6"] {
                flex: 0 0 calc(50% - 4px) !important;
                max-width: calc(50% - 4px) !important;
            }
            
            /* Card compact styling */
            .card {
                margin-bottom: 8px !important;
                border-radius: 8px;
            }
            
            .card:hover { 
                transform: none; 
            }
            
            .card-body {
                padding: 10px 12px !important;
            }
            
            /* KPI angka lebih kecil */
            .kpi-card h3,
            .card-body h3 {
                font-size: 1.3rem;
                margin-bottom: 0;
                line-height: 1.2;
            }
            
            /* KPI label lebih kecil */
            .kpi-card h6,
            .card-body h6.card-title {
                font-size: 0.7rem;
                margin-bottom: 2px;
            }
            
            /* Icon KPI lebih kecil */
            .kpi-card .fa-2x,
            .card-body .fa-2x {
                font-size: 1.4rem !important;
            }
            
            /* Badge di KPI */
            .kpi-card .badge {
                font-size: 0.55rem;
                padding: 2px 6px;
            }
            
            /* Badge group di Total Format */
            .card-body .mt-2.d-flex.gap-2 {
                flex-wrap: wrap;
                gap: 3px !important;
                margin-top: 6px !important;
            }
            
            .card-body .mt-2.d-flex.gap-2 .badge {
                font-size: 0.5rem;
                padding: 1px 4px;
            }
            
            /* ============================================
               CHART - LEBIH COMPACT
               ============================================ */
            .chart-container {
                height: 180px !important;
                min-height: 180px;
            }
            
            .chart-container[style*="height:320px"],
            .chart-container[style*="height: 320px"] {
                height: 180px !important;
            }
            
            .card-header {
                padding: 8px 12px !important;
            }
            
            .card-header h5 {
                font-size: 0.8rem;
                margin-bottom: 0;
            }

             /* ============================================
               CHARTS, AKTIVITAS & RINGKASAN - COMPACT
               ============================================ */
            
            /* Header chart lebih padat */
            .card-header {
                padding: 8px 12px !important;
            }
            
            .card-header h5 {
                font-size: 0.8rem;
                margin-bottom: 0;
            }
            
            /* Chart container lebih pendek */
            .chart-container {
                height: 170px !important;
                min-height: 170px;
            }
            
            /* List Aktivitas Terbaru */
            .list-group-item {
                padding: 8px 10px !important;
            }
            
            .list-group-item .d-flex {
                gap: 8px;
            }
            
            .list-group-item .me-3 {
                margin-right: 0 !important;
            }
            
            .list-group-item .fw-bold {
                font-size: 0.78rem;
                line-height: 1.3;
            }
            
            .list-group-item small {
                font-size: 0.68rem;
            }
            
            /* Ringkasan Sistem */
            .card-body > hr {
                margin: 8px 0 !important;
                opacity: 0.5;
            }
            
            .row.text-center h4 {
                font-size: 1.1rem !important;
                margin-bottom: 2px !important;
            }
            
            .row.text-center small {
                font-size: 0.7rem !important;
            }
            
            /* ============================================
               TABLE
               ============================================ */
            .table-responsive {
                margin: 0 -10px;
                padding: 0 10px;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
            
            .table-responsive .table {
                min-width: 600px;
            }
            
            .badge {
                font-size: 0.55rem;
                padding: 2px 5px;
            }
            
            .btn-group-sm .btn {
                padding: 0.2rem 0.4rem;
                font-size: 0.7rem;
            }
            
            /* ============================================
               MODAL - FULL SCREEN
               ============================================ */
            .modal-dialog {
                margin: 0;
                max-width: 100%;
                height: 100%;
                padding: 0;
            }
            
            .modal-dialog .modal-content {
                height: 100%;
                border-radius: 0;
                border: none;
            }
            
            .modal-dialog .modal-body {
                overflow-y: auto;
                -webkit-overflow-scrolling: touch;
            }
            
            /* ============================================
               DETAIL SECTION
               ============================================ */
            #suratDetailSection .col-md-8,
            #suratDetailSection .col-md-4 {
                flex: 0 0 100%;
                max-width: 100%;
            }
            
            #updateStatusContainer {
                width: 100%;
            }
            
            #updateStatusContainer .d-flex.gap-2,
            #updateStatusContainer .d-flex[style*="gap"] {
                flex-direction: column;
            }
            
            #updateStatusContainer select {
                width: 100% !important;
            }
            
            /* ============================================
               EMAIL/ITEM LIST
               ============================================ */
            .email-item .email-actions {
                opacity: 1 !important;
            }
            
            .email-item .d-flex.w-100 {
                flex-direction: column !important;
                align-items: flex-start !important;
                gap: 8px;
            }
            
            .email-item .ms-3 {
                margin-left: 0 !important;
                align-self: flex-end;
            }
            
            /* ============================================
               SUMMARY & RINGKASAN
               ============================================ */
            .row.text-center h4 {
                font-size: 1rem;
            }
            
            .row.text-center small {
                font-size: 0.7rem;
            }
            
            .text-truncate {
                max-width: 80px !important;
            }
            
            .fa-7x {
                font-size: 3rem !important;
            }
            
            /* ============================================
               FOOTER
               ============================================ */
            .footer {
                margin-top: 15px;
                padding: 10px 0;
            }
            
            .footer .row > div {
                text-align: center !important;
            }
            
            .footer .text-end {
                text-align: center !important;
                margin-top: 3px;
            }
            
            .footer p {
                font-size: 0.75rem;
            }
            
            /* ============================================
               SIDEBAR STATUS BOX
               ============================================ */
            .sidebar .mt-4.p-3 {
                margin-top: 0.75rem !important;
                padding: 0.6rem !important;
            }
            
            .sidebar .mt-4.p-3 h6 {
                font-size: 0.75rem;
                margin-bottom: 0.4rem;
            }
            
            .sidebar .mt-4.p-3 .small {
                font-size: 0.65rem;
            }

            /* ============================================
               KPI CARD INTERNAL LAYOUT - COMPACT
            ============================================ */
            
            /* Container flex agar icon di kanan tidak memaksa card memanjang */
            .kpi-card .card-body,
            .card-body .d-flex.justify-content-between {
                gap: 8px;
            }
            
            /* Container kiri (teks) ambil sisa ruang */
            .kpi-card .card-body .d-flex.justify-content-between > div:first-child,
            .card-body .d-flex.justify-content-between > div:first-child {
                flex: 1;
                min-width: 0;
            }
            
            /* Icon container fixed width */
            .kpi-card .card-body .d-flex.justify-content-between > i,
            .card-body .d-flex.justify-content-between > i {
                flex-shrink: 0;
            }
            
            /* Badge di bawah card */
            .kpi-card .card-body > .badge,
            .card-body > .badge {
                display: block;
                width: fit-content;
            }

            /* ============================================
            ICON FORMAT DI KOLOM TABEL
            ============================================ */
            .table td .fa-lg {
                font-size: 1rem !important;
            }

            .table .fa-file-pdf,
            .table .fa-file-word,
            .table .fa-file-excel,
            .table .fa-file,
            .table .fa-file-alt {
                display: inline-block;
                width: 24px;
                text-align: center;
                font-size: 1rem !important;
            }

            .table th[scope="col"]:last-child,
            .table td:has(.fa-file-pdf),
            .table td:has(.fa-file-word),
            .table td:has(.fa-file-excel),
            .table td:has(.fa-file),
            .table td:has(.fa-file-alt) {
                white-space: nowrap;
                padding: 8px 6px !important;
            }
        }

        /* ============================================
           RESPONSIVE - VERY SMALL PHONES (max 375px)
           ============================================ */
        @media (max-width: 375px) {
            .main-content-wrapper {
                padding-left: 6px !important;
                padding-right: 6px !important;
            }
            
            .card-body {
                padding: 8px 10px !important;
            }
            
            .kpi-card h3,
            .card-body h3 {
                font-size: 1.15rem;
            }
            
            .kpi-card .fa-2x,
            .card-body .fa-2x {
                font-size: 1.2rem !important;
            }
            
            .chart-container {
                height: 140px !important;
                min-height: 140px;
            }
            
            .list-group-item .fw-bold {
                font-size: 0.72rem;
            }
            
            .row.text-center h4 {
                font-size: 1rem !important;
            }
            
            .dashboard-section h1.h2 {
                font-size: 1rem;
            }
            
            .badge {
                font-size: 0.5rem;
                padding: 1px 4px;
            }
        }
        /* Batasi ukuran icon di dalam tabel */
        .table td i[class*="fa-file"] {
            font-size: 1rem !important;
            display: inline-block;
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
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fas fa-bell"></i></a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-1"></i> {{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
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

    <div class="container-fluid p-0">
        <!-- Sidebar Overlay untuk Mobile -->
        <div class="sidebar-overlay" id="sidebarOverlay"></div>
        
    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
                <div class="position-sticky pt-3">
                    <div class="sidebar-header d-flex flex-column align-items-center px-3 py-2 mb-3">
                        <img src="https://z-cdn-media.chatglm.cn/files/4d8c4a95-b906-43a0-a624-ba43cb3d6ab4_logo%20cabdin.png?auth_key=1863697320-0546550f413245d6a2c0b7ff7c9f44d2-0-1cdfec829aefd5795c821dc2fedd8142" alt="Logo" class="sidebar-logo mb-2">
                        <span class="sidebar-title d-block">Cabdin Pendidikan</span>
                        <span class="sidebar-title d-block">Wilayah VII</span>
                    </div>
                    
                    <ul class="nav flex-column" id="sidebarNav">
                        <li class="nav-item">
                            <a class="nav-link active" href="#" data-section="dashboard">
                                <i class="fas fa-tachometer-alt"></i> <span>Dashboard</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" data-section="surat-masuk">
                                <i class="fas fa-envelope"></i> <span>Surat Masuk</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" data-section="surat-keluar">
                                <i class="fas fa-paper-plane"></i> <span>Surat Keluar</span>
                            </a>
                        </li>
                        @if(Auth::user()->role === 'admin')
                        <li class="nav-item">
                            <a class="nav-link" href="#" data-section="beban-kerja">
                                <i class="fas fa-chart-bar"></i> <span>Beban Kerja</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" data-section="arsip">
                                <i class="fas fa-archive"></i> <span>Arsip</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" data-section="laporan">
                                <i class="fas fa-file-pdf"></i> <span>Laporan</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" data-section="pengguna">
                                <i class="fas fa-users"></i> <span>Pengguna</span>
                            </a>
                        </li>
                        @endif
                    </ul>
                    
                    <div class="mt-4 p-3 bg-light rounded mx-2">
                        <h6 class="mb-2">Status Sistem</h6>
                        @if(Auth::user()->role === 'admin')
                        <div class="d-flex justify-content-between small mb-1">
                            <span>Surat Masuk Hari Ini:</span>
                            <span class="text-primary fw-bold">{{ $suratMasukHariIni ?? 0 }}</span>
                        </div>
                        <div class="d-flex justify-content-between small mb-1">
                            <span>Surat Keluar Hari Ini:</span>
                            <span class="text-success fw-bold">{{ $suratKeluarHariIni ?? 0 }}</span>
                        </div>
                        @endif
                        <div class="d-flex justify-content-between small">
                            <span>Belum Ditindak:</span>
                            <span class="text-warning fw-bold">{{ $belumDitindakSidebarCount ?? 0 }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="main-content-wrapper ps-3 pe-4 py-4 flex-grow-1" id="mainContentWrapper">
                @yield('content')
            </div>
        </div>
    </div>

    @yield('modals')    

    <!-- Footer -->
    <footer class="footer">
        <div class="container-fluid px-4">
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-0">© 2025 Sistem Arsip Digital.</p>
                </div>
                <div class="col-md-6 text-end">
                    <p class="mb-0">Versi {{ config('app.version', '2.1.0') }}</p>
                </div>
            </div>
        </div>
    </footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<script>
// ============================================
// HELPER FUNCTIONS
// ============================================

function getFileIconClass(formatFileId, size, formatName) {
    size = size || 'fa-lg';
    formatName = (formatName || '').toLowerCase();
    var formatId = parseInt(formatFileId);
    
    if (formatName.includes('pdf')) return 'fas fa-file-pdf text-danger ' + size;
    if (formatName.includes('word') || formatName.includes('doc')) return 'fas fa-file-word text-primary ' + size;
    if (formatName.includes('excel') || formatName.includes('xls')) return 'fas fa-file-excel text-success ' + size;
    
    switch (formatId) {
        case 1: return 'fas fa-file-pdf text-danger ' + size;
        case 2: 
        case 3: return 'fas fa-file-word text-primary ' + size;
        case 5: return 'fas fa-file-excel text-success ' + size;
        default: return 'fas fa-file text-secondary ' + size;
    }
}

function togglePasswordVisibility(inputId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById('toggleIcon-' + inputId);
    if (!input || !icon) return;
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}

// ============================================
// MODAL FUNCTIONS
// ============================================

window.showSuccessModal = function(message) {
    const el = document.getElementById('successMessage');
    const modalEl = document.getElementById('successModal');
    if (!el || !modalEl) return;
    el.textContent = message;
    const modal = new bootstrap.Modal(modalEl);
    modal.show();
    setTimeout(() => modal.hide(), 3000);
};

window.showErrorModal = function(message) {
    const el = document.getElementById('errorMessage');
    const modalEl = document.getElementById('errorModal');
    if (!el || !modalEl) return;
    el.textContent = message;
    const modal = new bootstrap.Modal(modalEl);
    modal.show();
    setTimeout(() => modal.hide(), 3000);
};

// ============================================
// SIDEBAR MOBILE FUNCTIONS
// ============================================

function closeMobileSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    
    if (sidebar) {
        sidebar.classList.remove('show');
    }
    if (overlay) {
        overlay.classList.remove('active');
    }
    if (window.innerWidth < 768) {
        document.body.style.overflow = '';
    }
}

function openMobileSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    
    if (sidebar) {
        sidebar.classList.remove('collapsed');
        sidebar.classList.add('show');
    }
    if (overlay) {
        overlay.classList.add('active');
    }
    if (window.innerWidth < 768) {
        document.body.style.overflow = 'hidden';
    }
}

function toggleSidebar() {
    if (window.innerWidth < 768) {
        const sidebar = document.getElementById('sidebar');
        if (sidebar && sidebar.classList.contains('show')) {
            closeMobileSidebar();
        } else {
            openMobileSidebar();
        }
    } else {
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContentWrapper');
        if (sidebar) sidebar.classList.toggle('collapsed');
        if (mainContent) mainContent.classList.toggle('expanded');
        setTimeout(() => window.dispatchEvent(new Event('resize')), 310);
    }
}

// ============================================
// DOCUMENT READY
// ============================================

document.addEventListener('DOMContentLoaded', function() {
    
    // ==========================================
    // SIDEBAR TOGGLE - EVENT DELEGATION
    // ==========================================
    document.addEventListener('click', function(e) {
        // Tangkap klik pada tombol toggle sidebar (pakai class bukan ID)
        const toggleBtn = e.target.closest('.sidebar-toggle-btn, [data-toggle-sidebar], #sidebarToggle');
        
        if (toggleBtn) {
            e.preventDefault();
            e.stopPropagation();
            toggleSidebar();
            return false;
        }
        
        // Tangkap klik pada overlay
        if (e.target.id === 'sidebarOverlay' || e.target.classList.contains('sidebar-overlay')) {
            e.preventDefault();
            closeMobileSidebar();
            return false;
        }
    });

    // ==========================================
    // SECTION NAVIGATION
    // ==========================================
    const sidebarNav = document.getElementById('sidebarNav');
    if (sidebarNav) {
        sidebarNav.addEventListener('click', function(e) {
            const link = e.target.closest('[data-section]');
            if (!link) return;
            e.preventDefault();
            
            this.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
            link.classList.add('active');
            
            document.querySelectorAll('.dashboard-section').forEach(s => s.classList.remove('active'));
            const targetSection = document.getElementById(link.dataset.section);
            if (targetSection) targetSection.classList.add('active');
            
            setTimeout(closeMobileSidebar, 150);
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    // ==========================================
    // WINDOW RESIZE HANDLER
    // ==========================================
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            
            if (window.innerWidth >= 768) {
                if (sidebar) {
                    sidebar.classList.remove('show');
                }
                if (overlay) {
                    overlay.classList.remove('active');
                }
                document.body.style.overflow = '';
            }
        }, 250);
    });

    // ==========================================
    // FLASH MESSAGES
    // ==========================================
    @if (session('success'))
    setTimeout(function() { 
        if (typeof showSuccessModal === 'function') {
            showSuccessModal("{{ session('success') }}"); 
        }
    }, 300);
    @endif
    
    @if (session('error'))
    setTimeout(function() { 
        if (typeof showErrorModal === 'function') {
            showErrorModal("{{ session('error') }}"); 
        }
    }, 300);
    @endif
    
    @if ($errors->any())
    setTimeout(function() { 
        if (typeof showErrorModal === 'function') {
            showErrorModal("{{ $errors->first() }}"); 
        }
    }, 300);
    @endif

    // ==========================================
    // PROFILE FORM
    // ==========================================
    const saveProfileBtn = document.getElementById('saveProfile');
    if (saveProfileBtn) {
        saveProfileBtn.addEventListener('click', function() {
            const formData = new FormData();
            formData.append('full_name', document.getElementById('fullName')?.value || '');
            formData.append('phone', document.getElementById('phone')?.value || '');
            formData.append('department', document.getElementById('department')?.value || '');
            formData.append('bio', document.getElementById('bio')?.value || '');
            
            const profilePicture = document.getElementById('profilePicture');
            if (profilePicture && profilePicture.files[0]) {
                formData.append('profile_picture', profilePicture.files[0]);
            }

            fetch('/profile', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success || response.ok) {
                    showSuccessModal('Profile berhasil diupdate!');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showErrorModal(data.message || 'Gagal mengupdate profile');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showErrorModal('Terjadi kesalahan saat mengupdate profile');
            });
        });
    }

    // ==========================================
    // SURAT MASUK - TAMBAH BARU
    // ==========================================
    const formSuratBaru = document.getElementById('formSuratBaru');
    if (formSuratBaru) {
        formSuratBaru.addEventListener('submit', function(e) {
            e.preventDefault();
            if (this.submitting) return;
            this.submitting = true;

            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) submitBtn.disabled = true;

            const formData = new FormData(this);

            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(async response => {
                if (response.ok) {
                    showSuccessModal('Surat masuk berhasil disimpan!');
                    bootstrap.Modal.getInstance(document.getElementById('modalSuratBaru'))?.hide();
                    this.reset();
                    setTimeout(() => location.reload(), 1000);
                } else if (response.status === 422) {
                    const errorData = await response.json();
                    const errors = errorData.errors;
                    let errorMessages = 'Validasi gagal:\n';
                    for (const key in errors) {
                        if (errors.hasOwnProperty(key)) {
                            errorMessages += '- ' + errors[key].join(', ') + '\n';
                        }
                    }
                    alert(errorMessages);
                } else {
                    alert('Terjadi kesalahan. Status: ' + response.status);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Tidak dapat terhubung ke server.');
            })
            .finally(() => {
                this.submitting = false;
                if (submitBtn) submitBtn.disabled = false;
            });
        });
    }

    // ==========================================
    // SURAT MASUK - EDIT
    // ==========================================
    window.editSuratMasuk = function(id) {
        fetch('/api/surat-masuk/' + id, {
            headers: { 'Accept': 'application/json' }
        })
        .then(response => response.json().catch(() => ({})))
        .then(data => {
            if (!data || !data.success || !data.surat) {
                alert('Gagal mengambil data surat masuk');
                return;
            }

            const surat = data.surat;
            const modalElement = document.getElementById('modalEditSuratMasuk');
            if (!modalElement) {
                alert('Modal edit tidak ditemukan');
                return;
            }

            const setValue = (elId, value) => {
                const el = document.getElementById(elId);
                if (el) el.value = value ?? '';
            };

            setValue('editSuratId', surat.id);
            setValue('editSuratNomor', surat.nomor_surat);
            setValue('editSuratDivisi', surat.divisi_id);
            setValue('editSuratTanggal', surat.tanggal);
            setValue('editSuratPerihal', surat.perihal);
            setValue('editSuratPengirim', surat.pengirim);
            setValue('edit_instruksi_disposisi', surat.instruksi_disposisi);
            setValue('edit_instruksi_tambahan', surat.instruksi_tambahan);

            new bootstrap.Modal(modalElement).show();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat mengambil data surat masuk');
        });
    };

    // Form Edit Surat Masuk
    const formEditSuratMasuk = document.getElementById('formEditSuratMasuk');
    if (formEditSuratMasuk) {
        formEditSuratMasuk.addEventListener('submit', function(e) {
            e.preventDefault();
            const id = document.getElementById('editSuratId').value;
            const formData = new FormData(this);
            formData.append('_method', 'PUT');

            fetch('/surat-masuk/' + id, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(async response => {
                if (response.ok) {
                    showSuccessModal('Surat masuk berhasil diperbarui!');
                    bootstrap.Modal.getInstance(document.getElementById('modalEditSuratMasuk'))?.hide();
                    setTimeout(() => location.reload(), 1000);
                } else if (response.status === 422) {
                    const errorData = await response.json();
                    let errorMessages = 'Validasi gagal:\n';
                    for (const key in errorData.errors) {
                        errorMessages += '- ' + errorData.errors[key].join(', ') + '\n';
                    }
                    alert(errorMessages);
                } else {
                    alert('Terjadi kesalahan. Status: ' + response.status);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat memperbarui surat masuk');
            });
        });
    }

    // ==========================================
    // SURAT MASUK - HAPUS
    // ==========================================
    window.hapusSuratMasuk = function(id, event) {
        if (confirm('Anda yakin ingin menghapus surat masuk ini?')) {
            fetch('/surat-masuk/' + id, {
                method: 'POST',
                body: new URLSearchParams({ '_method': 'DELETE' }),
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showSuccessModal('Surat masuk berhasil dihapus!');
                    const row = event?.target?.closest('tr');
                    if (row) row.remove();
                    setTimeout(() => location.reload(), 1000);
                } else {
                    alert('Gagal menghapus: ' + (data.message || 'Terjadi kesalahan'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menghapus surat.');
            });
        }
    };

    // ==========================================
    // SURAT KELUAR - TAMBAH BARU
    // ==========================================
    const formSuratKeluarBaru = document.getElementById('formSuratKeluarBaru');
    if (formSuratKeluarBaru) {
        formSuratKeluarBaru.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
        .then(async response => {
            if (response.ok) {
                const data = await response.json();
                showSuccessModal('Surat keluar berhasil ditambahkan!');
                bootstrap.Modal.getInstance(document.getElementById('modalSuratKeluarBaru'))?.hide();
                this.reset();
                setTimeout(() => location.reload(), 1000);
            } else if (response.status === 422) {
                const errorData = await response.json();
                let errorMessage = 'Validasi gagal:\n\n';
                if (errorData.errors) {
                    for (const field in errorData.errors) {
                        errorMessage += '- ' + field + ': ' + errorData.errors[field].join(', ') + '\n';
                    }
                } else {
                    errorMessage += errorData.message || 'Terjadi kesalahan.';
                }
                alert(errorMessage);
            } else {
                alert('Gagal menyimpan surat keluar. Status: ' + response.status);
            }
        })
            .catch(error => {
                console.error('Error:', error);
                showErrorModal('Terjadi kesalahan saat menyimpan surat keluar');
            });
        });
    }

    // ==========================================
    // SURAT KELUAR - EDIT
    // ==========================================
    window.editSuratKeluar = function(id) {
        fetch('/surat-keluar/' + id, {
            headers: { 'Accept': 'application/json' }
        })
        .then(response => response.json())
        .then(data => {
            if (!data.success || !data.surat) {
                alert('Gagal mengambil data surat keluar');
                return;
            }

            const surat = data.surat;
            const modalElement = document.getElementById('modalEditSuratKeluar');
            if (!modalElement) {
                alert('Modal edit tidak ditemukan');
                return;
            }

            const setValue = (elId, value) => {
                const el = document.getElementById(elId);
                if (el) el.value = value ?? '';
            };

            setValue('editKeluarId', surat.id);
            setValue('editKeluarNomor', surat.nomor_surat);
            setValue('editKeluarJudul', surat.judul_laporan);
            setValue('editKeluarPenerima', surat.penerima);
            setValue('editKeluarDivisi', surat.divisi_id);
            setValue('editKeluarTanggal', surat.tanggal_kirim);
            setValue('editKeluarPerihal', surat.perihal);
            setValue('editKeluarStatus', surat.status);

            new bootstrap.Modal(modalElement).show();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat mengambil data surat keluar');
        });
    };

    // Form Edit Surat Keluar
    const formEditSuratKeluar = document.getElementById('formEditSuratKeluar');
    if (formEditSuratKeluar) {
        formEditSuratKeluar.addEventListener('submit', function(e) {
            e.preventDefault();
            const id = document.getElementById('editKeluarId')?.value;
            if (!id) return;

            const formData = new FormData(this);
            formData.append('_method', 'PUT');

            fetch('/surat-keluar/' + id, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(async response => {
                if (response.ok) {
                    showSuccessModal('Surat keluar berhasil diperbarui!');
                    bootstrap.Modal.getInstance(document.getElementById('modalEditSuratKeluar'))?.hide();
                    setTimeout(() => location.reload(), 1000);
                } else if (response.status === 422) {
                    const errorData = await response.json();
                    let errorMessages = 'Validasi gagal:\n';
                    for (const key in errorData.errors) {
                        errorMessages += '- ' + errorData.errors[key].join(', ') + '\n';
                    }
                    alert(errorMessages);
                } else {
                    alert('Terjadi kesalahan saat memperbarui surat keluar');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat memperbarui surat keluar');
            });
        });
    }

    // ==========================================
    // SURAT KELUAR - HAPUS
    // ==========================================
    window.hapusSuratKeluar = function(id) {
        if (confirm('Apakah Anda yakin ingin menghapus surat keluar ini?')) {
            const formData = new FormData();
            formData.append('_method', 'DELETE');

            fetch('/surat-keluar/' + id, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showSuccessModal('Surat keluar berhasil dihapus!');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    alert('Gagal menghapus: ' + (data.message || 'Terjadi kesalahan'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menghapus surat keluar');
            });
        }
    };

    // ==========================================
    // PENGGUNA - EDIT
    // ==========================================
    window.editPengguna = function(id) {
        fetch('/users/' + id, {
            headers: { 'Accept': 'application/json' }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.user) {
                const user = data.user;
                const form = document.getElementById('editPenggunaForm');
                const modalElement = document.getElementById('editPenggunaModal');
                
                if (!form || !modalElement) return;

                form.action = '/users/' + user.id;
                
                const setValue = (elId, value) => {
                    const el = document.getElementById(elId);
                    if (el) el.value = value ?? '';
                };

                setValue('editPenggunaId', user.id);
                setValue('editUsername', user.name);
                setValue('editFullName', user.full_name || user.name);
                setValue('editEmail', user.email);
                setValue('editRole', user.role);

                const divisiContainer = document.getElementById('editDivisiContainer');
                if (divisiContainer) {
                    divisiContainer.style.display = user.role === 'admin' ? 'none' : 'block';
                    if (user.role !== 'admin') {
                        setValue('editDivisi', user.divisi_id);
                    }
                }

                new bootstrap.Modal(modalElement).show();
            } else {
                alert('Gagal mengambil data pengguna');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat mengambil data pengguna');
        });
    };

    // ==========================================
    // PENGGUNA - HAPUS
    // ==========================================
    window.hapusPengguna = function(id) {
        if (confirm('Apakah Anda yakin ingin menghapus pengguna ini?')) {
            const formData = new FormData();
            formData.append('_method', 'DELETE');

            fetch('/users/' + id, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showSuccessModal('Pengguna berhasil dihapus!');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    alert('Gagal menghapus: ' + (data.message || 'Terjadi kesalahan'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menghapus pengguna');
            });
        }
    };

    // ==========================================
    // FORM PENGGUNA
    // ==========================================
    const penggunaForm = document.getElementById('penggunaForm');
    if (penggunaForm) {
        penggunaForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showSuccessModal('Pengguna berhasil ditambahkan!');
                    bootstrap.Modal.getInstance(document.getElementById('penggunaModal'))?.hide();
                    setTimeout(() => location.reload(), 1000);
                } else {
                    alert('Error: ' + (data.message || 'Terjadi kesalahan'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menambahkan pengguna');
            });
        });
    }

    const editPenggunaForm = document.getElementById('editPenggunaForm');
    if (editPenggunaForm) {
        editPenggunaForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showSuccessModal('Pengguna berhasil diperbarui!');
                    bootstrap.Modal.getInstance(document.getElementById('editPenggunaModal'))?.hide();
                    setTimeout(() => location.reload(), 1000);
                } else {
                    let errorMessage = 'Gagal memperbarui pengguna: ';
                    if (data.message) {
                        errorMessage += data.message;
                    } else if (data.errors) {
                        errorMessage += Object.values(data.errors).flat().join(' ');
                    } else {
                        errorMessage += 'Terjadi kesalahan yang tidak diketahui.';
                    }
                    alert(errorMessage);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat memperbarui pengguna');
            });
        });
    }

    // ==========================================
    // FORM LAPORAN & ARSIP
    // ==========================================
    const uploadLaporanForm = document.getElementById('uploadLaporanForm');
    if (uploadLaporanForm) {
        uploadLaporanForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showSuccessModal('Laporan berhasil diupload!');
                    bootstrap.Modal.getInstance(document.getElementById('uploadLaporanModal'))?.hide();
                    setTimeout(() => location.reload(), 1000);
                } else {
                    alert('Error: ' + (data.message || 'Terjadi kesalahan'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat mengupload laporan');
            });
        });
    }

    const inputLaporanForm = document.getElementById('inputLaporanForm');
    if (inputLaporanForm) {
        inputLaporanForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showSuccessModal('Laporan berhasil disimpan!');
                    bootstrap.Modal.getInstance(document.getElementById('inputLaporanModal'))?.hide();
                    setTimeout(() => location.reload(), 1000);
                } else {
                    alert('Error: ' + (data.message || 'Terjadi kesalahan'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menyimpan laporan');
            });
        });
    }

    // ==========================================
    // EVENT DELEGATION - TABEL PENGGUNA
    // ==========================================
    document.addEventListener('click', function(event) {
        const target = event.target.closest('button');
        if (!target) return;

        if (target.classList.contains('btn-edit-pengguna')) {
            editPengguna(target.dataset.userId);
        } else if (target.classList.contains('btn-hapus-pengguna')) {
            hapusPengguna(target.dataset.userId);
        }
    });

});
</script>
</body>
</html>