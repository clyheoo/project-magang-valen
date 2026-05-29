<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <div class="d-flex align-items-center">
        <button class="btn btn-outline-secondary me-3 sidebar-toggle-btn" type="button"><i class="fas fa-bars"></i></button>
        <h1 class="h2">Surat Keluar</h1>
    </div>
    <div class="d-flex align-items-center mb-2 mb-md-0 gap-2" style="gap: 10px;">
        <div class="input-group input-group-sm" style="width: 320px; height: 38px;">
            <input type="text" name="search" class="form-control" id="searchSuratKeluar" placeholder="Cari penerima, judul, perihal..." style="height: 38px;">
            <button class="btn btn-outline-secondary d-flex align-items-center justify-content-center" type="button" id="btnSearchSuratKeluar" style="height: 38px; width: 38px;"><i class="fas fa-search"></i></button>
        </div>
        <select name="divisi_id" class="form-select form-select-sm" id="filterDivisiKeluar" style="width: 170px; height: 38px; padding: 0 0.75rem;">
            <option value="">Semua Divisi</option>
            @foreach($divisi as $d)
                <option value="{{ $d->id }}">{{ $d->nama_divisi }}</option>
            @endforeach
        </select>
        <select name="status" class="form-select form-select-sm" id="filterStatusKeluar" style="width: 170px; height: 38px; padding: 0 0.75rem;">
            <option value="">Semua Status</option>
            <option value="draft">Draft</option>
            <option value="dikirim">Dikirim</option>
            <option value="diterima">Diterima</option>
        </select>
    </div>
    <div class="btn-toolbar mb-2 mb-md-0">
        <button class="btn btn-success" id="btnSuratKeluarBaru" data-bs-toggle="modal" data-bs-target="#modalSuratKeluarBaru">
            <i class="fas fa-plus me-1"></i> Buat Surat Keluar
        </button>
    </div>
</div>

<!-- Wrapper untuk konten yang akan di-refresh oleh AJAX -->
<div id="suratKeluarContent">
    <div class="card shadow-sm">
        <div class="card-body p-0">
            {{-- Target untuk AJAX --}}
            <div class="list-group list-group-flush" id="suratKeluarList">
                @forelse($suratKeluar as $item)
                <div class="list-group-item email-item" data-id="{{ $item->id }}">
                    <div class="d-flex w-100 align-items-center">
                        <div class="flex-grow-1">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1 email-subject">{{ $item->penerima }}</h6>
                                <small class="text-muted email-date">{{ $item->tanggal_kirim ? $item->tanggal_kirim->diffForHumans() : '' }}</small>
                            </div>
                            <p class="mb-1 email-preview">
                                <strong>{{ $item->judul_laporan }}</strong> - {{ \Illuminate\Support\Str::limit($item->perihal, 100) }}
                            </p>
                            <div class="email-meta mt-2">
                                <span class="badge bg-light text-dark me-2">{{ $item->divisi->nama_divisi ?? 'N/A' }}</span>
                                <span class="badge bg-{{ $item->status == 'draft' ? 'draft' : ($item->status == 'dikirim' ? 'dikirim' : 'diterima') }}">{{ ucfirst($item->status) }}</span>
                            </div>
                        </div>
                        <div class="ms-3 email-actions">
                            <div class="btn-group btn-group-sm" role="group">
                                <button class="btn btn-outline-primary" onclick="lihatSuratKeluar({{ $item->id }})" title="Lihat"><i class="fas fa-eye"></i></button>
                                <button class="btn btn-outline-secondary" onclick="editSuratKeluar({{ $item->id }})" title="Edit"><i class="fas fa-edit"></i></button>
                                <button class="btn btn-outline-danger" onclick="hapusSuratKeluar({{ $item->id }}, event)" title="Hapus"><i class="fas fa-trash"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="list-group-item text-center p-5">
                    <p>Tidak ada data surat keluar yang ditemukan.</p>
                </div>
                @endforelse
            </div>
        </div>
        <div class="card-footer bg-white">
            @if ($suratKeluar instanceof \Illuminate\Pagination\LengthAwarePaginator)
                {{ $suratKeluar->links() }}
            @endif
        </div>
    </div>
</div>

