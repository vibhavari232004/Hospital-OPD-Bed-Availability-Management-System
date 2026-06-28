-- Mint OPD Database Schema and CRUD Operations
-- MySQL Database for Hospital Management System
-- Import via phpMyAdmin (Import tab) or mysql CLI.
-- If you see errors about DELIMITER, import the file as a whole (not line-by-line).

-- Create (or reset) Database
DROP DATABASE IF EXISTS `mint_opd`;
CREATE DATABASE IF NOT EXISTS `mint_opd` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `mint_opd`;

-- ===========================================
-- TABLE CREATION
-- ===========================================

-- Cities Table
CREATE TABLE cities (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Hospitals Table
CREATE TABLE hospitals (
    id INT PRIMARY KEY AUTO_INCREMENT,
    city_id INT NOT NULL,
    name VARCHAR(200) NOT NULL,
    address TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (city_id) REFERENCES cities(id) ON DELETE CASCADE
);

-- Doctors Table
CREATE TABLE doctors (
    id INT PRIMARY KEY AUTO_INCREMENT,
    city_id INT NOT NULL,
    hospital_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    speciality VARCHAR(100) NOT NULL,
    department VARCHAR(100) NOT NULL,
    fees DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (city_id) REFERENCES cities(id) ON DELETE CASCADE,
    FOREIGN KEY (hospital_id) REFERENCES hospitals(id) ON DELETE CASCADE
);

-- Patients Table
CREATE TABLE patients (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE,
    password VARCHAR(255),
    age INT,
    gender ENUM('male', 'female', 'other'),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Beds Table
CREATE TABLE beds (
    id INT PRIMARY KEY AUTO_INCREMENT,
    city_id INT NOT NULL,
    hospital_id INT NOT NULL,
    type ENUM('general', 'icu', 'maternity', 'emergency', 'pediatric', 'all') NOT NULL,
    total INT NOT NULL DEFAULT 0,
    occupied INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (city_id) REFERENCES cities(id) ON DELETE CASCADE,
    FOREIGN KEY (hospital_id) REFERENCES hospitals(id) ON DELETE CASCADE,
    CHECK (occupied <= total)
);

-- Appointments Table
CREATE TABLE appointments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    patient_id INT NOT NULL,
    doctor_id INT NOT NULL,
    hospital_id INT NOT NULL,
    city_id INT NOT NULL,
    queue_number INT NOT NULL,
    slot VARCHAR(50),
    date DATE NOT NULL,
    completed BOOLEAN DEFAULT FALSE,
    follow_up BOOLEAN DEFAULT FALSE,
    prescription TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE,
    FOREIGN KEY (hospital_id) REFERENCES hospitals(id) ON DELETE CASCADE,
    FOREIGN KEY (city_id) REFERENCES cities(id) ON DELETE CASCADE
);

-- Doctor Available Slots Table (for complex slot management)
CREATE TABLE doctor_slots (
    id INT PRIMARY KEY AUTO_INCREMENT,
    doctor_id INT NOT NULL,
    date DATE NOT NULL,
    slot VARCHAR(50) NOT NULL,
    is_available BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE,
    UNIQUE KEY unique_doctor_slot (doctor_id, date, slot)
);

-- ===========================================
-- SAMPLE DATA INSERTION
-- ===========================================

-- Insert Cities
INSERT INTO cities (name) VALUES
('Jalgaon'),
('Pune'),
('Mumbai'),
('Delhi');

-- Insert Hospitals
INSERT INTO hospitals (city_id, name, address) VALUES
(1, 'Jalgaon Civil Hospital', 'Near Railway Station, Jalgaon'),
(1, 'Apollo Hospital Jalgaon', 'Ring Road, Jalgaon'),
(2, 'Pune General Hospital', 'FC Road, Pune'),
(2, 'Ruby Hall Clinic', 'Dhole Patil Road, Pune'),
(3, 'Lilavati Hospital', 'Bandra, Mumbai'),
(4, 'AIIMS Delhi', 'Ansari Nagar, Delhi');

-- Insert Doctors
INSERT INTO doctors (city_id, hospital_id, name, email, password, speciality, department, fees) VALUES
(1, 1, 'Dr. Rajesh Sharma', 'rajesh@jcivil.com', 'pass123', 'Cardiology', 'cardiology', 500.00),
(1, 1, 'Dr. Priya Patel', 'priya@jcivil.com', 'pass123', 'Gynecology', 'gynecology', 400.00),
(1, 2, 'Dr. Amit Kumar', 'amit@apollo.com', 'pass123', 'Orthopedics', 'orthopedics', 600.00),
(2, 3, 'Dr. Sunita Rao', 'sunita@pune.com', 'pass123', 'Pediatrics', 'pediatrics', 450.00),
(2, 4, 'Dr. Vikram Singh', 'vikram@ruby.com', 'pass123', 'Neurology', 'neurology', 700.00);

-- Insert Patients
INSERT INTO patients (name, email, password, age, gender) VALUES
('John Doe', 'john@example.com', 'pass123', 30, 'male'),
('Jane Smith', 'jane@example.com', 'pass123', 25, 'female'),
('Bob Johnson', 'bob@example.com', 'pass123', 45, 'male'),
('Alice Brown', 'alice@example.com', 'pass123', 35, 'female');

-- Insert Beds
INSERT INTO beds (city_id, hospital_id, type, total, occupied) VALUES
(1, 1, 'general', 50, 15),
(1, 1, 'icu', 10, 3),
(1, 1, 'maternity', 20, 5),
(1, 2, 'general', 30, 8),
(1, 2, 'icu', 8, 2),
(2, 3, 'general', 40, 12),
(2, 3, 'pediatric', 15, 4);

-- Insert Appointments
INSERT INTO appointments (patient_id, doctor_id, hospital_id, city_id, queue_number, slot, date, completed, follow_up) VALUES
(1, 1, 1, 1, 1, '9:00 AM - 10:00 AM', CURDATE(), FALSE, FALSE),
(2, 2, 1, 1, 1, '10:00 AM - 11:00 AM', CURDATE(), TRUE, TRUE),
(3, 3, 2, 1, 1, '11:00 AM - 12:00 PM', CURDATE(), FALSE, FALSE);

-- Insert Doctor Slots
INSERT INTO doctor_slots (doctor_id, date, slot, is_available) VALUES
(1, CURDATE(), '9:00 AM - 10:00 AM', TRUE),
(1, CURDATE(), '10:00 AM - 11:00 AM', TRUE),
(1, CURDATE(), '11:00 AM - 12:00 PM', TRUE),
(2, CURDATE(), '9:00 AM - 10:00 AM', TRUE),
(2, CURDATE(), '10:00 AM - 11:00 AM', TRUE),
(3, CURDATE(), '2:00 PM - 3:00 PM', TRUE),
(3, CURDATE(), '3:00 PM - 4:00 PM', TRUE);

-- ===========================================
-- CRUD OPERATIONS
-- ===========================================

-- ===================
-- CITIES CRUD
-- ===================

-- Create City
DELIMITER //
CREATE PROCEDURE create_city(IN city_name VARCHAR(100))
BEGIN
    INSERT INTO cities (name) VALUES (city_name);
    SELECT LAST_INSERT_ID() as city_id;
END //
DELIMITER ;

-- Read Cities
CREATE VIEW view_cities AS
SELECT id, name, created_at FROM cities ORDER BY name;

-- Update City
DELIMITER //
CREATE PROCEDURE update_city(IN city_id INT, IN new_name VARCHAR(100))
BEGIN
    UPDATE cities SET name = new_name WHERE id = city_id;
END //
DELIMITER ;

-- Delete City
DELIMITER //
CREATE PROCEDURE delete_city(IN city_id INT)
BEGIN
    DELETE FROM cities WHERE id = city_id;
END //
DELIMITER ;

-- ===================
-- HOSPITALS CRUD
-- ===================

-- Create Hospital
DELIMITER //
CREATE PROCEDURE create_hospital(IN city_id INT, IN hosp_name VARCHAR(200), IN hosp_address TEXT)
BEGIN
    INSERT INTO hospitals (city_id, name, address) VALUES (city_id, hosp_name, hosp_address);
    SELECT LAST_INSERT_ID() as hospital_id;
END //
DELIMITER ;

-- Read Hospitals
CREATE VIEW view_hospitals AS
SELECT h.id, h.name, h.address, c.name as city_name, h.created_at
FROM hospitals h
JOIN cities c ON h.city_id = c.id
ORDER BY c.name, h.name;

-- Update Hospital
DELIMITER //
CREATE PROCEDURE update_hospital(IN hosp_id INT, IN new_name VARCHAR(200), IN new_address TEXT)
BEGIN
    UPDATE hospitals SET name = new_name, address = new_address WHERE id = hosp_id;
END //
DELIMITER ;

-- Delete Hospital
DELIMITER //
CREATE PROCEDURE delete_hospital(IN hosp_id INT)
BEGIN
    DELETE FROM hospitals WHERE id = hosp_id;
END //
DELIMITER ;

-- ===================
-- DOCTORS CRUD
-- ===================

-- Create Doctor
DELIMITER //
CREATE PROCEDURE create_doctor(
    IN city_id INT, IN hosp_id INT, IN doc_name VARCHAR(100), 
    IN doc_email VARCHAR(100), IN doc_password VARCHAR(255),
    IN speciality VARCHAR(100), IN department VARCHAR(100), IN fees DECIMAL(10,2)
)
BEGIN
    INSERT INTO doctors (city_id, hospital_id, name, email, password, speciality, department, fees) 
    VALUES (city_id, hosp_id, doc_name, doc_email, doc_password, speciality, department, fees);
    SELECT LAST_INSERT_ID() as doctor_id;
END //
DELIMITER ;

-- Read Doctors
CREATE VIEW view_doctors AS
SELECT d.id, d.name, d.email, d.speciality, d.department, d.fees, 
       h.name as hospital_name, c.name as city_name, d.created_at
FROM doctors d
JOIN hospitals h ON d.hospital_id = h.id
JOIN cities c ON d.city_id = c.id
ORDER BY c.name, h.name, d.name;

-- Update Doctor
DELIMITER //
CREATE PROCEDURE update_doctor(
    IN doc_id INT, IN new_name VARCHAR(100), IN new_email VARCHAR(100),
    IN new_speciality VARCHAR(100), IN new_department VARCHAR(100), IN new_fees DECIMAL(10,2)
)
BEGIN
    UPDATE doctors 
    SET name = new_name, email = new_email, speciality = new_speciality, 
        department = new_department, fees = new_fees 
    WHERE id = doc_id;
END //
DELIMITER ;

-- Delete Doctor
DELIMITER //
CREATE PROCEDURE delete_doctor(IN doc_id INT)
BEGIN
    -- Cancel all appointments for this doctor
    DELETE FROM appointments WHERE doctor_id = doc_id;
    
    -- Delete doctor slots
    DELETE FROM doctor_slots WHERE doctor_id = doc_id;
    
    -- Delete doctor
    DELETE FROM doctors WHERE id = doc_id;
END //
DELIMITER ;

-- ===================
-- PATIENTS CRUD
-- ===================

-- Create Patient
DELIMITER //
CREATE PROCEDURE create_patient(
    IN pat_name VARCHAR(100), IN pat_email VARCHAR(100), IN pat_password VARCHAR(255),
    IN pat_age INT, IN pat_gender ENUM('male', 'female', 'other')
)
BEGIN
    INSERT INTO patients (name, email, password, age, gender) 
    VALUES (pat_name, pat_email, pat_password, pat_age, pat_gender);
    SELECT LAST_INSERT_ID() as patient_id;
END //
DELIMITER ;

-- Read Patients
CREATE VIEW view_patients AS
SELECT id, name, email, age, gender, created_at FROM patients ORDER BY name;

-- Update Patient
DELIMITER //
CREATE PROCEDURE update_patient(
    IN pat_id INT, IN new_name VARCHAR(100), IN new_email VARCHAR(100), 
    IN new_age INT, IN new_gender ENUM('male', 'female', 'other')
)
BEGIN
    UPDATE patients 
    SET name = new_name, email = new_email, age = new_age, gender = new_gender 
    WHERE id = pat_id;
END //
DELIMITER ;

-- Delete Patient
DELIMITER //
CREATE PROCEDURE delete_patient(IN pat_id INT)
BEGIN
    DELETE FROM patients WHERE id = pat_id;
END //
DELIMITER ;

-- ===================
-- BEDS CRUD
-- ===================

-- Create/Update Beds
DELIMITER //
CREATE PROCEDURE manage_beds(
    IN city_id INT, IN hosp_id INT, IN bed_type ENUM('general', 'icu', 'maternity', 'emergency', 'pediatric', 'all'),
    IN bed_total INT
)
BEGIN
    INSERT INTO beds (city_id, hospital_id, type, total, occupied) 
    VALUES (city_id, hosp_id, bed_type, bed_total, 0)
    ON DUPLICATE KEY UPDATE total = bed_total;
END //
DELIMITER ;

-- Read Beds
CREATE VIEW view_beds AS
SELECT b.id, b.type, b.total, b.occupied, (b.total - b.occupied) as available,
       h.name as hospital_name, c.name as city_name
FROM beds b
JOIN hospitals h ON b.hospital_id = h.id
JOIN cities c ON b.city_id = c.id
ORDER BY c.name, h.name, b.type;

-- Allocate Bed
DELIMITER //
CREATE PROCEDURE allocate_bed(IN hosp_id INT, IN bed_type VARCHAR(50))
BEGIN
    UPDATE beds 
    SET occupied = occupied + 1 
    WHERE hospital_id = hosp_id AND type = bed_type AND occupied < total;
    
    IF ROW_COUNT() = 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'No beds available';
    END IF;
END //
DELIMITER ;

-- Discharge Bed
DELIMITER //
CREATE PROCEDURE discharge_bed(IN hosp_id INT, IN bed_type VARCHAR(50))
BEGIN
    UPDATE beds 
    SET occupied = GREATEST(occupied - 1, 0) 
    WHERE hospital_id = hosp_id AND type = bed_type;
END //
DELIMITER ;

-- ===================
-- APPOINTMENTS CRUD
-- ===================

-- Create Appointment
DELIMITER //
CREATE PROCEDURE create_appointment(
    IN pat_id INT, IN doc_id INT, IN hosp_id INT, IN city_id INT,
    IN slot_time VARCHAR(50), IN app_date DATE
)
BEGIN
    DECLARE queue_num INT;
    SELECT COALESCE(MAX(queue_number), 0) + 1 INTO queue_num
    FROM appointments 
    WHERE doctor_id = doc_id AND date = app_date;
    
    INSERT INTO appointments (patient_id, doctor_id, hospital_id, city_id, queue_number, slot, date) 
    VALUES (pat_id, doc_id, hosp_id, city_id, queue_num, slot_time, app_date);
    
    SELECT LAST_INSERT_ID() as appointment_id, queue_num as queue_number;
END //
DELIMITER ;

-- Read Appointments
CREATE VIEW view_appointments AS
SELECT a.id, a.queue_number, a.slot, a.date, a.completed, a.follow_up, a.prescription,
       p.name as patient_name, d.name as doctor_name, h.name as hospital_name, c.name as city_name
FROM appointments a
JOIN patients p ON a.patient_id = p.id
JOIN doctors d ON a.doctor_id = d.id
JOIN hospitals h ON a.hospital_id = h.id
JOIN cities c ON a.city_id = c.id
ORDER BY a.date DESC, a.queue_number;

-- Complete Appointment
DELIMITER //
CREATE PROCEDURE complete_appointment(IN app_id INT, IN prescription_text TEXT, IN is_followup BOOLEAN)
BEGIN
    UPDATE appointments 
    SET completed = TRUE, prescription = prescription_text, follow_up = is_followup 
    WHERE id = app_id;
END //
DELIMITER ;

-- Cancel Appointment
DELIMITER //
CREATE PROCEDURE cancel_appointment(IN app_id INT)
BEGIN
    DELETE FROM appointments WHERE id = app_id;
END //
DELIMITER ;

-- ===================
-- DOCTOR SLOTS CRUD
-- ===================

-- Set Doctor Slots
DELIMITER //
CREATE PROCEDURE set_doctor_slots(IN doc_id INT, IN slot_date DATE, IN slot_times TEXT)
BEGIN
    -- Clear existing slots for the date
    DELETE FROM doctor_slots WHERE doctor_id = doc_id AND date = slot_date;
    
    -- Insert new slots (assuming slot_times is comma-separated)
    SET @sql = CONCAT('INSERT INTO doctor_slots (doctor_id, date, slot) VALUES ');
    SET @slots = slot_times;
    
    WHILE LOCATE(',', @slots) > 0 DO
        SET @slot = TRIM(SUBSTRING(@slots, 1, LOCATE(',', @slots) - 1));
        SET @sql = CONCAT(@sql, '(', doc_id, ', "', slot_date, '", "', @slot, '"),');
        SET @slots = SUBSTRING(@slots, LOCATE(',', @slots) + 1);
    END WHILE;
    
    -- Last slot
    SET @slot = TRIM(@slots);
    SET @sql = CONCAT(@sql, '(', doc_id, ', "', slot_date, '", "', @slot, '")');
    
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
END //
DELIMITER ;

-- Read Doctor Slots
CREATE VIEW view_doctor_slots AS
SELECT ds.id, d.name as doctor_name, ds.date, ds.slot, ds.is_available,
       h.name as hospital_name
FROM doctor_slots ds
JOIN doctors d ON ds.doctor_id = d.id
JOIN hospitals h ON d.hospital_id = h.id
ORDER BY ds.date, d.name, ds.slot;

-- ===========================================
-- USEFUL QUERIES
-- ===========================================

-- Get today's appointments for a doctor
DELIMITER //
CREATE PROCEDURE get_doctor_today_appointments(IN doc_id INT)
BEGIN
    SELECT a.*, p.name as patient_name, p.age, p.gender
    FROM appointments a
    JOIN patients p ON a.patient_id = p.id
    WHERE a.doctor_id = doc_id AND a.date = CURDATE() AND a.completed = FALSE
    ORDER BY a.queue_number;
END //
DELIMITER ;

-- Get available beds in a hospital
CREATE VIEW available_beds AS
SELECT h.name as hospital_name, b.type, (b.total - b.occupied) as available_beds
FROM beds b
JOIN hospitals h ON b.hospital_id = h.id
WHERE (b.total - b.occupied) > 0;

-- Get patient appointment history
DELIMITER //
CREATE PROCEDURE get_patient_history(IN pat_id INT)
BEGIN
    SELECT a.date, a.slot, d.name as doctor_name, h.name as hospital_name, 
           a.completed, a.prescription, a.follow_up
    FROM appointments a
    JOIN doctors d ON a.doctor_id = d.id
    JOIN hospitals h ON a.hospital_id = h.id
    WHERE a.patient_id = pat_id
    ORDER BY a.date DESC;
END //
DELIMITER ;

-- Get hospital statistics
CREATE VIEW hospital_stats AS
SELECT h.name as hospital_name, c.name as city_name,
       COUNT(DISTINCT d.id) as total_doctors,
       SUM(b.total) as total_beds,
       SUM(b.occupied) as occupied_beds,
       COUNT(a.id) as total_appointments_today
FROM hospitals h
JOIN cities c ON h.city_id = c.id
LEFT JOIN doctors d ON h.id = d.hospital_id
LEFT JOIN beds b ON h.id = b.hospital_id
LEFT JOIN appointments a ON h.id = a.hospital_id AND a.date = CURDATE()
GROUP BY h.id, h.name, c.name;

-- ===========================================
-- INDEXES FOR PERFORMANCE
-- ===========================================

CREATE INDEX idx_hospitals_city ON hospitals(city_id);
CREATE INDEX idx_doctors_hospital ON doctors(hospital_id);
CREATE INDEX idx_doctors_city ON doctors(city_id);
CREATE INDEX idx_beds_hospital ON beds(hospital_id);
CREATE INDEX idx_appointments_doctor ON appointments(doctor_id);
CREATE INDEX idx_appointments_patient ON appointments(patient_id);
CREATE INDEX idx_appointments_date ON appointments(date);
CREATE INDEX idx_doctor_slots_doctor_date ON doctor_slots(doctor_id, date);