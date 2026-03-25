CREATE DATABASE proyek_probis;
USE proyek_probis;

-- =========================
-- USERS
-- =========================
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    role VARCHAR(20) NOT NULL,
    status INT NOT NULL DEFAULT 1 COMMENT '1 = Active, 0 = Inactive',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- =========================
-- ROOMS
-- =========================
CREATE TABLE rooms (
    room_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    capacity INT NOT NULL,
    price INT NOT NULL,
    deposit_percent INT DEFAULT 0,
    location VARCHAR(255) NOT NULL,
    rules TEXT,
    description TEXT,
    status INT NOT NULL DEFAULT 1 COMMENT '1 = Diajukan, 2 = Diterima, 3 = Not Available, 0 = Inactive',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- =========================
-- FACILITIES
-- =========================
CREATE TABLE facilities (
    facility_id INT AUTO_INCREMENT PRIMARY KEY,
    room_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    price INT DEFAULT 0,
    photo VARCHAR(255),
    description TEXT,
    status INT NOT NULL DEFAULT 1 COMMENT '1 = Available, 2 = Not Available, 0 = Inactive',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- =========================
-- BOOKINGS
-- =========================
CREATE TABLE bookings (
    booking_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total INT NOT NULL,
    method_payment VARCHAR(50) NOT NULL,
    photo VARCHAR(255),
    start_date DATETIME NOT NULL,
    end_date DATETIME NOT NULL,
    status INT NOT NULL DEFAULT 1 COMMENT '1 = Booked, 2 = Occupied, 0 = Cancel',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- =========================
-- BOOKING DETAILS
-- =========================
CREATE TABLE booking_details (
    bd_id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    item_id INT NOT NULL COMMENT 'ID item (room / facility)',
    item_type INT NOT NULL COMMENT '1 = Room, 2 = Facility',
    item_price INT NOT NULL,
    status INT NOT NULL DEFAULT 1 COMMENT '1 = Active, 0 = Inactive',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- =========================
-- RATINGS
-- =========================
CREATE TABLE ratings (
    rating_id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    item_id INT NOT NULL COMMENT 'ID item (room / facility)',
    item_type INT NOT NULL COMMENT '1 = Room, 2 = Facility',
    kebersihan INT NOT NULL,
    pelayanan INT NOT NULL,
    kenyamanan INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);