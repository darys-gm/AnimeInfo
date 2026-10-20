<?php
require_once 'config/auth.php';
require_once 'config/anime_list.php';
include 'includes/header.php';

$auth = new Auth();
$animeList = new AnimeList();

// Check if user is logged in
if (!$auth->isLoggedIn()) {
    header('Location: auth.php');
    exit();
}

$user = $auth->getCurrentUser();
$status = isset($_GET['status']) ? $_GET['status'] : 'all';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 12; // Ubah jadi 12 untuk mobile friendly
$offset = ($page - 1) * $limit;

// Get user's anime list
$animes = [];
$totalCount = 0;

if ($status === 'all') {
    $animes = $animeList->getUserAnimeList($user['id'], null, $limit, $offset);
    $totalCount = $animeList->getListCount($user['id']);
} else {
    $animes = $animeList->getUserAnimeList($user['id'], $status, $limit, $offset);
    $totalCount = $animeList->getListCount($user['id'], $status);
}

$totalPages = ceil($totalCount / $limit);

// Status labels
$statusLabels = [
    'watching' => 'Sedang Tayang',
    'completed' => 'Selesai',
    'planned' => 'Akan Ditonton',
    'dropped' => 'Berhenti',
    'on_hold' => 'Ditunda'
];

$statusColors = [
    'watching' => 'success',
    'completed' => 'info',
    'planned' => 'warning',
    'dropped' => 'danger',
    'on_hold' => 'secondary'
];

$statusIcons = [
    'watching' => 'fa-play-circle',
    'completed' => 'fa-check-circle',
    'planned' => 'fa-clock',
    'dropped' => 'fa-times-circle',
    'on_hold' => 'fa-pause-circle'
];
?>

