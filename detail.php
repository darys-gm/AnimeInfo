<?php
require_once 'config/anilist_api.php';
require_once 'config/auth.php';
require_once 'config/anime_list.php';
include 'includes/header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$api = new AniListAPI();
$anime = $api->getAnimeDetail($id);

$auth = new Auth();
$animeList = new AnimeList();
$isLoggedIn = $auth->isLoggedIn();
$isInList = false;
$userAnimeStatus = '';

// Variabel untuk notifikasi
$notificationMessage = '';
$notificationType = '';

if ($isLoggedIn && $anime) {
    $user = $auth->getCurrentUser();
    $isInList = $animeList->isInList($user['id'], $anime['id']);
    
    if ($isInList) {
        $userAnimes = $animeList->getUserAnimeList($user['id'], null, 1000, 0);
        foreach ($userAnimes as $ua) {
            if ($ua['anime_id'] == $anime['id']) {
                $userAnimeStatus = $ua['anime_status'];
                break;
            }
        }
    }
}

// Handle add to list - DIPERBAIKI
if ($isLoggedIn && isset($_POST['add_to_list']) && $anime) {
    $status = $_POST['anime_status'];
    $progress = isset($_POST['anime_progress']) ? (int)$_POST['anime_progress'] : 0;
    
    $success = $animeList->addAnime(
        $user['id'],
        $anime['id'],
        $anime['title']['romaji'],
        $anime['coverImage']['large'],
        $anime['averageScore'] ? $anime['averageScore'] / 10 : null,
        $anime['episodes'],
        $status
    );
    
    if ($success) {
        // Update progress jika ada
        if ($progress > 0) {
            $animeList->updateStatus($user['id'], $anime['id'], $status, $progress);
        }
        $notificationMessage = $anime['title']['romaji'] . ' berhasil ditambahkan ke My List!';
        $notificationType = 'success';
    } else {
        $notificationMessage = 'Gagal menambahkan anime ke list. Silakan coba lagi.';
        $notificationType = 'error';
    }
}

// Handle remove from list
if ($isLoggedIn && isset($_POST['remove_from_list']) && $anime) {
    $success = $animeList->removeAnime($user['id'], $anime['id']);
    if ($success) {
        $notificationMessage = $anime['title']['romaji'] . ' berhasil dihapus dari My List!';
        $notificationType = 'info';
    } else {
        $notificationMessage = 'Gagal menghapus anime dari list. Silakan coba lagi.';
        $notificationType = 'error';
    }
}

// Handle update status
if ($isLoggedIn && isset($_POST['update_status']) && $anime) {
    $newStatus = $_POST['anime_status'];
    $progress = isset($_POST['anime_progress']) ? (int)$_POST['anime_progress'] : null;
    $success = $animeList->updateStatus($user['id'], $anime['id'], $newStatus, $progress);
    
    if ($success) {
        $status_labels = [
            'watching' => 'Sedang Ditonton',
            'completed' => 'Selesai',
            'planned' => 'Akan Ditonton',
            'dropped' => 'Berhenti',
            'on_hold' => 'Ditunda'
        ];
        $notificationMessage = 'Status anime berhasil diupdate menjadi "' . ($status_labels[$newStatus] ?? $newStatus) . '"!';
        $notificationType = 'success';
    } else {
        $notificationMessage = 'Gagal mengupdate status anime. Silakan coba lagi.';
        $notificationType = 'error';
    }
}

// Update session variable for isInList after action
if ($notificationMessage && !isset($_POST['update_status'])) {
    // Update status isInList
    $isInList = $animeList->isInList($user['id'], $anime['id']);
    if ($isInList) {
        $userAnimes = $animeList->getUserAnimeList($user['id'], null, 1000, 0);
        foreach ($userAnimes as $ua) {
            if ($ua['anime_id'] == $anime['id']) {
                $userAnimeStatus = $ua['anime_status'];
                break;
            }
        }
    }
}

if (!$anime):
?>
    <div class="alert alert-danger text-center">
        <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
        <h4>Anime tidak ditemukan!</h4>
        <p>Maaf, anime dengan ID <?php echo $id; ?> tidak dapat ditemukan.</p>
        <a href="index.php" class="btn btn-primary">Kembali ke Home</a>
    </div>
