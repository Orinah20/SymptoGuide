<?php

// Database configuration
const DB_HOST = 'localhost';
const DB_USERNAME = 'admin@gmail.com';
const DB_PASSWORD = '2022@admin#';
const DB_NAME = 'diagnosis';

// Create a database connection
$conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check if the connection was successful
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