<div class="mylist-page">
    <!-- Header -->
    <div class="mylist-header mb-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
            <div>
                <h1 class="mb-1">
                    <i class="fas fa-list text-primary"></i> 
                    My Anime List
                </h1>
                <p class="text-muted small mb-0">
                    <i class="fas fa-user"></i> <?php echo htmlspecialchars($user['username']); ?> • 
                    <i class="fas fa-film"></i> <?php echo $totalCount; ?> anime
                </p>
            </div>
            <a href="profile.php" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-user"></i> Profil Saya
            </a>
        </div>
    </div>
    
    <!-- Stats Cards Mobile Friendly -->
    <div class="row stats-row g-2 g-md-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="stat-card text-center p-2 p-md-3">
                <div class="stat-icon">
                    <i class="fas fa-eye text-primary"></i>
                </div>
                <div class="stat-value"><?php echo $animeList->getListCount($user['id'], 'watching'); ?></div>
                <div class="stat-label">Sedang Tayang</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card text-center p-2 p-md-3">
                <div class="stat-icon">
                    <i class="fas fa-check-circle text-success"></i>
                </div>
                <div class="stat-value"><?php echo $animeList->getListCount($user['id'], 'completed'); ?></div>
                <div class="stat-label">Selesai</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card text-center p-2 p-md-3">
                <div class="stat-icon">
                    <i class="fas fa-clock text-warning"></i>
                </div>
                <div class="stat-value"><?php echo $animeList->getListCount($user['id'], 'planned'); ?></div>
                <div class="stat-label">Akan Ditonton</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card text-center p-2 p-md-3">
                <div class="stat-icon">
                    <i class="fas fa-heart text-danger"></i>
                </div>
                <div class="stat-value"><?php echo $totalCount; ?></div>
                <div class="stat-label">Total Anime</div>
            </div>
        </div>
    </div>
    
    <!-- Filter Tabs - Mobile Friendly (Scrollable) -->
    <div class="filter-tabs-wrapper mb-4">
        <div class="filter-tabs-scroll">
            <div class="filter-tabs d-flex gap-2">
                <a href="?status=all" class="filter-tab <?php echo $status === 'all' ? 'active' : ''; ?>">
                    <i class="fas fa-list"></i>
                    <span>Semua</span>
                    <span class="badge"><?php echo $animeList->getListCount($user['id']); ?></span>
                </a>
                <a href="?status=watching" class="filter-tab <?php echo $status === 'watching' ? 'active' : ''; ?>">
                    <i class="fas fa-play-circle"></i>
                    <span>Sedang Tayang</span>
                    <span class="badge"><?php echo $animeList->getListCount($user['id'], 'watching'); ?></span>
                </a>
                <a href="?status=completed" class="filter-tab <?php echo $status === 'completed' ? 'active' : ''; ?>">
                    <i class="fas fa-check-circle"></i>
                    <span>Selesai</span>
                    <span class="badge"><?php echo $animeList->getListCount($user['id'], 'completed'); ?></span>
                </a>
                <a href="?status=planned" class="filter-tab <?php echo $status === 'planned' ? 'active' : ''; ?>">
                    <i class="fas fa-clock"></i>
                    <span>Rencana</span>
                    <span class="badge"><?php echo $animeList->getListCount($user['id'], 'planned'); ?></span>
                </a>
            </div>
        </div>
    </div>
    
    <!-- Anime List Grid - Responsive -->
    <?php if (empty($animes)): ?>
        <div class="empty-state text-center py-5">
            <div class="empty-icon">
                <i class="fas fa-folder-open fa-4x text-muted"></i>
            </div>
            <h4 class="mt-3">Belum ada anime di list Anda</h4>
            <p class="text-muted">Mulai tambahkan anime favorit Anda dengan mengklik tombol "Tambah ke My List" di halaman detail anime.</p>
            <a href="airing.php" class="btn btn-primary mt-2">
                <i class="fas fa-search"></i> Jelajahi Anime
            </a>
        </div>
    <?php else: ?>
        <div class="anime-list-grid">
            <?php foreach ($animes as $anime): ?>
                <div class="anime-list-card" onclick="window.location.href='detail.php?id=<?php echo $anime['anime_id']; ?>'">
                    <!-- Image Container -->
                    <div class="card-image">
                        <img src="<?php echo $anime['anime_image']; ?>" 
                             alt="<?php echo htmlspecialchars($anime['anime_title']); ?>"
                             onerror="this.src='https://via.placeholder.com/300x450?text=No+Image'">
                        
                        <!-- Status Badge -->
                        <div class="status-badge status-<?php echo $anime['anime_status']; ?>">
                            <i class="fas <?php echo $statusIcons[$anime['anime_status']] ?? 'fa-tv'; ?>"></i>
                            <span><?php echo $statusLabels[$anime['anime_status']] ?? $anime['anime_status']; ?></span>
                        </div>
                        
                        <!-- Score Badge -->
                        <?php if ($anime['anime_score']): ?>
                        <div class="score-badge">
                            <i class="fas fa-star"></i> <?php echo number_format($anime['anime_score'], 1); ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Content -->
                    <div class="card-content">
                        <h3 class="anime-title"><?php echo htmlspecialchars($anime['anime_title']); ?></h3>
                        
                        <!-- Progress Bar -->
                        <?php if ($anime['anime_total_episodes'] > 0 && $anime['anime_progress'] > 0): ?>
                        <div class="progress-container">
                            <div class="progress-info">
                                <span class="progress-label">Progress</span>
                                <span class="progress-value"><?php echo $anime['anime_progress']; ?>/<?php echo $anime['anime_total_episodes']; ?></span>
                            </div>
                            <div class="progress-bar-wrapper">
                                <div class="progress-bar-fill" style="width: <?php echo ($anime['anime_progress'] / $anime['anime_total_episodes']) * 100; ?>%"></div>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Action Buttons -->
                        <div class="card-actions">
                            <a href="detail.php?id=<?php echo $anime['anime_id']; ?>" class="btn-detail">
                                <i class="fas fa-info-circle"></i> Detail
                            </a>
                            <button class="btn-remove" onclick="event.stopPropagation(); removeAnime(<?php echo $anime['anime_id']; ?>, '<?php echo addslashes($anime['anime_title']); ?>')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <nav class="pagination-nav mt-4">
            <ul class="pagination justify-content-center flex-wrap">
                <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?status=<?php echo $status; ?>&page=<?php echo $page-1; ?>">
                            <i class="fas fa-chevron-left"></i> Prev
                        </a>
                    </li>
                <?php endif; ?>
                
                <?php 
                $startPage = max(1, $page - 2);
                $endPage = min($totalPages, $page + 2);
                for ($i = $startPage; $i <= $endPage; $i++): 
                ?>
                    <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                        <a class="page-link" href="?status=<?php echo $status; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
                
                <?php if ($page < $totalPages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?status=<?php echo $status; ?>&page=<?php echo $page+1; ?>">
                            Next <i class="fas fa-chevron-right"></i>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
        <?php endif; ?>
    <?php endif; ?>
</div>

<!-- Form untuk remove anime (hidden) -->
<form id="removeForm" method="POST" style="display: none;">
    <input type="hidden" name="remove_anime_id" id="removeAnimeId">
</form>

<style>
/* ========================================
   MY LIST PAGE - RESPONSIVE STYLES
   ======================================== */

.mylist-page {
    padding-bottom: 40px;
}

/* Stats Cards */
.stats-row {
    margin: 0 -8px;
}

.stat-card {
    background: white;
    border-radius: 16px;
    padding: 15px 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    transition: all 0.3s ease;
    height: 100%;
}

.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.stat-icon i {
    font-size: 1.5rem;
    margin-bottom: 8px;
}

.stat-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: #2c3e50;
    line-height: 1.2;
}

