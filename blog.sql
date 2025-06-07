-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: 07 يونيو 2025 الساعة 18:01
-- إصدار الخادم: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `blog`
--

-- --------------------------------------------------------

--
-- بنية الجدول `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `admins`
--

INSERT INTO `admins` (`id`, `username`, `email`, `password`, `created_at`, `updated_at`, `is_active`) VALUES
(1, 'admin', 'admin@example.com', '$2y$10$a355BwkHYFfgO4SrJWI6euG/HHpetOKATLDWQSAm2bADhB/VwHJGW', '2025-05-25 11:56:19', '2025-06-05 23:55:53', 1),
(2, 'admin2', 'admin2@example.com', '$2y$10$rB4PnmfUHAtsWcB/HDnIBOQTHqVivYg8r4FlAqFP.Pzf6N8gFu2rO', '2025-05-26 17:57:00', '2025-06-05 23:55:53', 1);

-- --------------------------------------------------------

--
-- بنية الجدول `articles`
--

CREATE TABLE `articles` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `image` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `articles`
--

INSERT INTO `articles` (`id`, `title`, `content`, `image`, `created_at`) VALUES
(7, 'Venom Article', 'Article on Venom: The Symbiote’s Enduring Legacy\r\nIntroduction\r\nVenom, the iconic Marvel Comics character, has captivated audiences as of June 4, 2025, at 03:51 PM EEST. Known for its complex anti-hero persona and symbiotic nature, Venom’s journey from villain to fan favorite spans comics, films, and games. This article explores its evolution and current prominence.\r\nOrigins and Evolution\r\nDebuting in The Amazing Spider-Man #300 in 1988, Venom began as a symbiote merged with Spider-Man, later bonding with Eddie Brock. Over decades, it transformed into a character with depth, balancing chaos with a twisted moral code. The 2018 film Venom, starring Tom Hardy, cemented its cinematic rise.\r\nCultural Impact\r\nVenom’s popularity surged with its cinematic universe, including Venom: Let There Be Carnage (2021) and the highly anticipated Venom: The Last Dance (2024). As of mid-2025, the character inspires merchandise, fan art, and a video game adaptation, reflecting its broad appeal across generations.\r\nVenom in 2025\r\nIn June 2025, Venom: The Last Dance continues to dominate discussions, praised for its stunning visuals and narrative closure. The game version, released earlier this year, offers players an immersive experience as the symbiote, blending action with storytelling.\r\nConclusion\r\nVenom’s journey from a Spider-Man foe to a cultural icon showcases its enduring allure. As of 2025, it remains a powerful force in entertainment, promising more symbiote adventures ahead.', 'art_68402c84832557.13372141.jpg', '2025-06-01 11:14:47'),
(8, 'Article on Karate', 'Article on Karate: The Art of Discipline and Strength\r\nIntroduction\r\nKarate, a traditional martial art originating from Okinawa, Japan, remains a global phenomenon as of June 4, 2025, at 03:49 PM EEST. Known for its blend of physical prowess and mental discipline, karate attracts millions of practitioners worldwide. This article explores its history, techniques, and modern significance.\r\nHistory of Karate\r\nKarate developed in the Ryukyu Kingdom during the 17th century, influenced by Chinese martial arts. The term \"karate\" means \"empty hand,\" reflecting its focus on unarmed combat. It gained global prominence in the 20th century through masters like Gichin Funakoshi, who introduced it to mainland Japan.\r\nTechniques and Practice\r\nKarate emphasizes strikes, kicks, and blocks, with training focusing on katas (forms), kumite (sparring), and kihon (basics). Styles like Shotokan and Goju-Ryu cater to different approaches, balancing power and fluidity. Practitioners also develop mental resilience, respect, and focus through rigorous training.\r\nKarate in 2025\r\nAs of mid-2025, karate continues to thrive, with its inclusion in the Olympics boosting its visibility. Competitions like the Karate World Championships highlight its competitive spirit, while dojos worldwide promote its values of self-improvement and discipline.\r\nConclusion\r\nKarate is more than a sport—it’s a way of life that fosters strength, respect, and perseverance. Its enduring legacy in 2025 ensures its place as a cherished martial art for generations.', 'art_6840410ca723d0.82846195.png', '2025-06-03 19:37:10'),
(10, 'Article on Hackers: The Minds Behind the Code', 'Article on Hackers: The Minds Behind the Code\r\nIntroduction\r\nHackers have become a prominent topic in the digital age, often portrayed as mysterious figures in movies and media. As of June 2025, their role in technology ranges from cybercrime to ethical innovation. This article explores who hackers are, their impact, and the evolving landscape of hacking.\r\nWho Are Hackers?\r\nHackers are individuals skilled in manipulating computer systems, networks, and software. They are categorized into three main types: black-hat hackers, who engage in illegal activities like data theft; white-hat hackers, who use their skills ethically to improve security; and gray-hat hackers, who operate in a moral gray area. Their expertise varies from coding to social engineering.\r\nThe Impact of Hacking\r\nIn 2025, hacking incidents continue to rise, with cyberattacks targeting corporations, governments, and individuals. Recent reports highlight breaches costing billions, yet white-hat hackers play a crucial role in preventing such threats by identifying vulnerabilities. The balance between security and innovation remains a key challenge in the tech world.\r\nHacking in the Modern Era\r\nToday, tools like artificial intelligence and quantum computing are transforming hacking techniques. Ethical hacking programs are also growing, with organizations hiring professionals to safeguard systems. As of June 4, 2025, the global focus is on strengthening cybersecurity to counter the ever-evolving tactics of malicious hackers.\r\nConclusion\r\nHackers, whether villains or guardians, shape the digital landscape. Understanding their methods and motivations is essential as we navigate the opportunities and risks of technology in 2025.', 'art_684034d2307432.05018595.jpg', '2025-06-04 11:58:10'),
(11, 'Gaming Article', 'Gaming Article: The Evolution of Gaming in 2025\r\nIntroduction\r\nGaming has evolved dramatically over the years, and 2025 is proving to be a landmark year for the industry. With cutting-edge technology and immersive storytelling, gamers worldwide are experiencing new heights of entertainment. In this article, we’ll explore the latest trends and dive into a highly anticipated title: Venom 3: The Last Dance.\r\nCurrent Trends in Gaming\r\nThe gaming industry in 2025 is dominated by advancements in virtual reality (VR) and augmented reality (AR), offering players unparalleled immersion. Cloud gaming has also taken off, allowing gamers to access high-end titles without needing expensive hardware. Additionally, narrative-driven games are gaining popularity, blending cinematic storytelling with interactive gameplay.\r\nSpotlight: Venom 3: The Last Dance\r\nVenom 3: The Last Dance is a standout title this year, captivating fans with its thrilling storyline and stunning graphics. The game follows Venom as he faces his ultimate challenge, combining fast-paced combat with emotional depth. Players can explore an open-world environment, engage in dynamic battles, and make choices that impact the story’s outcome. The game’s use of next-gen technology, like real-time ray tracing, makes every scene visually spectacular.\r\nConclusion\r\nThe gaming landscape in 2025 is more exciting than ever, with titles like Venom 3: The Last Dance pushing the boundaries of what games can achieve. Whether you’re a casual player or a hardcore gamer, there’s something for everyone to enjoy in this golden age of gaming.', 'art_68403d8dd142e2.18607837.jpg', '2025-06-04 12:35:25');

-- --------------------------------------------------------

--
-- بنية الجدول `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `article_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `content` text NOT NULL,
  `is_admin` tinyint(1) DEFAULT 0,
  `status` enum('pending','approved','rejected') DEFAULT 'approved',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `comments`
--

INSERT INTO `comments` (`id`, `article_id`, `user_id`, `admin_id`, `content`, `is_admin`, `status`, `created_at`) VALUES
(4, 10, NULL, 1, 'this content about hacker', 1, 'approved', '2025-06-04 12:54:47'),
(5, 11, NULL, 1, 'this is gaming article', 1, 'approved', '2025-06-04 12:56:52'),
(6, 10, 1, NULL, 'حلو اووي', 0, 'approved', '2025-06-04 13:07:31'),
(7, 10, 1, NULL, 'انا انبسطت اووي لما قرات المقال دا', 0, 'approved', '2025-06-04 13:09:43');

-- --------------------------------------------------------

--
-- بنية الجدول `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `created_at`, `updated_at`, `is_active`) VALUES
(1, 'mohamed', 'mmshsh05@gmail.com', '$2y$10$/RxYqQGwBGeEjKdxlN6pg.NWjcAGqjcyJLrMKxzkJPsmartos4AXe', '2025-05-25 11:44:06', '2025-06-06 00:32:17', 1),
(2, 'sharshar', 'user@example.com', '$2y$10$fAC4YSWM8Gs1GLWg03wGy.TAJ/BHbtJgZiuMkrirYmCEDFmF87ktS', '2025-05-26 17:44:54', '2025-06-05 23:55:20', 1),
(4, 'mm', 'mmshsh058@gmail.com', '$2y$10$qQCoc4/D/KVpJHntna.v6.1XWgg4WD8kvFAZpN.K9a0Hq34dyngmq', '2025-06-04 13:33:57', '2025-06-05 23:55:20', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username_2` (`username`);

--
-- Indexes for table `articles`
--
ALTER TABLE `articles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `article_id` (`article_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `email_2` (`email`),
  ADD UNIQUE KEY `username_2` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `articles`
--
ALTER TABLE `articles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- قيود الجداول المُلقاة.
--

--
-- قيود الجداول `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `comments_ibfk_3` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
