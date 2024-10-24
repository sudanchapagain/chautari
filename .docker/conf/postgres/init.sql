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