<?php else: ?>

<!-- BACK BUTTON - DESKTOP VERSION -->
<div class="back-button-desktop-container">
    <div class="container">
        <div class="back-button-wrapper">
            <a href="javascript:history.back()" class="back-button">
                <i class="fas fa-arrow-left"></i>
                <span>Kembali</span>
            </a>
            <div class="breadcrumb">
                <a href="index.php">Home</a>
                <i class="fas fa-chevron-right"></i>
                <span class="current"><?php echo htmlspecialchars($anime['title']['romaji']); ?></span>
            </div>
        </div>
    </div>
</div>

<!-- Mobile back button -->
<div class="mobile-back-btn" onclick="window.location.href='javascript:history.back()'">
    <i class="fas fa-arrow-left"></i>
</div>

<!-- Toast Notification -->
<div id="customToast" class="custom-toast" style="display: none;">
    <div class="toast-content">
        <div class="toast-icon" id="toastIcon">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="toast-message" id="toastMessage">
            Anime berhasil ditambahkan ke My List!
        </div>
        <button class="toast-close" onclick="closeToast()">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>

<div class="row detail-page-mobile">
    <!-- Sidebar Kiri: Poster & Info -->
    <div class="col-md-3">
        <img src="<?php echo $anime['coverImage']['extraLarge']; ?>" 
             class="img-fluid rounded w-100 mb-3" 
             alt="<?php echo htmlspecialchars($anime['title']['romaji']); ?>"
             onerror="this.src='https://via.placeholder.com/300x450?text=No+Image'"
             style="box-shadow: var(--shadow-md);">
        
        <!-- Quick Info Card -->
        <div class="card mb-3">
            <div class="card-header">
                <strong><i class="fas fa-info-circle"></i> Info Cepat</strong>
            </div>
            <div class="card-body small">
                <p><strong>⭐ Score:</strong> <?php echo AniListAPI::formatScore($anime['averageScore']); ?> / 10</p>
                <p><strong>❤️ Favorit:</strong> <?php echo number_format($anime['favourites']); ?></p>
                <p><strong>📺 Popularitas:</strong> #<?php echo number_format($anime['popularity']); ?></p>
                <p><strong>🎬 Format:</strong> <?php echo $anime['format']; ?></p>
                <p><strong>📊 Status:</strong> 
                    <?php 
                        $status_class = [
                            'RELEASING' => 'danger',
                            'FINISHED' => 'secondary',
                            'NOT_YET_RELEASED' => 'warning'
                        ];
                        $status_text = [
                            'RELEASING' => 'Sedang Tayang',
                            'FINISHED' => 'Selesai',
                            'NOT_YET_RELEASED' => 'Akan Datang'
                        ];
                    ?>
                    <span class="badge bg-<?php echo $status_class[$anime['status']] ?? 'secondary'; ?>">
                        <?php echo $status_text[$anime['status']] ?? $anime['status']; ?>
                    </span>
                </p>
                <?php if ($anime['episodes']): ?>
                    <p><strong>📀 Episode:</strong> <?php echo $anime['episodes']; ?></p>
                <?php endif; ?>
                <?php if ($anime['duration']): ?>
                    <p><strong>⏱️ Durasi:</strong> <?php echo $anime['duration']; ?> menit/episode</p>
                <?php endif; ?>
                <p><strong>📅 Rilis:</strong> 
                    <?php 
                        $startDate = "{$anime['startDate']['year']}";
                        if ($anime['startDate']['month']) $startDate .= "-{$anime['startDate']['month']}";
                        if ($anime['startDate']['day']) $startDate .= "-{$anime['startDate']['day']}";
                        echo $startDate;
                    ?>
                </p>
            </div>
        </div>
        
        <!-- Studio -->
        <?php if (isset($anime['studios']['nodes']) && count($anime['studios']['nodes']) > 0): ?>
        <div class="card mb-3">
            <div class="card-header bg-secondary text-white">
                <strong><i class="fas fa-building"></i> Studio</strong>
            </div>
            <div class="card-body">
                <?php foreach ($anime['studios']['nodes'] as $studio): ?>
                    <span class="badge bg-dark"><?php echo $studio['name']; ?></span>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- My List Actions -->
        <div class="card mb-3">
            <div class="card-header bg-success text-white">
                <strong><i class="fas fa-list"></i> My List</strong>
            </div>
            <div class="card-body">
                <?php if ($isLoggedIn): ?>
                    <?php if ($isInList): ?>
                        <div class="mb-3">
                            <div class="alert alert-info small">
                                <i class="fas fa-check-circle"></i> Anime ini ada di list Anda
                                <?php if ($userAnimeStatus): ?>
                                    <br>Status: <strong><?php 
                                        $status_labels = [
                                            'watching' => 'Sedang Tayang',
                                            'completed' => 'Selesai',
                                            'planned' => 'Akan Ditonton',
                                            'dropped' => 'Berhenti',
                                            'on_hold' => 'Ditunda'
                                        ];
                                        echo $status_labels[$userAnimeStatus] ?? $userAnimeStatus;
                                    ?></strong>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <form method="POST" action="" class="mb-3" id="updateStatusForm">
                            <div class="mb-2">
                                <label class="form-label small">Update Status</label>
                                <select name="anime_status" class="form-select form-select-sm">
                                    <option value="planned" <?php echo $userAnimeStatus == 'planned' ? 'selected' : ''; ?>>Akan Ditonton</option>
                                    <option value="watching" <?php echo $userAnimeStatus == 'watching' ? 'selected' : ''; ?>>Sedang Ditonton</option>
                                    <option value="completed" <?php echo $userAnimeStatus == 'completed' ? 'selected' : ''; ?>>Selesai</option>
                                    <option value="on_hold" <?php echo $userAnimeStatus == 'on_hold' ? 'selected' : ''; ?>>Ditunda</option>
                                    <option value="dropped" <?php echo $userAnimeStatus == 'dropped' ? 'selected' : ''; ?>>Berhenti</option>
                                </select>
                            </div>
                            <div class="mb-2">
                                <label class="form-label small">Progress Episode</label>
                                <input type="number" name="anime_progress" class="form-control form-control-sm" 
                                       placeholder="Episode ke-" min="0" max="<?php echo $anime['episodes'] ?? 999; ?>">
                            </div>
                            <button type="submit" name="update_status" class="btn btn-warning btn-sm w-100">
                                <i class="fas fa-sync-alt"></i> Update Status
                            </button>
                        </form>
                        
                        <form method="POST" action="" id="removeForm" onsubmit="return confirmRemove('<?php echo addslashes($anime['title']['romaji']); ?>')">
                            <input type="hidden" name="remove_from_list" value="1">
                            <button type="submit" class="btn btn-danger btn-sm w-100">
                                <i class="fas fa-trash"></i> Hapus dari My List
                            </button>
                        </form>
                    <?php else: ?>
                        <button type="button" class="btn btn-success w-100" onclick="openAddToListModal()">
                            <i class="fas fa-plus"></i> Tambah ke My List
                        </button>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="text-center">
                        <i class="fas fa-lock fa-2x mb-2 text-muted"></i>
                        <p class="small">Login untuk menambahkan anime ke list Anda</p>
                        <a href="auth.php" class="btn btn-primary btn-sm">
                            <i class="fas fa-sign-in-alt"></i> Login Sekarang
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Konten Utama -->
    <div class="col-md-9">
        <h1><?php echo htmlspecialchars($anime['title']['romaji']); ?></h1>
        <?php if ($anime['title']['english']): ?>
            <h5 class="text-muted"><?php echo htmlspecialchars($anime['title']['english']); ?></h5>
        <?php endif; ?>
        <?php if ($anime['title']['native'] && $anime['title']['native'] != $anime['title']['romaji']): ?>
            <h6 class="text-muted"><?php echo htmlspecialchars($anime['title']['native']); ?></h6>
        <?php endif; ?>
        
        <!-- Banner Image -->
        <?php if ($anime['bannerImage']): ?>
            <img src="<?php echo $anime['bannerImage']; ?>" 
                 class="img-fluid rounded mb-4 w-100" 
                 alt="Banner"
                 style="max-height: 200px; object-fit: cover;">
        <?php endif; ?>
        
        <!-- Next Episode Info -->
        <?php if ($anime['nextAiringEpisode']): ?>
            <div class="alert alert-success">
                <i class="fas fa-bell"></i> 
                <strong>Episode <?php echo $anime['nextAiringEpisode']['episode']; ?> akan tayang dalam:</strong>
                <span class="fw-bold"><?php echo AniListAPI::formatTimeUntilAiring($anime['nextAiringEpisode']['timeUntilAiring']); ?></span>
                <small class="text-muted">(<?php echo AniListAPI::formatDate($anime['nextAiringEpisode']['airingAt']); ?>)</small>
            </div>
        <?php endif; ?>
        
        <!-- Genres -->
        <div class="mb-3">
            <?php foreach ($anime['genres'] as $genre): ?>
                <span class="badge bg-primary me-1"><?php echo $genre; ?></span>
            <?php endforeach; ?>
        </div>
        
        <!-- Synopsis -->
        <div class="card mb-4">
            <div class="card-header">
                <strong><i class="fas fa-book-open"></i> Sinopsis</strong>
            </div>
            <div class="card-body">
                <p><?php echo nl2br(htmlspecialchars(AniListAPI::cleanDescription($anime['description']))); ?></p>
            </div>
        </div>
        
        <!-- Characters -->
        <?php if (isset($anime['characters']['edges']) && count($anime['characters']['edges']) > 0): ?>
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <strong><i class="fas fa-users"></i> Karakter</strong>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php foreach (array_slice($anime['characters']['edges'], 0, 8) as $char): ?>
                        <div class="col-md-3 col-sm-4 col-6 mb-3 text-center">
                            <?php if ($char['node']['image']['medium']): ?>
                                <img src="<?php echo $char['node']['image']['medium']; ?>" 
                                     class="rounded-circle mb-2" 
                                     width="60" height="60"
                                     alt="<?php echo htmlspecialchars($char['node']['name']['full']); ?>"
                                     style="object-fit: cover;">
                            <?php else: ?>
                                <div class="rounded-circle bg-secondary mb-2 d-flex align-items-center justify-content-center mx-auto"
                                     style="width: 60px; height: 60px;">
                                    <i class="fas fa-user fa-2x text-white"></i>
                                </div>
                            <?php endif; ?>
                            <small class="d-block fw-bold"><?php echo htmlspecialchars($char['node']['name']['full']); ?></small>
                            <small class="text-muted"><?php echo $char['role']; ?></small>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Related Anime -->
        <?php if (isset($anime['relations']['edges']) && count($anime['relations']['edges']) > 0): ?>
        <div class="card mb-4">
            <div class="card-header bg-warning text-dark">
                <strong><i class="fas fa-link"></i> Anime Terkait</strong>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php foreach ($anime['relations']['edges'] as $rel): ?>
                        <div class="col-md-4 col-sm-6 mb-2">
                            <a href="detail.php?id=<?php echo $rel['node']['id']; ?>" class="text-decoration-none">
                                <div class="d-flex align-items-center">
                                    <?php if ($rel['node']['coverImage']['medium']): ?>
                                        <img src="<?php echo $rel['node']['coverImage']['medium']; ?>" 
                                             width="40" height="56" class="rounded me-2"
                                             alt="<?php echo htmlspecialchars($rel['node']['title']['romaji']); ?>"
                                             style="object-fit: cover;">
                                    <?php endif; ?>
                                    <div>
                                        <small class="fw-bold text-dark"><?php echo htmlspecialchars($rel['node']['title']['romaji']); ?></small>
                                        <div>
                                            <span class="badge bg-secondary"><?php echo $rel['relationType']; ?></span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Recommendations -->
        <?php if (isset($anime['recommendations']['nodes']) && count($anime['recommendations']['nodes']) > 0): ?>
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <strong><i class="fas fa-thumbs-up"></i> Rekomendasi</strong>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php foreach (array_slice($anime['recommendations']['nodes'], 0, 4) as $rec): ?>
                        <div class="col-md-3 col-6 mb-3">
                            <div class="card h-100">
                                <img src="<?php echo $rec['mediaRecommendation']['coverImage']['medium']; ?>" 
                                     class="card-img-top" 
                                     alt="<?php echo htmlspecialchars($rec['mediaRecommendation']['title']['romaji']); ?>"
                                     style="height: 150px; object-fit: cover;">
                                <div class="card-body p-2 text-center">
                                    <small class="fw-bold">
                                        <a href="detail.php?id=<?php echo $rec['mediaRecommendation']['id']; ?>" class="text-decoration-none">
                                            <?php echo htmlspecialchars($rec['mediaRecommendation']['title']['romaji']); ?>
                                        </a>
                                    </small>
                                    <?php if ($rec['mediaRecommendation']['averageScore']): ?>
                                        <div>
                                            <small class="text-muted">⭐ <?php echo AniListAPI::formatScore($rec['mediaRecommendation']['averageScore']); ?></small>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Add to List -->
