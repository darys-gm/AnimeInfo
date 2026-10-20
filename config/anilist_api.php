<?php
class AniListAPI {
    private $endpoint = "https://graphql.anilist.co";
    private $cache = [];
    private $cacheTime = 1800; // 30 menit cache
    
    // === QUERY UNTUK HOME PAGE (Trending & Popular) ===
    private $homeQuery = '
    query ($page: Int, $perPage: Int) {
        trending: Page(page: $page, perPage: $perPage) {
            media(sort: TRENDING_DESC, type: ANIME) {
                id
                title {
                    romaji
                    english
                }
                coverImage {
                    large
                    medium
                }
                bannerImage
                description
                episodes
                status
                averageScore
                popularity
                format
                genres
                nextAiringEpisode {
                    airingAt
                    episode
                    timeUntilAiring
                }
                startDate {
                    year
                    month
                    day
                }
            }
        }
        popular: Page(page: $page, perPage: $perPage) {
            media(sort: POPULARITY_DESC, type: ANIME) {
                id
                title {
                    romaji
                    english
                }
                coverImage {
                    large
                }
                averageScore
                episodes
                format
                genres
            }
        }
    }';
    
    // === QUERY UNTUK ANIME SEDANG TAYANG ===
    private $airingQuery = '
    query ($page: Int, $perPage: Int, $sort: [MediaSort]) {
        Page(page: $page, perPage: $perPage) {
            pageInfo {
                total
                currentPage
                lastPage
                hasNextPage
            }
            media(status: RELEASING, sort: $sort, type: ANIME) {
                id
                title {
                    romaji
                    english
                    native
                }
                coverImage {
                    large
                    medium
                }
                bannerImage
                description
                episodes
                status
                averageScore
                popularity
                format
                genres
                nextAiringEpisode {
                    airingAt
                    timeUntilAiring
                    episode
                }
                startDate {
                    year
                    month
                    day
                }
                studios(isMain: true) {
                    nodes {
                        name
                    }
                }
            }
        }
    }';
    
    // === QUERY UNTUK PENCARIAN ===
    private $searchQuery = '
    query ($search: String, $page: Int, $perPage: Int) {
        Page(page: $page, perPage: $perPage) {
            pageInfo {
                total
                currentPage
                lastPage
                hasNextPage
            }
            media(search: $search, type: ANIME, sort: POPULARITY_DESC) {
                id
                title {
                    romaji
                    english
                    native
                }
                coverImage {
                    large
                    medium
                }
                description
                episodes
                status
                averageScore
                popularity
                format
                genres
                startDate {
                    year
                    month
                    day
                }
            }
        }
    }';
    
    // === QUERY UNTUK JADWAL (Next 7 days) ===
    private $scheduleQuery = '
    query ($page: Int, $perPage: Int, $airingAtGreater: Int, $airingAtLesser: Int) {
        Page(page: $page, perPage: $perPage) {
            pageInfo {
                total
                currentPage
                lastPage
                hasNextPage
            }
            airingSchedules(airingAt_greater: $airingAtGreater, airingAt_lesser: $airingAtLesser, sort: TIME) {
                id
                episode
                airingAt
                timeUntilAiring
                media {
                    id
                    title {
                        romaji
                        english
                    }
                    coverImage {
                        large
                        medium
                    }
                    episodes
                    format
                    status
                }
            }
        }
    }';
    
    // === QUERY DETAIL ANIME LENGKAP ===
    private $detailQuery = '
    query ($id: Int) {
        Media(id: $id, type: ANIME) {
            id
            title {
                romaji
                english
                native
            }
            coverImage {
                extraLarge
                large
                medium
                color
            }
            bannerImage
            description
            episodes
            duration
            status
            averageScore
            meanScore
            popularity
            favourites
            format
            genres
            tags {
                name
                rank
                description
            }
            studios(isMain: true) {
                nodes {
                    name
                }
            }
            staff {
                edges {
                    node {
                        name {
                            full
                        }
                    }
                    role
                }
            }
            characters(sort: ROLE) {
                edges {
                    node {
                        id
                        name {
                            full
                        }
                        image {
                            medium
                        }
                    }
                    role
                }
            }
            relations {
                edges {
                    node {
                        id
                        title {
                            romaji
                        }
                        coverImage {
                            medium
                        }
                        type
                        format
                    }
                    relationType
                }
            }
            recommendations(sort: RATING_DESC) {
                nodes {
                    mediaRecommendation {
                        id
                        title {
                            romaji
                        }
                        coverImage {
                            medium
                        }
                        averageScore
                    }
                }
            }
            startDate {
                year
                month
                day
            }
            endDate {
                year
                month
                day
            }
            nextAiringEpisode {
                airingAt
                episode
                timeUntilAiring
            }
            airingSchedule(perPage: 10) {
                nodes {
                    episode
                    airingAt
                }
            }
            trailer {
                id
                site
                thumbnail
            }
            externalLinks {
                url
                site
            }
        }
    }';
    
    // === QUERY UNTUK UPCOMING ANIME ===
    private $upcomingQuery = '
    query ($page: Int, $perPage: Int) {
        Page(page: $page, perPage: $perPage) {
            pageInfo {
                total
                currentPage
                lastPage
                hasNextPage
            }
            media(status: NOT_YET_RELEASED, sort: POPULARITY_DESC, type: ANIME) {
                id
                title {
                    romaji
                    english
                }
                coverImage {
                    large
                    medium
                }
                description
                episodes
                status
                averageScore
                popularity
                format
                genres
                startDate {
                    year
                    month
                    day
                }
            }
        }
    }';
    
