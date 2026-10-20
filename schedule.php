<?php
require_once 'config/anilist_api.php';
include 'includes/header.php';

$api = new AniListAPI();

// Ambil lebih banyak data anime yang sedang tayang (5 halaman)
$allAiringAnime = [];
for ($page = 1; $page <= 5; $page++) {
    $airingData = $api->getAiringAnime($page, 50);
    if ($airingData && isset($airingData['data']['Page']['media'])) {
        $allAiringAnime = array_merge($allAiringAnime, $airingData['data']['Page']['media']);
    }
    sleep(1);
}

// Urutan hari dalam seminggu
$daysOrder = [
    'monday' => 'Senin',
    'tuesday' => 'Selasa',
    'wednesday' => 'Rabu',
    'thursday' => 'Kamis',
    'friday' => 'Jumat',
    'saturday' => 'Sabtu',
    'sunday' => 'Minggu'
];

// Inisialisasi array untuk setiap hari
$scheduleByDay = [];
foreach ($daysOrder as $dayKey => $dayName) {
    $scheduleByDay[$dayKey] = [];
}

// Proses semua anime yang sedang tayang
if (count($allAiringAnime) > 0) {
    foreach ($allAiringAnime as $anime) {
        if ($anime['status'] !== 'RELEASING') {
            continue;
        }
        
        if (!isset($anime['nextAiringEpisode']) || !$anime['nextAiringEpisode']) {
            continue;
        }
        
        $nextEp = $anime['nextAiringEpisode'];
        $airingTimestamp = $nextEp['airingAt'];
        
        $dayEnglish = date('l', $airingTimestamp);
        $dayKey = strtolower($dayEnglish);
        
        if (!isset($scheduleByDay[$dayKey])) {
            if (isset($anime['broadcast']['day'])) {
                $broadcastDay = strtolower($anime['broadcast']['day']);
                if (isset($scheduleByDay[$broadcastDay])) {
                    $dayKey = $broadcastDay;
                }
            }
        }
        
        if (!isset($scheduleByDay[$dayKey])) {
            continue;
        }
        
        $exists = false;
        foreach ($scheduleByDay[$dayKey] as $existing) {
            if ($existing['id'] == $anime['id']) {
                $exists = true;
                break;
            }
        }
        
        if (!$exists) {
            $timeUntil = $nextEp['timeUntilAiring'];
            $days = floor($timeUntil / 86400);
            $hours = floor(($timeUntil % 86400) / 3600);
            
            $scheduleByDay[$dayKey][] = [
                'id' => $anime['id'],
                'title' => $anime['title']['romaji'],
                'english_title' => $anime['title']['english'] ?? '',
                'image' => $anime['coverImage']['large'],
                'episode' => $nextEp['episode'],
                'airing_at' => $airingTimestamp,
                'days_until' => $days,
                'hours_until' => $hours,
                'score' => $anime['averageScore'] ? AniListAPI::formatScore($anime['averageScore']) : 'N/A',
                'format' => $anime['format'] ?? 'TV',
                'genres' => array_slice($anime['genres'], 0, 2)
            ];
        }
    }
}

// Urutkan setiap hari berdasarkan waktu tayang
foreach ($scheduleByDay as $dayKey => $daySchedule) {
    usort($scheduleByDay[$dayKey], function($a, $b) {
        return $a['airing_at'] - $b['airing_at'];
    });
}

$totalScheduled = 0;
foreach ($scheduleByDay as $daySchedule) {
    $totalScheduled += count($daySchedule);
}
?>

<h1 class="mb-4">
    <i class="fas fa-calendar-week text-primary"></i> 
    Jadwal Tayang Episode
</h1>

<div class="alert alert-info mb-4">
    <i class="fas fa-info-circle"></i> 
    <strong>Info Jadwal:</strong> Menampilkan jadwal episode terbaru dari <strong><?php echo count($allAiringAnime); ?></strong> anime yang sedang tayang.
    <br><small class="text-muted">
        📊 Total <?php echo $totalScheduled; ?> anime terjadwal | 
        💡 Klik badge hari untuk langsung scroll
    </small>
</div>

