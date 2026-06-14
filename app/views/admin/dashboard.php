<?php
// app/views/admin/dashboard.php
$this->view('layouts/header', $data);
?>

<!-- Kad Metrik Pintar -->
<div class="metrics-grid">
    
    <div class="metric-card" id="card-total-scans">
        <div class="metric-icon-wrapper scans" aria-hidden="true">
            <!-- Scan Icon -->
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                <circle cx="12" cy="11" r="3"></circle>
            </svg>
        </div>
        <div class="metric-details">
            <span class="metric-label">Jumlah Imbasan</span>
            <h2 class="metric-val" id="metric-total-scans">0</h2>
        </div>
    </div>
    
    <div class="metric-card" id="card-scans-today">
        <div class="metric-icon-wrapper today" aria-hidden="true">
            <!-- Clock Icon -->
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <polyline points="12 6 12 12 16 14"></polyline>
            </svg>
        </div>
        <div class="metric-details">
            <span class="metric-label">Imbasan Hari Ini</span>
            <h2 class="metric-val" id="metric-scans-today">0</h2>
        </div>
    </div>
    
    <div class="metric-card" id="card-active-users">
        <div class="metric-icon-wrapper users-active" aria-hidden="true">
            <!-- User Check Icon -->
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                <circle cx="9" cy="7" r="4"></circle>
                <polyline points="16 11 18 13 22 9"></polyline>
            </svg>
        </div>
        <div class="metric-details">
            <span class="metric-label">Pengguna Aktif</span>
            <h2 class="metric-val" id="metric-active-users">0</h2>
        </div>
    </div>
    
    <div class="metric-card" id="card-total-users">
        <div class="metric-icon-wrapper users-total" aria-hidden="true">
            <!-- Users Group Icon -->
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M17 21v-2a4 4 0 0 0-3-3.87"></path>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                <path d="M21 21v-2a4 4 0 0 0-3-3.87"></path>
                <path d="M9 21v-2a4 4 0 0 0-4-4H3a4 4 0 0 0-4 4v2"></path>
                <circle cx="5" cy="7" r="4"></circle>
                <circle cx="13" cy="7" r="4"></circle>
            </svg>
        </div>
        <div class="metric-details">
            <span class="metric-label">Pegawai Berdaftar</span>
            <h2 class="metric-val" id="metric-total-users">0</h2>
        </div>
    </div>
    
</div>

<!-- Grid Peta & Carta -->
<div class="dashboard-grid">
    
    <!-- Peta Geografi LeafletJS -->
    <div class="dashboard-panel map-panel" id="panel-map-container">
        <div class="panel-header">
            <h2 class="panel-title">Peta Taburan Geografi Imbasan Daun Getah</h2>
            <span class="panel-subtitle">Lokasi imbasan GPS sebenar pegawai lapangan RISDA</span>
        </div>
        <div id="map" class="dashboard-map" aria-label="Peta interaktif taburan klon getah"></div>
    </div>
    
    <!-- Carta Analisis Chart.js -->
    <div class="dashboard-panel chart-panel" id="panel-charts-container">
        
        <div class="chart-section">
            <div class="panel-header">
                <h2 class="panel-title">Statistik Imbasan Klon Getah</h2>
                <span class="panel-subtitle">Perbandingan kekerapan mengikut jenis klon (Top 5)</span>
            </div>
            <div class="chart-canvas-wrapper">
                <canvas id="cloneChart" aria-label="Carta bar perbandingan imbasan klon getah" role="img"></canvas>
            </div>
        </div>
        
        <div class="chart-section" style="margin-top: 3rem;">
            <div class="panel-header">
                <h2 class="panel-title">Imbasan Mengikut Agensi Negeri</h2>
                <span class="panel-subtitle">Taburan pegawai aktif mengikut stesen RISDA</span>
            </div>
            <div class="chart-canvas-wrapper">
                <canvas id="agencyChart" aria-label="Carta pai taburan imbasan mengikut agensi RISDA" role="img"></canvas>
            </div>
        </div>
        
    </div>
    
</div>

<?php
$this->view('layouts/footer', $data);
?>