    // === 1. GET HOME PAGE DATA (Trending & Popular) ===
    public function getHomeData($page = 1, $perPage = 12) {
        $variables = [
            'page' => $page,
            'perPage' => $perPage
        ];
        
        return $this->makeRequest($this->homeQuery, $variables);
    }
    
    // === 2. GET ANIME SEDANG TAYANG ===
    public function getAiringAnime($page = 1, $perPage = 24) {
        $variables = [
            'page' => $page,
            'perPage' => $perPage,
            'sort' => 'POPULARITY_DESC'
        ];
        
        return $this->makeRequest($this->airingQuery, $variables);
    }
    
    // === 3. PENCARIAN ANIME ===
    public function searchAnime($query, $page = 1, $perPage = 24) {
        if (empty($query)) return null;
        
        $variables = [
            'search' => $query,
            'page' => $page,
            'perPage' => $perPage
        ];
        
        $result = $this->makeRequest($this->searchQuery, $variables);
        
        if ($result && isset($result['data']['Page']['media'])) {
            return [
                'data' => $result['data']['Page']['media'],
                'pagination' => $result['data']['Page']['pageInfo']
            ];
        }
        
        return null;
    }
    
    // === 4. JADWAL TAYANG (Next 7 days) ===
    public function getSchedule($days = 7, $page = 1, $perPage = 50) {
        $now = time();
        $endTime = $now + ($days * 86400);
        
        $variables = [
            'page' => $page,
            'perPage' => $perPage,
            'airingAtGreater' => $now,
            'airingAtLesser' => $endTime
        ];
        
        $result = $this->makeRequest($this->scheduleQuery, $variables);
        
        if ($result && isset($result['data']['Page']['airingSchedules'])) {
            return [
                'data' => $result['data']['Page']['airingSchedules'],
                'pagination' => $result['data']['Page']['pageInfo']
            ];
        }
        
        return ['data' => [], 'pagination' => []];
    }
    
    // === 5. DETAIL ANIME ===
    public function getAnimeDetail($id) {
        $variables = ['id' => $id];
        
        $result = $this->makeRequest($this->detailQuery, $variables);
        
        if ($result && isset($result['data']['Media'])) {
            return $result['data']['Media'];
        }
        
        return null;
    }
    
    // === 6. UPCOMING ANIME ===
    public function getUpcomingAnime($page = 1, $perPage = 24) {
        $variables = [
            'page' => $page,
            'perPage' => $perPage
        ];
        
        $result = $this->makeRequest($this->upcomingQuery, $variables);
        
        if ($result && isset($result['data']['Page']['media'])) {
            return [
                'data' => $result['data']['Page']['media'],
                'pagination' => $result['data']['Page']['pageInfo']
            ];
        }
        
        return ['data' => [], 'pagination' => []];
    }
    
    // === REQUEST HANDLER ===
    private function makeRequest($query, $variables) {
        // Cek cache
        $cacheKey = md5($query . json_encode($variables));
        if (isset($this->cache[$cacheKey]) && (time() - $this->cache[$cacheKey]['time']) < $this->cacheTime) {
            return $this->cache[$cacheKey]['data'];
        }
        
        $payload = json_encode([
            'query' => $query,
            'variables' => $variables
        ]);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json',
            'User-Agent: AnimeWebsite/3.0'
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);
        
        if ($httpCode == 200 && $response) {
            $data = json_decode($response, true);
            
            // Cek error GraphQL
            if (isset($data['errors'])) {
                error_log("GraphQL Error: " . json_encode($data['errors']));
                return null;
            }
            
            // Simpan cache
            $this->cache[$cacheKey] = [
                'data' => $data,
                'time' => time()
            ];
            
            return $data;
        }
        
        if ($curlError) {
            error_log("AniList API Error: " . $curlError);
        }
        
        return null;
    }
    
    // === UTILITY: Format waktu tayang berikutnya ===
    public static function formatTimeUntilAiring($seconds) {
        if (!$seconds) return 'Soon';
        
        $days = floor($seconds / 86400);
        $hours = floor(($seconds % 86400) / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        
        if ($days > 0) {
            return "$days hari $hours jam";
        } elseif ($hours > 0) {
            return "$hours jam $minutes menit";
        } elseif ($minutes > 0) {
            return "$minutes menit";
        } else {
            return "Segera tayang";
        }
    }
    
    // === UTILITY: Format score (AniList score 0-100) ===
    public static function formatScore($score) {
        if (!$score) return 'N/A';
        return number_format($score / 10, 1);
    }
    
    // === UTILITY: Format tanggal ===
    public static function formatDate($timestamp) {
        if (!$timestamp) return 'TBA';
        return date('d M Y', $timestamp);
    }
    
    // === UTILITY: Strip HTML dari description ===
    public static function cleanDescription($html) {
        if (!$html) return 'Deskripsi tidak tersedia.';
        $text = strip_tags($html);
        $text = preg_replace('/\s+/', ' ', $text);
        return trim($text);
    }
}
?>