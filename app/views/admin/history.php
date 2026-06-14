<?php
// app/views/admin/history.php
$this->view('layouts/header', $data);
?>

<div class="dashboard-panel table-panel" id="history-audit-panel">
    
    <div class="panel-header table-header">
        <div class="header-text-group">
            <h2 class="panel-title">Rekod Sejarah Imbasan Daun Getah</h2>
            <span class="panel-subtitle">Audit menyeluruh semua imej daun getah yang diproses melalui Gemini AI</span>
        </div>
        
        <div class="table-actions-group flex-wrap gap-2">
            <!-- Filter Klon -->
            <div class="select-wrapper">
                <select id="filter-clone-select" class="filter-select" aria-label="Tapis mengikut klon">
                    <option value="">Semua Klon</option>
                    <option value="RRIM 3001">RRIM 3001</option>
                    <option value="RRIM 600">RRIM 600</option>
                    <option value="PB 260">PB 260</option>
                    <option value="GT 1">GT 1</option>
                </select>
            </div>
            
            <!-- Input Carian -->
            <div class="search-bar-wrapper">
                <svg class="search-bar-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                <input type="text" id="search-history-input" placeholder="Cari stesen, nama pegawai..." aria-label="Cari rekod imbasan">
            </div>
        </div>
    </div>
    
    <div class="table-responsive-container">
        <table class="admin-table" id="history-data-table">
            <thead>
                <tr>
                    <th scope="col">Gambar Daun</th>
                    <th scope="col">Pegawai Lapangan</th>
                    <th scope="col">Nama Klon</th>
                    <th scope="col">Keyakinan AI</th>
                    <th scope="col">Tarikh & Masa</th>
                    <th scope="col">Stesen (GPS)</th>
                    <th scope="col">Maklumat Agronomi & Catatan</th>
                    <th scope="col">Tindakan</th>
                </tr>
            </thead>
            <tbody id="history-table-body">
                <tr>
                    <td colspan="8" class="table-loading-row">
                        <span class="spinner"></span>
                        Memuatkan rekod sejarah imbasan daun getah...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    
</div>

<!-- Modal Gambar Daun / Info Terperinci -->
<div id="info-modal" class="modal-overlay" style="display: none;" role="dialog" aria-modal="true" aria-labelledby="modal-title">
    <div class="modal-card">
        <button class="modal-close-btn" id="close-modal-btn" aria-label="Tutup modal">&times;</button>
        <div class="modal-body-content">
            <h2 id="modal-title" class="modal-header-title">Perincian Rekod Imbasan</h2>
            <div class="modal-grid">
                <div class="modal-visual">
                    <img id="modal-img-preview" src="" alt="Pratonton fail imej daun getah yang diimbas">
                </div>
                <div class="modal-text-details" id="modal-text-details">
                    <!-- Dinamik dimasukkan oleh JS -->
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->view('layouts/footer', $data);
?>
