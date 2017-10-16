-- phpMyAdmin SQL Dump -- version 4.2.12deb2+deb8u2 -- 
http://www.phpmyadmin.net -- -- Host: localhost -- Generation Time: Oct 
16, 2017 at 08:17 PM -- Server version: 5.5.54-0+deb8u1 -- PHP Version: 
5.6.29-0+deb8u1 SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO"; SET time_zone = 
"+00:00"; /*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT 
*/; /*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */; 
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */; 
/*!40101 SET NAMES utf8 */; -- -- Database: `piwall` -- -- 
-------------------------------------------------------- -- -- Table 
structure for table `clients` -- CREATE TABLE IF NOT EXISTS `clients` ( 
`id` int(11) NOT NULL,
  `wall_id` int(11) NOT NULL,
  `serial_number` varchar(255) NOT NULL,
  `RSA_key` varchar(255) NOT NULL,
  `confirmed` tinyint(1) NOT NULL ) ENGINE=InnoDB AUTO_INCREMENT=2 
DEFAULT CHARSET=latin1; -- 
-------------------------------------------------------- -- -- Table 
structure for table `walls` -- CREATE TABLE IF NOT EXISTS `walls` ( `id` 
int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `width_pixel` int(11) NOT NULL,
  `height_pixel` int(11) NOT NULL,
  `width_tile` int(11) NOT NULL,
  `height_tile` int(11) NOT NULL ) ENGINE=InnoDB AUTO_INCREMENT=2 
DEFAULT CHARSET=latin1; -- -- Dumping data for table `walls` -- INSERT 
INTO `walls` (`id`, `name`, `width_pixel`, `height_pixel`, `width_tile`, 
`height_tile`) VALUES (1, 'Piwall', 1920, 1080, 1, 1); -- -- Indexes for 
dumped tables -- -- -- Indexes for table `clients` -- ALTER TABLE 
`clients`
 ADD PRIMARY KEY (`id`), ADD KEY `id` (`id`), ADD KEY `wall_id` 
(`wall_id`); -- -- Indexes for table `walls` -- ALTER TABLE `walls`
 ADD PRIMARY KEY (`id`); -- -- AUTO_INCREMENT for dumped tables -- -- -- 
AUTO_INCREMENT for table `clients` -- ALTER TABLE `clients` MODIFY `id` 
int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2; -- -- AUTO_INCREMENT 
for table `walls` -- ALTER TABLE `walls` MODIFY `id` int(11) NOT NULL 
AUTO_INCREMENT,AUTO_INCREMENT=2; -- -- Constraints for dumped tables -- 
-- -- Constraints for table `clients` -- ALTER TABLE `clients` ADD 
CONSTRAINT `clients_ibfk_1` FOREIGN KEY (`wall_id`) REFERENCES `walls` 
(`id`); /*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */; 
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */; 
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
