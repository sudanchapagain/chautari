CREATE USER client WITH PASSWORD 'client';

CREATE DATABASE event_booking_system WITH OWNER client;

\c event_booking_system;

CREATE TABLE users (
    user_id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    phone_number VARCHAR(20),
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL
);

CREATE TABLE events (
    event_id SERIAL PRIMARY KEY,
    banner_image VARCHAR(255),
    name VARCHAR(255) NOT NULL,
    date DATE NOT NULL,
    time TIME NOT NULL,
    venue VARCHAR(255) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    description TEXT,
    notice_text TEXT,
    terms_conditions TEXT,
    refund_policy TEXT,
    promocode VARCHAR(50),
    organizer_name VARCHAR(255),
    booking_status VARCHAR(20) DEFAULT 'Available',
    seat_number_max INT NOT NULL,
    booked_seats INT NOT NULL
);

CREATE TABLE ticket_types (
    ticket_type_id SERIAL PRIMARY KEY,
    event_id INT,
    type_name VARCHAR(255) NOT NULL,
    type_price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (event_id) REFERENCES events (event_id) ON DELETE CASCADE
);

CREATE TABLE faqs (
    faq_id SERIAL PRIMARY KEY,
    event_id INT,
    question TEXT NOT NULL,
    answer TEXT NOT NULL,
    FOREIGN KEY (event_id) REFERENCES events (event_id) ON DELETE CASCADE
);

CREATE TABLE event_categories (
    category_id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL
);

CREATE TABLE event_regions (
    region_id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL
);

CREATE TABLE event_category_mapping (
    event_id INT,
    category_id INT,
    PRIMARY KEY (event_id, category_id),
    FOREIGN KEY (event_id) REFERENCES events (event_id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES event_categories (category_id) ON DELETE CASCADE
);

CREATE TABLE event_region_mapping (
    event_id INT,
    region_id INT,
    PRIMARY KEY (event_id, region_id),
    FOREIGN KEY (event_id) REFERENCES events (event_id) ON DELETE CASCADE,
    FOREIGN KEY (region_id) REFERENCES event_regions (region_id) ON DELETE CASCADE
);

CREATE TABLE bookings (
    booking_id SERIAL PRIMARY KEY,
    user_id INT,
    event_id INT,
    seat_number VARCHAR(50),
    status VARCHAR(20) DEFAULT 'Booked',
    FOREIGN KEY (user_id) REFERENCES users (user_id) ON DELETE SET NULL,
    FOREIGN KEY (event_id) REFERENCES events (event_id) ON DELETE CASCADE
);
