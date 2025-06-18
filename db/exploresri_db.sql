-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 18, 2025 at 09:58 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `exploresri`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `admin_id` int(11) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`admin_id`, `username`, `password`) VALUES
(1, 'admin', '$2y$10$tioIehQ1Ai38ZoEVEJbK0OXD5HWCUojfPMOT8DEP5rmKALJdlymTm');

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `attendance_id` int(11) NOT NULL,
  `guide_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `status` enum('Present','Absent','Late') NOT NULL DEFAULT 'Present',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `booking_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `destination_id` int(11) DEFAULT NULL,
  `hotel_id` int(11) NOT NULL,
  `booking_date` datetime DEFAULT current_timestamp(),
  `travel_date` date NOT NULL,
  `status` varchar(50) DEFAULT 'pending',
  `payment_status` varchar(50) DEFAULT 'unpaid',
  `nights` int(11) DEFAULT 1,
  `total_price` decimal(10,2) DEFAULT NULL,
  `check_in_date` date DEFAULT NULL,
  `check_out_date` date DEFAULT NULL,
  `guide_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`booking_id`, `user_id`, `destination_id`, `hotel_id`, `booking_date`, `travel_date`, `status`, `payment_status`, `nights`, `total_price`, `check_in_date`, `check_out_date`, `guide_id`) VALUES
(54, 2, 3, 9, '2025-06-01 22:20:49', '0000-00-00', 'confirmed', 'Paid', 3, NULL, '2025-06-09', '2025-06-12', NULL),
(61, 1, 1, 11, '2025-06-09 13:59:05', '0000-00-00', 'Pending', 'unpaid', 2, NULL, '2025-06-11', '2025-06-13', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `destinations`
--

CREATE TABLE `destinations` (
  `destination_id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `province` varchar(255) DEFAULT NULL,
  `top_attractions` varchar(255) DEFAULT NULL,
  `latitude` varchar(50) DEFAULT NULL,
  `longitude` varchar(50) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `destinations`
--

