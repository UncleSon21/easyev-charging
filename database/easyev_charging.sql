CREATE DATABASE IF NOT EXISTS easyev_charging;
USE easyev_charging;

CREATE TABLE users (
  id INT PRIMARY KEY AUTO_INCREMENT, 
  name VARCHAR(100) NOT NULL,
  phone VARCHAR(20) NOT NULL,
  email VARCHAR(100) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  type ENUM('Administrator', 'User') NOT NULL
);

CREATE TABLE locations (
  location_id INT PRIMARY KEY AUTO_INCREMENT,
  description VARCHAR(255) NOT NULL,
  number_of_stations INT NOT NULL,
  cost_per_hour DECIMAL(10,2) NOT NULL
);

CREATE TABLE chargings (
  id INT PRIMARY KEY AUTO_INCREMENT,
  user_id INT NOT NULL,
  location_id INT NOT NULL,
  check_in_time DATETIME NOT NULL,
  check_out_time DATETIME,
  total_cost DECIMAL(10,2),
  status ENUM('active','completed') DEFAULT 'active',
  FOREIGN KEY (user_id) REFERENCES users(id),
  FOREIGN KEY (location_id) REFERENCES locations(id)
);

INSERT INTO users (name, phone, email, password, type) VALUES
('Admin User', '1234567890', 'admin@example.com', '$2y$10$HfzIhGCCaxqyaIdGgjARSuOKAcm1Uy82YfLuNaajn6JrjLWy9Sj/W', 'Administrator'),
('John Doe', '9876543210', 'john@example.com', '$2y$10$HfzIhGCCaxqyaIdGgjARSuOKAcm1Uy82YfLuNaajn6JrjLWy9Sj/W', 'User'),
('Jane Smith', '5551234567', 'jane@example.com', '$2y$10$HfzIhGCCaxqyaIdGgjARSuOKAcm1Uy82YfLuNaajn6JrjLWy9Sj/W', 'User');

INSERT INTO locations (description, number_of_stations, cost_per_hour) VALUES
('Downtown Charging Hub', 5, 5.50),
('Mall Parking Level 2', 10, 4.75),
('Central Station', 8, 6.25),
('Airport Terminal A', 6, 8.00),
('Beach Road Station', 3, 7.50);