<div class="modal fade" id="addToListModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plus-circle"></i> Tambah ke My List
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="" id="addToListForm">
                <div class="modal-body">
                    <p class="mb-3">Menambahkan: <strong><?php echo htmlspecialchars($anime['title']['romaji']); ?></strong></p>
                    <div class="mb-3">
                        <label class="form-label">Status Anime</label>
                        <select name="anime_status" class="form-select" required>
                            <option value="planned">📋 Akan Ditonton (Plan to Watch)</option>
                            <option value="watching">▶️ Sedang Ditonton (Watching)</option>
                            <option value="completed">✅ Selesai (Completed)</option>
                            <option value="on_hold">⏸️ Ditunda (On Hold)</option>
                            <option value="dropped">❌ Berhenti (Dropped)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Progress Episode (Opsional)</label>
                        <input type="number" name="anime_progress" class="form-control" 
                               placeholder="Episode ke-" min="0" max="<?php echo $anime['episodes'] ?? 999; ?>">
                        <small class="text-muted">Kosongkan jika belum mulai</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="add_to_list" class="btn btn-primary">
                        <i class="fas fa-save"></i> Tambah ke List
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php endif; ?>

<style>
/* ========================================
   BACK BUTTON - DESKTOP (NON-STICKY / NORMAL FLOW)
   ======================================== */

