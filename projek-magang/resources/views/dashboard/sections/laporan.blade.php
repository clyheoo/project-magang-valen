<!-- Laporan Section -->
<section id="laporan" class="dashboard-section">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Laporan & Statistik</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <select class="form-select me-2" id="filterPeriode" style="width: auto;">
                <option value="bulan">Bulan Ini</option>
                <option value="triwulan">Triwulan Ini</option>
                <option value="tahun">Tahun Ini</option>
                <option value="custom">Custom</option>
            </select>
            <div class="input-group me-2" id="customDateRange" style="display: none;">
                <input type="date" class="form-control" id="startDate">
                <span class="input-group-text">-</span>
                <input type="date" class="form-control" id="endDate">
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-muted">Total Surat Masuk</h6>
                            <h3 class="card-text" id="totalSuratMasuk">{{ $totalSuratMasuk ?? 0 }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-envelope fa-2x text-primary"></i>
                        </div>
                    </div>
                    <div class="mt-2">
                        <span class="badge bg-primary">Periode</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-muted">Total Surat Keluar</h6>
                            <h3 class="card-text" id="totalSuratKeluar">{{ $totalSuratKeluar ?? 0 }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-paper-plane fa-2x text-success"></i>
                        </div>
                    </div>
                    <div class="mt-2">
                        <span class="badge bg-success">Periode</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-muted">Efisiensi Proses</h6>
                            <h3 class="card-text" id="efisiensiProses">{{ $efisiensiProses ?? 0 }}%</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-chart-line fa-2x text-info"></i>
                        </div>
                    </div>
                    <div class="mt-2">
                        <span class="badge bg-info">Rata-rata</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-muted">Total Laporan</h6>
                            <h3 class="card-text" id="totalLaporan">{{ $totalLaporan ?? 0 }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-file-alt fa-2x text-warning"></i>
                        </div>
                    </div>
                    <div class="mt-2">
                        <span class="badge bg-warning text-dark">Generated</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Trend Surat Bulanan</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="monthlyTrendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Distribusi per Divisi</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="divisionDistributionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Reports -->
    <div class="row g-3">
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Laporan Terbaru</h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush" id="recentReports">
                        <!-- Reports will be populated by JavaScript -->
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Ringkasan Kinerja</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <h4 id="avgProcessingTime">{{ $avgProcessingTime ?? 0 }}</h4>
                            <small class="text-muted">Waktu Proses Rata-rata</small>
                        </div>
                        <div class="col-6">
                            <h4 id="onTimePercentage">{{ $onTimePercentage ?? 0 }}%</h4>
                            <small class="text-muted">Tepat Waktu</small>
                        </div>
                    </div>
                    <hr>
                    <div class="row text-center">
                        <div class="col-6">
                            <h4 id="mostActiveDivision">{{ $mostActiveDivision ?? 'N/A' }}</h4>
                            <small class="text-muted">Divisi Teraktif</small>
                        </div>
                        <div class="col-6">
                            <h4 id="totalFilesProcessed">{{ $totalFilesProcessed ?? 0 }}</h4>
                            <small class="text-muted">File Diproses</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Export Modal -->
    <div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exportModalLabel">Ekspor Laporan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="exportForm">
                        <div class="mb-3">
                            <label for="exportType" class="form-label">Tipe Ekspor</label>
                            <select class="form-select" id="exportType" name="type" required>
                                <option value="">Pilih Tipe</option>
                                <option value="pdf">PDF</option>
                                <option value="excel">Excel</option>
                                <option value="csv">CSV</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="exportPeriod" class="form-label">Periode</label>
                            <select class="form-select" id="exportPeriod" name="period" required>
                                <option value="bulan">Bulan Ini</option>
                                <option value="triwulan">Triwulan Ini</option>
                                <option value="tahun">Tahun Ini</option>
                                <option value="custom">Custom</option>
                            </select>
                        </div>
                        <div class="mb-3" id="exportDateRange" style="display: none;">
                            <label class="form-label">Rentang Tanggal</label>
                            <div class="input-group">
                                <input type="date" class="form-control" id="exportStartDate" name="start_date">
                                <span class="input-group-text">-</span>
                                <input type="date" class="form-control" id="exportEndDate" name="end_date">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Data yang Diekspor</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="exportSuratMasuk" name="include_surat_masuk" checked>
                                <label class="form-check-label" for="exportSuratMasuk">
                                    Surat Masuk
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="exportSuratKeluar" name="include_surat_keluar" checked>
                                <label class="form-check-label" for="exportSuratKeluar">
                                    Surat Keluar
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="exportBelumDitindak" name="include_belum_ditindak">
                                <label class="form-check-label" for="exportBelumDitindak">
                                    Surat Belum Ditindak
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="exportCharts" name="include_charts">
                                <label class="form-check-label" for="exportCharts">
                                    Grafik & Statistik
                                </label>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="btnExportReport">Ekspor</button>
                </div>
            </div>
        </div>
    </div>
</section>
