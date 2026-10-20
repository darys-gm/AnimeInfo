<?php
require_once 'config/anilist_api.php';
include 'includes/header.php';

$api = new AniListAPI();
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$upcoming = $api->getUpcomingAnime($page, 24);
?>

<div class="upcoming-page">
    <h1 class="mb-3">
        <i class="fas fa-star text-primary"></i> 
        Anime Akan Datang
        <small class="text-muted fs-6">(Upcoming Anime)</small>
    </h1>

    <div class="alert alert-warning mb-4">
        <i class="fas fa-calendar-alt"></i> 
        <strong>Informasi:</strong> Menampilkan anime yang akan rilis di masa mendatang.
        Jadwal rilis dapat berubah sewaktu-waktu.
    </div>

    <?php if (isset($upcoming['data']) && count($upcoming['data']) > 0): ?>
        <div class="anime-grid-4">
            <?php foreach ($upcoming['data'] as $anime): ?>
                <div class="modern-anime-card" onclick="window.location.href='detail.php?id=<?php echo $anime['id']; ?>'">
                    <div class="card-image-wrapper">
                        <img src="<?php echo $anime['coverImage']['large']; ?>" 
                             class="anime-poster" 
                             alt="<?php echo htmlspecialchars($anime['title']['romaji']); ?>"
                             onerror="this.src='https://via.placeholder.com/300x450?text=No+Image'">
                        
                        <?php if ($anime['averageScore']): ?>
                            <div class="score-badge">
                                <i class="fas fa-star"></i> <?php echo AniListAPI::formatScore($anime['averageScore']); ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="type-badge">
                            <?php echo $anime['format'] ?? 'TV'; ?>
                        </div>
                        
                        <!-- Release Date Badge -->
                        <div class="release-badge">
                            <i class="fas fa-calendar"></i> 
                            <?php 
                                if ($anime['startDate']['year']) {
                                    echo $anime['startDate']['year'];
                                    if ($anime['startDate']['month']) echo "/{$anime['startDate']['month']}";
                                } else {
                                    echo 'TBA';
                                }
                            ?>
                        </div>
                    </div>
                    
                    <div class="card-content">
                        <div class="anime-title">
                            <h3><?php echo htmlspecialchars($anime['title']['romaji']); ?></h3>
                            <?php if ($anime['title']['english'] && $anime['title']['english'] != $anime['title']['romaji']): ?>
                                <div class="english-title"><?php echo htmlspecialchars($anime['title']['english']); ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="anime-stats">
                            <div class="stat-item">
                                <i class="fas fa-fire"></i> <?php echo number_format($anime['popularity']); ?>
                            </div>
                        </div>
                        
                        <?php if (!empty($anime['genres'])): ?>
                        <div class="anime-genres">
                            <?php foreach (array_slice($anime['genres'], 0, 3) as $genre): ?>
                                <span class="genre-tag"><?php echo $genre; ?></span>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                        
                        <button class="btn btn-primary detail-btn">
                            Lihat Detail <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Pagination -->
        <?php if (isset($upcoming['pagination']['lastPage']) && $upcoming['pagination']['lastPage'] > 1): ?>
        <nav class="mt-4">
            <ul class="pagination justify-content-center">
                <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $page-1; ?>">
                            <i class="fas fa-chevron-left"></i> Previous
                        </a>
                    </li>
                <?php endif; ?>
                
                <?php 
                $startPage = max(1, $page - 2);
                $endPage = min($upcoming['pagination']['lastPage'], $page + 2);
                for ($i = $startPage; $i <= $endPage; $i++): 
                ?>
                    <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
                
                <?php if ($page < $upcoming['pagination']['lastPage']): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $page+1; ?>">
                            Next <i class="fas fa-chevron-right"></i>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
        <?php endif; ?>
        
    <?php else: ?>
        <div class="alert alert-info text-center">
            <i class="fas fa-info-circle fa-3x mb-3"></i>
            <h4>Tidak ada data anime yang akan datang</h4>
            <p>Silakan cek kembali nanti untuk update terbaru.</p>
        </div>
    <?php endif; ?>
</div>

<style>
/* 4 Grid Layout untuk Desktop */
.anime-grid-4 {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 24px;
    margin-bottom: 40px;
}

/* Release Badge khusus untuk upcoming */
.release-badge {
    position: absolute;
    bottom: 12px;
    right: 12px;
    background: rgba(0,0,0,0.75);
    backdrop-filter: blur(4px);
    padding: 4px 10px;
    border-radius: 30px;
    font-size: 0.7rem;
    font-weight: 500;
    color: #f39c12;
    display: flex;
    align-items: center;
    gap: 4px;
    z-index: 2;
}

/* Tablet (768px - 991px) - 3 grid */
@media (max-width: 991px) {
    .anime-grid-4 {
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
    }
}

/* Mobile (576px - 767px) - 2 grid */
@media (max-width: 767px) {
    .anime-grid-4 {
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
    }
    
    .release-badge {
        padding: 3px 8px;
        font-size: 0.6rem;
        bottom: 8px;
        right: 8px;
    }
}

/* Small Mobile (≤575px) - 2 grid dengan gap kecil */
@media (max-width: 575px) {
    .anime-grid-4 {
        gap: 12px;
    }
}
</style>

<script>
// Make entire card clickable
document.querySelectorAll('.modern-anime-card').forEach(card => {
    card.addEventListener('click', function(e) {
        if (e.target.closest('.detail-btn')) {
            e.stopPropagation();
            const btn = e.target.closest('.detail-btn');
            const link = btn.closest('.modern-anime-card').getAttribute('onclick');
            if (link) {
                eval(link);
            }
        }
    });
});
</script>

<?php include 'includes/footer.php'; ?>