.back-button-desktop-container {
    background: white;
    border-bottom: 1px solid #e9ecef;
    margin-bottom: 24px;
    padding: 16px 0;
    position: relative;
}

.back-button-wrapper {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 15px;
}

.back-button {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #f8f9fa;
    color: #3498db;
    padding: 8px 20px;
    border-radius: 40px;
    text-decoration: none;
    font-weight: 500;
    font-size: 0.9rem;
    transition: all 0.3s ease;
    border: 1px solid #e9ecef;
}

.back-button i {
    font-size: 0.9rem;
    transition: transform 0.3s ease;
}

.back-button:hover {
    background: #3498db;
    color: white;
    border-color: #3498db;
    transform: translateX(-3px);
}

.back-button:hover i {
    transform: translateX(-3px);
}

.breadcrumb {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.85rem;
    color: #7f8c8d;
}

.breadcrumb a {
    color: #3498db;
    text-decoration: none;
    transition: color 0.3s ease;
}

.breadcrumb a:hover {
    color: #2980b9;
    text-decoration: underline;
}

.breadcrumb i {
    font-size: 0.7rem;
    color: #bdc3c7;
}

.breadcrumb .current {
    color: #2c3e50;
    font-weight: 500;
}

.mobile-back-btn {
    display: none;
    position: fixed;
    bottom: 20px;
    left: 20px;
    background: #3498db;
    color: white;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    cursor: pointer;
    transition: all 0.3s ease;
    border: none;
    z-index: 1000;
}