<!-- Modal Lihat Surat Keluar -->
<div class="modal fade" id="modalLihatSuratKeluar" tabindex="-1" aria-labelledby="modalLihatSuratKeluarLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLihatSuratKeluarLabel">Detail Surat Keluar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-8">
                        <table class="table table-borderless">
                            <tr><td width="30%"><strong>Nomor Surat</strong></td><td width="5%">:</td><td id="detailKeluarNomorSurat"></td></tr>
                            <tr><td><strong>Judul Laporan</strong></td><td>:</td><td id="detailKeluarJudulLaporan"></td></tr>
                            <tr><td><strong>Penerima</strong></td><td>:</td><td id="detailKeluarPenerima"></td></tr>
                            <tr><td><strong>Tanggal Kirim</strong></td><td>:</td><td id="detailKeluarTanggalKirim"></td></tr>
                            <tr><td><strong>Perihal</strong></td><td>:</td><td id="detailKeluarPerihal"></td></tr>
                            <tr><td><strong>Divisi</strong></td><td>:</td><td id="detailKeluarDivisi"></td></tr>
                            <tr><td><strong>Status</strong></td><td>:</td><td id="detailKeluarStatus"></td></tr>
                            <tr><td><strong>Pengirim (User)</strong></td><td>:</td><td id="detailKeluarUser"></td></tr>
                            <tr><td><strong>Format File</strong></td><td>:</td><td id="detailKeluarFormat"></td></tr>
                        </table>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-light h-100">
                            <div class="card-body text-center d-flex flex-column justify-content-center align-items-center">
                                <div id="detailKeluarFileIcon" class="mb-3"></div>
                                <h5 class="card-title">Lampiran</h5>
                                <a href="#" id="detailKeluarFileLink" target="_blank" rel="noopener noreferrer" class="btn btn-primary mt-2" style="display: none;"></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Surat Keluar -->
