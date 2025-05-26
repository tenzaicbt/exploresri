
-- Create admins table
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

-- Insert default admin user (username: admin, password: admin123)
INSERT INTO admins (username, password)
VALUES ('admin', '$2y$10$wfyRHMz1a7xwYdPBcrZ8YOdEIlUeT6ZLq2biZrbXBGS6gCk.qgmuC'); -- password = admin123