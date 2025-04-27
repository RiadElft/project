-- Create database
CREATE DATABASE IF NOT EXISTS travel_booking;
USE travel_booking;

-- Users table
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Flights table
CREATE TABLE flights (
    id INT PRIMARY KEY AUTO_INCREMENT,
    flight_number VARCHAR(20) NOT NULL,
    airline VARCHAR(100) NOT NULL,
    departure_city VARCHAR(100) NOT NULL,
    arrival_city VARCHAR(100) NOT NULL,
    departure_time DATETIME NOT NULL,
    arrival_time DATETIME NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    available_seats INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Hotels table
CREATE TABLE hotels (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    city VARCHAR(100) NOT NULL,
    address TEXT NOT NULL,
    description TEXT,
    price_per_night DECIMAL(10, 2) NOT NULL,
    rating DECIMAL(2, 1),
    available_rooms INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Cruises table
CREATE TABLE cruises (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    departure_port VARCHAR(100) NOT NULL,
    destination_ports TEXT NOT NULL,
    departure_date DATE NOT NULL,
    duration_days INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    available_cabins INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Bookings table
CREATE TABLE bookings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    booking_type ENUM('flight', 'hotel', 'cruise') NOT NULL,
    item_id INT NOT NULL,
    booking_date DATE NOT NULL,
    total_price DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Insert sample admin user
INSERT INTO users (name, email, password, role) VALUES 
('Admin', 'admin@travelease.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Insert sample flights
INSERT INTO flights (flight_number, airline, departure_city, arrival_city, departure_time, arrival_time, price, available_seats) VALUES
('FL001', 'TravelEase Airways', 'New York', 'London', '2024-04-01 10:00:00', '2024-04-01 22:00:00', 599.99, 150),
('FL002', 'TravelEase Airways', 'Paris', 'Tokyo', '2024-04-02 14:00:00', '2024-04-03 08:00:00', 799.99, 200),
('FL003', 'TravelEase Airways', 'Dubai', 'Singapore', '2024-04-03 08:00:00', '2024-04-03 20:00:00', 449.99, 180);

-- Insert sample hotels
INSERT INTO hotels (name, city, address, description, price_per_night, rating, available_rooms) VALUES
('Luxury Palace Hotel', 'Paris', '123 Champs-Élysées', 'Experience luxury in the heart of Paris', 299.99, 4.8, 50),
('Tokyo Bay Resort', 'Tokyo', '456 Shibuya Street', 'Modern comfort meets traditional Japanese hospitality', 199.99, 4.6, 100),
('Manhattan Suites', 'New York', '789 Broadway Ave', 'Your home in the city that never sleeps', 249.99, 4.7, 75);

-- Insert sample cruises
INSERT INTO cruises (name, departure_port, destination_ports, departure_date, duration_days, price, available_cabins) VALUES
('Caribbean Dream', 'Miami', 'Jamaica,Bahamas,Mexico', '2024-05-01', 7, 899.99, 100),
('Mediterranean Explorer', 'Barcelona', 'Rome,Athens,Santorini', '2024-06-01', 10, 1299.99, 80),
('Alaska Adventure', 'Seattle', 'Juneau,Skagway,Ketchikan', '2024-07-01', 7, 999.99, 90); 