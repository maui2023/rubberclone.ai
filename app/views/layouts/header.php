<?php
// app/views/layouts/header.php
$active_tab = $active_tab ?? 'dashboard';
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Portal Pentadbir - RISDA BESUT'; ?></title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- LeafletJS Map CSS (Open-source interactive map) -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body>

    <!-- Container Layout Utama -->
    <div class="admin-wrapper">
        
        <!-- Sidebar Navigation -->
        <aside class="sidebar">
            <div class="sidebar-header" style="display: flex; align-items: center; gap: 10px; padding: 1.5rem 1rem;">
                <img src="/public/assets/images/risda_logo.jpg" alt="RISDA Logo" style="width: 30px; height: 30px; border-radius: 50%; border: 1.2px solid var(--color-gold-latex);">
                <span class="sidebar-logo-text" style="font-family: 'Outfit', sans-serif; font-size: 1.1rem;">RISDA <span style="color: var(--color-gold-latex);">BESUT</span></span>
            </div>
            
            <nav class="sidebar-menu" aria-label="Menu Pentadbir">
                <a href="<?php echo route('pentadbir'); ?>" class="menu-item <?php echo $active_tab === 'dashboard' ? 'active' : ''; ?>">
                    <!-- Grid Icon SVG -->
                    <svg class="menu-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="7" height="7"></rect>
                        <rect x="14" y="3" width="7" height="7"></rect>
                        <rect x="14" y="14" width="7" height="7"></rect>
                        <rect x="3" y="14" width="7" height="7"></rect>
                    </svg>
                    Papan Pemuka
                </a>
                
                <a href="<?php echo route('admin/users'); ?>" class="menu-item <?php echo $active_tab === 'users' ? 'active' : ''; ?>">
                    <!-- Users Icon SVG -->
                    <svg class="menu-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                    Direktori Pengguna
                </a>
                
                <a href="<?php echo route('admin/history'); ?>" class="menu-item <?php echo $active_tab === 'history' ? 'active' : ''; ?>">
                    <!-- History Icon SVG -->
                    <svg class="menu-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                    Rekod Imbasan
                </a>

                <a href="<?php echo route('admin/cms'); ?>" class="menu-item <?php echo $active_tab === 'cms' ? 'active' : ''; ?>">
                    <!-- Settings/Edit Icon SVG -->
                    <svg class="menu-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                        <path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                    </svg>
                    Urus Portal (CMS)
                </a>
            </nav>
            
            <div class="sidebar-footer">
                <div class="admin-profile">
                    <div class="admin-avatar">
                        <?php echo strtoupper(substr($_SESSION['admin_fullname'] ?? 'A', 0, 1)); ?>
                    </div>
                    <div class="admin-info">
                        <span class="admin-name"><?php echo htmlspecialchars($_SESSION['admin_fullname'] ?? 'Admin RISDA'); ?></span>
                        <span class="admin-role">Pentadbir RISDA</span>
                    </div>
                </div>
                <a href="<?php echo route('admin/logoutWeb'); ?>" class="btn-logout" title="Log Keluar">
                    <!-- Log Out Icon SVG -->
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                        <polyline points="16 17 21 12 16 7"></polyline>
                        <line x1="21" y1="12" x2="9" y2="12"></line>
                    </svg>
                    Log Keluar
                </a>
            </div>
        </aside>
        
        <!-- Main Content Area -->
        <main class="main-content">
            
            <!-- Topbar -->
            <header class="topbar">
                <div class="topbar-title">
                    <h1><?php echo $title ?? 'Papan Pemuka'; ?></h1>
                </div>
                <div class="topbar-actions">
                    <span class="live-status-badge">
                        <span class="badge-dot"></span>
                        Pelayan Aktif: rubberclone-ai.pats.my
                    </span>
                </div>
            </header>
            
            <!-- Dynamic Page Content Starts Here -->
            <div class="page-content-wrapper">