.stat-label {
    font-size: 0.7rem;
    color: #7f8c8d;
    margin-top: 4px;
}

/* Filter Tabs - Scrollable on Mobile */
.filter-tabs-wrapper {
    position: relative;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: thin;
}

.filter-tabs-scroll {
    min-width: min-content;
}

.filter-tabs {
    display: flex;
    gap: 8px;
    padding-bottom: 4px;
}

.filter-tab {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 40px;
    text-decoration: none;
    color: #2c3e50;
    font-size: 0.85rem;
    font-weight: 500;
    transition: all 0.3s ease;
    white-space: nowrap;
}

.filter-tab i {
    font-size: 0.85rem;
}

.filter-tab .badge {
    background: #e9ecef;
    color: #2c3e50;
    padding: 2px 6px;
    border-radius: 20px;
    font-size: 0.7rem;
    margin-left: 4px;
}

.filter-tab:hover {
    background: #ebf5ff;
    border-color: #3498db;
    color: #3498db;
}

.filter-tab.active {
    background: #3498db;
    border-color: #3498db;
    color: white;
}

.filter-tab.active .badge {
    background: rgba(255,255,255,0.3);
    color: white;
}

/* Anime List Grid - Responsive */
.anime-list-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

/* Anime List Card */
.anime-list-card {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    cursor: pointer;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.anime-list-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.12);
}

.card-image {
    position: relative;
    overflow: hidden;
    aspect-ratio: 2/3;
}

.card-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.anime-list-card:hover .card-image img {
    transform: scale(1.05);
}

/* Status Badge */
.status-badge {
    position: absolute;
    top: 12px;
    left: 12px;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 0.7rem;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 4px;
    backdrop-filter: blur(4px);
    background: rgba(0,0,0,0.7);
    color: white;
}

.status-badge.status-watching {
    background: #28a745;
}
.status-badge.status-completed {
    background: #17a2b8;
}
.status-badge.status-planned {
    background: #ffc107;
    color: #2c3e50;
}
.status-badge.status-dropped {
    background: #dc3545;
}
.status-badge.status-on_hold {
    background: #6c757d;
}

/* Score Badge */
.score-badge {
    position: absolute;
    top: 12px;
    right: 12px;
    background: rgba(0,0,0,0.7);
    backdrop-filter: blur(4px);
    padding: 4px 8px;
    border-radius: 20px;
    font-size: 0.7rem;
    font-weight: 500;
    color: #ffd700;
    display: flex;
    align-items: center;
    gap: 4px;
}

