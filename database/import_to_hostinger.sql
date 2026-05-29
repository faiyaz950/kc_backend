-- KarbalaConnect: Local → Hostinger (phpMyAdmin)
-- Database: u163472436_kc (select in phpMyAdmin before Import)
-- Run AFTER: php artisan migrate --force on server

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';

-- Clear existing app data (keeps migrations table)
TRUNCATE TABLE `anjuman_tracks`;
TRUNCATE TABLE `tracks`;
TRUNCATE TABLE `personal_access_tokens`;
TRUNCATE TABLE `anjumans`;
TRUNCATE TABLE `reciters`;
TRUNCATE TABLE `users`;

-- 1. Users (admin login works with same password as local)
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `favorites`, `recently_played`, `is_admin`, `created_at`, `updated_at`) VALUES
(1,'Admin','faiyazmujtaba72@gmail.com',NULL,'$2y$12$SXiQHb57F8Ovqn6HfMClweTTj2d21D2/dnseeKhPJCZtjsC27ZCsu',NULL,'[]','[]',1,'2026-05-24 16:04:24','2026-05-24 16:04:24'),
(2,'Test User','user@karbalaconnect.com',NULL,'$2y$12$68Jo.mVLpmiJ/T5EdVkXze7ykYMpsxn9XRG886gk.gkp4wPD1ZyIC',NULL,'[\"1\"]','[\"1\", \"at_1\", \"at_2\"]',0,'2026-05-24 16:35:56','2026-05-24 20:52:26');

-- 2. Reciters
INSERT INTO `reciters` (`id`, `name`, `bio`, `image_url`, `categories`, `languages`, `total_tracks`, `is_verified`, `created_at`, `updated_at`) VALUES
(1,'Mir Hasan Mir','Famous Noha khwan',NULL,'[\"noha\"]','[\"Urdu\", \"Punjabi\"]',100,1,'2026-05-24 16:16:37','2026-05-24 16:16:37'),
(2,'Hosein Taheri',NULL,'https://res.cloudinary.com/dsrxoq9es/image/upload/v1779639506/karbalaconnect/reciters/u5n5zbo0qtpxjl1h1d85.jpg','[\"noha\"]','[\"Farsi\"]',0,1,'2026-05-24 16:18:27','2026-05-24 16:18:27');

-- 3. Tracks
INSERT INTO `tracks` (`id`, `title`, `category`, `reciter_id`, `reciter_name`, `language`, `occasion`, `audio_url`, `image_url`, `duration`, `play_count`, `is_featured`, `lyrics`, `created_at`, `updated_at`) VALUES
(1,'حسین طاهری _ نماهنگ صل علی خون خدا _ محرم 1404_ Hosein Taheri','noha',2,'Hosein Taheri','Farsi','Muharram','https://res.cloudinary.com/dsrxoq9es/video/upload/v1779639561/karbalaconnect/tracks/noha/ekymbglomajszhevslnl.mp3','https://res.cloudinary.com/dsrxoq9es/image/upload/v1779639567/karbalaconnect/covers/onzdrmpmqsmmtv6kmkkv.png',240,6,1,NULL,'2026-05-24 16:16:37','2026-05-25 05:16:05');

-- 4. Anjumans
INSERT INTO `anjumans` (`id`, `name`, `city`, `bio`, `image_url`, `is_verified`, `total_tracks`, `created_at`, `updated_at`) VALUES
(1,'Anjuman e Panjatani','Lucknow','l.ieqielfhlekfn','https://res.cloudinary.com/dsrxoq9es/image/upload/v1779653453/anjumans/a4iykbv0qwzxxau3op77.png',0,1,'2026-05-24 20:10:54','2026-05-24 20:12:01'),
(2,'anjuman e abbasiya','Patna','uigugiugiku','https://res.cloudinary.com/dsrxoq9es/image/upload/v1779653603/anjumans/g8bgkhnz1ihrsq1j4wby.jpg',0,1,'2026-05-24 20:13:23','2026-05-24 20:14:25'),
(3,'Anjuman e HAIDERY','Lucknow','fewgrehrhmjrtetw',NULL,1,0,'2026-05-24 20:29:38','2026-05-24 20:29:38');

-- 5. Anjuman tracks
INSERT INTO `anjuman_tracks` (`id`, `anjuman_id`, `title`, `audio_url`, `image_url`, `duration`, `play_count`, `occasion`, `created_at`, `updated_at`) VALUES
(1,1,'anjumannn','https://res.cloudinary.com/dsrxoq9es/video/upload/v1779653515/anjuman_tracks/rhkeuq04jv8pdufc5z9z.mp3','https://res.cloudinary.com/dsrxoq9es/image/upload/v1779653520/anjuman_tracks/likrgjru6km27ffmal0m.png','02:21',0,'Muharram','2026-05-24 20:12:01','2026-05-24 20:12:01'),
(2,2,'anjumannannana','https://res.cloudinary.com/dsrxoq9es/video/upload/v1779653659/anjuman_tracks/hcgzwlwvnuhe7lahtsqo.mp3','https://res.cloudinary.com/dsrxoq9es/image/upload/v1779653665/anjuman_tracks/mzhyphupcdpsauvfy98r.png','03:04',0,'Safar','2026-05-24 20:14:25','2026-05-24 20:14:25');

-- Reset auto-increment IDs
ALTER TABLE `users` AUTO_INCREMENT = 3;
ALTER TABLE `reciters` AUTO_INCREMENT = 3;
ALTER TABLE `tracks` AUTO_INCREMENT = 2;
ALTER TABLE `anjumans` AUTO_INCREMENT = 4;
ALTER TABLE `anjuman_tracks` AUTO_INCREMENT = 3;

SET FOREIGN_KEY_CHECKS = 1;
