<?php

if (!function_exists('getStatusColor')) {
    function getStatusColor($status)
    {
        $colors = [
            'baru' => 'primary',
            'diterima' => 'success',
            'ditolak' => 'danger',
            'diproses' => 'warning text-dark',
            'selesai' => 'info',
            'draft' => 'secondary',
            'dikirim' => 'info',
        ];

        return $colors[$status] ?? 'secondary';
    }
}

if (!function_exists('getStatusName')) {
    function getStatusName($status)
    {
        $names = [
            'baru' => 'Baru',
            'diterima' => 'Diterima',
            'ditolak' => 'Ditolak',
            'diproses' => 'Diproses',
            'selesai' => 'Selesai',
            'draft' => 'Draft',
            'dikirim' => 'Dikirim',
        ];

        return $names[$status] ?? $status;
    }
}

if (!function_exists('getFormatColor')) {
    function getFormatColor($format)
    {
        $colors = [
            'PDF' => 'danger',
            'DOCX' => 'primary',
            'XLSX' => 'success',
        ];

        return $colors[$format] ?? 'secondary';
    }
}

if (!function_exists('getDepartmentName')) {
    function getDepartmentName($code)
    {
        $names = [
            'umum' => 'Umum & Kepegawaian',
            'keuangan' => 'Keuangan',
            'perencanaan' => 'Perencanaan',
            'hukum' => 'Hukum & Kerjasama',
            'ti' => 'Teknologi Informasi',
        ];

        return $names[$code] ?? $code;
    }
}

if (!function_exists('getFileIconClass')) {
    /**
     * Mendapatkan kelas ikon file berdasarkan format ID.
     *
     * @param int|null $formatId
     * @param string $sizeClass
     * @return string
     */
    function getFileIconClass($formatId, $sizeClass = 'fa-2x')
    {
        $icon = 'fas fa-file ' . $sizeClass . ' text-secondary'; // Default icon
        if ($formatId == 1) { // PDF
            $icon = 'fas fa-file-pdf ' . $sizeClass . ' text-danger';
        } elseif ($formatId == 2) { // DOC
            $icon = 'fas fa-file-word ' . $sizeClass . ' text-primary';
        } elseif ($formatId == 3) { // DOCX
            $icon = 'fas fa-file-word ' . $sizeClass . ' text-primary';
        } elseif ($formatId == 4) { // XLS
            $icon = 'fas fa-file-excel ' . $sizeClass . ' text-success';
        } elseif ($formatId == 5) { // XLSX
            $icon = 'fas fa-file-excel ' . $sizeClass . ' text-success';
        }

        return $icon;
    }
}

if (!function_exists('getStatusHexColor')) {
    function getStatusHexColor($status)
    {
        $colors = [
            'baru' => '#007bff', // primary
            'diterima' => '#28a745', // success
            'ditolak' => '#dc3545', // danger
            'diproses' => '#ffc107', // warning
            'selesai' => '#17a2b8', // info
            'draft' => '#6c757d', // secondary
            'dikirim' => '#17a2b8', // info
        ];

        return $colors[$status] ?? '#6c757d';
    }
}
