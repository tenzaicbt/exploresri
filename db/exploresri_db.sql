
-- ExploreSri Database SQL Dump

CREATE DATABASE IF NOT EXISTS exploresri;
USE exploresri;

-- Users Table
CREATE TABLE users (
  user_id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('user', 'admin') DEFAULT 'user',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Destinations Table
CREATE TABLE destinations (
  destination_id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  description TEXT NOT NULL,
  image VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


-- Hotels Table
CREATE TABLE hotels (
  hotel_id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  destination_id INT NOT NULL,
  description TEXT NOT NULL,
  image VARCHAR(255),
  price DECIMAL(10,2) NOT NULL,
  address VARCHAR(255),
  contact VARCHAR(100),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (destination_id) REFERENCES destinations(destination_id) ON DELETE CASCADE
);

-- Bookings Table
CREATE TABLE bookings (
  booking_id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  hotel_id INT NOT NULL,
  check_in DATE NOT NULL,
  check_out DATE NOT NULL,
  guests INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
  FOREIGN KEY (hotel_id) REFERENCES hotels(hotel_id) ON DELETE CASCADE
);


-- Reviews Table
CREATE TABLE reviews (
    review_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    destination_id INT,
    rating INT,
    comment TEXT,
    date_posted DATE,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (destination_id) REFERENCES destinations(destination_id)
);

-- Itineraries Table
CREATE TABLE itineraries (
    itinerary_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    title VARCHAR(100),
    created_at DATE,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- Itinerary Details Table
CREATE TABLE itinerary_details (
    detail_id INT AUTO_INCREMENT PRIMARY KEY,
    itinerary_id INT,
    day INT,
    destination_id INT,
    notes TEXT,
    FOREIGN KEY (itinerary_id) REFERENCES itineraries(itinerary_id),
    FOREIGN KEY (destination_id) REFERENCES destinations(destination_id)
);

CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);


INSERT INTO admins (email, password)
VALUES ('admin@gmail.com', '$2y$10$bIqRXm9j00Dg6F7x2H91ruagcfTinDDM3EF8QufSIMgvoS9I/AHyy');



INSERT INTO destinations 
(name, description, location, category, image, province, top_attractions, latitude, longitude) 
VALUES 
('Colombo', 'Capital city with modern and colonial mix.', 'Colombo', 'City', 'colombo.jpg', 'Western', 'Galle Face Green, Gangaramaya Temple, Pettah Market', 6.927079, 79.861244),
('Kandy', 'A picturesque city surrounded by hills, famous for the Temple of the Tooth.', 'Kandy', 'Cultural', 'kandy.jpg', 'Central', 'Temple of the Tooth, Kandy Lake, Royal Botanical Gardens', 7.290571, 80.633728),
('Galle', 'A historic fortified city on the southwest coast.', 'Galle', 'Heritage', 'galle.jpg', 'Southern', 'Galle Fort, Lighthouse, Dutch Church', 6.032987, 80.217002),
('Nuwara Eliya', 'Cool climate town often called "Little England".', 'Nuwara Eliya', 'Hill Country', 'nuwara_eliya.jpg', 'Central', 'Gregory Lake, Horton Plains, Pedro Tea Estate', 6.949722, 80.789444),
('Sigiriya', 'Ancient rock fortress known as Lion Rock.', 'Sigiriya', 'Historic', 'sigiriya.jpg', 'Central', 'Sigiriya Rock Fortress, Frescoes, Water Gardens', 7.9570, 80.7603),
('Ella', 'A laid-back hill town with scenic beauty and hiking trails.', 'Ella', 'Hill Country', 'ella.jpg', 'Uva', 'Nine Arches Bridge, Little Adam’s Peak, Ravana Falls', 6.8753, 81.0460),
('Anuradhapura', 'An ancient city with massive stupas and sacred sites.', 'Anuradhapura', 'Cultural', 'anuradhapura.jpg', 'North Central', 'Sri Maha Bodhi, Ruwanwelisaya, Abhayagiri Dagoba', 8.3114, 80.4037),
('Mirissa', 'Popular beach town with whale watching.', 'Mirissa', 'Beach', 'mirissa.jpg', 'Southern', 'Whale Watching, Coconut Tree Hill, Secret Beach', 5.9485, 80.4550),
('Trincomalee', 'Eastern port city with rich history and beaches.', 'Trincomalee', 'Coastal', 'trincomalee.jpg', 'Eastern', 'Nilaveli Beach, Koneswaram Temple, Fort Frederick', 8.5874, 81.2152),
('Jaffna', 'Cultural and spiritual center of the Tamil community.', 'Jaffna', 'Cultural', 'jaffna.jpg', 'Northern', 'Nallur Temple, Jaffna Fort, Casuarina Beach', 9.6685, 80.0074);

INSERT INTO hotels (name, location, description, price_per_night, contact_info, image, destination_id, rating, address, price)
VALUES 
('Galle Face Hotel', 'Colombo', 'A historic seafront hotel offering colonial elegance and luxury.', 22000, '0112 541010', 'galle_face_hotel.jpg', 1, 4.6, '2 Galle Road, Colombo 03', 22000),
('Earls Regency', 'Kandy', 'Luxury hotel nestled in the hills with panoramic views of the Mahaweli River.', 18000, '0812 422122', 'earls_regency.jpg', 2, 4.5, '84 Tenna Kumbura, Kandy', 18000),
('Jetwing Lighthouse', 'Galle', 'A modern heritage hotel designed by Geoffrey Bawa near Galle Fort.', 25000, '0912 224745', 'jetwing_lighthouse.jpg', 3, 4.7, 'Dadella, Galle 80000', 25000),
('Grand Hotel', 'Nuwara Eliya', 'A colonial-style hotel with British charm and beautiful gardens.', 16000, '0522 222881', 'grand_hotel.jpg', 4, 4.4, 'Grand Hotel Road, Nuwara Eliya 22200', 16000),
('98 Acres Resort & Spa', 'Ella', 'Eco-luxury resort surrounded by tea plantations and mountain views.', 28000, '0572 050050', '98_acres.jpg', 6, 4.8, 'Passara Rd, Ella 90090', 28000);
