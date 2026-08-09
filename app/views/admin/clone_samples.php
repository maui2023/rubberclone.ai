<?php
// app/views/admin/clone_samples.php
$this->view('layouts/header', $data);
?>

<div class="dashboard-panel table-panel" id="clone-samples-panel">
    
    <div class="panel-header table-header">
        <div class="header-text-group">
            <h2 class="panel-title">Pengurusan Sampel Klon Getah</h2>
            <span class="panel-subtitle">Tambah, sunting, dan urus data sampel klon getah untuk analisis AI pada aplikasi Flutter</span>
        </div>
        <div class="table-actions-group" style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
            <!-- Filter Warna Lateks -->
            <select id="filter-latex-select" class="filter-select" aria-label="Tapis mengikut warna lateks">
                <option value="">Semua Lateks</option>
                <option value="Putih">Putih</option>
                <option value="Krim">Krim</option>
                <option value="Kekuningan">Kekuningan</option>
            </select>

            <!-- Filter Status -->
            <select id="filter-clone-status-select" class="filter-select" aria-label="Tapis mengikut status">
                <option value="">Semua Status</option>
                <option value="active">Aktif</option>
                <option value="inactive">Nyahaktif</option>
            </select>

            <!-- Input Carian -->
            <div class="search-bar-wrapper">
                <svg class="search-bar-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                <input type="text" id="search-clones-input" placeholder="Cari nama klon, warisan..." aria-label="Cari sampel klon">
            </div>

            <!-- Butang Tambah Klon -->
            <button class="btn btn-primary" id="btn-add-clone" style="display: inline-flex; align-items: center; gap: 0.5rem; height: 42px; padding: 0 1.25rem; border: none; font-weight: 600; border-radius: var(--radius-md); transition: var(--transition-smooth);">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Tambah Klon
            </button>
        </div>
    </div>
    
    <div class="table-responsive-container">
        <table class="admin-table" id="clones-data-table">
            <thead>
                <tr>
                    <th scope="col">Nama Klon</th>
                    <th scope="col">Warisan</th>
                    <th scope="col">Potensi Hasil</th>
                    <th scope="col">Bentuk Daun</th>
                    <th scope="col">Warna Lateks</th>
                    <th scope="col">Status</th>
                    <th scope="col">Tindakan</th>
                </tr>
            </thead>
            <tbody id="clones-table-body">
                <tr>
                    <td colspan="7" class="table-loading-row">
                        <span class="spinner"></span>
                        Memuatkan data sampel klon getah...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Pagination Footer -->
    <div class="table-pagination-footer" id="clones-pagination">
        <div class="pagination-info" id="clones-pagination-info">
            Menunjukkan 0 - 0 daripada 0 rekod
        </div>
        <div class="pagination-controls">
            <label for="clones-page-size" class="pagination-size-label">Papar:</label>
            <select id="clones-page-size" class="filter-select pagination-size-select">
                <option value="10" selected>10</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>
            <div class="pagination-buttons">
                <button class="btn-pagination" id="clones-prev-page" title="Halaman Sebelumnya" disabled>&laquo; Sebelah</button>
                <span class="pagination-page-indicator" id="clones-page-indicator">Halaman 1 / 1</span>
                <button class="btn-pagination" id="clones-next-page" title="Halaman Seterusnya" disabled>Seterusnya &raquo;</button>
            </div>
        </div>
    </div>
    
</div>