.mobile-back-btn:hover {
    transform: scale(1.05);
    background: #2980b9;
}

/* Custom Toast */
.custom-toast {
    position: fixed;
    top: 80px;
    right: 20px;
    z-index: 9999;
    min-width: 320px;
    max-width: 450px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.15);
    overflow: hidden;
    animation: slideInRight 0.3s ease;
}

@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@keyframes slideOutRight {
    from {
        transform: translateX(0);
        opacity: 1;
    }
    to {
        transform: translateX(100%);
        opacity: 0;
    }
}

.custom-toast.toast-hide {
    animation: slideOutRight 0.3s ease forwards;
}

.toast-content {
    display: flex;
    align-items: center;
    padding: 15px 20px;
    gap: 12px;
}

.toast-icon {
    flex-shrink: 0;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
}

.toast-icon.success {
    background: #d4edda;
    color: #28a745;
}

.toast-icon.error {
    background: #f8d7da;
    color: #dc3545;
}

.toast-icon.info {
    background: #d1ecf1;
    color: #17a2b8;
}

.toast-message {
    flex: 1;
    font-size: 0.85rem;
    color: #2c3e50;
    font-weight: 500;
    line-height: 1.4;
}

.toast-close {
    background: transparent;
    border: none;
    color: #7f8c8d;
    cursor: pointer;
    font-size: 0.9rem;
    padding: 5px;
    transition: color 0.3s ease;
}

