<?php
// app/views/admin/login.php
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Log Masuk Pentadbir - Rubber Clone AI'; ?></title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Outfit:wght@600;700;800&display=swap" rel="stylesheet">
    
    <!-- CSS File -->
    <link rel="stylesheet" href="/assets/css/admin.css?v=<?php echo filemtime(__DIR__ . '/../../public/assets/css/admin.css'); ?>">
</head>
<body class="login-body">
    
    <div class="login-container">
        <div class="login-card">
            
            <!-- Logo Header -->
            <header class="login-header" style="text-align: center; margin-bottom: 2rem;">
                <img src="/assets/images/risda_logo.jpg" alt="RISDA Logo" style="width: 80px; height: 80px; border-radius: 50%; border: 2px solid var(--color-gold-latex); margin-bottom: 1rem;">
                <h1 class="login-brand-title" style="font-size: 1.6rem; margin: 0; font-family: 'Outfit', sans-serif;">Sistem Pengecaman Klon Pokok Getah</h1>
                <p class="login-subtitle" style="color: var(--color-gold-latex); font-weight: bold; margin-top: 0.2rem; font-size: 1.1rem; letter-spacing: 1px;">RISDA BESUT</p>
            </header>

            <!-- Paparan Ralat -->
            <?php if (isset($error)): ?>
                <div class="login-error" role="alert">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    <span><?php echo htmlspecialchars($error); ?></span>
                </div>
            <?php endif; ?>

            <?php 
            $isIndexPhp = (strpos($_SERVER['REQUEST_URI'], 'index.php') !== false);
            $loginAction = $isIndexPhp ? '/index.php?url=admin/loginWeb' : '/admin/loginWeb';
            $homeUrl = $isIndexPhp ? '/index.php?url=' : '/';
            ?>
            <!-- Borang Log Masuk -->
            <form action="<?php echo $loginAction; ?>" method="POST" class="login-form">
                <div class="form-group">
                    <label for="email">E-mel Pentadbir</label>
                    <input type="email" id="email" name="email" required placeholder="admin@demo.com" autocomplete="email" aria-required="true">
                </div>
                
                <div class="form-group">
                    <label for="password">Kata Laluan</label>
                    <input type="password" id="password" name="password" required placeholder="••••••••" autocomplete="current-password" aria-required="true">
                </div>
                
                <button type="submit" class="btn btn-primary btn-block" id="btn-submit-login">
                    Log Masuk Pentadbir
                </button>
            </form>

            <footer class="login-footer">
                <p><a href="<?php echo $homeUrl; ?>" class="back-link">&larr; Kembali ke Landing Page</a></p>
            </footer>
            
        </div>
    </div>

</body>
</html>
