import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER ?? 'mt1',
    wsHost: import.meta.env.VITE_PUSHER_HOST ? import.meta.env.VITE_PUSHER_HOST : `ws-${import.meta.env.VITE_PUSHER_APP_CLUSTER ?? 'mt1'}.pusher.com`,
    wsPort: import.meta.env.VITE_PUSHER_PORT ?? 80,
    wssPort: import.meta.env.VITE_PUSHER_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_PUSHER_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
});

document.addEventListener('DOMContentLoaded', function() {

    // ==========================================
    // FUNGSI UMUM
    // ==========================================

    // Function to show success modal
    window.showSuccessModal = function(message) {
        const el = document.getElementById('successMessage');
        const modalEl = document.getElementById('successModal');
        if (!el || !modalEl) return;

        el.textContent = message;
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
        setTimeout(() => modal.hide(), 3000);
    };

    // Function to show error modal
    window.showErrorModal = function(message) {
        const el = document.getElementById('errorMessage');
        const modalEl = document.getElementById('errorModal');
        if (!el || !modalEl) return;

        el.textContent = message;
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
        setTimeout(() => modal.hide(), 3000);
    };

    // Toggle password visibility
    window.togglePasswordVisibility = function(inputId) {
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
    };

    // Helper function to capitalize first letter
    function ucfirst(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    // ==========================================
    // SIDEBAR & NAVIGATION (Ditangani Layout)
    // ==========================================
    // Sidebar toggle dan navigasi sudah ditangani di layouts/dashboard.blade.php
    // Tidak perlu duplikasi di sini

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
                    const data = await response.json().catch(() => ({}));
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
                            errorMessages += `- ${errors[key].join(', ')}\n`;
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
        fetch(`/api/surat-masuk/${id}`, {
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

            const setValue = (id, value) => {
                const el = modalElement.querySelector(`#${id}`);
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

    // Form Edit Surat Masuk submission
    const formEditSuratMasuk = document.getElementById('formEditSuratMasuk');
    if (formEditSuratMasuk) {
        formEditSuratMasuk.addEventListener('submit', function(e) {
            e.preventDefault();
            const id = document.getElementById('editSuratId').value;
            const formData = new FormData(this);
            formData.append('_method', 'PUT');

            fetch(`/surat-masuk/${id}`, {
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
                        errorMessages += `- ${errorData.errors[key].join(', ')}\n`;
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
            fetch(`/surat-masuk/${id}`, {
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
            .then(response => response.json().catch(() => ({})))
            .then(data => {
                if (data.success) {
                    showSuccessModal('Surat keluar berhasil ditambahkan!');
                    bootstrap.Modal.getInstance(document.getElementById('modalSuratKeluarBaru'))?.hide();
                    this.reset();
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showErrorModal(data.message || 'Gagal menyimpan surat keluar');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showErrorModal('Terjadi kesalahan saat menyimpan surat keluar');
            });
        });
    }

    // ==========================================
    // SURAT KELUAR - LIHAT DETAIL
    // ==========================================
    window.lihatSuratKeluar = function(id) {
        fetch(`/surat-keluar/${id}`, {
            headers: { 'Accept': 'application/json' }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.surat) {
                const surat = data.surat;
                const modalEl = document.getElementById('modalLihatSuratKeluar');
                if (!modalEl) return;

                // Isi data modal
                const setText = (id, value) => {
                    const el = document.getElementById(id);
                    if (el) el.textContent = value || '-';
                };

                setText('detailKeluarNomorSurat', surat.nomor_surat);
                setText('detailKeluarJudulLaporan', surat.judul_laporan);
                setText('detailKeluarFormat', surat.format_name);
                setText('detailKeluarPenerima', surat.penerima);
                setText('detailKeluarTanggalKirim', surat.tanggal_kirim);
                setText('detailKeluarPerihal', surat.perihal);
                setText('detailKeluarDivisi', surat.divisi_name);
                setText('detailKeluarUser', surat.user_name);

                const statusEl = document.getElementById('detailKeluarStatus');
                if (statusEl) {
                    statusEl.innerHTML = `<span class="badge bg-${surat.status_color || 'secondary'}">${surat.status_name || surat.status}</span>`;
                }

                // File icon dan link
                const fileIconContainer = document.getElementById('detailKeluarFileIcon');
                const fileLink = document.getElementById('detailKeluarFileLink');
                
                if (fileIconContainer) {
                    let iconClass = 'fas fa-file fa-7x mb-3';
                    let iconColor = 'text-secondary';
                    if (surat.format_file_id == 1) { iconClass = 'fas fa-file-pdf fa-7x mb-3'; iconColor = 'text-danger'; }
                    else if (surat.format_file_id == 2) { iconClass = 'fas fa-file-word fa-7x mb-3'; iconColor = 'text-primary'; }
                    else if (surat.format_file_id == 3) { iconClass = 'fas fa-file-excel fa-7x mb-3'; iconColor = 'text-success'; }
                    fileIconContainer.innerHTML = `<i class="${iconClass} ${iconColor}"></i>`;
                }

                if (fileLink) {
                    if (surat.file_path) {
                        fileLink.href = `${window.location.origin}/storage/${surat.file_path}`;
                        fileLink.style.display = 'inline-block';
                        fileLink.target = surat.format_file_id == 1 ? '_blank' : '_self';
                        if (surat.format_file_id != 1) fileLink.setAttribute('download', '');
                    } else {
                        fileLink.style.display = 'none';
                    }
                }

                new bootstrap.Modal(modalEl).show();
            } else {
                alert('Gagal mengambil data surat keluar');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat mengambil data surat keluar');
        });
    };

    // ==========================================
    // SURAT KELUAR - EDIT
    // ==========================================
    window.editSuratKeluar = function(id) {
        fetch(`/surat-keluar/${id}`, {
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

            const setValue = (id, value) => {
                const el = modalElement.querySelector(`#${id}`);
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

    // Form Edit Surat Keluar submission
    const formEditSuratKeluar = document.getElementById('formEditSuratKeluar');
    if (formEditSuratKeluar) {
        formEditSuratKeluar.addEventListener('submit', function(e) {
            e.preventDefault();
            const id = document.getElementById('editKeluarId')?.value;
            if (!id) return;

            const formData = new FormData(this);
            formData.append('_method', 'PUT');

            fetch(`/surat-keluar/${id}`, {
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
                        errorMessages += `- ${errorData.errors[key].join(', ')}\n`;
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

            fetch(`/surat-keluar/${id}`, {
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
        fetch(`/users/${id}`, {
            headers: { 'Accept': 'application/json' }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.user) {
                const user = data.user;
                const form = document.getElementById('editPenggunaForm');
                const modalElement = document.getElementById('editPenggunaModal');
                
                if (!form || !modalElement) return;

                form.action = `/users/${user.id}`;
                
                const setValue = (id, value) => {
                    const el = document.getElementById(id);
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

            fetch(`/users/${id}`, {
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
    // EVENT DELEGATION - TABEL PENGGUNA
    // ==========================================
    const penggunaTableBody = document.getElementById('penggunaTableBody');
    if (penggunaTableBody) {
        penggunaTableBody.addEventListener('click', function(event) {
            const target = event.target.closest('button');
            if (!target) return;

            if (target.classList.contains('btn-edit-pengguna')) {
                editPengguna(target.dataset.userId);
            } else if (target.classList.contains('btn-hapus-pengguna')) {
                hapusPengguna(target.dataset.userId);
            }
        });
    }

    // ==========================================
    // FORM SUBMISSIONS - PENGGUNA
    // ==========================================
    
    // Form Tambah Pengguna
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

    // Form Edit Pengguna
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
    // FORM SUBMISSIONS - LAPORAN & ARSIP
    // ==========================================
    
    // Upload Laporan
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

    // Input Laporan Manual
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
    // REAL-TIME UPDATES (Echo)
    // ==========================================
    function initRealTimeUpdates() {
        try {
            window.Echo.channel('surat-masuk')
                .listen('.surat-masuk.updated', (e) => {
                    console.log('Real-time update received:', e);
                    // Refresh halaman jika ada update penting
                    // location.reload(); // Uncomment jika ingin auto refresh
                });
        } catch (error) {
            console.warn('Echo not configured or error:', error);
        }
    }

    initRealTimeUpdates();

});