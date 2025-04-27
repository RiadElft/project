-- Create users table if it doesn't exist
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'client') DEFAULT 'client',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create initial admin user (password: admin123)
-- Note: The hash might be different if regenerated, like $2y$10$YYnZL7RYnJgctjH5Vkl5ueql0cgtXbzwDfJ52lZgagP0nQ.4yoBAm
INSERT INTO users (name, email, password, role) 
SELECT 'Admin', 'admin@example.com', '$2y$10$YYnZL7RYnJgctjH5Vkl5ueql0cgtXbzwDfJ52lZgagP0nQ.4yoBAm', 'admin' 
ON DUPLICATE KEY UPDATE password = VALUES(password); -- Update hash if user exists

-- Create airlines table
CREATE TABLE IF NOT EXISTS airlines (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    logo_image VARCHAR(255) NULL,
    description TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Modify flights table
-- Step 1: Drop old columns and add the new one (MATCHING TYPE: INT)
ALTER TABLE flights 
    DROP COLUMN IF EXISTS airline, 
    DROP COLUMN IF EXISTS logo_image, 
    ADD COLUMN IF NOT EXISTS airline_id INT NULL DEFAULT NULL AFTER id; -- Use INT (signed) to match airlines.id, allow NULL temporarily

-- Step 2: Add the foreign key constraint separately
-- Check if constraint already exists before adding (optional but good practice)
SET @constraint_name = 'fk_flight_airline';
SET @sql = CONCAT(
    'SELECT COUNT(*) INTO @constraint_exists 
     FROM information_schema.TABLE_CONSTRAINTS 
     WHERE CONSTRAINT_SCHEMA = DATABASE() 
     AND CONSTRAINT_NAME = "', @constraint_name, '" 
     AND TABLE_NAME = \'flights\';'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = IF(@constraint_exists = 0, 
    'ALTER TABLE flights ADD CONSTRAINT fk_flight_airline FOREIGN KEY (airline_id) REFERENCES airlines(id) ON DELETE SET NULL;', 
    'SELECT \'Constraint already exists.\'');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Seed Airlines Data
INSERT IGNORE INTO airlines (name, logo_image, description)
VALUES 
('Air France', 'airfrance.png', 'Compagnie aérienne nationale française.'),
('British Airways', 'britishairways.png', 'Compagnie aérienne porte-drapeau du Royaume-Uni.'),
('Lufthansa', 'lufthansa.png', 'Plus grande compagnie aérienne allemande.');

-- Update Sample Flights to use airline_id (Assuming IDs 1, 2, 3 for seeded airlines)
-- Warning: This assumes the INSERT IGNORE above resulted in IDs 1, 2, 3. 
-- A more robust approach involves subqueries or updating after insertion.
UPDATE flights SET airline_id = 1 WHERE flight_number = 'AF006';
UPDATE flights SET airline_id = 2 WHERE flight_number = 'BA005';
UPDATE flights SET airline_id = 3 WHERE flight_number = 'LH103';

-- Now make airline_id NOT NULL if desired (ensure all existing flights have an ID)
-- ALTER TABLE flights MODIFY COLUMN airline_id INT UNSIGNED NOT NULL;

-- Create flights table
CREATE TABLE IF NOT EXISTS flights (
    id INT AUTO_INCREMENT PRIMARY KEY,
    flight_number VARCHAR(20) NOT NULL,
    departure_city VARCHAR(100) NOT NULL,
    arrival_city VARCHAR(100) NOT NULL,
    departure_time DATETIME NOT NULL,
    arrival_time DATETIME NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    stops INT DEFAULT 0,
    available_seats INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY flight_number_unique (flight_number) -- Added unique constraint
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create hotels table
CREATE TABLE IF NOT EXISTS hotels (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    city VARCHAR(100) NOT NULL,
    country VARCHAR(100) NOT NULL,
    address TEXT NULL,
    rating TINYINT DEFAULT 0, -- 0-5 stars
    price_per_night DECIMAL(10, 2) NOT NULL,
    description TEXT NULL,
    amenities TEXT NULL, -- Comma-separated or JSON
    image VARCHAR(255) NULL, -- Store main image filename
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create cruises table
CREATE TABLE IF NOT EXISTS cruises (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    cruise_line VARCHAR(100) NOT NULL,
    ship_name VARCHAR(100) NULL,
    departure_port VARCHAR(100) NOT NULL,
    destination_ports TEXT NULL, -- Comma-separated or JSON of ports visited
    duration_days INT NOT NULL,
    departure_date DATE NULL,
    price DECIMAL(10, 2) NOT NULL,
    description TEXT NULL,
    image VARCHAR(255) NULL, -- Store main image filename
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- --- SEED SAMPLE DATA --- 

-- Sample Flights (Ignoring ID to let auto-increment work, adding flight numbers)
INSERT IGNORE INTO flights (flight_number, departure_city, arrival_city, departure_time, arrival_time, price, stops, available_seats)
VALUES 
('AF006', 'Paris', 'New York', '2024-09-15 08:00:00', '2024-09-15 10:30:00', 650.00, 0, 150),
('BA005', 'London', 'Tokyo', '2024-09-20 14:00:00', '2024-09-21 09:00:00', 820.50, 1, 120),
('LH103', 'Frankfurt', 'Paris', '2024-09-18 10:15:00', '2024-09-18 11:30:00', 150.75, 0, 80);

-- Sample Hotels (Ignoring ID)
INSERT IGNORE INTO hotels (name, city, country, rating, price_per_night, image)
VALUES
('Hôtel Plaza Athénée', 'Paris', 'France', 5, 950.00, 'hotel_paris.jpg'),
('Park Hyatt Tokyo', 'Tokyo', 'Japan', 5, 780.00, 'hotel_tokyo.jpg'),
('The Standard, High Line', 'New York', 'USA', 4, 450.00, 'hotel_newyork.jpg');

-- Sample Cruises (Ignoring ID)
INSERT IGNORE INTO cruises (name, cruise_line, departure_port, duration_days, price, image)
VALUES
('Croisière Méditerranéenne', 'MSC Croisières', 'Marseille', 7, 899.00, 'cruise_med.jpg'),
('Aventure dans les Caraïbes', 'Royal Caribbean', 'Miami', 10, 1250.00, 'cruise_caribbean.jpg'),
('Fjords Norvégiens', 'Hurtigruten', 'Bergen', 8, 1500.00, 'cruise_norway.jpg'); 