<div class="modal fade" id="modalEditSuratKeluar" tabindex="-1" aria-labelledby="modalEditSuratKeluarLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditSuratKeluarLabel">Edit Surat Keluar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
<form id="formEditSuratKeluar" method="POST" enctype="multipart/form-data" onsubmit="return false">
    @csrf
    <input type="hidden" id="editKeluarId" name="id">
    <div class="modal-body">
        <div class="mb-3">
            <label for="editKeluarNomor" class="form-label">Nomor Surat</label>
            <input type="text" class="form-control" id="editKeluarNomor" name="nomor_surat" required>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="editKeluarTanggal" class="form-label">Tanggal Kirim</label>
                <input type="date" class="form-control" id="editKeluarTanggal" name="tanggal_kirim" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="editKeluarDivisi" class="form-label">Divisi</label>
                <select class="form-select" id="editKeluarDivisi" name="divisi_id" required>
                    <option value="">Pilih Divisi</option>
                    @foreach($divisi as $d)
                        <option value="{{ $d->id }}">{{ $d->nama_divisi }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="mb-3">
            <label for="editKeluarPenerima" class="form-label">Penerima</label>
            <input type="text" class="form-control" id="editKeluarPenerima" name="penerima" required>
        </div>
        <div class="mb-3">
            <label for="editKeluarJudul" class="form-label">Judul Laporan</label>
            <input type="text" class="form-control" id="editKeluarJudul" name="judul_laporan" required>
        </div>
        <div class="mb-3">
            <label for="editKeluarPerihal" class="form-label">Perihal</label>
            <textarea class="form-control" id="editKeluarPerihal" name="perihal" rows="3" required></textarea>
        </div>

        <div class="mb-3">
            <label for="editKeluarInstruksiDisposisi" class="form-label">Instruksi Disposisi</label>
            <input type="text" class="form-control" id="editKeluarInstruksiDisposisi" name="instruksi_disposisi">
        </div>

        <div class="mb-3">
            <label for="editKeluarInstruksiTambahan" class="form-label">Instruksi Tambahan</label>
            <input type="text" class="form-control" id="editKeluarInstruksiTambahan" name="instruksi_tambahan">
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <!-- Removed the Format File dropdown as per request -->
            </div>
            <div class="col-md-6 mb-3">
                <label for="editKeluarStatus" class="form-label">Status</label>
                <select class="form-select" id="editKeluarStatus" name="status" required>
                    <option value="draft">Draft</option>
                    <option value="dikirim">Dikirim</option>
                    <option value="diterima">Diterima</option>
                </select>
            </div>
        </div>
        <div class="mb-3">
            <label for="editKeluarFile" class="form-label">Ganti Lampiran (Opsional)</label>
            <input type="file" class="form-control" id="editKeluarFile" name="file">
            <div class="form-text">Kosongkan jika tidak ingin mengubah lampiran yang sudah ada.</div>
        </div>
    </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.email-item .email-actions {
    opacity: 0;
    transition: opacity 0.2s ease-in-out;
}
.email-item:hover .email-actions {
    opacity: 1;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Fungsi ini sekarang hanya bisa diakses dari dalam scope event listener ini.
    // Kita perlu membuatnya global agar bisa dipanggil dari `onclick`.
    window.lihatSuratKeluar = function(id) {
        fetch(`/surat-keluar/${id}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Gagal mengambil data surat.');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    const surat = data.surat;
                    const modal = new bootstrap.Modal(document.getElementById('modalLihatSuratKeluar'));

                    // Mengisi data ke dalam modal
                    document.getElementById('detailKeluarNomorSurat').textContent = surat.nomor_surat || '-';
                    document.getElementById('detailKeluarJudulLaporan').textContent = surat.judul_laporan || '-';
                    document.getElementById('detailKeluarPenerima').textContent = surat.penerima || '-';
                    document.getElementById('detailKeluarTanggalKirim').textContent = new Date(surat.tanggal_kirim).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
                    document.getElementById('detailKeluarPerihal').textContent = surat.perihal || '-';
                    document.getElementById('detailKeluarDivisi').textContent = surat.divisi_name || '-';
                    document.getElementById('detailKeluarUser').textContent = surat.user_name || '-';
                    document.getElementById('detailKeluarFormat').textContent = surat.format_name || '-';

                    // Mengatur status dengan badge
                    const statusBadge = document.getElementById('detailKeluarStatus');
                    statusBadge.innerHTML = `<span class="badge bg-${surat.status_color}">${surat.status_name}</span>`;

                    // Mengatur link dan ikon file
                    const fileLink = document.getElementById('detailKeluarFileLink');
                    const fileIcon = document.getElementById('detailKeluarFileIcon');
                    if (surat.file_path) {
                        fileLink.href = `/storage/${surat.file_path}`;
                        fileLink.textContent = `Lihat ${surat.format_name}`;
                        fileLink.style.display = 'inline-block';
                        fileIcon.innerHTML = `<i class="fas fa-file-${surat.format_name.toLowerCase().includes('pdf') ? 'pdf' : (surat.format_name.toLowerCase().includes('word') ? 'word' : 'excel')} fa-3x text-secondary"></i>`;
                    } else {
                        fileLink.style.display = 'none';
                        fileIcon.innerHTML = '<p>Tidak ada lampiran.</p>';
                    }

                    modal.show();
                } else {
                    alert(data.message || 'Gagal memuat detail surat.');
                }
            })
            .catch(error => console.error('Error:', error));
    }

    // Tempat untuk menambahkan kode AJAX untuk filter, search, edit, dan hapus di sini
});
</script>

<!-- Modal Input Surat Keluar Baru -->
<div class="modal fade" id="modalSuratKeluarBaru" tabindex="-1" aria-labelledby="modalSuratKeluarBaruLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalSuratKeluarBaruLabel">Buat Surat Keluar Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formSuratKeluarBaru" method="POST" action="/surat-keluar" enctype="multipart/form-data" onsubmit="return false">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="baruKeluarNomor" class="form-label">Nomor Surat</label>
                        <input type="text" class="form-control" id="baruKeluarNomor" name="nomor_surat" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="baruKeluarTanggal" class="form-label">Tanggal Kirim</label>
                            <input type="date" class="form-control" id="baruKeluarTanggal" name="tanggal_kirim" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="baruKeluarDivisi" class="form-label">Divisi</label>
                            <select class="form-select" id="baruKeluarDivisi" name="divisi_id" required>
                                <option value="">Pilih Divisi</option>
                                @foreach($divisi as $d)
                                    <option value="{{ $d->id }}">{{ $d->nama_divisi }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="baruKeluarPenerima" class="form-label">Penerima</label>
                        <input type="text" class="form-control" id="baruKeluarPenerima" name="penerima" required>
                    </div>
                    <div class="mb-3">
                        <label for="baruKeluarJudul" class="form-label">Judul Laporan</label>
                        <input type="text" class="form-control" id="baruKeluarJudul" name="judul_laporan" required>
                    </div>
                    <div class="mb-3">
                        <label for="baruKeluarPerihal" class="form-label">Perihal</label>
                        <textarea class="form-control" id="baruKeluarPerihal" name="perihal" rows="3" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="baruKeluarInstruksiDisposisi" class="form-label">Instruksi Disposisi</label>
                        <input type="text" class="form-control" id="baruKeluarInstruksiDisposisi" name="instruksi_disposisi">
                    </div>

                    <div class="mb-3">
                        <label for="baruKeluarInstruksiTambahan" class="form-label">Instruksi Tambahan</label>
                        <input type="text" class="form-control" id="baruKeluarInstruksiTambahan" name="instruksi_tambahan">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="baruKeluarStatus" class="form-label">Status</label>
                            <select class="form-select" id="baruKeluarStatus" name="status" required>
                                <option value="draft">Draft</option>
                                <option value="dikirim" selected>Dikirim</option>
                                <option value="diterima">Diterima</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="baruKeluarFile" class="form-label">Lampiran</label>
                        <input type="file" class="form-control" id="baruKeluarFile" name="file" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