<!-- Tampilkan ringkasan hari - BISA DIKLIK -->
<div class="schedule-summary mb-4">
    <div class="d-flex flex-wrap gap-2">
        <?php foreach ($daysOrder as $dayKey => $dayName): 
            $count = count($scheduleByDay[$dayKey]);
        ?>
            <div class="day-summary-badge <?php echo $count > 0 ? 'has-schedule' : 'no-schedule'; ?>" 
                 data-day="<?php echo $dayKey; ?>"
                 onclick="scrollToDay('<?php echo $dayKey; ?>')">
                <i class="fas <?php echo $count > 0 ? 'fa-check-circle' : 'fa-clock'; ?>"></i>
                <span><?php echo $dayName; ?></span>
                <?php if ($count > 0): ?>
                    <span class="count-badge"><?php echo $count; ?></span>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php if ($totalScheduled == 0): ?>
    <div class="alert alert-warning text-center">
        <i class="fas fa-clock fa-3x mb-3"></i>
        <h4>Sedang mengambil data jadwal...</h4>
        <p>Silakan refresh halaman atau coba lagi nanti.</p>
    </div>
<?php else: ?>

<div class="schedule-container">
    <?php foreach ($daysOrder as $dayKey => $dayName): 
        $daySchedule = $scheduleByDay[$dayKey];
        $isToday = (strtolower(date('l', time())) == $dayKey);
    ?>
        <div id="day-<?php echo $dayKey; ?>" class="schedule-day-card <?php echo $isToday ? 'today-highlight' : ''; ?>">
            <div class="schedule-day-header">
                <div class="day-name">
                    <?php if ($isToday): ?>
                        <i class="fas fa-calendar-day text-primary"></i>
                    <?php else: ?>
                        <i class="fas fa-calendar-alt"></i>
                    <?php endif; ?>
                    <h3><?php echo $dayName; ?></h3>
                    <?php if ($isToday): ?>
                        <span class="today-badge">Hari Ini</span>
                    <?php endif; ?>
                </div>
                <div class="episode-count">
                    <span class="badge bg-primary"><?php echo count($daySchedule); ?> Anime</span>
                </div>
            </div>
            
            <div class="schedule-day-content">
                <?php if (empty($daySchedule)): ?>
                    <div class="no-schedule">
                        <i class="fas fa-clock"></i>
                        <p>Tidak ada jadwal anime untuk hari <?php echo $dayName; ?></p>
                    </div>
                <?php else: ?>
                    <div class="anime-list">
                        <?php foreach ($daySchedule as $anime): ?>
                            <div class="anime-schedule-item" onclick="window.location.href='detail.php?id=<?php echo $anime['id']; ?>'">
                                <!-- GAMBAR DI ATAS -->
                                <div class="anime-schedule-image">
                                    <img src="<?php echo $anime['image']; ?>" 
                                         alt="<?php echo htmlspecialchars($anime['title']); ?>"
                                         onerror="this.src='https://via.placeholder.com/200x280?text=No+Image'">
                                    <div class="episode-badge-schedule">
                                        Ep <?php echo $anime['episode']; ?>
                                    </div>
                                    <?php if ($anime['score'] != 'N/A'): ?>
                                    <div class="score-badge-schedule">
                                        ⭐ <?php echo $anime['score']; ?>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- INFO DI BAWAH GAMBAR -->
                                <div class="anime-schedule-info">
                                    <h4 class="anime-schedule-title">
                                        <?php echo htmlspecialchars($anime['title']); ?>
                                    </h4>
                                    <?php if ($anime['english_title'] && $anime['english_title'] != $anime['title']): ?>
                                        <div class="anime-schedule-english">
                                            <?php echo htmlspecialchars($anime['english_title']); ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="anime-schedule-meta">
                                        <span class="meta-item">
                                            <i class="fas fa-tv"></i> <?php echo $anime['format']; ?>
                                        </span>
                                        <?php foreach ($anime['genres'] as $genre): ?>
                                            <span class="genre-tag-small"><?php echo $genre; ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                    
                                    <div class="countdown-info">
                                        <i class="fas fa-hourglass-half"></i> 
                                        Episode <?php echo $anime['episode']; ?>: 
                                        <strong><?php echo $anime['days_until'] . " hari " . $anime['hours_until'] . " jam"; ?></strong>
                                    </div>
                                    
                                    <a href="detail.php?id=<?php echo $anime['id']; ?>" class="btn-schedule-detail">
                                        <i class="fas fa-info-circle"></i> Detail
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php endif; ?>

<style>
/* ========================================
   SCHEDULE PAGE STYLES - VERTICAL CARD (GAMBAR DI ATAS)
   ======================================== */

