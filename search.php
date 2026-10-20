<?php
require_once 'config/anilist_api.php';
include 'includes/header.php';

$api = new AniListAPI();
$query = isset($_GET['q']) ? trim($_GET['q']) : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$searchResult = null;
$error = null;

if (!empty($query)) {
    if (strlen($query) < 2) {
        $error = "Masukkan minimal 2 karakter untuk pencarian.";
    } else {
        $searchResult = $api->searchAnime($query, $page, 24);
        
        if (!$searchResult) {
            $error = "Gagal terhubung ke AniList API. Silakan coba lagi.";
        } elseif (!isset($searchResult['data']) || empty($searchResult['data'])) {
            $error = "Tidak ditemukan anime dengan judul \"<strong>" . htmlspecialchars($query) . "</strong>\". Coba dengan kata kunci lain.";
        }
    }
}
?>

<div class="container">
    <!-- Search Header -->
    <div class="hero-section text-center mb-4">
        <h1>Hasil Pencarian Anime</h1>
    </div>
    
    <!-- Search Form -->
    <div class="row justify-content-center mb-5">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-body">
                    <form action="search.php" method="GET" id="searchForm">
                        <div class="input-group input-group-lg">
                            <input type="text" 
                                   class="form-control" 
                                   name="q" 
                                   id="searchInput"
                                   value="<?php echo htmlspecialchars($query); ?>" 
                                   placeholder="Cari anime... (Naruto, One Piece, Attack on Titan, dll)"
                                   required>
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-search"></i> Cari
                            </button>
                        </div>
                        <div class="form-text mt-2">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Search Results -->
    <?php if (!empty($query) && $searchResult && !empty($searchResult['data'])): ?>
        <div class="row">
            <div class="col-12">
                <h3 class="mb-4">
                    Hasil pencarian untuk: 
                    <span class="text-primary">"<?php echo htmlspecialchars($query); ?>"</span>
                    <small class="text-muted">(<?php echo number_format($searchResult['pagination']['total'] ?? count($searchResult['data'])); ?> hasil)</small>
                </h3>
                
                <div class="row">
                    <?php foreach ($searchResult['data'] as $anime): ?>
                        <div class="col-md-4 col-lg-3 mb-4">
                            <div class="card anime-card h-100">
                                <img src="<?php echo $anime['coverImage']['large']; ?>" 
                                     class="card-img-top" 
                                     alt="<?php echo htmlspecialchars($anime['title']['romaji']); ?>"
                                     onerror="this.src='https://via.placeholder.com/225x320?text=No+Image'"
                                     style="height: 320px; object-fit: cover;">
                                <div class="card-body">
                                    <h6 class="card-title"><?php echo htmlspecialchars($anime['title']['romaji']); ?></h6>
                                    <?php if ($anime['title']['english']): ?>
                                        <div class="small text-muted mb-2"><?php echo htmlspecialchars($anime['title']['english']); ?></div>
                                    <?php endif; ?>
                                    
                                    <div class="mb-2">
                                        <?php if ($anime['averageScore']): ?>
                                            <span class="badge bg-warning text-dark">
                                                <i class="fas fa-star"></i> <?php echo AniListAPI::formatScore($anime['averageScore']); ?>
                                            </span>
                                        <?php endif; ?>
                                        <span class="badge bg-info">
                                            <?php echo $anime['format'] ?? 'TV'; ?>
                                        </span>
                                        <span class="badge <?php echo $anime['status'] == 'RELEASING' ? 'bg-danger' : ($anime['status'] == 'FINISHED' ? 'bg-secondary' : 'bg-primary'); ?>">
                                            <?php 
                                                $status_map = [
                                                    'RELEASING' => 'Tayang',
                                                    'FINISHED' => 'Selesai',
                                                    'NOT_YET_RELEASED' => 'Akan Datang'
                                                ];
                                                echo $status_map[$anime['status']] ?? $anime['status'];
                                            ?>
                                        </span>
                                    </div>
                                    
                                    <?php if (!empty($anime['genres'])): ?>
                                        <div class="mb-2">
                                            <?php foreach (array_slice($anime['genres'], 0, 2) as $genre): ?>
                                                <span class="badge bg-dark small"><?php echo $genre; ?></span>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="card-footer bg-transparent">
                                    <a href="detail.php?id=<?php echo $anime['id']; ?>" class="btn btn-sm btn-primary w-100">
                                        <i class="fas fa-info-circle"></i> Lihat Detail
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Pagination -->
                <?php if (isset($searchResult['pagination']['lastPage']) && $searchResult['pagination']['lastPage'] > 1): ?>
                <nav class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?q=<?php echo urlencode($query); ?>&page=<?php echo $page-1; ?>">
                                    <i class="fas fa-chevron-left"></i> Previous
                                </a>
                            </li>
                        <?php endif; ?>
                        
                        <?php for ($i = max(1, $page-2); $i <= min($searchResult['pagination']['lastPage'], $page+2); $i++): ?>
                            <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                <a class="page-link" href="?q=<?php echo urlencode($query); ?>&page=<?php echo $i; ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                        
                        <?php if ($page < $searchResult['pagination']['lastPage']): ?>
                            <li class="page-item">
                                <a class="page-link" href="?q=<?php echo urlencode($query); ?>&page=<?php echo $page+1; ?>">
                                    Next <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
                <?php endif; ?>
            </div>
        </div>
        
    <?php elseif (!empty($query) && $error): ?>
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
        </div>
        
    <?php else: ?>
        <!-- Rekomendasi Anime Populer -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> 
                    <strong>Tips:</strong> Coba cari dengan judul anime populer seperti Naruto, One Piece, Attack on Titan, Demon Slayer, atau Jujutsu Kaisen.
                </div>
                
                <h4 class="mb-3"><i class="fas fa-fire"></i> Anime Populer yang Bisa Dicari:</h4>
                <div class="row g-2">
                    <?php 
                    $popularAnime = [
                        'Naruto', 'One Piece', 'Attack on Titan', 'Demon Slayer', 
                        'Jujutsu Kaisen', 'My Hero Academia', 'Death Note', 
                        'Fullmetal Alchemist', 'Tokyo Ghoul', 'Sword Art Online',
                        'Spy x Family', 'Chainsaw Man', 'One Punch Man', 'Hunter x Hunter'
                    ];
                    ?>
                    <?php foreach ($popularAnime as $popular): ?>
                        <div class="col-md-2 col-sm-3 col-4 mb-2">
                            <a href="?q=<?php echo urlencode($popular); ?>" class="btn btn-outline-primary w-100 btn-sm">
                                <i class="fas fa-search"></i> <?php echo $popular; ?>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>