<!-- Modal Tambah / Sunting Klon (Glassmorphism Modal Overlay) -->
<div class="modal-overlay" id="clone-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); backdrop-filter: blur(8px); z-index: 1000; align-items: center; justify-content: center;">
    <div class="modal-container glass-card" style="max-width: 680px; width: 90%; max-height: 90vh; overflow-y: auto; padding: 2.5rem; border-radius: var(--radius-lg); border: 1px solid rgba(255, 255, 255, 0.08); box-shadow: 0 20px 50px rgba(0,0,0,0.4); position: relative; background: rgba(13, 27, 21, 0.92); backdrop-filter: blur(20px);">
        
        <!-- Butang Tutup -->
        <button class="modal-close-btn" id="btn-close-clone-modal" aria-label="Tutup modal" style="position: absolute; top: 1.25rem; right: 1.25rem; background: none; border: none; color: var(--color-text-muted); cursor: pointer; transition: var(--transition-smooth);">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>

        <h3 class="modal-title" id="clone-modal-title" style="margin-top: 0; margin-bottom: 0.5rem; font-family: var(--font-heading); color: var(--color-text-primary); font-size: 1.5rem; font-weight: 700;">Tambah Klon Baharu</h3>
        <p class="modal-desc" style="margin-top: 0; margin-bottom: 2rem; color: var(--color-text-muted); font-size: 0.9rem;">Isikan maklumat ciri-ciri morfologi klon getah untuk kegunaan analisis AI.</p>

        <!-- Borang Klon -->
        <form id="clone-form" style="display: flex; flex-direction: column; gap: 1.25rem;">
            <input type="hidden" id="clone-edit-id" value="">
            
            <!-- Baris 1: Nama Klon & Warisan -->
            <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group" style="display: flex; flex-direction: column; gap: 0.5rem;">
                    <label for="clone-name" style="color: var(--color-text-secondary); font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Nama Klon *</label>
                    <input type="text" id="clone-name" name="clone_name" required placeholder="Contoh: PB 350" style="width: 100%; padding: 0.8rem 1rem; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.1); border-radius: var(--radius-md); color: var(--color-text-primary); transition: var(--transition-smooth);">
                </div>
                <div class="form-group" style="display: flex; flex-direction: column; gap: 0.5rem;">
                    <label for="clone-warisan" style="color: var(--color-text-secondary); font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Warisan</label>
                    <input type="text" id="clone-warisan" name="warisan" placeholder="Contoh: RRIM 600 × PB 235" style="width: 100%; padding: 0.8rem 1rem; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.1); border-radius: var(--radius-md); color: var(--color-text-primary); transition: var(--transition-smooth);">
                </div>
            </div>

            <!-- Baris 2: Potensi Hasil & Anggaran Kayu -->
            <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group" style="display: flex; flex-direction: column; gap: 0.5rem;">
                    <label for="clone-potensi" style="color: var(--color-text-secondary); font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Potensi Hasil (kg/ha/th)</label>
                    <input type="text" id="clone-potensi" name="potensi_hasil" placeholder="Contoh: 2,765" style="width: 100%; padding: 0.8rem 1rem; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.1); border-radius: var(--radius-md); color: var(--color-text-primary); transition: var(--transition-smooth);">
                </div>
                <div class="form-group" style="display: flex; flex-direction: column; gap: 0.5rem;">
                    <label for="clone-kayu" style="color: var(--color-text-secondary); font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Anggaran Hasil Kayu</label>
                    <input type="text" id="clone-kayu" name="anggaran_kayu" placeholder="Contoh: 19T/1.6" style="width: 100%; padding: 0.8rem 1rem; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.1); border-radius: var(--radius-md); color: var(--color-text-primary); transition: var(--transition-smooth);">
                </div>
            </div>

            <!-- Baris 3: Bentuk Daun & Hujung Daun -->
            <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group" style="display: flex; flex-direction: column; gap: 0.5rem;">
                    <label for="clone-bentuk-daun" style="color: var(--color-text-secondary); font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Bentuk Daun</label>
                    <input type="text" id="clone-bentuk-daun" name="bentuk_daun" placeholder="Contoh: Bulat (rounded)" style="width: 100%; padding: 0.8rem 1rem; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.1); border-radius: var(--radius-md); color: var(--color-text-primary); transition: var(--transition-smooth);">
                </div>
                <div class="form-group" style="display: flex; flex-direction: column; gap: 0.5rem;">
                    <label for="clone-hujung-daun" style="color: var(--color-text-secondary); font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Bentuk Hujung Daun</label>
                    <input type="text" id="clone-hujung-daun" name="bentuk_hujung_daun" placeholder="Contoh: Kuspidat (Cuspidate)" style="width: 100%; padding: 0.8rem 1rem; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.1); border-radius: var(--radius-md); color: var(--color-text-primary); transition: var(--transition-smooth);">
                </div>
            </div>

            <!-- Baris 4: Pangkal Daun & Kedudukan Lai -->
            <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group" style="display: flex; flex-direction: column; gap: 0.5rem;">
                    <label for="clone-pangkal-daun" style="color: var(--color-text-secondary); font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Bentuk Pangkal Daun</label>
                    <input type="text" id="clone-pangkal-daun" name="bentuk_pangkal_daun" placeholder="Contoh: Bulat (Obtuse)" style="width: 100%; padding: 0.8rem 1rem; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.1); border-radius: var(--radius-md); color: var(--color-text-primary); transition: var(--transition-smooth);">
                </div>
                <div class="form-group" style="display: flex; flex-direction: column; gap: 0.5rem;">
                    <label for="clone-lai-daun" style="color: var(--color-text-secondary); font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Kedudukan Lai Daun</label>
                    <input type="text" id="clone-lai-daun" name="kedudukan_lai_daun" placeholder="Contoh: Bersentuh ke bertindih" style="width: 100%; padding: 0.8rem 1rem; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.1); border-radius: var(--radius-md); color: var(--color-text-primary); transition: var(--transition-smooth);">
                </div>
            </div>

            <!-- Baris 5: Tepi Daun & Warna Daun -->
            <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group" style="display: flex; flex-direction: column; gap: 0.5rem;">
                    <label for="clone-tepi-daun" style="color: var(--color-text-secondary); font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Bentuk Tepi Daun</label>
                    <input type="text" id="clone-tepi-daun" name="bentuk_tepi_daun" placeholder="Contoh: Gelombang" style="width: 100%; padding: 0.8rem 1rem; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.1); border-radius: var(--radius-md); color: var(--color-text-primary); transition: var(--transition-smooth);">
                </div>
                <div class="form-group" style="display: flex; flex-direction: column; gap: 0.5rem;">
                    <label for="clone-warna-daun" style="color: var(--color-text-secondary); font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Warna Daun & Kilauan</label>
                    <input type="text" id="clone-warna-daun" name="warna_daun_kilauan" placeholder="Contoh: Hijau tua, sedikit berkilat" style="width: 100%; padding: 0.8rem 1rem; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.1); border-radius: var(--radius-md); color: var(--color-text-primary); transition: var(--transition-smooth);">
                </div>
            </div>

            <!-- Baris 6: Permukaan Daun & Pandangan Memanjang -->
            <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group" style="display: flex; flex-direction: column; gap: 0.5rem;">
                    <label for="clone-permukaan" style="color: var(--color-text-secondary); font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Permukaan Daun</label>
                    <input type="text" id="clone-permukaan" name="permukaan_daun" placeholder="Contoh: Licin" style="width: 100%; padding: 0.8rem 1rem; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.1); border-radius: var(--radius-md); color: var(--color-text-primary); transition: var(--transition-smooth);">
                </div>
                <div class="form-group" style="display: flex; flex-direction: column; gap: 0.5rem;">
                    <label for="clone-memanjang" style="color: var(--color-text-secondary); font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Pandangan Memanjang</label>
                    <input type="text" id="clone-memanjang" name="pandangan_memanjang" placeholder="Contoh: Rata/Selanjar" style="width: 100%; padding: 0.8rem 1rem; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.1); border-radius: var(--radius-md); color: var(--color-text-primary); transition: var(--transition-smooth);">
                </div>
            </div>

            <!-- Baris 7: Pandangan Melintang & Saiz Gagang -->
            <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group" style="display: flex; flex-direction: column; gap: 0.5rem;">
                    <label for="clone-melintang" style="color: var(--color-text-secondary); font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Pandangan Melintang</label>
                    <input type="text" id="clone-melintang" name="pandangan_melintang" placeholder="Contoh: Rata" style="width: 100%; padding: 0.8rem 1rem; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.1); border-radius: var(--radius-md); color: var(--color-text-primary); transition: var(--transition-smooth);">
                </div>
                <div class="form-group" style="display: flex; flex-direction: column; gap: 0.5rem;">
                    <label for="clone-gagang" style="color: var(--color-text-secondary); font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Saiz Gagang Daun</label>
                    <input type="text" id="clone-gagang" name="saiz_gagang_daun" placeholder="Contoh: Sederhana panjang, rata" style="width: 100%; padding: 0.8rem 1rem; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.1); border-radius: var(--radius-md); color: var(--color-text-primary); transition: var(--transition-smooth);">
                </div>
            </div>

            <!-- Baris 8: Anak Gagang & Warna Lateks -->
            <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group" style="display: flex; flex-direction: column; gap: 0.5rem;">
                    <label for="clone-anak-gagang" style="color: var(--color-text-secondary); font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Saiz Anak Gagang</label>
                    <input type="text" id="clone-anak-gagang" name="saiz_anak_gagang" placeholder="Contoh: Pendek dan rata" style="width: 100%; padding: 0.8rem 1rem; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.1); border-radius: var(--radius-md); color: var(--color-text-primary); transition: var(--transition-smooth);">
                </div>
                <div class="form-group" style="display: flex; flex-direction: column; gap: 0.5rem;">
                    <label for="clone-lateks" style="color: var(--color-text-secondary); font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Warna Lateks</label>
                    <input type="text" id="clone-lateks" name="warna_lateks" placeholder="Contoh: Putih" style="width: 100%; padding: 0.8rem 1rem; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.1); border-radius: var(--radius-md); color: var(--color-text-primary); transition: var(--transition-smooth);">
                </div>
            </div>

            <!-- Mesej Ralat Borang -->
            <div id="clone-form-error" style="color: #ef4444; font-size: 0.85rem; display: none;"></div>

            <button type="submit" class="btn btn-primary" id="btn-submit-clone" style="border: none; padding: 0.85rem; font-weight: 600; border-radius: var(--radius-md); margin-top: 0.5rem; display: flex; align-items: center; justify-content: center; gap: 0.5rem; cursor: pointer;">
                Simpan Klon
            </button>
        </form>

    </div>
</div>

<!-- Modal Paparan Penuh Ciri-Ciri Klon -->
<div class="modal-overlay" id="clone-detail-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); backdrop-filter: blur(8px); z-index: 1000; align-items: center; justify-content: center;">
    <div class="modal-card" style="max-width: 600px; width: 90%; max-height: 90vh; overflow-y: auto;">
        <button class="modal-close-btn" id="close-detail-modal-btn" style="cursor: pointer; background: none; border: none;" aria-label="Tutup">&times;</button>
        <div class="modal-body-content">
            <h3 class="modal-header-title" id="detail-clone-title">Ciri-Ciri Klon</h3>
            <div class="modal-text-details" id="detail-clone-body">
                <!-- Populated by JS -->
            </div>
        </div>
    </div>
</div>

<?php
$this->view('layouts/footer', $data);
?>
