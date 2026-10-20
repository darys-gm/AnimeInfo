-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Waktu pembuatan: 20 Okt 2026 pada 11.49
-- Versi server: 8.0.30
-- Versi PHP: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Basis data: `anime_db`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `anime_genres`
--

CREATE TABLE `anime_genres` (
  `id` int NOT NULL,
  `genre_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `anime_genres`
--

INSERT INTO `anime_genres` (`id`, `genre_name`) VALUES
(1, 'Action'),
(2, 'Adventure'),
(3, 'Comedy'),
(4, 'Drama'),
(5, 'Fantasy'),
(6, 'Horror'),
(15, 'Mecha'),
(7, 'Mystery'),
(14, 'Psychological'),
(8, 'Romance'),
(9, 'Sci-Fi'),
(10, 'Slice of Life'),
(11, 'Sports'),
(13, 'Supernatural'),
(12, 'Thriller');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `avatar` varchar(255) DEFAULT 'default-avatar.png',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `avatar`, `created_at`, `updated_at`) VALUES
(1, 'haidar', 'haidarlwijaya12@gmail.com', '$2y$10$5njyO.ILZTmEJfaQ59pOfeNufgQoeO1dnpUCd8SxmFcI8oHmyiBha', 'default-avatar.png', '2026-06-07 07:42:39', '2026-06-07 07:42:39'),
(2, 'darys', 'darys@gmail.com', '$2y$10$DH3MHP/qmLQsWcrdXgNt4ewdeZ.s7zGG0U2gBNk3fdpLSrXf60rYm', 'default-avatar.png', '2026-06-07 07:44:46', '2026-06-07 07:44:46');

-- --------------------------------------------------------

--
-- Struktur dari tabel `user_anime_list`
--

CREATE TABLE `user_anime_list` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `anime_id` int NOT NULL,
  `anime_title` varchar(255) NOT NULL,
  `anime_image` varchar(500) DEFAULT NULL,
  `anime_score` decimal(3,1) DEFAULT NULL,
  `anime_status` enum('watching','completed','planned','dropped','on_hold') DEFAULT 'planned',
  `anime_progress` int DEFAULT '0',
  `anime_total_episodes` int DEFAULT '0',
  `added_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `user_anime_list`
--

INSERT INTO `user_anime_list` (`id`, `user_id`, `anime_id`, `anime_title`, `anime_image`, `anime_score`, `anime_status`, `anime_progress`, `anime_total_episodes`, `added_at`, `updated_at`) VALUES
(1, 1, 182205, 'Tensei Shitara Slime Datta Ken 4th Season', 'https://s4.anilist.co/file/anilistcdn/media/anime/cover/medium/bx182205-q2AeO1owuQbO.jpg', 8.1, 'planned', 0, NULL, '2026-06-07 07:58:24', '2026-06-07 07:58:24'),
(2, 2, 170019, 'Otonari no Tenshi-sama ni Itsunomanika Dame Ningen ni Sareteita Ken 2nd Season', 'https://s4.anilist.co/file/anilistcdn/media/anime/cover/medium/bx170019-djbN0b934MhE.jpg', 7.6, 'planned', 0, 12, '2026-06-07 13:12:35', '2026-06-07 13:12:35'),
(3, 2, 169580, 'Class de 2-banme ni Kawaii Onnanoko to Tomodachi ni Natta', 'https://s4.anilist.co/file/anilistcdn/media/anime/cover/medium/bx169580-nXxpmqu6UVux.jpg', 8.0, 'planned', 0, 12, '2026-06-07 13:25:48', '2026-06-07 13:25:48'),
(4, 2, 143338, 'Otonari no Tenshi-sama ni Itsunomanika Dame Ningen ni Sareteita Ken', 'https://s4.anilist.co/file/anilistcdn/media/anime/cover/medium/bx143338-zhyDVYgEzsm5.png', 7.8, 'completed', 12, 12, '2026-06-07 13:26:53', '2026-06-07 13:26:53'),
(5, 2, 197824, 'Isekai Nonbiri Nouka 2', 'https://s4.anilist.co/file/anilistcdn/media/anime/cover/medium/bx197824-k9Uyef8g49hB.png', 7.3, 'watching', 9, 12, '2026-06-07 13:28:48', '2026-06-07 13:28:48');

--
-- Indeks untuk tabel yang dibuang
--

--
-- Indeks untuk tabel `anime_genres`
--
ALTER TABLE `anime_genres`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `genre_name` (`genre_name`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indeks untuk tabel `user_anime_list`
--
ALTER TABLE `user_anime_list`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_anime` (`user_id`,`anime_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_anime_status` (`anime_status`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `anime_genres`
--
ALTER TABLE `anime_genres`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `user_anime_list`
--
ALTER TABLE `user_anime_list`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `user_anime_list`
--
ALTER TABLE `user_anime_list`
  ADD CONSTRAINT `user_anime_list_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
