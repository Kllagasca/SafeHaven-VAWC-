-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 15, 2025 at 01:26 PM
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
-- Database: `gender_dev`
--

-- --------------------------------------------------------

--
-- Table structure for table `carousel`
--

CREATE TABLE `carousel` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `image` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0=visible,1=hidden'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `carousel`
--

INSERT INTO `carousel` (`id`, `name`, `image`, `status`) VALUES
(1, 'Gender and Development', 'assets/uploads/carousel/1736925883.png', 0),
(2, 'Mission and Vision', 'assets/uploads/carousel/1736925892.png', 0),
(3, 'Gender and Development Online Database', 'assets/uploads/carousel/1736925899.png', 0);

-- --------------------------------------------------------

--
-- Table structure for table `documents`
--

CREATE TABLE `documents` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `file` varchar(100) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0=visible,1=hidden',
  `approval_status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `documents`
--

INSERT INTO `documents` (`id`, `name`, `file`, `status`, `approval_status`, `created_at`) VALUES
(1, 'Gender and Development in the Philippines - Overview', 'assets/files/Gender and Development in the Philippines - Overview.pdf', 0, 'approved', '2025-01-15'),
(2, 'Community-Based Gender and Development Programs in the Philippines', 'assets/files/Community-Based Gender and Development Programs in the Philippines.pdf', 0, 'approved', '2025-01-15'),
(3, 'Combating Gender-Based Violence Through Education and Policy in the Philippines', 'assets/files/Combating Gender-Based Violence Through Education and Policy in the Philippines.pdf', 0, 'approved', '2025-01-15');

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE `news` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `slug` varchar(300) NOT NULL,
  `image` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `long_description` mediumtext DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0=visible,1=hidden'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `news`
--

INSERT INTO `news` (`id`, `name`, `slug`, `image`, `long_description`, `status`) VALUES
(1, 'asdsad', 'asdsad', 'assets/uploads/news/1736906519.png', 'sadsadsa', 0),
(2, 'sadsad', 'sadsad', 'assets/uploads/news/1736914024.png', 'sadsadsad', 0),
(3, 'sadsad', 'sadsad', 'assets/uploads/news/1736918399.png', 'sadsadsadsda', 0);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `event` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `event`, `message`, `created_at`) VALUES
(1, 12, 'login', 'User admin admin logged in.', '2025-01-15 12:09:43'),
(2, 12, 'login', 'User admin admin logged in.', '2025-01-15 12:16:32'),
(3, 12, 'login', 'User admin admin logged in.', '2025-01-15 12:17:40');

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(300) NOT NULL,
  `long_description` mediumtext DEFAULT NULL,
  `image` varchar(100) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0=visible,1=hidden',
  `approval_status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `name`, `slug`, `long_description`, `image`, `status`, `approval_status`, `created_at`) VALUES
