<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom gap-3">
    <div class="d-flex align-items-center flex-shrink-0">
        <button class="btn btn-outline-secondary me-3 sidebar-toggle-btn" type="button"><i class="fas fa-bars"></i></button>
        <h1 class="h2 mb-0">Surat Keluar</h1>
    </div>
    
    <!-- Search Bar -->
    <div class="input-group input-group-sm flex-grow-1" style="height: 38px;">
        <input type="text" name="search" class="form-control" id="searchSuratKeluar" placeholder="Cari penerima, judul, perihal..." style="height: 38px;">
        <button class="btn btn-outline-secondary d-flex align-items-center justify-content-center" type="button" id="btnSearchSuratKeluar" style="height: 38px; width: 38px;"><i class="fas fa-search"></i></button>
    </div>
    
    <!-- Dropdowns & Tombol Aksi -->
    <div class="d-flex align-items-stretch gap-2 flex-grow-1">
        <select name="divisi_id" class="form-select form-select-sm flex-grow-1" id="filterDivisiKeluar" style="height: 38px;">
            <option value="">Semua Divisi</option>
            @foreach($divisi as $d)
                <option value="{{ $d->id }}">{{ $d->nama_divisi }}</option>
            @endforeach
        </select>
        <select name="status" class="form-select form-select-sm flex-grow-1" id="filterStatusKeluar" style="height: 38px;">
            <option value="">Semua Status</option>
            <option value="draft">Draft</option>
            <option value="dikirim">Dikirim</option>
            <option value="diterima">Diterima</option>
        </select>
        <button class="btn btn-success text-nowrap" id="btnSuratKeluarBaru" data-bs-toggle="modal" data-bs-target="#modalSuratKeluarBaru" style="height: 38px;">
            <i class="fas fa-plus me-1"></i> Buat Surat
        </button>
    </div>
</div>

<!-- Wrapper untuk konten yang akan di-refresh oleh AJAX -->
<div id="suratKeluarContent">
    <div class="card shadow-sm">
        <div class="card-body p-0">
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