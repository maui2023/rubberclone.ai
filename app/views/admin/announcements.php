<?php
// app/views/admin/announcements.php
$this->view('layouts/header', $data);
?>

<div class="dashboard-panel table-panel" id="announcements-panel">
    
    <div class="panel-header table-header">
        <div class="header-text-group">
            <h2 class="panel-title">Pengurusan Pekeliling & Makluman RISDA</h2>
            <span class="panel-subtitle">Terbit, kemas kini, dan urus makluman rasmi atau pekeliling untuk dipaparkan pada aplikasi mudah alih RISDA</span>
        </div>
        <div class="table-actions-group" style="display: flex; align-items: center; gap: 1rem;">
            <!-- Input Carian -->
            <div class="search-bar-wrapper">
                <svg class="search-bar-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                <input type="text" id="search-announcements-input" placeholder="Cari pekeliling..." aria-label="Cari pekeliling">
            </div>

            <!-- Butang Tambah Pekeliling -->
            <button class="btn btn-primary" id="btn-add-announcement" style="display: inline-flex; align-items: center; gap: 0.5rem; height: 42px; padding: 0 1.25rem; border: none; font-weight: 600; border-radius: var(--radius-md); transition: var(--transition-smooth);">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Tambah Pekeliling
            </button>
        </div>
    </div>
    
    <div class="table-responsive-container">
        <table class="admin-table" id="announcements-data-table">
            <thead>
                <tr>
                    <th scope="col">Tajuk Pekeliling</th>
                    <th scope="col">Pengarang</th>
                    <th scope="col">Tarikh Siaran</th>
                    <th scope="col">Tarikh Luput</th>
                    <th scope="col">Status</th>
                    <th scope="col">Tindakan</th>
                </tr>
            </thead>
            <tbody id="announcements-table-body">
                <tr>
                    <td colspan="6" class="table-loading-row">
                        <span class="spinner"></span>
                        Memuatkan senarai pekeliling & makluman...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    
</div>