(1, 'The Importance of Gender Equality in Nation-Building', 'the-importance-of-gender-equality-in-nation-building', 'A gender-equal society is not only an ideal but a necessity for sustainable development. In the Philippines, the push for Gender and Development (GAD) policies highlights the critical role of gender equality in nation-building. By integrating gender perspectives in governance, education, and economic policies, the country empowers individuals to contribute meaningfully to society regardless of gender. Did you know? The Philippines consistently ranks high in gender equality metrics in Asia. However, challenges like wage gaps, representation, and access to resources remain.Let\\\\\\\'s continue advocating for inclusive policies and initiatives that promote equity. Together, we can build a nation where everyone thrives.#GenderAndDevelopment #Philippines #GenderEquality #SustainableDevelopment', 'assets/uploads/services/1736924488.png', 0, 'approved', '2025-01-15'),
(2, 'Womenâ€™s Empowerment and Local Governance', 'womenâ€™s-empowerment-and-local-governance', 'Local government units (LGUs) in the Philippines play a vital role in advancing womenâ€™s rights through GAD programs. From livelihood training for women to protection services against gender-based violence, LGUs are at the forefront of change.One remarkable example is the implementation of GAD budgets by LGUs, allocating at least 5% of their annual budget to gender-responsive projects. This ensures that women have access to education, health, and livelihood opportunities.Empowered women create empowered communities. Letâ€™s support and celebrate these initiatives!What gender-responsive programs have you seen in your community? Share them below!#WomenEmpowerment #LocalGovernance #GenderEqualityPH #GADPrograms', 'assets/uploads/services/1736924522.png', 0, 'approved', '2025-01-15'),
(3, 'Addressing Gender-Based Violence Through Education', 'addressing-gender-based-violence-through-education', 'Gender-based violence (GBV) remains a pressing issue in the Philippines, with many cases going unreported due to stigma and fear.Education plays a pivotal role in addressing GBV. By integrating gender sensitivity into school curricula and community training programs, we can challenge stereotypes and empower individuals to stand against violence.Government and NGOs have collaborated on campaigns like the Safe Spaces Act, promoting awareness and legal protection for victims. Still, there is much to do in ensuring these laws are felt at the grassroots level.It starts with each of us: educate, speak out, and support initiatives that fight GBV. Together, we can create safer spaces for everyone.#StopGBV #EducationForAll #GenderEquality #SafeSpacesPH', 'assets/uploads/services/1736924549.png', 0, 'approved', '2025-01-15');

-- --------------------------------------------------------

--
-- Table structure for table `social_medias`
--

CREATE TABLE `social_medias` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0=shown,1=hidden'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `social_medias`
--

INSERT INTO `social_medias` (`id`, `name`, `url`, `status`) VALUES
(1, 'Facebook', 'https://www.facebook.com/', 0),
(2, 'Instagram', 'www.instagram.com/HoneyBunch', 0),
(4, 'Twitter', 'www.twitter.com/HoneyBunch', 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) UNSIGNED NOT NULL,
  `fname` varchar(100) NOT NULL,
  `lname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `is_ban` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0=unban,1=ban',
  `role` varchar(100) NOT NULL COMMENT 'admin,fperson,researcher',
  `created_at` date DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fname`, `lname`, `email`, `password`, `is_ban`, `role`, `created_at`) VALUES
(12, 'admin', 'admin', 'admin@admin.com', '$2y$10$ii2O46JxjRVZ/TEFux4Rc.eTOLP.y7mFfJUbMz1Fifpdgf.9yS0Ri', 0, 'admin', '2025-01-10'),
(13, 'admin1', 'admin2', 'admin1@admin', '$2y$10$pV2FDM8fJD9rD0N.Se3UXe99AXR4IO1QT90V4usC9hCoCPINw1L0e', 0, 'admin', '2025-01-10'),
(14, 'researcher', 'researcher', 'researcher@gmail', '$2y$10$SWD3L8aXb4fmOtSdaJJ9jeOFMdtxK8T5bt8mGF7FifeYbjuvGR4UG', 0, 'researcher', '2025-01-11'),
(16, 'focal', 'focal', 'focal@gmail', '$2y$10$Cb6PY81LsYjJ2Sy.xkBPye6gZTL1nTbRyclKjsVgkRx14ZPcPKwGe', 0, 'fperson', '2025-01-11'),
(17, 'Trial', 'Trial', 'trial@gmail', '$2y$10$Med5Iibt3X2u1imWhSIB3O0Ol4epLKKqj73bdA/FlQRfCI5uUW32e', 0, 'fperson', '2025-01-15');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `carousel`
--
ALTER TABLE `carousel`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `social_medias`
--
ALTER TABLE `social_medias`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `carousel`
--
ALTER TABLE `carousel`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `documents`
--
ALTER TABLE `documents`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `news`
--
ALTER TABLE `news`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `social_medias`
--
ALTER TABLE `social_medias`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