INSERT INTO `destinations` (`destination_id`, `name`, `description`, `location`, `category`, `image`, `province`, `top_attractions`, `latitude`, `longitude`, `status`) VALUES
(1, 'Colombo', 'The vibrant commercial capital of Sri Lanka, known for its colonial heritage and seaside views', 'Colombo', 'City', 'colombo.jpg', 'Western', 'Galle Face Green, Gangaramaya Temple, Pettah Market', '6.9271', '79.8612', 'active'),
(2, 'Kandy', 'Famous for the Temple of the Tooth and lush hill-country scenery.', 'Kandy', 'Cultural', 'kandy.jpg', 'Central', 'Temple of the Tooth, Kandy Lake, Royal Botanical Gardens', '7.2906', '80.6337', 'active'),
(3, 'Sigiriya', 'An ancient rock fortress and UNESCO World Heritage Site.', 'Sigiriya', 'Historical', 'sigiriya.jpg', 'Central', 'Sigiriya Rock Fortress, Frescoes, Water Gardens', '7.9570', '80.7603', 'active'),
(4, 'Ella', 'A small mountain town with panoramic views and tea plantations.', 'Ella', 'Nature', 'ella.jpg', 'Uva', 'Nine Arches Bridge, Little Adam’s Peak, Ella Rock', '6.8667', '81.0461', 'active'),
(5, 'Galle', 'A fortified city on the southwest coast, famous for its Dutch colonial architecture.', 'Galle', 'Coastal', 'galle.jpg', 'Southern', 'Galle Fort, Lighthouse, Unawatuna Beach', '6.0535', '80.2210', 'active'),
(6, 'Nuwara Eliya', 'Known as \"Little England\" for its cool climate and British-style buildings.', 'Nuwara Eliya', 'Hill Station', 'nuwaraeliya.jpg', 'Central', 'Gregory Lake, Tea Estates, Horton Plains', '6.9497', '80.7891', 'active'),
(7, 'Anuradhapura', 'A sacred city with ancient Buddhist ruins and stupas.', 'Anuradhapura', 'Heritage', 'anuradhapura.jpg', 'North Central', 'Ruwanwelisaya, Sri Maha Bodhi, Isurumuniya', '8.3114', '80.4037', 'active'),
(8, 'Polonnaruwa', 'A well-preserved medieval capital with impressive archaeological ruins.', 'Polonnaruwa', 'Historical', 'polonnaruwa.jpg', 'North Central', 'Gal Vihara, Royal Palace, Parakrama Samudraya', '7.9403', '81.0188', 'active'),
(9, 'Trincomalee', 'A port city with beautiful beaches and cultural landmarks.', 'Trincomalee', 'Beach', 'trincomalee.jpg', 'Eastern', 'Nilaveli Beach, Fort Frederick, Koneswaram Temple', '8.5874', '81.2152', 'active'),
(10, 'Mirissa', 'A laid-back beach town known for whale watching and surfing.', 'Mirissa', 'Beach', 'mirissa.jpg', 'Southern', 'Whale Watching, Mirissa Beach, Coconut Tree Hill', '5.9476', '80.4591', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `guide`
--

CREATE TABLE `guide` (
  `guide_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `country` varchar(100) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `languages` varchar(255) DEFAULT NULL,
  `experience_years` int(11) DEFAULT 0,
  `bio` text DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `contact_info` varchar(100) DEFAULT NULL,
  `price_per_day` decimal(10,2) DEFAULT 0.00,
  `rating` float DEFAULT 0,
  `is_verified` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `status` varchar(50) DEFAULT 'active',
  `is_available` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `guide`
--

INSERT INTO `guide` (`guide_id`, `name`, `email`, `country`, `password`, `languages`, `experience_years`, `bio`, `photo`, `contact_info`, `price_per_day`, `rating`, `is_verified`, `created_at`, `status`, `is_available`) VALUES
(1, 'Yohan Koshala Hetti Archchi', 'ethozhub@gmail.com', NULL, '$2y$10$FTW7XAgzCIjgr.apPuvZROlO0XbfU6DUubBjNk8rKTBEHsydD9idi', 'English,Sinhala,Tamil', 5, 'Experienced guide specializing in city tours.', '1749025744_istockphoto-1309315007-612x612.jpg', '+94 57 222 8888', 150.00, 0, 1, '2025-06-04 13:59:04', 'active', 1),
(2, 'Tharindu Darshana', 'ethoz@gmail.com', NULL, '$2y$10$k3cfqrJKtXReO6HbnA/9n.y9tdfci.YjA2XUnSva8zxGyZypn.9Ve', 'English,Sinhala', 10, 'Friendly and knowledgeable guide in Southeast Asia.', '1749026775_istockphoto-1409155424-612x612.jpg', '+94 91 222 3744', 210.00, 0, 1, '2025-06-04 14:16:15', 'active', 1);

-- --------------------------------------------------------

--
-- Table structure for table `guide_bookings`
--

CREATE TABLE `guide_bookings` (
  `booking_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `guide_id` int(11) NOT NULL,
  `travel_date` date NOT NULL,
  `duration_days` int(11) NOT NULL,
  `status` varchar(50) DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `payment_status` varchar(50) DEFAULT 'unpaid',
  `payment_method` varchar(50) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `check_in_date` date DEFAULT NULL,
  `check_out_date` date DEFAULT NULL,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `guide_payments`
--

CREATE TABLE `guide_payments` (
  `payment_id` int(11) NOT NULL,
  `booking_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_date` datetime DEFAULT current_timestamp(),
  `status` varchar(20) DEFAULT 'paid'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `guide_reviews`
--

CREATE TABLE `guide_reviews` (
  `review_id` int(11) NOT NULL,
  `guide_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` between 1 and 5),
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hotels`
--

CREATE TABLE `hotels` (
  `hotel_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `price_per_night` decimal(10,2) DEFAULT NULL,
  `contact_info` varchar(255) DEFAULT NULL,
  `rating` decimal(2,1) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `destination_id` int(11) DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'active',
  `facilities` text DEFAULT NULL,
  `image_gallery` text DEFAULT NULL,
  `popular_features` text DEFAULT NULL,
  `map_embed_link` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hotels`
--

INSERT INTO `hotels` (`hotel_id`, `name`, `location`, `description`, `price_per_night`, `contact_info`, `rating`, `address`, `destination_id`, `latitude`, `longitude`, `image`, `status`, `facilities`, `image_gallery`, `popular_features`, `map_embed_link`) VALUES
(7, 'Jetwing Lighthouse', 'Galle', 'Colonial-style beachfront hotel offering panoramic views and heritage luxury.', 28000.00, '+94 91 222 3744', 5.0, 'Dadella, Galle 80000', 5, NULL, NULL, 'jetwinglighthouse.jpg', 'active', NULL, NULL, NULL, NULL),
(8, '98 Acres Resort & Spa', 'Ella', 'Eco-friendly resort nestled among tea plantations with stunning Ella Gap views.', 35000.00, '+94 57 222 8888', 5.0, 'Passara Road, Ella 90090', 4, NULL, NULL, '98acres.jpg', 'active', NULL, NULL, NULL, NULL),
(9, 'Earl’s Regency', 'Kandy', 'Elegant hillside hotel with views of the Mahaweli River and royal heritage.', 32000.00, '+94 81 242 2122', 5.0, 'Earl’s Regency, Tennekumbura, Kandy', 3, NULL, NULL, 'earlsregency.jpg', 'active', NULL, NULL, NULL, NULL),
(10, 'Galle Face Hotel', 'Colombo', 'Historic oceanfront hotel with colonial charm and sunset views.', 40000.00, '+94 11 254 1010', 5.0, '2 Galle Road, Colombo 03', 2, NULL, NULL, '6837d72c88d7a_gallefacehotel.jpg', 'active', NULL, NULL, NULL, NULL),
(11, 'Grand Hotel', 'Nuwara Eliya', 'The Grand Hotel, a timeless masterpiece, where heritage and natural beauty converge. Nestled amid lush surroundings, this luxury hotel in Nuwara Eliya, exudes luxury and sophistication. With its rich history, exquisite architecture, and top-tier amenities, it offers an enchanting blend of old world charm and modern opulence, making it a premier destination for discerning travelers.', 20000.00, '+94 52 222 2882', 5.0, 'Nuwara Eliya 22200', 1, NULL, NULL, '683b530c29410_grand-hotel-graden-1920x1000-1.jpg', 'active', 'Free Wifi, Family rooms, Airport shuttle (free), Parking, Restaurant ,Non-smoking rooms, Room service, Air conditioning ,Tea/Coffee Maker in All Rooms , Good Breakfast', '1748718348_grand-hotel-contact-us-1920x800-1.jpg,1748718348_Discover-Grand-Luxury-1920x1166-v1.jpg,1748718388_grand-hotel-graden-1920x1000-1.jpg', 'Free Wifi, Family rooms, Airport shuttle (free),Good Breakfast', '');

-- --------------------------------------------------------

--
-- Table structure for table `itineraries`
--

CREATE TABLE `itineraries` (
  `itinerary_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `destination_id` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `itinerary`
--

CREATE TABLE `itinerary` (
  `id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `booking_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_date` datetime DEFAULT current_timestamp(),
  `status` varchar(20) DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `booking_id`, `user_id`, `amount`, `payment_method`, `payment_date`, `status`) VALUES
(11, 54, 2, 96000.00, 'paypal', '2025-06-01 22:20:55', 'Paid');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `review_id` int(11) NOT NULL,
  `hotel_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `comment` text DEFAULT NULL,
  `review_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`review_id`, `hotel_id`, `user_id`, `rating`, `comment`, `review_date`) VALUES
(1, 11, 1, 5, 'We had a wonderful stay at Earl’s Regency Hotel! The location is absolutely stunning, surrounded by lush green hills with breathtaking views. The hotel itself is elegant and well-maintained, with spacious and comfortable rooms. The staff were extremely friendly and helpful throughout our stay, making us feel truly welcome. We especially enjoyed the delicious breakfast buffet and the relaxing pool area. It’s also very close to the city center and the Temple of the Tooth, which made sightseeing easy. Highly recommended for anyone visiting Kandy!', '2025-05-31 22:32:17');

-- --------------------------------------------------------

--
-- Table structure for table `transport_companies`
--

CREATE TABLE `transport_companies` (
  `company_id` int(11) NOT NULL,
  `company_name` varchar(150) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `website` varchar(255) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` enum('active','inactive','suspended') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `transport_companies`
--

INSERT INTO `transport_companies` (`company_id`, `company_name`, `email`, `phone`, `address`, `password`, `website`, `logo`, `created_at`, `updated_at`, `status`) VALUES
(1, 'Uber', 'uber@gmail.com', '0766446354', 'colombo', '$2y$10$komKAKkZbY2Gih4.KpiwCuME0lONv0g5Rwi85CDsfyKgF2emOJSvy', 'www.uber.com/lk/en/', 'uploads/logos/logo_68522a51984388.67441189.jpg', '2025-06-07 14:31:38', '2025-06-18 06:37:00', 'active'),
(2, 'Pick Me', 'pickme@gmail.com', '0766446355', 'colombo', '$2y$10$Jplo8rcnSt.qjiut9Pvn3Oc/L3Fxm1bMiC6xeWVNHJdo/cqhicwRq', 'www.pickme.com/lk/en/', 'uploads/logos/logo_68525e578e8113.51688393.png', '2025-06-18 06:36:07', '2025-06-18 06:36:07', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `contact` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `is_verified` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(50) DEFAULT 'active',
  `role` varchar(50) DEFAULT 'user',
  `profile_pic` varchar(255) DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `email`, `contact`, `password`, `is_verified`, `created_at`, `status`, `role`, `profile_pic`, `contact_number`, `country`) VALUES
(1, 'yohan', 'ethozhub@gmail.com', NULL, '$2y$10$0sC5j.4UeUgaLz4k09Jrr.qVLRDGyA7aCRsknRm14yz1PyHmLNsN2', 0, '2025-05-28 12:18:20', 'active', 'user', 'uploads/1749035324_6840293cd24c9.jpg', '+946553163', 'Sri Lanka'),
(2, 'koshala', 'ethoz@gmail.com', NULL, '$2y$10$Wo4IIWl9zGXwfFVDi6ZrROeyB.MpLF/.xQr5QOUFtIEbf7rn3v62K', 0, '2025-05-31 17:30:38', 'active', 'user', 'uploads/1748795507_Screenshot 2025-04-20 001949.png', '0766446354', 'Sri Lanka');

-- --------------------------------------------------------

--
-- Table structure for table `vehicles`
--

CREATE TABLE `vehicles` (
  `vehicle_id` int(11) NOT NULL,
  `company_id` int(11) DEFAULT NULL,
  `model` varchar(100) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `capacity` int(11) DEFAULT NULL,
  `rental_price` decimal(10,2) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `features` text DEFAULT NULL,
  `registration_number` varchar(50) DEFAULT NULL,
  `fuel_type` varchar(50) DEFAULT NULL,
  `availability` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `image_gallery` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vehicles`
--

INSERT INTO `vehicles` (`vehicle_id`, `company_id`, `model`, `type`, `capacity`, `rental_price`, `image`, `description`, `features`, `registration_number`, `fuel_type`, `availability`, `created_at`, `image_gallery`) VALUES
(3, 1, 'BYD ATTO 3', 'SUV', 4, 200.00, '1750158806_byd-tang-2024.jpg', 'The BYD ATTO 3 redefines electric driving with cutting-edge BYD Blade Battery technology, Dragon Face design, Integrated Sports Seats and intelligent features-offering a thrilling, efficient, and futuristic driving experience.\r\nAdvanced\r\nSuperior', 'Air Conditioning,Bluetooth,GPS,Rear Camera', 'ABC-5678', 'Electric', 1, '2025-06-17 11:13:26', '1750158806_BYD-ATTO-3-b-800x800-1.png,1750158806_BYD-ATTO-3-s-800x800-1.png,1750158806_BYD-ATTO-3-f-800x800-1.png'),
(4, 1, 'BYD M6', 'Van', 5, 200.00, '1750159041_black.webp', 'Discover spacious comfort and cutting-edge technology. Enjoy a luxurious, smooth 6-seater ride designed for ultimate convenience and unparalleled driving pleasure.', 'Air Conditioning,Bluetooth,GPS,Rear Camera', 'ABC-1234', 'Electric', 1, '2025-06-17 11:17:21', '1750159041_BYD-M6-b-800x800-1.png,1750159041_BYD-M6-s-800x800-1.png,1750159041_BYD-M6-f-800x800-1.png'),
(5, 1, 'BYD SEALION 6 (Superior)', 'SUV', 4, 150.00, '1750159309_composit.jpg', 'Experience the future with cutting-edge DM-i Plug-in Hybrid technology with an Ocean Aesthetics’ design, and fusion performance. Drive smarter, cleaner, and seamlessly.', 'Air Conditioning,Bluetooth,GPS,Rear Camera,Drive smarter, cleaner,seamlessly', 'ABC-1345', 'Electric', 1, '2025-06-17 11:21:49', '1750159309_BYD-SEALION-6-b-800x800-1.png,1750159309_BYD-SEALION-6-s-800x800-1.png,1750159309_BYD-SEALION-6-f-800x800-1.png'),
(6, 2, 'NISSAN TRITON L200', 'Cab', 3, 350.00, '1750229412_2024-mitsubishi-l200-triton-unofficial-rendering-front.jpg', 'Triton Club Cab Pick Up is on its way. Pick up a Triton that conquers tough conditions without compromising on comfort. With improved driver control, a spacious and safer cabin, as well as a 3.5 tonne towing capacity, your workday duties and outdoor weekends are sorted. Enquire today to find out more.', 'Air Conditioning,Bluetooth,GPS,Rear Camera, FPB Pack, includes Winch Cradle, Under Ride Protection , Black Steel Front Protection Bar for LED headlamp models', 'ABC-1238', 'Petrol', 1, '2025-06-18 06:50:12', '1750229412_img_car_03_03.png,1750229412_img_car_03_02.png,1750229412_img_car_03_01.png');

-- --------------------------------------------------------

--
-- Table structure for table `vehicle_bookings`
--

CREATE TABLE `vehicle_bookings` (
  `booking_id` int(11) NOT NULL,
  `vehicle_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `booking_start` date NOT NULL,
  `booking_end` date NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `status` enum('pending','confirmed','cancelled','completed') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `payment_status` enum('unpaid','paid') NOT NULL DEFAULT 'unpaid',
  `payment_method` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vehicle_bookings`
--

INSERT INTO `vehicle_bookings` (`booking_id`, `vehicle_id`, `user_id`, `booking_start`, `booking_end`, `total_price`, `status`, `created_at`, `payment_status`, `payment_method`) VALUES
(1, 3, 1, '2025-06-23', '2025-06-26', 600.00, 'confirmed', '2025-06-17 11:27:18', 'unpaid', NULL),
(2, 5, 1, '2025-06-19', '2025-06-22', 450.00, 'pending', '2025-06-18 06:25:29', 'unpaid', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `vehicle_payments`
--

CREATE TABLE `vehicle_payments` (
  `payment_id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `payment_date` datetime NOT NULL DEFAULT current_timestamp(),
  `status` varchar(50) NOT NULL DEFAULT 'Paid'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vehicle_payments`
--

INSERT INTO `vehicle_payments` (`payment_id`, `booking_id`, `user_id`, `amount`, `payment_method`, `payment_date`, `status`) VALUES
(1, 1, 1, 600.00, 'paypal', '2025-06-17 16:57:24', 'Paid');

-- --------------------------------------------------------

--
-- Table structure for table `vehicle_reviews`
--

CREATE TABLE `vehicle_reviews` (
  `review_id` int(11) NOT NULL,
  `vehicle_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` tinyint(4) NOT NULL CHECK (`rating` between 1 and 5),
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`attendance_id`),
  ADD KEY `fk_guide` (`guide_id`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `destination_id` (`destination_id`),
  ADD KEY `hotel_id` (`hotel_id`);

--
-- Indexes for table `destinations`
--
ALTER TABLE `destinations`
  ADD PRIMARY KEY (`destination_id`);

--
-- Indexes for table `guide`
--
ALTER TABLE `guide`
  ADD PRIMARY KEY (`guide_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `guide_bookings`
--
ALTER TABLE `guide_bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `guide_id` (`guide_id`);

--
-- Indexes for table `guide_payments`
--
ALTER TABLE `guide_payments`
  ADD PRIMARY KEY (`payment_id`);

--
-- Indexes for table `guide_reviews`
--
ALTER TABLE `guide_reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `guide_id` (`guide_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `hotels`
--
ALTER TABLE `hotels`
  ADD PRIMARY KEY (`hotel_id`),
  ADD KEY `destination_id` (`destination_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `hotel_id` (`hotel_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `transport_companies`
--
ALTER TABLE `transport_companies`
  ADD PRIMARY KEY (`company_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD PRIMARY KEY (`vehicle_id`);

--
-- Indexes for table `vehicle_bookings`
--
ALTER TABLE `vehicle_bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `vehicle_id` (`vehicle_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `vehicle_payments`
--
ALTER TABLE `vehicle_payments`
  ADD PRIMARY KEY (`payment_id`);

--
-- Indexes for table `vehicle_reviews`
--
ALTER TABLE `vehicle_reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `vehicle_id` (`vehicle_id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `attendance_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `destinations`
--
ALTER TABLE `destinations`
  MODIFY `destination_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `guide`
--
ALTER TABLE `guide`
  MODIFY `guide_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `guide_bookings`
--
ALTER TABLE `guide_bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `guide_payments`
--
ALTER TABLE `guide_payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `guide_reviews`
--
ALTER TABLE `guide_reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hotels`
--
ALTER TABLE `hotels`
  MODIFY `hotel_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `transport_companies`
--
ALTER TABLE `transport_companies`
  MODIFY `company_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `vehicles`
--
ALTER TABLE `vehicles`
  MODIFY `vehicle_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `vehicle_bookings`
--
ALTER TABLE `vehicle_bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `vehicle_payments`
--
ALTER TABLE `vehicle_payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `vehicle_reviews`
--
ALTER TABLE `vehicle_reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `fk_guide` FOREIGN KEY (`guide_id`) REFERENCES `guide` (`guide_id`) ON DELETE CASCADE;

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`destination_id`) REFERENCES `destinations` (`destination_id`),
  ADD CONSTRAINT `bookings_ibfk_3` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`hotel_id`);

--
-- Constraints for table `guide_bookings`
--
ALTER TABLE `guide_bookings`
  ADD CONSTRAINT `guide_bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `guide_bookings_ibfk_2` FOREIGN KEY (`guide_id`) REFERENCES `guide` (`guide_id`) ON DELETE CASCADE;

--
-- Constraints for table `guide_reviews`
--
ALTER TABLE `guide_reviews`
  ADD CONSTRAINT `guide_reviews_ibfk_1` FOREIGN KEY (`guide_id`) REFERENCES `guide` (`guide_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `guide_reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `hotels`
--
ALTER TABLE `hotels`
  ADD CONSTRAINT `hotels_ibfk_1` FOREIGN KEY (`destination_id`) REFERENCES `destinations` (`destination_id`) ON DELETE SET NULL;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`booking_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`hotel_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `vehicle_bookings`
--
ALTER TABLE `vehicle_bookings`
  ADD CONSTRAINT `fk_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `fk_vehicle` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`vehicle_id`),
  ADD CONSTRAINT `vehicle_bookings_ibfk_1` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`vehicle_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `vehicle_bookings_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `vehicle_reviews`
--
ALTER TABLE `vehicle_reviews`
  ADD CONSTRAINT `vehicle_reviews_ibfk_1` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`vehicle_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `vehicle_reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