.toast-close:hover {
    color: #dc3545;
}

@media (max-width: 768px) {
    .back-button-desktop-container {
        display: none;
    }
    
    .mobile-back-btn {
        display: flex;
    }
    
    .custom-toast {
        top: 70px;
        right: 10px;
        left: 10px;
        min-width: auto;
        max-width: none;
    }
    
    .detail-page-mobile {
        margin-bottom: 60px;
    }
}

@media (min-width: 769px) and (max-width: 991px) {
    .back-button {
        padding: 6px 16px;
        font-size: 0.85rem;
    }
}

html {
    scroll-behavior: smooth;
}
</style>

<script>
function showToast(message, type = 'success') {
    const toast = document.getElementById('customToast');
    const toastIcon = document.getElementById('toastIcon');
    const toastMessage = document.getElementById('toastMessage');
    
    let iconClass = 'success';
    let iconHtml = '<i class="fas fa-check-circle"></i>';
    
    if (type === 'error') {
        iconClass = 'error';
        iconHtml = '<i class="fas fa-exclamation-circle"></i>';
    } else if (type === 'info') {
        iconClass = 'info';
        iconHtml = '<i class="fas fa-info-circle"></i>';
    }
    
    toastIcon.className = 'toast-icon ' + iconClass;
    toastIcon.innerHTML = iconHtml;
    toastMessage.innerHTML = message;
    
    toast.style.display = 'block';
    toast.classList.remove('toast-hide');
    
    setTimeout(() => {
        toast.classList.add('toast-hide');
        setTimeout(() => {
            toast.style.display = 'none';
        }, 300);
    }, 3000);
}

function closeToast() {
    const toast = document.getElementById('customToast');
    toast.classList.add('toast-hide');
    setTimeout(() => {
        toast.style.display = 'none';
    }, 300);
}

function openAddToListModal() {
    const modal = new bootstrap.Modal(document.getElementById('addToListModal'));
    modal.show();
}

function confirmRemove(animeTitle) {
    return confirm(`Apakah Anda yakin ingin menghapus "${animeTitle}" dari My List?`);
}

// Show notification if exists
<?php if ($notificationMessage): ?>
document.addEventListener('DOMContentLoaded', function() {
    showToast('<?php echo addslashes($notificationMessage); ?>', '<?php echo $notificationType; ?>');
});
<?php endif; ?>

document.addEventListener('DOMContentLoaded', function() {
    const floatingBtn = document.querySelector('.mobile-back-btn');
    if (floatingBtn) {
        window.addEventListener('scroll', function() {
            const currentScroll = window.pageYOffset;
            if (currentScroll > 100) {
                floatingBtn.style.opacity = '1';
                floatingBtn.style.transform = 'scale(1)';
            } else {
                floatingBtn.style.opacity = '0.8';
                floatingBtn.style.transform = 'scale(0.95)';
            }
        });
    }
});
</script>

<?php include 'includes/footer.php'; ?>