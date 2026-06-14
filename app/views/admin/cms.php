<?php
// app/views/admin/cms.php
$this->view('layouts/header', $data);
?>

<div class="cms-page-grid">
    
    <!-- Panel 1: Kemas Kini Teks Portal (CMS) -->
    <div class="dashboard-panel" id="cms-settings-panel">
        <div class="panel-header">
            <h2 class="panel-title">Kandungan Utama Landing Page (CMS)</h2>
            <span class="panel-subtitle">Suntik kandungan teks hero dan statistik halaman utama secara dinamik</span>
        </div>
        
        <form id="cms-settings-form" class="admin-form" style="margin-top: 1.5rem;">
            <div class="form-group">
                <label for="hero_title">Tajuk Hero Halaman Utama (Hero Title)</label>
                <input type="text" id="hero_title" name="hero_title" value="<?php echo htmlspecialchars($settings['hero_title'] ?? ''); ?>" required style="width:100%; padding:0.8rem 1rem; background:rgba(7,14,11,0.6); border:1px solid var(--glass-border); border-radius:var(--radius-md); color:var(--color-text-primary); font-family:var(--font-body); font-size:0.95rem;">
            </div>
            
            <div class="form-group">
                <label for="hero_desc">Keterangan Hero (Hero Description)</label>
                <textarea id="hero_desc" name="hero_desc" rows="4" required style="width:100%; padding:0.8rem 1rem; background:rgba(7,14,11,0.6); border:1px solid var(--glass-border); border-radius:var(--radius-md); color:var(--color-text-primary); font-family:var(--font-body); font-size:0.95rem; resize:vertical;"><?php echo htmlspecialchars($settings['hero_desc'] ?? ''); ?></textarea>
            </div>
            
            <div class="form-row" style="display:grid; grid-template-columns:repeat(3, 1fr); gap:1rem;">
                <div class="form-group">
                    <label for="stat_scans">Metrik: Imbasan Selesai</label>
                    <input type="text" id="stat_scans" name="stat_scans" value="<?php echo htmlspecialchars($settings['stat_scans'] ?? ''); ?>" required style="width:100%; padding:0.8rem 1rem; background:rgba(7,14,11,0.6); border:1px solid var(--glass-border); border-radius:var(--radius-md); color:var(--color-text-primary); font-size:0.95rem;">
                </div>
                <div class="form-group">
                    <label for="stat_clones">Metrik: Klon Disokong</label>
                    <input type="text" id="stat_clones" name="stat_clones" value="<?php echo htmlspecialchars($settings['stat_clones'] ?? ''); ?>" required style="width:100%; padding:0.8rem 1rem; background:rgba(7,14,11,0.6); border:1px solid var(--glass-border); border-radius:var(--radius-md); color:var(--color-text-primary); font-size:0.95rem;">
                </div>
                <div class="form-group">
                    <label for="stat_officers">Metrik: Pegawai Lapangan</label>
                    <input type="text" id="stat_officers" name="stat_officers" value="<?php echo htmlspecialchars($settings['stat_officers'] ?? ''); ?>" required style="width:100%; padding:0.8rem 1rem; background:rgba(7,14,11,0.6); border:1px solid var(--glass-border); border-radius:var(--radius-md); color:var(--color-text-primary); font-size:0.95rem;">
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary" id="btn-save-cms" style="margin-top: 1rem; border:none;">
                Simpan Perubahan Portal
            </button>
        </form>
    </div>

    <!-- Panel 2: Tambah Kisah Baru (Blog Stories Builder) -->
    <div class="dashboard-panel" id="cms-blog-form-panel" style="margin-top: 2rem;">
        <div class="panel-header">
            <h2 class="panel-title">Terbitkan Kisah Baru (Blog Story)</h2>
            <span class="panel-subtitle">Tambah artikel lapangan, berita tapak semaian, atau testimoni pekebun RISDA</span>
        </div>
        
        <form id="blog-create-form" class="admin-form" style="margin-top: 1.5rem;" enctype="multipart/form-data">
            <div class="form-group">
                <label for="blog_title">Tajuk Kisah</label>
                <input type="text" id="blog_title" name="title" placeholder="cth: Kejayaan Klon RRIM 3001 di Tapak Semaian Ipoh" required style="width:100%; padding:0.8rem 1rem; background:rgba(7,14,11,0.6); border:1px solid var(--glass-border); border-radius:var(--radius-md); color:var(--color-text-primary); font-size:0.95rem;">
            </div>
            
            <div class="form-group">
                <label for="blog_author">Penulis / Unit Agensi</label>
                <input type="text" id="blog_author" name="author" placeholder="cth: Unit Agronomi RISDA Perak" required style="width:100%; padding:0.8rem 1rem; background:rgba(7,14,11,0.6); border:1px solid var(--glass-border); border-radius:var(--radius-md); color:var(--color-text-primary); font-size:0.95rem;">
            </div>
            
            <div class="form-group">
                <label for="blog_image">Gambar Hiasan / Cover Photo (Pilihan)</label>
                <input type="file" id="blog_image" name="image" accept="image/*" style="border:none; background:none; padding:0; color:var(--color-text-secondary);">
            </div>

            <div class="form-group">
                <label for="blog_content">Kandungan Kisah</label>
                <textarea id="blog_content" name="content" rows="6" placeholder="Tulis kisah kejayaan atau laporan penuh di sini..." required style="width:100%; padding:0.8rem 1rem; background:rgba(7,14,11,0.6); border:1px solid var(--glass-border); border-radius:var(--radius-md); color:var(--color-text-primary); font-family:var(--font-body); font-size:0.95rem; resize:vertical;"></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary" id="btn-publish-blog" style="border:none;">
                Terbitkan Kisah Baru
            </button>
        </form>
    </div>

    <!-- Panel 3: Senarai Kisah Kejayaan Aktif -->
    <div class="dashboard-panel" id="cms-blogs-list-panel" style="margin-top: 2rem; margin-bottom: 2rem;">
        <div class="panel-header">
            <h2 class="panel-title">Kisah Kejayaan & Laporan Aktif</h2>
            <span class="panel-subtitle">Pengurusan senarai artikel blog yang dipaparkan pada Halaman Utama Awam</span>
        </div>
        
        <div class="table-responsive-container" style="margin-top: 1.5rem;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th scope="col">Foto</th>
                        <th scope="col">Tajuk Artikel</th>
                        <th scope="col">Penulis</th>
                        <th scope="col">Tarikh</th>
                        <th scope="col">Tindakan</th>
                    </tr>
                </thead>
                <tbody id="blog-list-table-body">
                    <?php if (empty($blogs)): ?>
                        <tr id="blog-empty-row">
                            <td colspan="5" style="text-align: center; padding: 2rem;">Tiada kisah kejayaan aktif dalam sistem.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($blogs as $b): ?>
                            <tr id="blog-row-<?php echo $b['id']; ?>">
                                <td>
                                    <?php if ($b['image_url']): ?>
                                        <img src="/<?php echo htmlspecialchars($b['image_url']); ?>" alt="" class="table-leaf-thumbnail" style="width: 45px; height: 45px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="no-image-placeholder" style="width: 45px; height: 45px; font-size: 0.6rem;">Tiada</div>
                                    <?php endif; ?>
                                </td>
                                <td style="font-weight:600; color:var(--color-mint-light);"><?php echo htmlspecialchars($b['title']); ?></td>
                                <td><?php echo htmlspecialchars($b['author']); ?></td>
                                <td style="font-size: 0.8rem;"><?php echo date('d M Y', strtotime($b['created_at'])); ?></td>
                                <td>
                                    <button class="btn-action-delete" title="Padam Artikel" onclick="deleteBlogStory(<?php echo $b['id']; ?>)">
                                        <!-- Trash Icon -->
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="3 6 5 6 21 6"></polyline>
                                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                            <line x1="10" y1="11" x2="10" y2="17"></line>
                                            <line x1="14" y1="11" x2="14" y2="17"></line>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
</div>

<?php
$this->view('layouts/footer', $data);
?>
