-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 04, 2025 at 01:12 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `capstone`
--

-- --------------------------------------------------------

--
-- Table structure for table `favorites`
--

CREATE TABLE `favorites` (
  `favorite_id` int(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `plant_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `favorites`
--

INSERT INTO `favorites` (`favorite_id`, `user_id`, `plant_id`) VALUES
(28, 18, 3),
(30, 21, 3),
(31, 21, 4),
(72, 22, 3);

-- --------------------------------------------------------

--
-- Table structure for table `modules`
--

CREATE TABLE `modules` (
  `module_id` int(100) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `category` varchar(100) NOT NULL,
  `type` varchar(100) NOT NULL,
  `content` varchar(255) NOT NULL,
  `image_path` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `modules`
--

INSERT INTO `modules` (`module_id`, `title`, `description`, `category`, `type`, `content`, `image_path`, `created_at`, `updated_at`) VALUES
(1, 'Introduction to Urban Agriculture', 'This module provides an overview of urban agriculture, its benefits, and its potential to address food security, environmental sustainability, and community resilience. It sets the foundation for understanding how farming can thrive in urban spaces.', 'Urban Agriculture Fundamentals', 'Concept/Overview', '../html/modulefiles/Module1.html', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQxe7PI9MYcdrRfdNEMJMKJSdeAWds4RicLaQ&s', '2024-12-01 13:48:43', '2025-07-03 22:52:40'),
(4, 'Planning and Designing Urban Agriculture Systems', 'Learn the principles of creating efficient and sustainable urban agriculture systems, including space optimization, resource management, and integration with urban infrastructure.', 'Design and Planning', 'Practical/Hands-on', '../html/modulefiles/Module2.html	', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQyMUNuWtmuAkB9U84qYA_tunbAm-ZDNP_kQg&s', '2024-12-01 14:55:37', '2025-07-03 22:53:20'),
(5, 'Techniques and Practices in Urban Agriculture', 'Explore modern and traditional methods of urban farming, including vertical farming, hydroponics, aquaponics, container gardening, and rooftop farming.', 'Techniques and Practices', 'Strategy/Planning', '../html/modulefiles/Module3.html	', '../html/moduleimages/technique.jpg', '2024-12-01 14:56:50', '2024-12-02 07:34:27'),
(6, 'Pest and Disease Management in Urban Farming', 'Understand the challenges of pest and disease control in urban settings and discover eco-friendly, sustainable strategies to protect crops and maximize yields.', 'Maintenance and Management', 'Management/Prevention', '../html/modulefiles/Module4.html', '../html/moduleimages/pest.jpg', '2024-12-01 14:57:01', '2024-12-02 07:35:33'),
(7, 'Crop Selection and Calendar Planning (Baguio City Focus)', 'Gain insights into selecting crops best suited for Baguio City\\\'s climate and conditions. Learn how to plan planting schedules for year-round harvests.', 'Design and Planning', 'Strategy/Planning', '../html/modulefiles/Module5.html	', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQxe7PI9MYcdrRfdNEMJMKJSdeAWds4RicLaQ&s', '2024-12-01 15:04:04', '2025-07-03 22:52:21'),
(8, 'Community Engagement in Urban Agriculture', 'Discover strategies to involve local communities in urban agriculture projects, fostering collaboration, education, and shared responsibility for sustainable food production.', 'Community and Education', 'Community/Social', '../html/modulefiles/Module6.html', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQxe7PI9MYcdrRfdNEMJMKJSdeAWds4RicLaQ&s', '2024-12-01 15:04:13', '2025-07-03 21:54:24'),
(14, 'HALAMAN', 'Plants are a diverse group of multicellular eukaryotes that primarily use photosynthesis to produce their own food, using sunlight, water, and carbon dioxide', 'type 2', 'type', 'https://en.wikipedia.org/wiki/Plant', 'https://www.saferbrand.com/media/Articles/Safer-Brand/26-best-indoor-plants.jpg', '2025-07-03 19:50:07', '2025-07-03 20:59:51'),
(16, 'Urban Farming', 'Urban farming refers to the practice of cultivating, processing, and distributing food within and around urban areas. It encompasses a variety of agricultural activities, including horticulture, animal husbandry, aquaculture, and beekeeping, adapted for the urban environment. Urban farming can occur in backyards, rooftops, patios, and even indoors, often utilizing smaller plots of land compared to rural farms. ', 'Urban', 'Urban Farming Guides', 'https://unity.edu/careers/what-is-urban-farming/', 'https://unity.edu/wp-content/uploads/2025/04/Community-Urban-Agriculture-scaled-1-1024x684.jpg.webp', '2025-07-03 21:16:41', '2025-07-03 21:20:49');

-- --------------------------------------------------------

--
-- Table structure for table `plant`
--

CREATE TABLE `plant` (
  `plant_id` int(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `image` varchar(255) NOT NULL,
  `container_soil` text DEFAULT NULL,
  `watering` text DEFAULT NULL,
  `sunlight` text DEFAULT NULL,
  `tips` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `plant`
--

INSERT INTO `plant` (`plant_id`, `name`, `description`, `image`, `container_soil`, `watering`, `sunlight`, `tips`) VALUES
(3, 'Eggplant', 'A versatile plant for urban farming, eggplants can be grown in large pots or vertical gardens. They are heat-tolerant and produce an abundant harvest in limited spaces. Perfect for homegrown meals, eggplants bring a fresh, rich flavor to dishes like stir-fries and pasta.', '../images/eggplant.jpg', 'Large pot, nutrient-rich soil', 'Water evenly, avoid overwatering', '6-8 hours sunlight', 'Use stakes or cages for support'),
(4, 'Lettuce', 'A staple for urban farming beginners, lettuce is easy to grow in small containers or hydroponic systems. With its quick growth cycle, it provides a continuous harvest of fresh greens for salads and wraps. Lettuce thrives in partial sunlight, making it ideal for balcony or rooftop gardens.\r\n\r\n', '../images/lettuce.jpg', 'Shallow containers, loose soil', 'Water lightly and frequently', 'Partial sunlight', 'Harvest outer leaves first'),
(6, 'Tomato', 'A favorite for urban farmers, tomatoes grow well in containers, hanging baskets, or vertical trellises. They love sunlight and adapt easily to urban spaces, producing vibrant, juicy fruits. Whether cherry or beefsteak, fresh tomatoes are a rewarding addition to any urban garden.\r\n\r\n', '../images/tomato.jpg', 'Large container, loose nutrient-rich soil', 'Water deeply and consistently', '6-8 hours sunlight', 'Use stakes, cages, or trellises for support');

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `question_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`question_id`, `user_id`, `title`, `body`, `image_path`, `created_at`, `updated_at`) VALUES
(12, 22, 'Hatdog ', 'hatdog', NULL, '2025-07-03 15:56:31', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `reply`
--

CREATE TABLE `reply` (
  `reply_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `body` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reply`
--

INSERT INTO `reply` (`reply_id`, `question_id`, `user_id`, `body`, `created_at`, `updated_at`) VALUES
(17, 12, 26, 'hahaha\r\n', '2025-07-03 15:57:42', NULL),
(19, 12, 26, 'taulom\r\n', '2025-07-03 16:09:01', NULL),
(20, 12, 22, 'asda d', '2025-07-03 16:09:50', NULL),
(21, 12, 27, 'dasda', '2025-07-03 16:11:11', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `suggestions`
--

CREATE TABLE `suggestions` (
  `suggestion_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(50) NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `suggestions`
--

INSERT INTO `suggestions` (`suggestion_id`, `message`, `created_at`, `status`) VALUES
(2, 'Suiggestion', '2024-12-03 07:36:44', 'rejected'),
(3, 'Dagdag Modules', '2024-12-04 05:05:12', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('student','admin','agriculturist') NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(10) DEFAULT 'active',
  `profile_picture` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `username`, `password`, `role`, `date_created`, `status`, `profile_picture`) VALUES
(1, 'admin', 'admin', '$2y$10$B.jf5ql45Tg/wj5YijIaZuANWPNmTtc0GGh.ZfTSckvnl.SCITgmq', 'admin', '2024-12-01 06:06:20', 'approved', NULL),
(2, 'John Deez', 'john', '$2y$10$lOq6wWKkVnwTM4lXeUW82.leBH8eCRnpC5wrk4hCA5O.0xarYsKNS', 'agriculturist', '2024-12-01 06:13:33', 'inactive', NULL),
(3, 'Jane Deez', 'janed', '$2y$10$Y21.U4KYjDwj1eW8Hz7tgOjRVpyi1q.U0Q1JDlzfV54QNTYC2.s/e', 'agriculturist', '2024-12-01 06:51:44', 'inactive', NULL),
(4, 'Sarah Smith', 'sarahs', '$2y$10$RvbFS7fhw0IScpxZxNCrXuBjzWHBfa6yBUB1gjs/ac1ixKGPPSfhi', 'student', '2024-12-01 06:58:59', 'inactive', NULL),
(5, 'Michael Johnson', 'mikejohnson', '$2y$10$fdF7YJ/exR8.8vNBOO23vu2xxyQBcAPcO5jY8LU6gw4N9DcPKMz7q', 'student', '2024-12-01 06:59:16', 'inactive', NULL),
(6, 'Emily Davis', 'emilyd', '$2y$10$bfXj0XEaYspqapoXnQ9mtucTFyK0ljHDof.KJJA9stGBOSyJwmV6u', 'agriculturist', '2024-12-01 06:59:29', 'inactive', NULL),
(7, 'William Brown', 'willbrown2020', '$2y$10$VyZE7YRtYUkEonWBJjCXzO5e94cgCrZWq5Fhb9FUz8tvDpCb8Ij7i', 'student', '2024-12-01 06:59:43', 'inactive', NULL),
(8, 'Olivia Wilson', 'oliviawilson23', '$2y$10$Pc7lODm9l5fgSa50khOxn.p83nCQmSnxzxqXsE2CutBsfVkctpel.', 'student', '2024-12-01 06:59:56', 'inactive', NULL),
(9, 'James Taylor', 'jamestaylor34', '$2y$10$jTakODqa30ThT2DyX.2azO4HrwA7Q.5p8MEUPWdu4RakDC1mU0FM2', 'student', '2024-12-01 07:00:14', 'active', NULL),
(10, 'Isabella Martinez', 'isabellamartinez', '$2y$10$.ZUFC6q7/FnKTbamKSDPJuRjUzBqgMV0S6p/f.GYAKWR/aAJeZcn.', 'student', '2024-12-01 07:00:46', 'active', NULL),
(11, 'Jean Jones', 'JeanJ', '$2y$10$ZvlxDzDyEiObKpbwRQ5etuVnZVbMmxDMd1ZL8E2g.qTsyU3umC.7S', 'student', '2025-01-07 00:24:42', 'active', NULL),
(12, 'Jones Logan', 'jonesl', '$2y$10$0GCwJTG7Ry7b70w7qJMB9Oii2sJKBwzldtrc/b/mtQZM.fXhXYD2W', 'student', '2025-01-07 00:26:59', 'active', NULL),
(13, 'miles ocampo', 'milesd', '$2y$10$vJUb1UaRO/LvIClT0PT0SuenSp42fslYyKO1viNTlmlJez3DdKnz6', 'student', '2025-01-07 09:17:26', 'active', NULL),
(14, 'miles ocampo', 'miles', '$2y$10$unctcCw/bcNikQ3E09yTzOtEZl9gEDr7HC9Z30y0FLL0cebznHbP2', 'student', '2025-01-07 09:19:19', 'active', NULL),
(17, 'miles', 'mileso', '$2y$10$9iZbKsvQamBa4q8ssds6De.MI0EmIavum7cW2v3w7g6L1cqcisYnm', 'student', '2025-01-07 09:22:10', 'active', NULL),
(18, 'Anthony', 'Anthony', '$2y$10$9q/iDzty7P.flpXactu8oe0sHseCyIJZPk1F49.wnapFfZgCzZT0W', 'student', '2025-01-09 09:29:15', 'inactive', NULL),
(21, 'Alexander Flores Lavarias', 'Axelflolav', '$2y$10$kE1xMv.crJpOfVlWUgVUsu9tT95g9SyBADk1Tk5Xh6pKjqV7fgGEO', 'student', '2025-06-26 18:32:01', 'active', NULL),
(22, 'Alexander F. Lavarias', 'alex', '$2y$10$hZuqccRaPWGGsdOEcRbte.YjPm6fnrsJiZECLU8Q//582pMOhPjN2', 'student', '2025-07-01 16:09:16', 'active', 'user_22_1751584099.jpg'),
(25, 'Jonard', 'jon', '$2y$10$oq5ZliAeDb3kwxMN./U17.Vq.LmChXl3a4ZStsEbYAayL1PpMng8u', 'student', '2025-07-03 18:27:35', 'active', NULL),
(26, 'hatdog', 'hatdog', '$2y$10$fZAjUO.CjXwnK1CnBElbDO5INuy/0BvanHJ7kyY6.EzDMS0HiwhXy', '', '2025-07-03 22:57:17', 'active', 'user_26_1751583798.png'),
(27, 'bogs', 'bogs', '$2y$10$wklqm8QTrOnTINUD4hQXieSGYhbwjd08ylRXlc9TKh1EvHkhpg5WG', '', '2025-07-03 23:10:55', 'active', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`favorite_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `plant_id` (`plant_id`);

--
-- Indexes for table `modules`
--
ALTER TABLE `modules`
  ADD PRIMARY KEY (`module_id`);

--
-- Indexes for table `plant`
--
ALTER TABLE `plant`
  ADD PRIMARY KEY (`plant_id`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`question_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `reply`
--
ALTER TABLE `reply`
  ADD PRIMARY KEY (`reply_id`),
  ADD KEY `question_id` (`question_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `suggestions`
--
ALTER TABLE `suggestions`
  ADD PRIMARY KEY (`suggestion_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `favorites`
--
ALTER TABLE `favorites`
  MODIFY `favorite_id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT for table `modules`
--
ALTER TABLE `modules`
  MODIFY `module_id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `plant`
--
ALTER TABLE `plant`
  MODIFY `plant_id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `question_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `reply`
--
ALTER TABLE `reply`
  MODIFY `reply_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `suggestions`
--
ALTER TABLE `suggestions`
  MODIFY `suggestion_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `favorites`
--
ALTER TABLE `favorites`
  ADD CONSTRAINT `favorites_ibfk_1` FOREIGN KEY (`plant_id`) REFERENCES `plant` (`plant_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `favorites_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `reply`
--
ALTER TABLE `reply`
  ADD CONSTRAINT `reply_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `questions` (`question_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reply_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
