<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Rubber Clone AI - Inisiatif berasaskan AI menggunakan Google Gemini untuk membantu pekebun kecil RISDA dan pegawai lapangan mengenalpasti klon getah melalui imbasan daun.">
    <meta name="keywords" content="RISDA, Rubber Clone AI, Getah, Klon Getah, Gemini AI, Pertanian, Malaysia, APK, Pekebun Kecil">
    
    <!-- Open Graph Meta Tags for Social Previews -->
    <meta property="og:title" content="Rubber Clone AI - Pengecaman Klon Getah Pintar">
    <meta property="og:description" content="Pengecaman klon getah RISDA dengan ketepatan tinggi menggunakan kuasa kecerdasan buatan (Gemini AI). Muat turun aplikasi Android sekarang.">
    <meta property="og:image" content="assets/images/rubber_clone_mockup.png">
    <meta property="og:url" content="https://rubberclone-ai.pats.my">
    <meta property="og:type" content="website">

    <title>Rubber Clone AI - Pengecaman Klon Getah RISDA Pintar</title>
    
    <!-- External Stylesheet -->
    <link rel="stylesheet" href="/styles.css">
</head>
<body>

    <!-- Header & Navigation -->
    <header class="header-nav" id="main-header">
        <div class="nav-container">
            <a href="#" class="logo" id="brand-logo" aria-label="Laman Utama Rubber Clone AI">
                <!-- Inline SVG Leaf Icon -->
                <svg class="logo-leaf" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 3.5 1 9.8a7 7 0 0 1-9 8.2z"></path>
                    <path d="M9 22v-4h4"></path>
                </svg>
                <span class="logo-brand">RubberClone<span style="color: var(--color-gold-latex);">AI</span></span>
                <span class="logo-tag">RISDA</span>
            </a>
            <nav class="nav-links" id="navigation-menu" aria-label="Menu Navigasi Utama">
                <a href="#features" class="nav-link" id="link-features">Ciri-Ciri</a>
                <a href="#how-it-works" class="nav-link" id="link-how-it-works">Cara Penggunaan</a>
                <a href="<?php echo route('stories'); ?>" class="nav-link" id="link-stories">Kisah & Kejayaan</a>
                <a href="#stats" class="nav-link" id="link-stats">Statistik</a>
                <!-- NOTA: Butang Login Admin dikeluarkan dari menu atas arahan keselamatan pentadbiran -->
            </nav>
        </div>
    </header>

    <main id="content-wrap">
        
        <!-- Hero Section -->
        <section class="hero" id="hero-section">
            <div class="hero-grid">
                <div class="hero-content">
                    <div class="hero-badge" id="hero-status-badge">
                        <span class="hero-badge-dot"></span>
                        Disokong Google Gemini AI
                    </div>
                    <h1 class="hero-title" id="main-heading-title">
                        <?php echo htmlspecialchars($settings['hero_title'] ?? 'Pengecaman Klon Getah RISDA Pintar Menggunakan Kuasa AI'); ?>
                    </h1>
                    <p class="hero-desc" id="hero-description-text">
                        <?php echo htmlspecialchars($settings['hero_desc'] ?? 'Inisiatif pintar digital untuk pekebun kecil RISDA dan pegawai lapangan. Kenalpasti klon pokok getah dengan tepat dalam beberapa saat melalui imbasan morfologi daun secara masa nyata.'); ?>
                    </p>
                    <div class="hero-ctas" id="hero-actions-container">
                        <a href="https://rubberclone-ai.pats.my/assets/app/rubberclone-ai.apk" class="btn btn-primary" id="btn-download-apk" download="rubberclone-ai.apk">
                            <!-- Download SVG -->
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                <polyline points="7 10 12 15 17 10"></polyline>
                                <line x1="12" y1="15" x2="12" y2="3"></line>
                            </svg>
                            Muat Turun APK Android
                        </a>
                    </div>
                </div>
                <div class="hero-mockup-wrapper" id="hero-visual-mockup">
                    <div class="mockup-glow"></div>
                    <div class="phone-mockup-container">
                        <img src="/assets/images/rubber_clone_mockup.png" alt="Paparan aplikasi Rubber Clone AI memaparkan hasil pengecaman klon daun getah RRIM 3001 dengan kadar keyakinan 98% berserta cadangan agronomi" id="mockup-image" loading="eager">
                    </div>
                </div>
            </div>
        </section>

        <!-- Statistics Section -->
        <section class="stats-section" id="stats">
            <div class="stats-grid" id="stats-counter-grid">
                <div class="stat-item" id="stat-scans">
                    <span class="stat-number"><?php echo htmlspecialchars($settings['stat_scans'] ?? '1,800+'); ?></span>
                    <span class="stat-label">Imbasan Selesai</span>
                </div>
                <div class="stat-item" id="stat-clones">
                    <span class="stat-number"><?php echo htmlspecialchars($settings['stat_clones'] ?? '150+'); ?></span>
                    <span class="stat-label">Klon Getah Disokong</span>
                </div>
                <div class="stat-item" id="stat-officers">
                    <span class="stat-number"><?php echo htmlspecialchars($settings['stat_officers'] ?? '500+'); ?></span>
                    <span class="stat-label">Pegawai Lapangan RISDA</span>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="features-section" id="features">
            <div class="section-header">
                <span class="section-subtitle">Kelebihan Sistem</span>
                <h2>Teknologi Canggih Di Hujung Jari Anda</h2>
                <p class="section-desc">Dibina khusus untuk menangani cabaran verifikasi klon getah di tapak semaian dan lapangan dengan pantas, telus dan saintifik.</p>
            </div>
            
            <div class="features-grid" id="features-cards-layout">
                <!-- Feature 1 -->
                <article class="feature-card" id="feature-card-gemini">
                    <div class="feature-icon-wrapper" aria-hidden="true">
                        <!-- AI Star SVG -->
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                        </svg>
                    </div>
                    <h3 class="feature-title">AI Multimodal Gemini</h3>
                    <p class="feature-desc">Menganalisis bentuk daun, struktur urat, susunan lobus, dan tangkai daun menggunakan model kecerdasan buatan Gemini RISDA untuk memberikan hasil yang sangat tepat.</p>
                </article>

                <!-- Feature 2 -->
                <article class="feature-card" id="feature-card-geotagging">
                    <div class="feature-icon-wrapper" aria-hidden="true">
                        <!-- Map Pin SVG -->
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                            <circle cx="12" cy="10" r="3"></circle>
                        </svg>
                    </div>
                    <h3 class="feature-title">Geo-Tagging Pintar</h3>
                    <p class="feature-desc">Setiap rekod imbasan secara automatik ditandakan dengan koordinat GPS bagi membina peta taburan klon getah secara interaktif untuk rujukan peringkat agensi.</p>
                </article>

                <!-- Feature 3 -->
                <article class="feature-card" id="feature-card-agronomy">
                    <div class="feature-icon-wrapper" aria-hidden="true">
                        <!-- Database/Tree SVG -->
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="2" width="20" height="8" rx="2" ry="2"></rect>
                            <rect x="2" y="14" width="20" height="8" rx="2" ry="2"></rect>
                            <line x1="6" y1="6" x2="6.01" y2="6"></line>
                            <line x1="6" y1="18" x2="6.01" y2="18"></line>
                        </svg>
                    </div>
                    <h3 class="feature-title">Garis Panduan Agronomi</h3>
                    <p class="feature-desc">Dapatkan cadangan kesesuaian jenis tanah, taburan hujan, dan ketinggian tapak semaian yang optimum untuk klon getah yang dikenal pasti secara langsung.</p>
                </article>
            </div>
        </section>

        <!-- How It Works Section -->
        <section class="how-it-works" id="how-it-works">
            <div class="section-header">
                <span class="section-subtitle">Langkah Mudah</span>
                <h2>Bagaimanakah Ia Berfungsi?</h2>
                <p class="section-desc">Tiga langkah mudah untuk mengenal pasti klon getah secara saintifik tanpa perlu menunggu rujukan manual yang memakan masa.</p>
            </div>
            
            <div class="steps-container" id="usage-steps-flow">
                <!-- Step 1 -->
                <div class="step-item" id="step-one">
                    <div class="step-badge">1</div>
                    <h3 class="step-title">Tangkap Gambar</h3>
                    <p class="step-desc">Tangkap foto daun pokok getah secara menegak dan jelas menggunakan kamera aplikasi mudah alih.</p>
                </div>

                <!-- Step 2 -->
                <div class="step-item" id="step-two">
                    <div class="step-badge">2</div>
                    <h3 class="step-title">Analisis AI</h3>
                    <p class="step-desc">Gemini AI memproses ciri morfologi daun secara automatik berpandukan standard pangkalan klon RISDA.</p>
                </div>

                <!-- Step 3 -->
                <div class="step-item" id="step-three">
                    <div class="step-badge">3</div>
                    <h3 class="step-title">Dapatkan Laporan</h3>
                    <p class="step-desc">Perolehi nama klon getah, peratusan keyakinan, dan panduan kesesuaian tanah serta merta.</p>
                </div>
            </div>
        </section>

        <!-- Kisah Kejayaan Promo Banner Section -->
        <section class="features-section" id="stories-promo" style="background: linear-gradient(180deg, transparent, rgba(22, 46, 36, 0.1), transparent); padding: 5rem 2rem; text-align: center;">
            <div class="section-header" style="max-width: 650px; margin: 0 auto;">
                <span class="section-subtitle">Laporan Lapangan</span>
                <h2>Kisah Kejayaan & Cerita Blog</h2>
                <p class="section-desc" style="margin-bottom: 2rem;">Ikuti maklum balas tapak semaian, kisah kejayaan pekebun kecil RISDA, serta rujukan kajian agronomi klon getah terbaharu di seluruh Malaysia.</p>
                <a href="<?php echo route('stories'); ?>" class="btn btn-primary" id="btn-view-all-stories" style="border: none; padding: 0.8rem 2rem;">
                    Lihat Kisah & Laporan Lapangan &rarr;
                </a>
            </div>
        </section>

    </main>

    <!-- Footer -->
    <footer class="footer" id="main-footer">
        <div class="footer-container">
            <div class="footer-top">
                <a href="#" class="logo" aria-label="Rubber Clone AI Footer Logo">
                    <svg class="logo-leaf" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 3.5 1 9.8a7 7 0 0 1-9 8.2z"></path>
                    </svg>
                    <span class="logo-brand">RubberClone<span style="color: var(--color-gold-latex);">AI</span></span>
                </a>
                <div class="footer-links" id="footer-menu-links">
                    <a href="https://www.risda.gov.my" class="footer-link" target="_blank" rel="noopener noreferrer" id="link-risda-rasmi">Portal Rasmi RISDA</a>
                    <a href="#" class="footer-link" id="link-terms">Terma Syarat</a>
                    <a href="#" class="footer-link" id="link-privacy">Dasar Privasi</a>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2026 Rubber Clone AI. RISDA Malaysia. Hak Cipta Terpelihara.</p>
                <p>Ujian Sistem Aktif: <a href="https://rubberclone-ai.pats.my" style="color: var(--color-emerald); text-decoration: underline;">rubberclone-ai.pats.my</a></p>
            </div>
        </div>
    </footer>

</body>
</html>
