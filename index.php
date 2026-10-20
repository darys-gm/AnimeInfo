<?php
require_once 'config/anilist_api.php';
include 'includes/header.php';

$api = new AniListAPI();
$homeData = $api->getHomeData(1, 9);
?>

<!-- Hero Section FULL WIDTH dengan SLIDESHOW BACKGROUND (Tanpa Kontrol) -->
<div class="hero-fullwidth">
    <div class="hero-slideshow">
        <div class="hero-slide active" style="background-image: url('https://img.magnific.com/free-photo/digital-art-style-illustration-thunderstorm_23-2151812821.jpg?semt=ais_hybrid&w=740&q=80');"></div>
        <div class="hero-slide" style="background-image: url('https://a-static.besthdwallpaper.com/anime-city-photo-with-blue-and-purple-colors-wallpaper-1920x1080-92989_48.jpg');"></div>
        <div class="hero-slide" style="background-image: url('https://wallpaperaccess.com/full/1851953.jpg');"></div>
        <div class="hero-slide" style="background-image: url('https://wallpaperaccess.com/full/6563109.jpg');"></div>
        <div class="hero-slide" style="background-image: url('https://c4.wallpaperflare.com/wallpaper/851/806/395/blue-sky-anime-anime-girl-summer-wallpaper-preview.jpg');"></div>
    </div>
    <div class="hero-overlay"></div>
    
    <div class="hero-container">
        <div class="hero-text">
            <h1> Selamat Datang di AnimeInfo</h1>
            <p class="hero-lead">Info terbaru tentang jadwal episode dan anime yang akan datang</p>
        </div>
    </div>
</div>

<!-- Konten Utama -->
<div class="container">
    <div class="row">
        <!-- Trending Anime - 3 GRID -->
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-chart-line text-primary"></i> Trending Anime</h2>
                <a href="airing.php" class="btn btn-outline-primary btn-sm">Lihat Semua →</a>
            </div>
            
            <div class="trending-grid">
                <?php if ($homeData && isset($homeData['data']['trending']['media'])): ?>
                    <?php foreach ($homeData['data']['trending']['media'] as $anime): ?>
                        <div class="trending-card" onclick="window.location.href='detail.php?id=<?php echo $anime['id']; ?>'">
                            <div class="trending-card-image">
                                <img src="<?php echo $anime['coverImage']['large']; ?>" 
                                     alt="<?php echo htmlspecialchars($anime['title']['romaji']); ?>"
                                     onerror="this.src='https://via.placeholder.com/300x450?text=No+Image'">
                                <div class="trending-score">
                                    ⭐ <?php echo AniListAPI::formatScore($anime['averageScore']); ?>
                                </div>
                                <div class="trending-type">
                                    <?php echo $anime['format'] ?? 'TV'; ?>
                                </div>
                            </div>
                            <div class="trending-card-info">
                                <h3 class="trending-title"><?php echo htmlspecialchars($anime['title']['romaji']); ?></h3>
                                <?php if ($anime['title']['english'] && $anime['title']['english'] != $anime['title']['romaji']): ?>
                                    <div class="trending-english"><?php echo htmlspecialchars($anime['title']['english']); ?></div>
                                <?php endif; ?>
                                <div class="trending-meta">
                                    <span class="trending-episodes">
                                        <i class="fas fa-tv"></i> <?php echo $anime['episodes'] ?? '?'; ?> eps
                                    </span>
                                    <span class="trending-popularity">
                                        <i class="fas fa-fire"></i> <?php echo number_format($anime['popularity']); ?>
                                    </span>
                                </div>
                                <div class="trending-genres">
                                    <?php foreach (array_slice($anime['genres'], 0, 3) as $genre): ?>
                                        <span class="genre-badge"><?php echo $genre; ?></span>
                                    <?php endforeach; ?>
                                </div>
                                <?php if (isset($anime['nextAiringEpisode'])): ?>
                                    <div class="trending-next-ep">
                                        <i class="fas fa-clock"></i> 
                                        Ep <?php echo $anime['nextAiringEpisode']['episode']; ?>: 
                                        <?php echo floor($anime['nextAiringEpisode']['timeUntilAiring'] / 86400); ?> hari lagi
                                    </div>
                                <?php endif; ?>
                                <button class="trending-detail-btn">
                                    Lihat Detail <i class="fas fa-arrow-right"></i>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="alert alert-warning">Gagal memuat data trending.</div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Sidebar: Popular Anime & Info -->
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-fire"></i> Anime Populer
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <?php if ($homeData && isset($homeData['data']['popular']['media'])): ?>
                            <?php foreach (array_slice($homeData['data']['popular']['media'], 0, 6) as $anime): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <img src="<?php echo $anime['coverImage']['large']; ?>" 
                                             width="40" height="56" class="rounded me-2" 
                                             alt="<?php echo htmlspecialchars($anime['title']['romaji']); ?>"
                                             style="object-fit: cover;">
                                        <div>
                                            <a href="detail.php?id=<?php echo $anime['id']; ?>" class="text-decoration-none">
                                                <small class="fw-bold"><?php echo htmlspecialchars($anime['title']['romaji']); ?></small>
                                            </a>
                                            <div>
                                                <small class="text-muted">
                                                    ⭐ <?php echo AniListAPI::formatScore($anime['averageScore']); ?>
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                    <span class="badge bg-primary rounded-pill"><?php echo $anime['episodes'] ?? '?'; ?> eps</span>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
            
            <div class="card mb-3 bg-primary text-white">
                <div class="card-body text-center">
                    <i class="fas fa-calendar-week fa-3x mb-2"></i>
                    <h5>Cek Jadwal Hari Ini</h5>
                    <p>Lihat anime apa saja yang tayang hari ini!</p>
                    <a href="schedule.php" class="btn btn-light btn-sm">Lihat Jadwal →</a>
                </div>
            </div>
            
            <div class="card mb-3 bg-warning text-dark">
                <div class="card-body text-center">
                    <i class="fas fa-star fa-3x mb-2"></i>
                    <h5>Upcoming Anime</h5>
                    <p>Anime yang akan datang musim ini!</p>
                    <a href="upcoming.php" class="btn btn-dark btn-sm">Lihat Semua →</a>
                </div>
            </div>
            
        </div>
    </div>
