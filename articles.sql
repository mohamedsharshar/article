-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 22, 2025 at 02:57 PM
-- Server version: 8.4.3
-- PHP Version: 8.3.16

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
-- Table structure for table `articles`
--

CREATE TABLE `articles` (
  `id` int NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `content` text COLLATE utf8mb4_general_ci NOT NULL,
  `image` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `category_id` int DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `admin_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `articles`
--

INSERT INTO `articles` (`id`, `title`, `content`, `image`, `created_at`, `category_id`, `user_id`, `admin_id`) VALUES
(7, 'Venom Article', 'Article on Venom: The Symbiote’s Enduring Legacy\r\nIntroduction\r\nVenom, the iconic Marvel Comics character, has captivated audiences as of June 4, 2025, at 03:51 PM EEST. Known for its complex anti-hero persona and symbiotic nature, Venom’s journey from villain to fan favorite spans comics, films, and games. This article explores its evolution and current prominence.\r\nOrigins and Evolution\r\nDebuting in The Amazing Spider-Man #300 in 1988, Venom began as a symbiote merged with Spider-Man, later bonding with Eddie Brock. Over decades, it transformed into a character with depth, balancing chaos with a twisted moral code. The 2018 film Venom, starring Tom Hardy, cemented its cinematic rise.\r\nCultural Impact\r\nVenom’s popularity surged with its cinematic universe, including Venom: Let There Be Carnage (2021) and the highly anticipated Venom: The Last Dance (2024). As of mid-2025, the character inspires merchandise, fan art, and a video game adaptation, reflecting its broad appeal across generations.\r\nVenom in 2025\r\nIn June 2025, Venom: The Last Dance continues to dominate discussions, praised for its stunning visuals and narrative closure. The game version, released earlier this year, offers players an immersive experience as the symbiote, blending action with storytelling.\r\nConclusion\r\nVenom’s journey from a Spider-Man foe to a cultural icon showcases its enduring allure. As of 2025, it remains a powerful force in entertainment, promising more symbiote adventures ahead.', 'art_68402c84832557.13372141.jpg', '2025-06-01 11:14:47', 7, 1, NULL),
(8, 'Article on Karate', 'Article on Karate: The Art of Discipline and Strength\r\nIntroduction\r\nKarate, a traditional martial art originating from Okinawa, Japan, remains a global phenomenon as of June 4, 2025, at 03:49 PM EEST. Known for its blend of physical prowess and mental discipline, karate attracts millions of practitioners worldwide. This article explores its history, techniques, and modern significance.\r\nHistory of Karate\r\nKarate developed in the Ryukyu Kingdom during the 17th century, influenced by Chinese martial arts. The term \"karate\" means \"empty hand,\" reflecting its focus on unarmed combat. It gained global prominence in the 20th century through masters like Gichin Funakoshi, who introduced it to mainland Japan.\r\nTechniques and Practice\r\nKarate emphasizes strikes, kicks, and blocks, with training focusing on katas (forms), kumite (sparring), and kihon (basics). Styles like Shotokan and Goju-Ryu cater to different approaches, balancing power and fluidity. Practitioners also develop mental resilience, respect, and focus through rigorous training.\r\nKarate in 2025\r\nAs of mid-2025, karate continues to thrive, with its inclusion in the Olympics boosting its visibility. Competitions like the Karate World Championships highlight its competitive spirit, while dojos worldwide promote its values of self-improvement and discipline.\r\nConclusion\r\nKarate is more than a sport—it’s a way of life that fosters strength, respect, and perseverance. Its enduring legacy in 2025 ensures its place as a cherished martial art for generations.', 'art_6840410ca723d0.82846195.png', '2025-06-03 19:37:10', 2, 1, 1),
(10, 'Article on Hackers: The Minds Behind the Code', 'Article on Hackers: The Minds Behind the Code\r\nIntroduction\r\nHackers have become a prominent topic in the digital age, often portrayed as mysterious figures in movies and media. As of June 2025, their role in technology ranges from cybercrime to ethical innovation. This article explores who hackers are, their impact, and the evolving landscape of hacking.\r\nWho Are Hackers?\r\nHackers are individuals skilled in manipulating computer systems, networks, and software. They are categorized into three main types: black-hat hackers, who engage in illegal activities like data theft; white-hat hackers, who use their skills ethically to improve security; and gray-hat hackers, who operate in a moral gray area. Their expertise varies from coding to social engineering.\r\nThe Impact of Hacking\r\nIn 2025, hacking incidents continue to rise, with cyberattacks targeting corporations, governments, and individuals. Recent reports highlight breaches costing billions, yet white-hat hackers play a crucial role in preventing such threats by identifying vulnerabilities. The balance between security and innovation remains a key challenge in the tech world.\r\nHacking in the Modern Era\r\nToday, tools like artificial intelligence and quantum computing are transforming hacking techniques. Ethical hacking programs are also growing, with organizations hiring professionals to safeguard systems. As of June 4, 2025, the global focus is on strengthening cybersecurity to counter the ever-evolving tactics of malicious hackers.\r\nConclusion\r\nHackers, whether villains or guardians, shape the digital landscape. Understanding their methods and motivations is essential as we navigate the opportunities and risks of technology in 2025.', 'art_684034d2307432.05018595.jpg', '2025-06-04 11:58:10', 4, 1, 1),
(11, 'Gaming Article', 'Gaming Article: The Evolution of Gaming in 2025\r\nIntroduction\r\nGaming has evolved dramatically over the years, and 2025 is proving to be a landmark year for the industry. With cutting-edge technology and immersive storytelling, gamers worldwide are experiencing new heights of entertainment. In this article, we’ll explore the latest trends and dive into a highly anticipated title: Venom 3: The Last Dance.\r\nCurrent Trends in Gaming\r\nThe gaming industry in 2025 is dominated by advancements in virtual reality (VR) and augmented reality (AR), offering players unparalleled immersion. Cloud gaming has also taken off, allowing gamers to access high-end titles without needing expensive hardware. Additionally, narrative-driven games are gaining popularity, blending cinematic storytelling with interactive gameplay.\r\nSpotlight: Venom 3: The Last Dance\r\nVenom 3: The Last Dance is a standout title this year, captivating fans with its thrilling storyline and stunning graphics. The game follows Venom as he faces his ultimate challenge, combining fast-paced combat with emotional depth. Players can explore an open-world environment, engage in dynamic battles, and make choices that impact the story’s outcome. The game’s use of next-gen technology, like real-time ray tracing, makes every scene visually spectacular.\r\nConclusion\r\nThe gaming landscape in 2025 is more exciting than ever, with titles like Venom 3: The Last Dance pushing the boundaries of what games can achieve. Whether you’re a casual player or a hardcore gamer, there’s something for everyone to enjoy in this golden age of gaming.', 'art_68403d8dd142e2.18607837.jpg', '2025-06-04 12:35:25', 7, 1, 1),
(12, 'الجرافك ديزاين', 'الجرافك ديزاين من اقوي المجلات المربحة', 'art_685014e68c7142.77348566.png', '2025-06-16 12:57:56', 2, 4, NULL),
(22, 'malware', 'malware', 'art_6851ce03b0ed07.20347724.jpg', '2025-06-17 20:20:19', 4, NULL, 3),
(23, 'bootstrap', 'asdasddsadasd', 'art_6851ced810b26.png', '2025-06-17 20:23:52', 4, 1, NULL),
(24, 'سشسيشسيسشي', 'سشيشسيشسيسشي', 'art_6855782510072.jpg', '2025-06-20 15:03:01', NULL, 1, NULL),
(25, 'asd', 'sad', 'art_68559ada6df647.62175297.jpeg', '2025-06-20 17:31:06', NULL, NULL, 3),
(26, 'asd', 'ads', 'art_68559ae2040b21.68658478.jpeg', '2025-06-20 17:31:14', 2, NULL, 3),
(27, 'sad', 'das', 'art_68559aea584493.07956221.jpeg', '2025-06-20 17:31:22', 4, NULL, 3),
(28, 'sad', 'sad', 'art_68581276ad314.jpeg', '2025-06-22 14:25:58', NULL, 1, NULL),
(29, 'user article', 'aslkdnsakjdas', 'art_685818abc01fd.jpeg', '2025-06-22 14:52:27', 9, 1, NULL),
(30, 'asd', 'sad', 'art_6858190c69eea5.47565030.jpeg', '2025-06-22 14:54:04', 9, NULL, 3),
(31, 'سشيش', 'سشيسشي', 'art_6858198d3b6cd.jpeg', '2025-06-22 14:56:13', 9, 1, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `articles`
--
ALTER TABLE `articles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_articles_category` (`category_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `articles`
--
ALTER TABLE `articles`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `articles`
--
ALTER TABLE `articles`
  ADD CONSTRAINT `fk_articles_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
