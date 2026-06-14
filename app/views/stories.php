<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Laporan Lapangan & Kisah Kejayaan - RISDA BESUT. Cerita dan rujukan agronomi tapak semaian getah.">
    <title>Laporan Lapangan & Kisah Kejayaan - RISDA BESUT</title>
    <link rel="stylesheet" href="/styles.css?v=<?php echo @filemtime(__DIR__ . '/../../public/styles.css'); ?>">
    <style>
        :root {
            --color-bg-forest-light: #162E24;
            --color-bg-card-glass: rgba(22, 46, 36, 0.4);
        }

        .stories-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 8rem 2rem 6rem;
        }

        .page-header {
            text-align: center;
            margin-bottom: 4rem;
        }

        .back-btn-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--color-emerald);
            font-weight: 600;
            text-decoration: none;
            margin-bottom: 1.5rem;
            transition: var(--transition-smooth);
        }

        .back-btn-link:hover {
            color: var(--color-emerald-hover);
            transform: translateX(-4px);
        }

        .stories-list {
            display: flex;
            flex-direction: column;
            gap: 2.5rem;
        }

        .story-card {
            background: var(--color-bg-card-glass);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: var(--radius-lg);
            padding: 2.5rem;
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .story-card:hover {
            border-color: rgba(16, 185, 129, 0.2);
            box-shadow: 0 15px 40px rgba(16, 185, 129, 0.05);
        }

        .story-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .story-author-badge {
            font-size: 0.75rem;
            padding: 0.2rem 0.75rem;
            border-radius: 50px;
            background: rgba(16, 185, 129, 0.08);
            border: 1px solid rgba(16, 185, 129, 0.2);
            color: var(--color-emerald);
            font-weight: 500;
        }

        .story-date {
            font-size: 0.8rem;
            color: var(--color-text-muted);
        }

        .story-title {
            font-family: var(--font-heading);
            font-size: 1.5rem;
            line-height: 1.3;
            color: var(--color-text-primary);
            margin: 0;
            font-weight: 700;
        }

        .story-cover-image {
            width: 100%;
            height: 320px;
            object-fit: cover;
            border-radius: var(--radius-md);
            border: 1px solid rgba(255, 255, 255, 0.04);
        }

        /* Excerpt & Expand/Collapse */
        .story-content {
            max-height: 90px;
            overflow: hidden;
            position: relative;
            transition: max-height 0.5s cubic-bezier(0.16, 1, 0.3, 1);
            color: var(--color-text-secondary);
            font-size: 0.95rem;
            line-height: 1.7;
        }

        .story-content::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 45px;
            background: linear-gradient(180deg, transparent, #0d1b15); /* Matches Forest Dark BG */
            pointer-events: none;
            transition: opacity 0.3s ease;
        }

        .story-card.expanded .story-content {
            max-height: 2000px;
        }

        .story-card.expanded .story-content::after {
            opacity: 0;
        }

        .btn-toggle-story {
            background: none;
            border: none;
            color: var(--color-emerald);
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0;
            width: fit-content;
            transition: var(--transition-smooth);
            margin-top: 0.5rem;
        }

        .btn-toggle-story:hover {
            color: var(--color-emerald-hover);
        }

        .btn-toggle-story svg {
            transition: transform 0.4s ease;
        }

        .story-card.expanded .btn-toggle-story svg {
            transform: rotate(180deg);
        }
    </style>
</head>
<body style="background-color: var(--color-bg-dark); background-image: radial-gradient(circle at 50% 0%, var(--color-bg-forest) 0%, var(--color-bg-dark) 100%);">

    <!-- Header & Navigation -->
    <header class="header-nav" id="main-header">
        <div class="nav-container">
            <a href="<?php echo route(''); ?>" class="logo" id="brand-logo" aria-label="Laman Utama Rubber Clone AI" style="display: flex; align-items: center; gap: 10px;">
                <img src="/assets/images/risda_logo.jpg" alt="RISDA Logo" style="width: 38px; height: 38px; border-radius: 50%; border: 1.5px solid var(--color-gold-latex);">
                <span class="logo-brand">RISDA <span style="color: var(--color-gold-latex);">BESUT</span></span>
            </a>
            <nav class="nav-links" id="navigation-menu">
                <a href="<?php echo route(''); ?>" class="nav-link">Laman Utama</a>
            </nav>
            <button class="burger-menu" id="burger-menu-btn" aria-label="Buka Menu Navigasi" aria-expanded="false">
                <span class="burger-bar"></span>
                <span class="burger-bar"></span>
                <span class="burger-bar"></span>
            </button>
        </div>
    </header>

    <main class="stories-container">
        
        <div class="page-header">
            <a href="<?php echo route(''); ?>" class="back-btn-link">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
                Kembali ke Utama
            </a>
            <span class="section-subtitle">Laporan Lapangan</span>
            <h1 class="story-title" style="font-size: 2.25rem;">Kisah Kejayaan & Cerita Blog</h1>
            <p class="section-desc" style="margin-top: 0.5rem;">Perkongsian pengalaman dari stesen tapak semaian dan rujukan penyelidikan klon getah RISDA.</p>
        </div>

        <div class="stories-list">
            <?php if (empty($blogs)): ?>
                <div class="story-card" style="text-align: center; padding: 4rem 2rem;">
                    <p style="color: var(--color-text-muted); font-style: italic; margin: 0;">Tiada sebarang artikel kisah kejayaan buat masa ini.</p>
                </div>
            <?php else: ?>
                <?php foreach ($blogs as $post): ?>
                    <article class="story-card" id="story-<?php echo $post['id']; ?>">
                        <?php if ($post['image_url']): ?>
                            <img src="/<?php echo htmlspecialchars($post['image_url']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>" class="story-cover-image">
                        <?php endif; ?>
                        
                        <div class="story-meta">
                            <span class="story-author-badge">
                                <?php echo htmlspecialchars($post['author']); ?>
                            </span>
                            <span class="story-date">
                                <?php echo date('d M Y', strtotime($post['created_at'])); ?>
                            </span>
                        </div>

                        <h2 class="story-title">
                            <?php echo htmlspecialchars($post['title']); ?>
                        </h2>

                        <div class="story-content">
                            <?php echo nl2br(htmlspecialchars($post['content'])); ?>
                        </div>

                        <button class="btn-toggle-story" onclick="toggleStory(<?php echo $post['id']; ?>)">
                            <span class="btn-text">Baca Sepenuhnya</span>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="6 9 12 15 18 9"></polyline>
                            </svg>
                        </button>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </main>

    <!-- Footer -->
    <footer class="footer" id="main-footer" style="margin-top: 4rem;">
        <div class="footer-container">
            <div class="footer-bottom" style="border-top: 1px solid rgba(255,255,255,0.03); padding-top: 2rem; text-align: center;">
                <p>&copy; 2026 Rubber Clone AI. RISDA Malaysia. Hak Cipta Terpelihara.</p>
            </div>
        </div>
    </footer>

    <script>
        function toggleStory(id) {
            const card = document.getElementById('story-' + id);
            const btnText = card.querySelector('.btn-text');
            
            if (card.classList.contains('expanded')) {
                card.classList.remove('expanded');
                btnText.textContent = 'Baca Sepenuhnya';
                card.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            } else {
                card.classList.add('expanded');
                btnText.textContent = 'Tutup Cerita';
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
            const burgerBtn = document.getElementById("burger-menu-btn");
            const navMenu = document.getElementById("navigation-menu");

            if (burgerBtn && navMenu) {
                burgerBtn.addEventListener("click", function() {
                    const isActive = navMenu.classList.toggle("active");
                    burgerBtn.classList.toggle("active");
                    burgerBtn.setAttribute("aria-expanded", isActive);
                });

                const navLinks = navMenu.querySelectorAll(".nav-link");
                navLinks.forEach(link => {
                    link.addEventListener("click", function() {
                        navMenu.classList.remove("active");
                        burgerBtn.classList.remove("active");
                        burgerBtn.setAttribute("aria-expanded", "false");
                    });
                });
            }
        });
    </script>
</body>
</html>
