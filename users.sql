-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql302.infinityfree.com
-- Generation Time: Nov 26, 2024 at 03:19 PM
-- Server version: 10.6.19-MariaDB
-- PHP Version: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `if0_37729401_crms`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('Male','Female','Other') DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('officer','citizen') DEFAULT 'citizen',
  `phone_number` varchar(15) DEFAULT NULL,
  `nid` varchar(50) NOT NULL,
  `security_question` text DEFAULT NULL,
  `address` text DEFAULT NULL,
  `occupation` varchar(255) DEFAULT NULL,
  `profile_photo` varchar(255) DEFAULT NULL,
  `terms_accepted` tinyint(1) NOT NULL DEFAULT 0,
  `privacy_policy_accepted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `date_of_birth`, `gender`, `email`, `password`, `role`, `phone_number`, `nid`, `security_question`, `address`, `occupation`, `profile_photo`, `terms_accepted`, `privacy_policy_accepted`, `created_at`) VALUES
(1, 'Officer John', NULL, NULL, 'dd@dd.dd', '123', 'officer', '123456789', '', NULL, 'Police HQ', NULL, NULL, 0, 0, '2024-11-22 05:08:40'),
(5, 'Md Ebrahim Hossain', NULL, NULL, 'captainibrahim996@gmail.com', '$2y$10$7Rb5pxJcr8EW58jtx7j4zu/GS3sX9Pl6/C2apyRZfx86/Pq2n1eLm', 'officer', '987654321', '', NULL, 'hgkujhgjkjjkhjkhkjhjkhjkhjhjjhkhjkhjhjhjhjhjhjhj', NULL, NULL, 0, 0, '2024-11-22 11:44:06'),
(2, 'Test', NULL, NULL, 'abc@gmail.com', '$2y$10$gjxwAumY2DtKfUp/afIXeOiXswaFTRe10YQypZ/QoSNdV527oix8O', 'citizen', '017243450000', '', NULL, 'ok', NULL, NULL, 0, 0, '2024-11-22 05:32:32'),
(3, 'Test', NULL, NULL, 'd@l.lk', '$2y$10$L.N6az/WxuU6J2Tfx138ked4XrqIKUv8dfx1CuKjaZjeOmwR9fjkG', 'officer', 'ddd', '', NULL, 's', NULL, NULL, 0, 0, '2024-11-22 05:45:17'),
(4, 'Officer John', NULL, NULL, 'officer.john@example.com', '*214C2FAF32F109AE748170BFABDDFB9B05889E64', 'officer', '1234567890', '', NULL, 'Police Headquarters', NULL, NULL, 0, 0, '2024-11-22 05:49:15'),
(6, 'sanjida afrin niha', NULL, NULL, 'sanjuafrin698@gmail.com', '$2y$10$AoqwxEzAuRWD0dnvwAVAXOtRZ3nZQwWee.Pnz4TqsnIzfEGUg8gvm', 'officer', '0987654321', '', NULL, 'mm', NULL, NULL, 0, 0, '2024-11-22 17:09:36'),
(7, 'md zahid hasan ', NULL, NULL, 'zahidhasantonmoy360@gmail.com', '$2y$10$CiRoE1kFDuT2S1mttQDEqONIV11RoYXA/TiD8IbI07MuxgDJ1wRz.', 'citizen', '01724348000', '', NULL, 'amijanina', NULL, NULL, 0, 0, '2024-11-22 17:32:43'),
(8, 'abc@abc.com', NULL, NULL, 'abc@abc.com', '$2y$10$brNxQimKWDHXhyCqzgBenuJmawjK3KY./MisE6AEeC6ts./WMhQtK', 'officer', '123456790', '', NULL, 'abc', NULL, NULL, 0, 0, '2024-11-22 17:39:58'),
(9, 'Officer Demo', NULL, NULL, 'officer@gmail.com', '$2y$10$l3.CJM7EtjPaCguqCTajKOvxDy0myk.mwCVprxvAA9EVdY3CKNIYy', 'officer', '1234567890', '', NULL, 'Dhaka', NULL, NULL, 0, 0, '2024-11-23 06:18:50'),
(10, 'Citizen', NULL, NULL, 'citizen@gmail.com', '$2y$10$h41nKTppHg/PWKt19MmbG.cVCGiLpRhscLctJmcruG2w6rGS4zkkK', 'citizen', '1234567890', '', NULL, 'Dhaka', NULL, NULL, 0, 0, '2024-11-23 06:19:15'),
(11, 'iuhfasp', NULL, NULL, 'samiullah7mahmud@gmail.com', '$2y$10$LwRkFRxUC6BKhuXIx.xIxOTA6optRJ0VErFSs.sCt2PZu2MUeVDOC', 'citizen', ';siughpo', '', NULL, '56468468', NULL, NULL, 0, 0, '2024-11-26 09:37:27'),
(12, 'bubt', NULL, NULL, 'bubt@gmail.com', '$2y$10$MmFM05xFFnu4DP3ZYhn/RuPELOx2nzniChwTFVxxG8Ccj7Xpxy8ei', 'citizen', '01700000000', '', NULL, 'dhaka', NULL, NULL, 0, 0, '2024-11-26 11:51:40'),
(13, 'hiiiiiiiiiii', NULL, NULL, 'ss@gjn.l', '$2y$10$vgKpYfJ/zcKnQ6u6/iJVGup0KjD.gYTH6Ev90u8sLi/5Y4UOYuDe2', 'citizen', 'dd', '', NULL, '', NULL, NULL, 0, 0, '2024-11-26 19:42:41'),
(14, 'g', NULL, NULL, 'gg@g.l', '$2y$10$8uAB0fD.ps6MnvEWgyzgfO34jNLKo8dZtVBJT8XvZKeSseQZjpfzq', 'citizen', 'gg', '', NULL, '', NULL, NULL, 0, 0, '2024-11-26 19:49:10'),
(15, 'w', NULL, NULL, 'w@e.l', '$2y$10$pGwGGzDk024esio/B9XBoOGwOBltOeyI4IZwwG5QFcs/TgGhp6dIW', 'citizen', '1234', '', NULL, '', NULL, NULL, 0, 0, '2024-11-26 19:50:42'),
(16, 'rr', '2024-12-04', 'Male', 'rr@f.l', '$2y$10$Odgx8QL7qQT4lBiEXlG2yeq1YZi.cixDXvfILnEkw3yS0dJ2PR6Ee', 'citizen', 'r', '11111111', NULL, NULL, NULL, NULL, 0, 0, '2024-11-26 20:01:39');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