/* Card Content */
.card-content {
    padding: 12px;
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.anime-title {
    font-size: 0.9rem;
    font-weight: 600;
    color: #2c3e50;
    margin: 0;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    line-height: 1.3;
}

/* Progress Bar */
.progress-container {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.progress-info {
    display: flex;
    justify-content: space-between;
    font-size: 0.65rem;
    color: #7f8c8d;
}

.progress-label {
    font-weight: 500;
}

.progress-value {
    font-weight: 600;
    color: #3498db;
}

.progress-bar-wrapper {
    background: #e9ecef;
    border-radius: 10px;
    overflow: hidden;
    height: 6px;
}

.progress-bar-fill {
    background: linear-gradient(90deg, #3498db, #2980b9);
    height: 100%;
    border-radius: 10px;
    transition: width 0.3s ease;
}

/* Card Actions */
.card-actions {
    display: flex;
    gap: 8px;
    margin-top: auto;
}

.btn-detail {
    flex: 1;
    background: #3498db;
    color: white;
    border: none;
    padding: 8px 12px;
    border-radius: 40px;
    font-size: 0.75rem;
    font-weight: 500;
    text-decoration: none;
    text-align: center;
    transition: all 0.3s ease;
}

.btn-detail:hover {
    background: #2980b9;
    transform: translateY(-2px);
}

.btn-remove {
    width: 36px;
    background: #dc3545;
    color: white;
    border: none;
    border-radius: 40px;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-remove:hover {
    background: #c82333;
    transform: translateY(-2px);
}

/* Empty State */
.empty-state {
    background: white;
    border-radius: 20px;
    padding: 50px 20px;
    text-align: center;
}

.empty-icon i {
    opacity: 0.5;
}

/* Pagination */
.pagination-nav {
    margin-top: 30px;
}

.pagination {
    gap: 5px;
}

.pagination .page-link {
    border-radius: 10px !important;
    padding: 8px 14px;
    color: #3498db;
    border: 1px solid #e9ecef;
    transition: all 0.3s ease;
}

.pagination .page-link:hover {
    background: #3498db;
    color: white;
    border-color: #3498db;
}

.pagination .page-item.active .page-link {
    background: #3498db;
    border-color: #3498db;
    color: white;
}

/* ========================================
   RESPONSIVE - MOBILE
   ======================================== */
@media (max-width: 768px) {
    /* Stats cards mobile */
    .stat-card {
        padding: 10px 8px;
    }
    
    .stat-icon i {
        font-size: 1.2rem;
    }
    
    .stat-value {
        font-size: 1.2rem;
    }
    
    .stat-label {
        font-size: 0.6rem;
    }
    
    /* Grid 2 columns on mobile */
    .anime-list-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
    }
    
    /* Card content mobile */
    .card-content {
        padding: 10px;
    }
    
    .anime-title {
        font-size: 0.8rem;
    }
    
    .status-badge {
        padding: 3px 8px;
        font-size: 0.6rem;
        top: 8px;
        left: 8px;
    }
    
    .score-badge {
        padding: 3px 6px;
        font-size: 0.6rem;
        top: 8px;
        right: 8px;
    }
    
    .btn-detail {
        padding: 6px 10px;
        font-size: 0.7rem;
    }
    
    .btn-remove {
        width: 32px;
    }
    
    .progress-info {
        font-size: 0.6rem;
    }
    
    /* Filter tabs mobile */
    .filter-tab {
        padding: 6px 12px;
        font-size: 0.75rem;
    }
    
    .filter-tab span:not(.badge) {
        display: none;
    }
    
    .filter-tab i {
        font-size: 1rem;
    }
    
    .filter-tab .badge {
        display: inline-block;
    }
    
    /* Pagination mobile */
    .pagination .page-link {
        padding: 6px 10px;
        font-size: 0.75rem;
    }
}

/* Small mobile (≤480px) */
@media (max-width: 480px) {
    .anime-list-grid {
        gap: 10px;
    }
    
    .card-content {
        padding: 8px;
    }
    
    .anime-title {
        font-size: 0.7rem;
    }
    
    .btn-detail {
        padding: 5px 8px;
        font-size: 0.65rem;
    }
    
    .btn-remove {
        width: 28px;
    }
    
    .status-badge span {
        display: none;
    }
    
    .status-badge i {
        margin: 0;
    }
    
    .score-badge {
        font-size: 0.55rem;
    }
}

/* Tablet (769px - 991px) */
@media (min-width: 769px) and (max-width: 991px) {
    .anime-list-grid {
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
    }
}

/* Desktop (992px+) */
@media (min-width: 992px) {
    .anime-list-grid {
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
    }
}
</style>

<script>
// Function to remove anime
function removeAnime(animeId, animeTitle) {
    event.stopPropagation();
    if (confirm(`Hapus "${animeTitle}" dari list Anda?`)) {
        // Create form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '';
        
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'remove_from_list';
        input.value = '1';
        
        const input2 = document.createElement('input');
        input2.type = 'hidden';
        input2.name = 'anime_id';
        input2.value = animeId;
        
        form.appendChild(input);
        form.appendChild(input2);
        document.body.appendChild(form);
        form.submit();
    }
}

// Handle remove form submission via PHP
<?php if (isset($_POST['remove_from_list']) && isset($_POST['anime_id'])): ?>
    <?php
    $removeId = (int)$_POST['anime_id'];
    $success = $animeList->removeAnime($user['id'], $removeId);
    if ($success) {
        echo 'window.location.href = "mylist.php?status=' . $status . '";';
    }
    ?>
<?php endif; ?>
</script>

<?php include 'includes/footer.php'; ?>