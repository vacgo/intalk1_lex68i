CREATE DATABASE IF NOT EXISTS data;
USE data;

CREATE TABLE IF NOT EXISTS data (
    id INT AUTO_INCREMENT PRIMARY KEY,
    present_name VARCHAR(255) NOT NULL,
    present_price DECIMAL(10, 2) NOT NULL,
    purchase_date DATE NOT NULL,
	added_datetime DATETIME NOT NULL
	
);