<!-- Modal Tambah / Sunting Pekeliling (Glassmorphism Modal Overlay) -->
<div class="modal-overlay" id="announcement-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); backdrop-filter: blur(8px); z-index: 1000; align-items: center; justify-content: center;">
    <div class="modal-container glass-card" style="max-width: 600px; width: 90%; max-height: 90vh; overflow-y: auto; padding: 2.5rem; border-radius: var(--radius-lg); border: 1px solid rgba(255, 255, 255, 0.08); box-shadow: 0 20px 50px rgba(0,0,0,0.4); position: relative; background: rgba(13, 27, 21, 0.92); backdrop-filter: blur(20px);">
        
        <!-- Butang Tutup -->
        <button class="modal-close-btn" id="btn-close-announcement-modal" aria-label="Tutup modal" style="position: absolute; top: 1.25rem; right: 1.25rem; background: none; border: none; color: var(--color-text-muted); cursor: pointer; transition: var(--transition-smooth);">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>

        <h3 class="modal-title" id="announcement-modal-title" style="margin-top: 0; margin-bottom: 0.5rem; font-family: var(--font-heading); color: var(--color-text-primary); font-size: 1.5rem; font-weight: 700;">Tambah Pekeliling Baharu</h3>
        <p class="modal-desc" style="margin-top: 0; margin-bottom: 2rem; color: var(--color-text-muted); font-size: 0.9rem;">Isikan maklumat pekeliling rasmi RISDA untuk diterbitkan kepada pekebun & pegawai.</p>

        <!-- Borang Pekeliling -->
        <form id="announcement-form" class="admin-form" style="display: flex; flex-direction: column; gap: 1.25rem;">
            <input type="hidden" id="announcement-edit-id" value="">
            
            <div class="form-group" style="display: flex; flex-direction: column; gap: 0.5rem;">
                <label for="announcement-title" style="color: var(--color-text-secondary); font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Tajuk Pekeliling / Makluman *</label>
                <input type="text" id="announcement-title" name="title" required placeholder="Contoh: MAKLUMAN INTENSIF BAJA GETAH RISDA 2026" style="width: 100%; padding: 0.8rem 1rem; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.1); border-radius: var(--radius-md); color: var(--color-text-primary); transition: var(--transition-smooth);">
            </div>

            <div class="form-group" style="display: flex; flex-direction: column; gap: 0.5rem;">
                <label for="announcement-content" style="color: var(--color-text-secondary); font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Kandungan Pekeliling *</label>
                <textarea id="announcement-content" name="content" rows="5" required placeholder="Tuliskan butiran lengkap pekeliling di sini..." style="width: 100%; padding: 0.8rem 1rem; background: rgba(7, 14, 11, 0.6); border: 1px solid rgba(255,255,255,0.1); border-radius: var(--radius-md); color: var(--color-text-primary); transition: var(--transition-smooth); resize: vertical;"></textarea>
            </div>

            <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group" style="display: flex; flex-direction: column; gap: 0.5rem;">
                    <label for="announcement-publish-date" style="color: var(--color-text-secondary); font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Tarikh Siaran</label>
                    <input type="date" id="announcement-publish-date" style="width: 100%; padding: 0.8rem 1rem; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.1); border-radius: var(--radius-md); color: var(--color-text-primary); transition: var(--transition-smooth);">
                </div>

                <div class="form-group" style="display: flex; flex-direction: column; gap: 0.5rem;">
                    <label for="announcement-expire-date" style="color: var(--color-text-secondary); font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Tarikh Luput (Opsional)</label>
                    <input type="date" id="announcement-expire-date" style="width: 100%; padding: 0.8rem 1rem; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.1); border-radius: var(--radius-md); color: var(--color-text-primary); transition: var(--transition-smooth);">
                </div>
            </div>

            <div class="form-group" style="display: flex; flex-direction: column; gap: 0.5rem;">
                <label for="announcement-status" style="color: var(--color-text-secondary); font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Status Pekeliling</label>
                <select id="announcement-status" name="status" style="width: 100%; padding: 0.8rem 1rem; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.1); border-radius: var(--radius-md); color: var(--color-text-primary); transition: var(--transition-smooth); cursor: pointer;">
                    <option value="active" style="background: #0d1b15; color: #fff;">Aktif (Diterbitkan)</option>
                    <option value="inactive" style="background: #0d1b15; color: #fff;">Draf / Dinyahaktifkan</option>
                </select>
            </div>

            <!-- Mesej Ralat Borang -->
            <div id="announcement-form-error" style="color: #ef4444; font-size: 0.85rem; display: none;"></div>

            <button type="submit" class="btn btn-primary" id="btn-submit-announcement" style="border: none; padding: 0.85rem; font-weight: 600; border-radius: var(--radius-md); margin-top: 0.5rem; display: flex; align-items: center; justify-content: center; gap: 0.5rem; cursor: pointer;">
                Terbitkan Pekeliling
            </button>
        </form>

    </div>
</div>

<!-- Modal Paparan Penuh Pekeliling -->
<div class="modal-overlay" id="announcement-detail-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); backdrop-filter: blur(8px); z-index: 1000; align-items: center; justify-content: center;">
    <div class="modal-card" style="max-width: 600px; width: 90%; max-height: 90vh; overflow-y: auto;">
        <button class="modal-close-btn" id="close-announcement-detail-btn" style="cursor: pointer; background: none; border: none;" aria-label="Tutup">&times;</button>
        <div class="modal-body-content">
            <h3 class="modal-header-title" id="detail-announcement-title">Butiran Pekeliling</h3>
            <div class="modal-text-details" id="detail-announcement-body">
                <!-- Populated by JS -->
            </div>
        </div>
    </div>
</div>

<?php
$this->view('layouts/footer', $data);
?>
