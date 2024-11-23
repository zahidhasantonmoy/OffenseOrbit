-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql302.infinityfree.com
-- Generation Time: Nov 23, 2024 at 04:53 AM
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
-- Table structure for table `crime_reports`
--

CREATE TABLE `crime_reports` (
  `id` int(11) NOT NULL,
  `incident_type` varchar(255) NOT NULL,
  `incident_description` text NOT NULL,
  `location` varchar(255) NOT NULL,
  `latitude` decimal(9,6) DEFAULT NULL,
  `longitude` decimal(9,6) DEFAULT NULL,
  `report_date` date NOT NULL,
  `contact_info` varchar(255) NOT NULL,
  `reporting_officer` varchar(255) NOT NULL,
  `priority_level` varchar(50) NOT NULL,
  `evidence` varchar(255) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Pending',
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `crime_reports`
--

INSERT INTO `crime_reports` (`id`, `incident_type`, `incident_description`, `location`, `latitude`, `longitude`, `report_date`, `contact_info`, `reporting_officer`, `priority_level`, `evidence`, `status`, `last_updated`) VALUES
(3, 'Theft', 'Stolen wallet from the downtown park.', 'Downtown Park', '40.712800', '-74.006000', '2024-11-01', 'John: 555-1234', 'Officer John Doe', 'High', 'wallet.jpg', 'Pending', '2024-11-21 18:17:49'),
(5, 'Theft', 'Stolen wallet from the downtown park.', 'Downtown Park', '23.810300', '90.412500', '2024-11-01', 'John: 555-1234', '', '', 'wallet.jpg', 'Pending', '2024-11-21 18:24:06'),
(7, 'Theft', 'Stolen wallet from the downtown park.', 'Downtown Park', '23.810300', '90.412500', '2024-11-01', 'John: 555-1234', '', '', 'wallet.jpg', 'Pending', '2024-11-21 18:26:55'),
(9, 'Theft', 'Stolen wallet from downtown park.', 'Downtown Park', '23.810300', '90.412500', '2024-11-01', 'John: 555-1234', '', '', 'wallet.jpg', 'Pending', '2024-11-21 18:37:44'),
(42, 'Assault', 'zz', 'zz', NULL, NULL, '2024-11-19', 'z11111', '', '', NULL, 'Pending', '2024-11-22 10:26:31'),
(43, 'Robbery', 'fsdfsd', 'all bangladesh', NULL, NULL, '2000-02-15', '987654321', 'fsdfsdf', 'High', '', 'Pending', '2024-11-22 11:55:30'),
(44, 'Robbery', 'fsdfsdf', 'Dhaka', NULL, NULL, '2002-02-05', '987654321', 'fdsf', 'High', NULL, 'Pending', '2024-11-22 11:56:21'),
(12, 'Theft', 'A motorcycle was stolen from the parking lot.', 'Dhaka, Gulshan', NULL, NULL, '2024-01-01', '01710000000', 'Officer Karim', 'High', 'evidence1.jpg', 'Pending', '2024-11-22 10:06:13'),
(13, 'Assault', 'A person was assaulted during a street altercation.', 'Chattogram, Agrabad', NULL, NULL, '2024-01-02', '01710000001', 'Officer Rahman', 'Medium', 'evidence2.jpg', 'Under Investigation', '2024-11-22 10:06:13'),
(14, 'Fraud', 'A large sum of money was swindled through a phishing scam.', 'Sylhet, Zindabazar', NULL, NULL, '2024-01-03', '01710000002', 'Officer Ahmed', 'High', 'evidence3.jpg', 'Pending', '2024-11-22 10:06:13'),
(15, 'Harassment', 'A student was harassed on public transport.', 'Dhaka, Mirpur', NULL, NULL, '2024-01-04', '01710000003', 'Officer Hossain', 'Medium', 'evidence4.jpg', 'Resolved', '2024-11-22 10:06:13'),
(16, 'Vandalism', 'Windows of a local shop were broken by miscreants.', 'Rajshahi, Shaheb Bazar', NULL, NULL, '2024-01-05', '01710000004', 'Officer Siddique', 'Low', 'evidence5.jpg', 'Pending', '2024-11-22 10:06:13'),
(17, 'Robbery', 'A bank was robbed in the early morning hours.', 'Dhaka, Motijheel', NULL, NULL, '2024-01-06', '01710000005', 'Officer Alam', 'High', 'evidence6.jpg', 'Under Investigation', '2024-11-22 10:06:13'),
(18, 'Theft', 'A mobile phone was snatched from a pedestrian.', 'Barishal, Nathullabad', NULL, NULL, '2024-01-07', '01710000006', 'Officer Azad', 'Low', 'evidence7.jpg', 'Resolved', '2024-11-22 10:06:13'),
(19, 'Assault', 'A shopkeeper was physically attacked during a robbery.', 'Khulna, Rupsha', NULL, NULL, '2024-01-08', '01710000007', 'Officer Farid', 'Medium', 'evidence8.jpg', 'Under Investigation', '2024-11-22 10:06:13'),
(20, 'Fraud', 'Fake investment scheme defrauded multiple individuals.', 'Comilla, Kandirpar', NULL, NULL, '2024-01-09', '01710000008', 'Officer Moin', 'High', 'evidence9.jpg', 'Pending', '2024-11-22 10:06:13'),
(21, 'Harassment', 'An office worker reported workplace harassment.', 'Dhaka, Banani', NULL, NULL, '2024-01-10', '01710000009', 'Officer Anwar', 'Medium', 'evidence10.jpg', 'Resolved', '2024-11-22 10:06:13'),
(22, 'Vandalism', 'A car was vandalized in a residential area.', 'Sylhet, Uposhohor', NULL, NULL, '2024-01-11', '01710000010', 'Officer Jalal', 'Low', 'evidence11.jpg', 'Pending', '2024-11-22 10:06:13'),
(23, 'Robbery', 'A jewelry shop was looted during the night.', 'Dhaka, Dhanmondi', NULL, NULL, '2024-01-12', '01710000011', 'Officer Hasan', 'High', 'evidence12.jpg', 'Under Investigation', '2024-11-22 10:06:13'),
(24, 'Theft', 'Bicycle stolen from a school campus.', 'Chattogram, Halishahar', NULL, NULL, '2024-01-13', '01710000012', 'Officer Bashir', 'Low', 'evidence13.jpg', 'Resolved', '2024-11-22 10:06:13'),
(25, 'Assault', 'Domestic violence incident reported in a local area.', 'Rajshahi, Ghoramara', NULL, NULL, '2024-01-14', '01710000013', 'Officer Firoz', 'High', 'evidence14.jpg', 'Under Investigation', '2024-11-22 10:06:13'),
(26, 'Fraud', 'Counterfeit currency was circulated in the market.', 'Khulna, Daulatpur', NULL, NULL, '2024-01-15', '01710000014', 'Officer Kabir', 'Medium', 'evidence15.jpg', 'Pending', '2024-11-22 10:06:13'),
(27, 'Harassment', 'A shop assistant was verbally abused by a customer.', 'Barishal, C&B Road', NULL, NULL, '2024-01-16', '01710000015', 'Officer Nafis', 'Low', 'evidence16.jpg', 'Resolved', '2024-11-22 10:06:13'),
(28, 'Vandalism', 'School property damaged by unidentified individuals.', 'Chattogram, GEC', NULL, NULL, '2024-01-17', '01710000016', 'Officer Zubair', 'Medium', 'evidence17.jpg', 'Under Investigation', '2024-11-22 10:06:13'),
(29, 'Robbery', 'Armed robbery at a petrol pump.', 'Dhaka, Uttara', NULL, NULL, '2024-01-18', '01710000017', 'Officer Nayeem', 'High', 'evidence18.jpg', 'Pending', '2024-11-22 10:06:13'),
(30, 'Theft', 'Laptops were stolen from a university lab.', 'Sylhet, Amberkhana', NULL, NULL, '2024-01-19', '01710000018', 'Officer Tariq', 'Medium', 'evidence19.jpg', 'Resolved', '2024-11-22 10:06:13'),
(31, 'Assault', 'A journalist was attacked while reporting.', 'Dhaka, Tejgaon', NULL, NULL, '2024-01-20', '01710000019', 'Officer Arif', 'High', 'evidence20.jpg', 'Under Investigation', '2024-11-22 10:06:13'),
(32, 'Fraud', 'False land documents used to scam buyers.', 'Comilla, Kotbari', NULL, NULL, '2024-01-21', '01710000020', 'Officer Aslam', 'High', 'evidence21.jpg', 'Pending', '2024-11-22 10:06:13'),
(33, 'Harassment', 'Online harassment complaint lodged by a student.', 'Khulna, Khalishpur', NULL, NULL, '2024-01-22', '01710000021', 'Officer Badrul', 'Medium', 'evidence22.jpg', 'Resolved', '2024-11-22 10:06:13'),
(34, 'Vandalism', 'A government building was defaced with graffiti.', 'Rajshahi, New Market', NULL, NULL, '2024-01-23', '01710000022', 'Officer Saif', 'Low', 'evidence23.jpg', 'Pending', '2024-11-22 10:06:13'),
(35, 'Robbery', 'Electronics showroom robbed during power outage.', 'Barishal, Port Road', NULL, NULL, '2024-01-24', '01710000024', 'Officer Haider', 'High', 'evidence24.jpg', 'Under Investigation', '2024-11-22 10:06:13'),
(36, 'Theft', 'A handbag was stolen at a bus station.', 'Dhaka, Mohakhali', NULL, NULL, '2024-01-25', '01710000025', 'Officer Khaled', 'Low', 'evidence25.jpg', 'Resolved', '2024-11-22 10:06:13'),
(37, 'Assault', 'Youth injured during a political clash.', 'Chattogram, Patenga', NULL, NULL, '2024-01-26', '01710000026', 'Officer Ahsan', 'Medium', 'evidence26.jpg', 'Under Investigation', '2024-11-22 10:06:13'),
(38, 'Fraud', 'Fake employment agency scammed job seekers.', 'Dhaka, Paltan', NULL, NULL, '2024-01-27', '01710000027', 'Officer Rashed', 'High', 'evidence27.jpg', 'Pending', '2024-11-22 10:06:13'),
(39, 'Harassment', 'A woman reported stalking by a neighbor.', 'Rajshahi, Kajla', NULL, NULL, '2024-01-28', '01710000028', 'Officer Munim', 'Medium', 'evidence28.jpg', 'Resolved', '2024-11-22 10:06:13'),
(40, 'Vandalism', 'Public park benches destroyed by vandals.', 'Sylhet, Shibganj', NULL, NULL, '2024-01-29', '01710000029', 'Officer Latif', 'Low', 'evidence29.jpg', 'Pending', '2024-11-22 10:06:13'),
(41, 'Robbery', 'Supermarket robbed at closing hours.', 'Dhaka, Bashundhara', NULL, NULL, '2024-01-30', '01710000030', 'Officer Wahid', 'High', 'evidence30.jpg', 'Under Investigation', '2024-11-22 10:06:13');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `crime_reports`
--
ALTER TABLE `crime_reports`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `crime_reports`
--
ALTER TABLE `crime_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
