CREATE USER client WITH PASSWORD 'client';

CREATE DATABASE event_booking_system WITH OWNER client;

\c event_booking_system;

CREATE TABLE Users
(
    user_id       SERIAL PRIMARY KEY,
    name          VARCHAR(255)        NOT NULL,
    phone_number  VARCHAR(20),
    email         VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255)        NOT NULL,
    reset_token_hash VARCHAR(64) NULL DEFAULT NULL,
    reset_token_expires_at DATE NULL DEFAULT NULL,
    UNIQUE (reset_token_hash)
);

CREATE TABLE Events
(
    event_id         SERIAL PRIMARY KEY,
    banner_image     VARCHAR(255),
    name             VARCHAR(255)   NOT NULL,
    date             DATE           NOT NULL,
    time             TIME           NOT NULL,
    venue            VARCHAR(255)   NOT NULL,
    price            DECIMAL(10, 2) NOT NULL,
    description      TEXT,
    notice_text      TEXT,
    terms_conditions TEXT,
    refund_policy    TEXT,
    promocode        VARCHAR(50),
    organizer_name   VARCHAR(255),
    booking_status   VARCHAR(20) DEFAULT 'Available',
    seat_number_max  INT            NOT NULL,
    left_seats       INT            NOT NULL,
    booked_seats     INT            NOT NULL
);

CREATE TABLE Ticket_Types
(
    ticket_type_id SERIAL PRIMARY KEY,
    event_id       INT,
    type_name      VARCHAR(255)   NOT NULL,
    type_price     DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (event_id) REFERENCES Events (event_id) ON DELETE CASCADE
);

CREATE TABLE FAQs
(
    faq_id   SERIAL PRIMARY KEY,
    event_id INT,
    question TEXT NOT NULL,
    answer   TEXT NOT NULL,
    FOREIGN KEY (event_id) REFERENCES Events (event_id) ON DELETE CASCADE
);

CREATE TABLE Event_Categories
(
    category_id SERIAL PRIMARY KEY,
    name        VARCHAR(255) NOT NULL
);

CREATE TABLE Event_Regions
(
    region_id SERIAL PRIMARY KEY,
    name      VARCHAR(255) NOT NULL
);

CREATE TABLE Event_Category_Mapping
(
    event_id    INT,
    category_id INT,
    PRIMARY KEY (event_id, category_id),
    FOREIGN KEY (event_id) REFERENCES Events (event_id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES Event_Categories (category_id) ON DELETE CASCADE
);

CREATE TABLE Event_Region_Mapping
(
    event_id  INT,
    region_id INT,
    PRIMARY KEY (event_id, region_id),
    FOREIGN KEY (event_id) REFERENCES Events (event_id) ON DELETE CASCADE,
    FOREIGN KEY (region_id) REFERENCES Event_Regions (region_id) ON DELETE CASCADE
);

CREATE TABLE Bookings
(
    booking_id  SERIAL PRIMARY KEY,
    user_id     INT,
    event_id    INT,
    seat_number VARCHAR(50),
    status      VARCHAR(20) DEFAULT 'Booked',
    FOREIGN KEY (user_id) REFERENCES Users (user_id) ON DELETE SET NULL,
    FOREIGN KEY (event_id) REFERENCES Events (event_id) ON DELETE CASCADE
);
