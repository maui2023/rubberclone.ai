<?php
// app/views/admin/users.php
$this->view('layouts/header', $data);
?>

<div class="dashboard-panel table-panel" id="users-directory-panel">
    
    <div class="panel-header table-header">
        <div class="header-text-group">
            <h2 class="panel-title">Direktori Pengguna RISDA</h2>
            <span class="panel-subtitle">Sahkan peranti, urus kelulusan, dan status akses pegawai lapangan</span>
        </div>
        <div class="table-actions-group" style="display: flex; align-items: center; gap: 1rem;">
            <!-- Input Carian -->
            <div class="search-bar-wrapper">
                <svg class="search-bar-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                <input type="text" id="search-users-input" placeholder="Cari nama, e-mel, stesen..." aria-label="Cari pengguna">
            </div>

            <!-- Butang Tambah Pengguna -->
            <button class="btn btn-primary" id="btn-add-user" style="display: inline-flex; align-items: center; gap: 0.5rem; height: 42px; padding: 0 1.25rem; border: none; font-weight: 600; border-radius: var(--radius-md); transition: var(--transition-smooth);">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Tambah Pengguna
            </button>
        </div>
    </div>
    
    <div class="table-responsive-container">
        <table class="admin-table" id="users-data-table">
            <thead>
                <tr>
                    <th scope="col">Nama Penuh</th>
                    <th scope="col">Username</th>
                    <th scope="col">E-mel Pentadbir/Pegawai</th>
                    <th scope="col">Stesen Agensi RISDA</th>
                    <th scope="col">Jumlah Imbasan</th>
                    <th scope="col">Status Akaun</th>
                    <th scope="col">Tindakan Kawalan</th>
                </tr>
            </thead>
            <tbody id="users-table-body">
                <tr>
                    <td colspan="7" class="table-loading-row">
                        <span class="spinner"></span>
                        Memuatkan rekod pendaftaran pengguna...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    
</div>

<!-- Modal Tambah Pengguna Baharu (Glassmorphism Modal Overlay) -->
<div class="modal-overlay" id="add-user-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); backdrop-filter: blur(8px); z-index: 1000; align-items: center; justify-content: center;">
    <div class="modal-container glass-card" style="max-width: 500px; width: 90%; padding: 2.5rem; border-radius: var(--radius-lg); border: 1px solid rgba(255, 255, 255, 0.08); box-shadow: 0 20px 50px rgba(0,0,0,0.4); position: relative; background: rgba(13, 27, 21, 0.92); backdrop-filter: blur(20px);">
        
        <!-- Butang Tutup -->
        <button class="modal-close-btn" id="btn-close-modal" aria-label="Tutup modal" style="position: absolute; top: 1.25rem; right: 1.25rem; background: none; border: none; color: var(--color-text-muted); cursor: pointer; transition: var(--transition-smooth);">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>

        <h3 class="modal-title" style="margin-top: 0; margin-bottom: 0.5rem; font-family: var(--font-heading); color: var(--color-text-primary); font-size: 1.5rem; font-weight: 700;">Daftar Pengguna Baharu</h3>
        <p class="modal-desc" style="margin-top: 0; margin-bottom: 2rem; color: var(--color-text-muted); font-size: 0.9rem;">Cipta akaun pegawai lapangan atau pentadbir RISDA secara selamat.</p>

        <!-- Borang Tambah Pengguna -->
        <form id="add-user-form" style="display: flex; flex-direction: column; gap: 1.25rem;">
            
            <div class="form-group" style="display: flex; flex-direction: column; gap: 0.5rem;">
                <label for="reg-fullname" style="color: var(--color-text-secondary); font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Nama Penuh</label>
                <input type="text" id="reg-fullname" name="fullname" required placeholder="Contoh: Ahmad bin Ismail" style="width: 100%; padding: 0.8rem 1rem; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.1); border-radius: var(--radius-md); color: var(--color-text-primary); transition: var(--transition-smooth);">
            </div>

            <div class="form-group" style="display: flex; flex-direction: column; gap: 0.5rem;">
                <label for="reg-username" style="color: var(--color-text-secondary); font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Nama Pengguna (Username)</label>
                <input type="text" id="reg-username" name="username" required placeholder="Contoh: ahmad_risda" style="width: 100%; padding: 0.8rem 1rem; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.1); border-radius: var(--radius-md); color: var(--color-text-primary); transition: var(--transition-smooth);">
            </div>

            <div class="form-group" style="display: flex; flex-direction: column; gap: 0.5rem;">
                <label for="reg-email" style="color: var(--color-text-secondary); font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Alamat E-mel</label>
                <input type="email" id="reg-email" name="email" required placeholder="Contoh: ahmad@risda.gov.my" style="width: 100%; padding: 0.8rem 1rem; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.1); border-radius: var(--radius-md); color: var(--color-text-primary); transition: var(--transition-smooth);">
            </div>

            <div class="form-group" style="display: flex; flex-direction: column; gap: 0.5rem;">
                <label for="reg-password" style="color: var(--color-text-secondary); font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Kata Laluan Awalan</label>
                <input type="password" id="reg-password" name="password" required placeholder="Minimum 8 aksara" style="width: 100%; padding: 0.8rem 1rem; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.1); border-radius: var(--radius-md); color: var(--color-text-primary); transition: var(--transition-smooth);">
            </div>

            <div class="form-group" style="display: flex; flex-direction: column; gap: 0.5rem;">
                <label for="reg-agency" style="color: var(--color-text-secondary); font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Stesen Agensi RISDA</label>
                <input type="text" id="reg-agency" name="agency" required placeholder="Contoh: RISDA Ibu Pejabat" style="width: 100%; padding: 0.8rem 1rem; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.1); border-radius: var(--radius-md); color: var(--color-text-primary); transition: var(--transition-smooth);">
            </div>

            <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group" style="display: flex; flex-direction: column; gap: 0.5rem;">
                    <label for="reg-role" style="color: var(--color-text-secondary); font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Peranan (Role)</label>
                    <select id="reg-role" name="role" style="width: 100%; padding: 0.8rem 1rem; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.1); border-radius: var(--radius-md); color: var(--color-text-primary); transition: var(--transition-smooth); cursor: pointer;">
                        <option value="user" style="background: #0d1b15; color: #fff;">User (Pegawai)</option>
                        <option value="admin" style="background: #0d1b15; color: #fff;">Admin (Pentadbir)</option>
                    </select>
                </div>

                <div class="form-group" style="display: flex; flex-direction: column; gap: 0.5rem;">
                    <label for="reg-status" style="color: var(--color-text-secondary); font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Status Awal</label>
                    <select id="reg-status" name="status" style="width: 100%; padding: 0.8rem 1rem; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.1); border-radius: var(--radius-md); color: var(--color-text-primary); transition: var(--transition-smooth); cursor: pointer;">
                        <option value="active" style="background: #0d1b15; color: #fff;">Aktif</option>
                        <option value="inactive" style="background: #0d1b15; color: #fff;">Nyahaktif</option>
                    </select>
                </div>
            </div>

            <!-- Mesej Ralat Borang -->
            <div id="add-user-error" style="color: #ef4444; font-size: 0.85rem; display: none;"></div>

            <button type="submit" class="btn btn-primary" id="btn-submit-add-user" style="border: none; padding: 0.85rem; font-weight: 600; border-radius: var(--radius-md); margin-top: 0.5rem; display: flex; align-items: center; justify-content: center; gap: 0.5rem; cursor: pointer;">
                Daftar Pengguna
            </button>
        </form>

    </div>
</div>

<?php
$this->view('layouts/footer', $data);
?>
