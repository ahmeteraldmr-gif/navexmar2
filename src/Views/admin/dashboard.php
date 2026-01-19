<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Navexmar Admin</title>
    <link rel="stylesheet" href="<?php echo asset('css/style.css'); ?>">
    <link rel="stylesheet" href="<?php echo asset('css/admin.css'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="admin-body">
    <!-- Sidebar -->
    <div class="admin-sidebar">
        <div class="sidebar-header">
            <h2><i class="fas fa-anchor"></i> NAVEXMAR</h2>
        </div>
        <nav class="sidebar-nav">
            <a href="<?php echo url('/admin'); ?>" class="nav-item active">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            <a href="<?php echo url('/admin/services'); ?>" class="nav-item">
                <i class="fas fa-briefcase"></i> Hizmetler
            </a>
            <a href="<?php echo url('/admin/messages'); ?>" class="nav-item">
                <i class="fas fa-envelope"></i> Mesajlar
                <?php if (isset($stats['unread_messages']) && $stats['unread_messages'] > 0): ?>
                    <span class="badge"><?php echo $stats['unread_messages']; ?></span>
                <?php endif; ?>
            </a>
            <a href="<?php echo url('/admin/pages'); ?>" class="nav-item">
                <i class="fas fa-file-alt"></i> Sayfalar
            </a>
            <a href="<?php echo url('/admin/headers'); ?>" class="nav-item">
                <i class="fas fa-images"></i> Header Görselleri
            </a>
            <a href="<?php echo url('/admin/settings'); ?>" class="nav-item">
                <i class="fas fa-cog"></i> Ayarlar
            </a>
        </nav>
        <div class="sidebar-footer">
            <a href="<?php echo url('/admin/logout'); ?>" class="nav-item">
                <i class="fas fa-sign-out-alt"></i> Çıkış
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="admin-main">
        <!-- Top Bar -->
        <div class="admin-topbar">
            <div class="topbar-left">
                <button class="sidebar-toggle" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <h1>Dashboard</h1>
            </div>
            <div class="topbar-right">
                <span class="admin-user">
                    <i class="fas fa-user-circle"></i> <?php echo e($adminName); ?>
                </span>
                <a href="<?php echo url('/'); ?>" target="_blank" class="btn btn-sm">
                    <i class="fas fa-external-link-alt"></i> Siteyi Görüntüle
                </a>
            </div>
        </div>

        <!-- Content -->
        <div class="admin-content">
            <?php if (isset($flash)): ?>
                <div class="alert alert-<?php echo e($flash['type']); ?>">
                    <i class="fas fa-<?php echo $flash['type'] === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
                    <?php echo e($flash['message']); ?>
                </div>
            <?php endif; ?>

            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon" style="background: #4CAF50;">
                        <i class="fas fa-briefcase"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $stats['total_services']; ?></h3>
                        <p>Toplam Hizmet</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background: #2196F3;">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $stats['total_messages']; ?></h3>
                        <p>Toplam Mesaj</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background: #FF9800;">
                        <i class="fas fa-envelope-open"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $stats['unread_messages']; ?></h3>
                        <p>Okunmamış Mesaj</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background: #9C27B0;">
                        <i class="fas fa-images"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $stats['total_images']; ?></h3>
                        <p>Aktif Görseller</p>
                    </div>
                </div>
            </div>

            <!-- Recent Messages -->
            <div class="card">
                <div class="card-header">
                    <h2><i class="fas fa-envelope"></i> Son Mesajlar</h2>
                    <a href="<?php echo url('/admin/messages'); ?>" class="btn btn-sm">Tümünü Gör</a>
                </div>
                <div class="card-body">
                    <?php if (isset($recentMessages) && !empty($recentMessages)): ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Ad</th>
                                        <th>E-posta</th>
                                        <th>Hizmet</th>
                                        <th>Tarih</th>
                                        <th>Durum</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentMessages as $message): ?>
                                        <tr>
                                            <td><?php echo e($message['name']); ?></td>
                                            <td><?php echo e($message['email']); ?></td>
                                            <td><?php echo e($message['service'] ?? '-'); ?></td>
                                            <td><?php echo date('d.m.Y H:i', strtotime($message['created_at'])); ?></td>
                                            <td>
                                                <?php if ($message['is_read'] == 1): ?>
                                                    <span class="badge badge-success">Okundu</span>
                                                <?php else: ?>
                                                    <span class="badge badge-warning">Yeni</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-center text-muted">Henüz mesaj bulunmamaktadır.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="quick-actions">
                <h2><i class="fas fa-bolt"></i> Hızlı Erişim</h2>
                <div class="actions-grid">
                    <a href="<?php echo url('/admin/services'); ?>" class="action-card">
                        <i class="fas fa-plus-circle"></i>
                        <span>Yeni Hizmet Ekle</span>
                    </a>
                    <a href="<?php echo url('/admin/headers'); ?>" class="action-card">
                        <i class="fas fa-upload"></i>
                        <span>Görsel Yükle</span>
                    </a>
                    <a href="<?php echo url('/admin/pages'); ?>" class="action-card">
                        <i class="fas fa-edit"></i>
                        <span>Sayfa Düzenle</span>
                    </a>
                    <a href="<?php echo url('/admin/settings'); ?>" class="action-card">
                        <i class="fas fa-cog"></i>
                        <span>Ayarları Düzenle</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Sidebar toggle
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            document.querySelector('.admin-sidebar').classList.toggle('collapsed');
        });
    </script>
</body>
</html>
