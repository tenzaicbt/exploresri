
-- Create admins table
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

-- admin user
INSERT INTO admins (username, password)
VALUES ('$2y$10$byWenGXV5oBZelWH2yDuD.kTHr686WKzwNsH2of/SO5NYrE1LQuo.');