.schedule-container {
    display: flex;
    flex-direction: column;
    gap: 24px;
    margin-bottom: 40px;
}

/* Badge clickable style */
.day-summary-badge {
    cursor: pointer;
    transition: all 0.3s ease;
}

.day-summary-badge:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.day-summary-badge.has-schedule:hover {
    background: #2980b9;
}

.schedule-summary {
    background: white;
    padding: 15px 20px;
    border-radius: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    border: 1px solid #e9ecef;
}

.day-summary-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 6px 14px;
    border-radius: 30px;
    font-size: 0.8rem;
    font-weight: 500;
}

.day-summary-badge.has-schedule {
    background: #3498db;
    color: white;
}

.day-summary-badge.no-schedule {
    background: #e9ecef;
    color: #7f8c8d;
}

.day-summary-badge .count-badge {
    background: rgba(255,255,255,0.3);
    padding: 2px 8px;
    border-radius: 20px;
    font-size: 0.7rem;
    font-weight: 600;
    margin-left: 4px;
}

.schedule-day-card {
    background: white;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    border: 1px solid #e9ecef;
    scroll-margin-top: 80px;
}

.schedule-day-card.today-highlight {
    border: 2px solid #3498db;
    box-shadow: 0 4px 15px rgba(52,152,219,0.15);
}

.schedule-day-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 20px;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-bottom: 1px solid #e9ecef;
}

.day-name {
    display: flex;
    align-items: center;
    gap: 10px;
}

.day-name i {
    font-size: 1.2rem;
    color: #3498db;
}

.day-name h3 {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 600;
    color: #2c3e50;
}

.today-badge {
    background: #3498db;
    color: white;
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 0.65rem;
    font-weight: 500;
}

.episode-count .badge {
    padding: 5px 10px;
    font-size: 0.7rem;
}

.schedule-day-content {
    padding: 16px;
}

.no-schedule {
    text-align: center;
    padding: 30px;
    color: #7f8c8d;
}

.no-schedule i {
    font-size: 2rem;
    margin-bottom: 10px;
    opacity: 0.5;
}

/* ========================================
   ANIME LIST GRID - VERTICAL CARD (GAMBAR DI ATAS)
   ======================================== */
.anime-list {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
}

/* Anime Schedule Item - VERTICAL LAYOUT */
.anime-schedule-item {
    display: flex;
    flex-direction: column;
    background: #f8f9fa;
    border-radius: 16px;
    overflow: hidden;
    transition: all 0.3s ease;
    cursor: pointer;
    height: 100%;
}

.anime-schedule-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.12);
    background: #ebf5ff;
}

