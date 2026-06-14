<?php
// app/views/admin/mobile_block.php
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Akses Dihalang - Rubber Clone AI'; ?></title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Outfit:wght@600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --color-bg-dark: #070e0b;
            --color-bg-forest: #0d1b15;
            --color-bg-forest-light: #162e24;
            --color-emerald: #10b981;
            --color-mint-light: #ecfdf5;
            --color-gold-latex: #f59e0b;
            --glass-bg: rgba(22, 46, 36, 0.45);
            --glass-border: rgba(255, 255, 255, 0.06);
            --radius-lg: 24px;
            --radius-md: 16px;
        }

        body {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
            background: radial-gradient(circle at 50% 50%, var(--color-bg-forest-light) 0%, var(--color-bg-dark) 100%);
            color: var(--color-mint-light);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        .block-container {
            max-width: 450px;
            width: 100%;
            text-align: center;
        }

        .block-card {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: var(--radius-lg);
            padding: 3rem 2rem;
            box-shadow: 0 20px 50px rgba(0,0,0,0.5);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1.5rem;
        }

        .icon-wrapper {
            width: 90px;
            height: 90px;
            background: rgba(245, 158, 11, 0.1);
            border: 2px solid var(--color-gold-latex);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--color-gold-latex);
            box-shadow: 0 0 25px rgba(245, 158, 11, 0.2);
            animation: pulse 2s infinite;
        }

        .brand-title {
            font-family: 'Outfit', sans-serif;
            font-size: 1.5rem;
            margin: 0;
            background: linear-gradient(135deg, var(--color-mint-light) 30%, var(--color-emerald) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .warning-title {
            font-family: 'Outfit', sans-serif;
            font-size: 1.25rem;
            color: var(--color-gold-latex);
            margin: 0;
            font-weight: 700;
        }

        .warning-desc {
            font-size: 0.95rem;
            color: rgba(236, 253, 245, 0.7);
            line-height: 1.6;
            margin: 0;
        }

        .btn-home {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.85rem 1.75rem;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-md);
            color: var(--color-mint-light);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            width: 100%;
            box-sizing: border-box;
        }

        .btn-home:hover {
            background: rgba(16, 185, 129, 0.1);
            border-color: rgba(16, 185, 129, 0.3);
            color: var(--color-emerald);
        }

        @keyframes pulse {
            0% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.4);
            }
            70% {
                transform: scale(1);
                box-shadow: 0 0 0 15px rgba(245, 158, 11, 0);
            }
            100% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(245, 158, 11, 0);
            }
        }
    </style>
</head>
<body>

    <div class="block-container">
        <div class="block-card">
            <div class="icon-wrapper">
                <!-- Monitor SVG -->
                <svg width="45" height="45" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                    <line x1="8" y1="21" x2="16" y2="21"></line>
                    <line x1="12" y1="17" x2="12" y2="21"></line>
                </svg>
            </div>
            
            <h1 class="brand-title">Rubber Clone AI</h1>
            
            <h2 class="warning-title">Kebenaran Pentadbir Diperlukan Melalui Desktop</h2>
            
            <p class="warning-desc">
                Demi keselamatan keselamatan maklumat dan kesesuaian reka bentuk papan pemuka (peta geografi, graf analisis, dan jadual audit), Portal Pentadbir **tidak dibenarkan** diakses melalui peranti mudah alih (mobile).
            </p>
            
            <p class="warning-desc" style="font-weight: 500;">
                Sila gunakan komputer desktop atau komputer riba (laptop) untuk melawat halaman pentadbiran ini.
            </p>
            
            <?php 
            $isIndexPhp = (strpos($_SERVER['REQUEST_URI'], 'index.php') !== false);
            $homeUrl = $isIndexPhp ? '/index.php?url=' : '/';
            ?>
            <a href="<?php echo $homeUrl; ?>" class="btn-home">
                &larr; Kembali ke Laman Utama
            </a>
        </div>
    </div>

</body>
</html>
