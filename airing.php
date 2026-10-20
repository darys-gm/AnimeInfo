<?php
require_once 'config/anilist_api.php';
include 'includes/header.php';

$api = new AniListAPI();
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$airing = $api->getAiringAnime($page, 24);
?>

<h1 class="mb-3">
    <i class="fas fa-play-circle text-primary"></i> 
    Anime Sedang Tayang
</h1>

<p class="text-muted mb-4">
    Menampilkan anime yang sedang tayang dengan episode terbaru.
</p>

<?php if ($airing && isset($airing['data']['Page']['media'])): ?>
    <div class="anime-grid-4">
        <?php foreach ($airing['data']['Page']['media'] as $anime): ?>
            <div class="modern-anime-card" onclick="window.location.href='detail.php?id=<?php echo $anime['id']; ?>'">
                <div class="card-image-wrapper">
                    <img src="<?php echo $anime['coverImage']['large']; ?>" 
                         class="anime-poster" 
                         alt="<?php echo htmlspecialchars($anime['title']['romaji']); ?>"
                         onerror="this.src='https://via.placeholder.com/300x450?text=No+Image'">
                    
                    <div class="score-badge">
                        <i class="fas fa-star"></i> <?php echo AniListAPI::formatScore($anime['averageScore']); ?>
                    </div>
                    
                    <div class="episode-badge">
                        <i class="fas fa-tv"></i> Ep <?php echo $anime['episodes'] ?? '?'; ?>
                    </div>
                    
                    <div class="type-badge">
                        <?php echo $anime['format'] ?? 'TV'; ?>
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
                        <div class="stat-item">
                            <i class="far fa-heart"></i> <?php echo number_format($anime['favourites'] ?? 0); ?>
                        </div>
                    </div>
                    
                    <?php if (!empty($anime['genres'])): ?>
                    <div class="anime-genres">
                        <?php foreach (array_slice($anime['genres'], 0, 3) as $genre): ?>
                            <span class="genre-tag"><?php echo $genre; ?></span>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (isset($anime['nextAiringEpisode'])): ?>
                    <div class="next-episode">
                        <div class="next-episode-label">
                            <i class="fas fa-clock"></i> EPISODE BERIKUTNYA
                        </div>
                        <div class="next-episode-time">
                            <span class="days"><?php echo floor($anime['nextAiringEpisode']['timeUntilAiring'] / 86400); ?> hari</span>
                            <span class="hours"><?php echo floor(($anime['nextAiringEpisode']['timeUntilAiring'] % 86400) / 3600); ?> jam</span>
                        </div>
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
    <?php if (isset($airing['data']['Page']['pageInfo']['hasNextPage'])): ?>
    <nav class="mt-4">
        <ul class="pagination justify-content-center">
            <?php if ($page > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?php echo $page-1; ?>">
                        <i class="fas fa-chevron-left"></i> Previous
                    </a>
                </li>
            <?php endif; ?>
            
            <li class="page-item active">
                <span class="page-link">Halaman <?php echo $page; ?></span>
            </li>
            
            <?php if ($airing['data']['Page']['pageInfo']['hasNextPage']): ?>
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
        <i class="fas fa-info-circle fa-2x mb-2"></i>
        <p>Tidak ada data anime yang sedang tayang.</p>
    </div>
<?php endif; ?>

<style>
/* 4 Grid Layout untuk Desktop */
.anime-grid-4 {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 24px;
    margin-bottom: 40px;
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