/* GAMBAR DI ATAS */
.anime-schedule-image {
    position: relative;
    width: 100%;
    aspect-ratio: 2/3;
    overflow: hidden;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.anime-schedule-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.anime-schedule-item:hover .anime-schedule-image img {
    transform: scale(1.05);
}

.episode-badge-schedule {
    position: absolute;
    bottom: 10px;
    right: 10px;
    background: #3498db;
    color: white;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 0.7rem;
    font-weight: 600;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    z-index: 2;
}

.score-badge-schedule {
    position: absolute;
    top: 10px;
    right: 10px;
    background: #f39c12;
    color: #2c3e50;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 0.7rem;
    font-weight: 600;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    z-index: 2;
}

/* INFO DI BAWAH GAMBAR */
.anime-schedule-info {
    padding: 12px;
    display: flex;
    flex-direction: column;
    gap: 6px;
    flex: 1;
}

.anime-schedule-title {
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

.anime-schedule-english {
    font-size: 0.65rem;
    color: #7f8c8d;
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.anime-schedule-meta {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 4px;
    margin: 4px 0;
}

.meta-item {
    display: inline-flex;
    align-items: center;
    gap: 3px;
    font-size: 0.6rem;
    color: #e67e22;
    background: #fef9e7;
    padding: 2px 6px;
    border-radius: 20px;
}

.genre-tag-small {
    font-size: 0.55rem;
    background: #e9ecef;
    color: #2c3e50;
    padding: 2px 6px;
    border-radius: 20px;
}

.countdown-info {
    font-size: 0.65rem;
    color: #27ae60;
    background: #e8f8f5;
    padding: 5px 8px;
    border-radius: 12px;
    margin: 4px 0;
    text-align: center;
}

.countdown-info i {
    margin-right: 4px;
}

.btn-schedule-detail {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    background: #3498db;
    border: none;
    color: white;
    padding: 6px 12px;
    border-radius: 25px;
    font-size: 0.65rem;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.3s ease;
    margin-top: 4px;
    width: 100%;
}

.btn-schedule-detail:hover {
    background: #2980b9;
    transform: translateY(-2px);
    color: white;
}

/* ========================================
   RESPONSIVE GRID
   ======================================== */

/* Desktop Large (1400px+) - 5 grid */
@media (min-width: 1400px) {
    .anime-list {
        grid-template-columns: repeat(5, 1fr);
        gap: 20px;
    }
}

/* Desktop (992px - 1399px) - 4 grid */
@media (min-width: 992px) and (max-width: 1399px) {
    .anime-list {
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
    }
}

/* Tablet (768px - 991px) - 3 grid */
@media (max-width: 991px) and (min-width: 768px) {
    .anime-list {
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
    }
}

/* Mobile (576px - 767px) - 2 grid */
@media (max-width: 767px) and (min-width: 576px) {
    .anime-list {
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
    }
    
    .anime-schedule-title {
        font-size: 0.75rem;
    }
    
    .meta-item, .genre-tag-small {
        font-size: 0.5rem;
        padding: 2px 4px;
    }
    
    .countdown-info {
        font-size: 0.55rem;
    }
    
    .btn-schedule-detail {
        font-size: 0.6rem;
        padding: 5px 8px;
    }
}

/* Small Mobile (≤575px) - 2 grid */
@media (max-width: 575px) {
    .anime-list {
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
    }
    
    .schedule-day-content {
        padding: 12px;
    }
    
    .anime-schedule-info {
        padding: 8px;
    }
    
    .anime-schedule-title {
        font-size: 0.7rem;
    }
    
    .anime-schedule-english {
        font-size: 0.55rem;
    }
    
    .meta-item, .genre-tag-small {
        font-size: 0.45rem;
        padding: 1px 3px;
    }
    
    .countdown-info {
        font-size: 0.5rem;
        padding: 3px 6px;
    }
    
    .btn-schedule-detail {
        font-size: 0.55rem;
        padding: 4px 6px;
    }
    
    .episode-badge-schedule, .score-badge-schedule {
        font-size: 0.55rem;
        padding: 2px 6px;
    }
}

/* ========================================
   MOBILE MENU SCROLL
   ======================================== */
@media (max-width: 768px) {
    .schedule-summary .d-flex {
        flex-wrap: nowrap;
        overflow-x: auto;
        padding-bottom: 8px;
        -webkit-overflow-scrolling: touch;
    }
    
    .day-summary-badge {
        flex-shrink: 0;
    }
}
</style>

<script>
// Fungsi untuk scroll ke hari yang dipilih
function scrollToDay(dayKey) {
    const dayId = 'day-' + dayKey;
    const targetElement = document.getElementById(dayId);
    
    if (targetElement) {
        const offset = 80;
        const elementPosition = targetElement.getBoundingClientRect().top;
        const offsetPosition = elementPosition + window.pageYOffset - offset;
        
        window.scrollTo({
            top: offsetPosition,
            behavior: 'smooth'
        });
        
        targetElement.style.transition = 'all 0.3s ease';
        targetElement.style.boxShadow = '0 0 0 3px #3498db, 0 4px 15px rgba(0,0,0,0.1)';
        setTimeout(() => {
            targetElement.style.boxShadow = '';
        }, 1500);
        
        history.pushState(null, null, '#' + dayKey);
    }
}

// Jika URL memiliki hash, scroll otomatis
document.addEventListener('DOMContentLoaded', function() {
    const hash = window.location.hash.substring(1);
    const validDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
    
    if (hash && validDays.includes(hash)) {
        setTimeout(function() {
            scrollToDay(hash);
        }, 500);
    }
});

// Make card clickable
document.querySelectorAll('.anime-schedule-item').forEach(card => {
    card.addEventListener('click', function(e) {
        if (e.target.closest('.btn-schedule-detail')) {
            e.stopPropagation();
            return;
        }
        const link = this.querySelector('.btn-schedule-detail');
        if (link) {
            window.location.href = link.getAttribute('href');
        }
    });
});
</script>

<?php include 'includes/footer.php'; ?>