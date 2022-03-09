-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 06, 2022 at 03:18 AM
-- Server version: 10.4.21-MariaDB
-- PHP Version: 8.0.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `fox`
--

-- --------------------------------------------------------

--
-- Table structure for table `interfaces`
--

CREATE TABLE `interfaces` (
  `interface` varchar(10) NOT NULL,
  `tipo` set('fisica','virtual') DEFAULT NULL,
  `funcao` set('lan','wan','vpn','dmz') DEFAULT NULL,
  `mac` varchar(20) DEFAULT NULL,
  `addressing` set('dinâmico','estatico') DEFAULT NULL,
  `status` set('ativo','inativo') DEFAULT NULL,
  `interface_pai` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `interfaces`
--

INSERT INTO `interfaces` (`interface`, `tipo`, `funcao`, `mac`, `addressing`, `status`, `interface_pai`) VALUES
('dmz_test', 'virtual', 'dmz', '', 'estatico', 'ativo', NULL),
('eth1', 'fisica', 'lan', 'AA:BB:CC:01:02:03', 'estatico', 'ativo', NULL),
('eth1:0', 'virtual', 'lan', '', 'estatico', 'ativo', 'eth1'),
('eth1:1', 'virtual', 'lan', '', 'dinâmico', 'inativo', 'eth1'),
('eth2', 'fisica', 'wan', 'DD:EE:CC:04:05:06', 'estatico', 'ativo', NULL),
('eth2:0', 'virtual', 'wan', '', 'dinâmico', 'ativo', 'eth2'),
('tap_bkp', 'virtual', 'vpn', '', 'estatico', 'ativo', NULL),
('tap_h2info', 'virtual', 'vpn', '', 'estatico', 'ativo', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `ipv4`
--

CREATE TABLE `ipv4` (
  `ipv4` varchar(15) NOT NULL,
  `ip4_mask` varchar(15) DEFAULT NULL,
  `ip4_gw` varchar(15) DEFAULT NULL,
  `ip4_net` varchar(15) DEFAULT NULL,
  `ip4_cidr` varchar(3) DEFAULT NULL,
  `ip4_bcast` varchar(15) DEFAULT NULL,
  `interface` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `ipv4`
--

INSERT INTO `ipv4` (`ipv4`, `ip4_mask`, `ip4_gw`, `ip4_net`, `ip4_cidr`, `ip4_bcast`, `interface`) VALUES
('10.10.20.1', '255.255.255.0', NULL, '10.10.20.0', '30', '10.10.20.255', 'tap_bkp'),
('172.16.26.1', '255.255.255.0', '', '172.16.26.0', '28', '172.16.26.255', 'tap_h2info'),
('192.168.15.240', '255.255.255.0', '192.168.15.1', '192.168.15.0', '24', '192.168.15.255', 'eth2'),
('192.168.15.241', '255.255.255.0', '192.168.15.1', '192.168.15.0', '24', '192.168.15.255', 'eth2:0'),
('192.168.5.1', '255.255.255.0', NULL, '192.168.5.0', '24', '192.168.5.255', 'eth1'),
('192.168.6.1', '255.255.255.0', NULL, '192.168.6.0', '24', '192.168.6.255', 'eth1:0'),
('192.168.7.1', '255.255.255.0', NULL, '192.168.7.0', '24', '192.168.7.255', 'eth1:1');

-- --------------------------------------------------------

--
-- Table structure for table `ipv6`
--

CREATE TABLE `ipv6` (
  `ipv6` varchar(15) NOT NULL,
  `ip6_mask` varchar(15) DEFAULT NULL,
  `ip6_gw` varchar(15) DEFAULT NULL,
  `ip6_net` varchar(15) DEFAULT NULL,
  `ip6_cidr` varchar(3) DEFAULT NULL,
  `ip6_bcast` varchar(15) DEFAULT NULL,
  `interface` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `proxy`
--

CREATE TABLE `proxy` (
  `id_proxy` int(11) NOT NULL,
  `modo` binary(1) DEFAULT NULL,
  `ip4_net` varchar(20) DEFAULT NULL,
  `ip6_net` varchar(60) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `interfaces`
--
ALTER TABLE `interfaces`
  ADD PRIMARY KEY (`interface`);

--
-- Indexes for table `ipv4`
--
ALTER TABLE `ipv4`
  ADD PRIMARY KEY (`ipv4`),
  ADD UNIQUE KEY `interface` (`interface`);

--
-- Indexes for table `ipv6`
--
ALTER TABLE `ipv6`
  ADD PRIMARY KEY (`ipv6`),
  ADD KEY `interface` (`interface`);

--
-- Indexes for table `proxy`
--
ALTER TABLE `proxy`
  ADD PRIMARY KEY (`id_proxy`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `ipv4`
--
ALTER TABLE `ipv4`
  ADD CONSTRAINT `ipv4_ibfk_1` FOREIGN KEY (`interface`) REFERENCES `interfaces` (`interface`);

--
-- Constraints for table `ipv6`
--
ALTER TABLE `ipv6`
  ADD CONSTRAINT `ipv6_ibfk_1` FOREIGN KEY (`interface`) REFERENCES `interfaces` (`interface`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
