
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

INSERT INTO admins (username, password)
VALUES ('admin', 'admin123');


INSERT INTO destinations (name, description, image) VALUES
('Galle', 'Historic city with colonial architecture and beach views.', 'galle.jpg'),
('Sigiriya', 'Ancient rock fortress with gardens and frescoes.', 'sigiriya.jpg'),
('Colombo', 'Capital city with a mix of modern and colonial buildings.', 'colombo.jpg'),
('Kandy', 'Sacred city with the Temple of the Tooth and lake views.', 'kandy.jpg');

-- Sample Hotels (Sri Lankan data)
INSERT INTO hotels (name, location, description, price_per_night, contact_info, image, destination_id) VALUES
('Jetwing Lighthouse', 'Galle', 'Luxury hotel by the sea with excellent views.', 250.00, '+94 91 2223744', 'jetwing_lighthouse.jpg', 1),
('Le Grand Galle', 'Galle', 'Upscale beachside hotel near Galle Fort.', 230.00, '+94 91 2228228', 'le_grand_galle.jpg', 1),
('Heritance Kandalama', 'Dambulla', 'Eco-friendly hotel with scenic architecture.', 220.00, '+94 66 5555000', 'heritance_kandalama.jpg', 2),
('Aliya Resort & Spa', 'Sigiriya', 'Nature-themed resort near Sigiriya Rock.', 190.00, '+94 66 2050250', 'aliya_resort.jpg', 2),
('Cinnamon Grand Colombo', 'Colombo', 'Luxury 5-star hotel with fine dining.', 200.00, '+94 11 2437437', 'cinnamon_grand.jpg', 3),
('Cinnamon Red Colombo', 'Colombo', 'Modern city hotel with rooftop pool.', 130.00, '+94 11 2145145', 'cinnamon_red.jpg', 3),
('Amaya Hills', 'Kandy', 'Hilltop resort with views of Kandy.', 175.00, '+94 81 4474022', 'amaya_hills.jpg', 4),
('Golden Crown Hotel', 'Kandy', 'Elegant hotel with modern facilities.', 185.00, '+94 81 2030500', 'golden_crown.jpg', 4);