</div>

<style>
/* ========================================
   HERO SECTION SLIDESHOW (Tanpa Kontrol)
   ======================================== */

.hero-fullwidth {
    position: relative;
    width: 100%;
    min-height: 450px;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    margin-top: 0;
    margin-bottom: 40px;
}

/* Slideshow Container */
.hero-slideshow {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    width: 100%;
    height: 100%;
}

/* Slide */
.hero-slide {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    width: 100%;
    height: 100%;
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    opacity: 0;
    transition: opacity 1s ease-in-out;
    z-index: 0;
}

.hero-slide.active {
    opacity: 1;
    z-index: 1;
}

/* Overlay gelap */
.hero-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0.5) 100%);
    z-index: 2;
}

/* Container untuk konten hero */
.hero-container {
    position: relative;
    z-index: 5;
    width: 100%;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

/* Teks Hero */
.hero-text {
    text-align: center;
    color: white;
    padding: 60px 20px;
}

.hero-text h1 {
    font-size: 2.8rem;
    font-weight: 700;
    margin-bottom: 20px;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
    animation: fadeInDown 0.8s ease;
}

.hero-text .hero-lead {
    font-size: 1.2rem;
    margin-bottom: 20px;
    opacity: 0.95;
    animation: fadeInUp 0.8s ease;
}

.hero-text .hero-small {
    font-size: 0.9rem;
    opacity: 0.8;
    animation: fadeInUp 0.8s ease 0.2s both;
}

/* Animasi */
@keyframes fadeInDown {
    from {
        opacity: 0;
        transform: translateY(-30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* ========================================
   TRENDING 3 GRID STYLES
   ======================================== */

.trending-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    margin-bottom: 30px;
}

.trending-card {
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

.trending-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.12);
}

.trending-card-image {
    position: relative;
    overflow: hidden;
    aspect-ratio: 2/3;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.trending-card-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.4s ease;
}

.trending-card:hover .trending-card-image img {
    transform: scale(1.05);
}

.trending-score {
    position: absolute;
    top: 10px;
    left: 10px;
    background: rgba(0,0,0,0.7);
    backdrop-filter: blur(4px);
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 0.7rem;
    font-weight: 600;
    color: #ffd700;
    display: flex;
    align-items: center;
    gap: 4px;
    z-index: 2;
}

.trending-type {
    position: absolute;
    top: 10px;
    right: 10px;
    background: rgba(0,0,0,0.7);
    backdrop-filter: blur(4px);
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 0.65rem;
    font-weight: 600;
    color: white;
    z-index: 2;
}

.trending-card-info {
    padding: 12px;
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.trending-title {
    font-size: 0.85rem;
    font-weight: 700;
    color: #2c3e50;
    margin: 0;
    line-height: 1.3;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.trending-english {
    font-size: 0.65rem;
    color: #7f8c8d;
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.trending-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.65rem;
    color: #7f8c8d;
    margin: 4px 0;
}

.trending-genres {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
    margin: 4px 0;
}

.genre-badge {
    font-size: 0.55rem;
    background: #e9ecef;
    color: #2c3e50;
    padding: 2px 8px;
    border-radius: 20px;
}

.trending-next-ep {
    font-size: 0.6rem;
    color: #27ae60;
    background: #e8f8f5;
    padding: 4px 8px;
    border-radius: 12px;
    margin: 4px 0;
    text-align: center;
}

.trending-detail-btn {
    background: #3498db;
    border: none;
    color: white;
    padding: 6px 12px;
    border-radius: 25px;
    font-size: 0.65rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    width: 100%;
    margin-top: 6px;
}

.trending-detail-btn:hover {
    background: #2980b9;
    transform: translateY(-2px);
}

/* Responsive Grid */
@media (min-width: 992px) and (max-width: 1199px) {
    .trending-grid {
        gap: 16px;
    }
}

@media (max-width: 991px) and (min-width: 768px) {
    .trending-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
    }
}

@media (max-width: 767px) and (min-width: 576px) {
    .trending-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 14px;
    }
}

@media (max-width: 575px) {
    .trending-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
    }
    
    .trending-card-info {
        padding: 8px;
    }
    
    .trending-title {
        font-size: 0.7rem;
    }
}

/* Hero Section Responsive */
@media (max-width: 992px) {
    .hero-fullwidth {
        min-height: 380px;
    }
    
    .hero-text {
        padding: 50px 20px;
    }
    
    .hero-text h1 {
        font-size: 2.2rem;
    }
    
    .hero-text .hero-lead {
        font-size: 1rem;
    }
}

@media (max-width: 768px) {
    .hero-fullwidth {
        min-height: 320px;
    }
    
    .hero-text {
        padding: 40px 15px;
    }
    
    .hero-text h1 {
        font-size: 1.6rem;
    }
    
    .hero-text .hero-lead {
        font-size: 0.9rem;
    }
    
    .hero-text .hero-small {
        font-size: 0.7rem;
    }
}

@media (max-width: 480px) {
    .hero-fullwidth {
        min-height: 280px;
    }
    
    .hero-text {
        padding: 30px 15px;
    }
    
    .hero-text h1 {
        font-size: 1.3rem;
    }
    
    .hero-text .hero-lead {
        font-size: 0.8rem;
    }
}

@media (max-width: 991px) {
    .col-lg-4 {
        margin-top: 30px;
    }
}
</style>

<script>
// Slideshow variables
let slideIndex = 0;
let slides = [];
let slideInterval;

// Initialize slideshow
document.addEventListener('DOMContentLoaded', function() {
    slides = document.querySelectorAll('.hero-slide');
    
    if (slides.length > 0) {
        // Start slideshow
        startSlideshow();
        
        // Optional: Pause slideshow on hover (bisa dihapus jika tidak ingin)
        const heroSection = document.querySelector('.hero-fullwidth');
        if (heroSection) {
            heroSection.addEventListener('mouseenter', pauseSlideshow);
            heroSection.addEventListener('mouseleave', startSlideshow);
        }
    }
});

// Auto change slide
function changeSlide() {
    // Remove active class from current slide
    slides[slideIndex].classList.remove('active');
    
    // Move to next slide
    slideIndex = (slideIndex + 1) % slides.length;
    
    // Add active class to new slide
    slides[slideIndex].classList.add('active');
}

// Start slideshow
function startSlideshow() {
    if (slideInterval) clearInterval(slideInterval);
    slideInterval = setInterval(() => {
        changeSlide();
    }, 3000); // 4 detik
}

// Pause slideshow
function pauseSlideshow() {
    if (slideInterval) {
        clearInterval(slideInterval);
        slideInterval = null;
    }
}

// Make trending card clickable
document.querySelectorAll('.trending-card').forEach(card => {
    card.addEventListener('click', function(e) {
        if (e.target.closest('.trending-detail-btn')) {
            e.stopPropagation();
            return;
        }
        const animeId = this.getAttribute('onclick')?.match(/\d+/)?.[0];
        if (animeId) {
            window.location.href = 'detail.php?id=' + animeId;
        }
    });
});
</script>

<?php include 'includes/footer.php'; ?>