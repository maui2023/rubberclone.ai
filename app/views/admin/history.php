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
        
        <div class="table-actions-group flex-wrap gap-2" style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
            <!-- Filter Klon -->
            <div class="select-wrapper">
                <select id="filter-clone-select" class="filter-select" aria-label="Tapis mengikut klon">
                    <option value="">Semua Klon</option>
                    <option value="PB 350">PB 350</option>
                    <option value="PB 260">PB 260</option>
                    <option value="RRIM 2002">RRIM 2002</option>
                    <option value="RRIM 3001">RRIM 3001</option>
                    <option value="RRIM 600">RRIM 600</option>
                </select>
            </div>

            <!-- Filter Keyakinan AI -->
            <div class="select-wrapper">
                <select id="filter-confidence-select" class="filter-select" aria-label="Tapis mengikut keyakinan AI">
                    <option value="">Semua Keyakinan</option>
                    <option value="high">Sangat Tinggi (&ge; 90%)</option>
                    <option value="medium">Sederhana (75% - 89%)</option>
                    <option value="low">Rendah (&lt; 75%)</option>
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

    <!-- Pagination Footer -->
    <div class="table-pagination-footer" id="history-pagination">
        <div class="pagination-info" id="history-pagination-info">
            Menunjukkan 0 - 0 daripada 0 rekod
        </div>
        <div class="pagination-controls">
            <label for="history-page-size" class="pagination-size-label">Papar:</label>
            <select id="history-page-size" class="filter-select pagination-size-select">
                <option value="10" selected>10</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>
            <div class="pagination-buttons">
                <button class="btn-pagination" id="history-prev-page" title="Halaman Sebelumnya" disabled>&laquo; Sebelah</button>
                <span class="pagination-page-indicator" id="history-page-indicator">Halaman 1 / 1</span>
                <button class="btn-pagination" id="history-next-page" title="Halaman Seterusnya" disabled>Seterusnya &raquo;</button>
            </div>
        </div>
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
