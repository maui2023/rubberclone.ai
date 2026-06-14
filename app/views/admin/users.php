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
        <div class="table-actions-group">
            <!-- Input Carian -->
            <div class="search-bar-wrapper">
                <svg class="search-bar-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                <input type="text" id="search-users-input" placeholder="Cari nama, e-mel, stesen..." aria-label="Cari pengguna">
            </div>
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

<?php
$this->view('layouts/footer', $data);
?>
