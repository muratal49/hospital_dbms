-- Nathanael Kumar - Work for CSC 261 Group 3, Milestone 3 - November 15, 2025

CREATE TABLE Department(
    id INT AUTO_INCREMENT,
    name VARCHAR(32) NOT NULL UNIQUE,
    building VARCHAR(32) NOT NULL,
    PRIMARY KEY (id)
)

CREATE TABLE Doctor(
    id INT AUTO_INCREMENT,
    password VARCHAR(32) NOT NULL,
    first_name VARCHAR(32) NOT NULL,
    last_name VARCHAR(32) NOT NULL,
    phone INT(10) NOT NULL UNIQUE,
    email VARCHAR(32) NOT NULL UNIQUE,
    department_id INT,
    is_active BOOL NOT NULL DEFAULT 1,
    PRIMARY KEY (id),
    FOREIGN KEY (department_id) REFERENCES Department(id)
)

CREATE TABLE Appointment(
    id INT AUTO_INCREMENT,
    start DATETIME NOT NULL,
    end DATETIME NOT NULL,
    notes TINYTEXT DEFAULT "",
    patient_id INT NOT NULL,
    doctor_id INT NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (patient_id) REFERENCES Patient(id),
    FOREIGN KEY (doctor_id) REFERENCES Doctor(id)
)

CREATE TABLE Prescription(
	id INT AUTO_INCREMENT,
	name VARCHAR(64) NOT NULL,
	dosage VARCHAR(16) NOT NULL,
	expiration DATETIME NOT NULL,
    PRIMARY KEY (id),
	FOREIGN KEY (appointment_id) REFERENCES appointment(id)
)


CREATE TABLE address_info (
    -- have two attributes: country, zip, state as primary key
  ,
 address_country varchar(50),
 address_zip varchar(15),
 address_state varchar(10)
 primary key (address_country, address_zip)
);

CREATE TABLE patients (

 id int auto_increment primary key,
 password varchar(50),
 first_name varchar(50),
 last_name varchar(50),
 dob date,
 phone varchar(20),
 email varchar(100),
 address_street varchar(100),
 address_country varchar(50),
 address_zip varchar(15),
 insurance varchar(100),
 pharmacy_address varchar(100),

 foreign key (address_country, address_zip) references address_info(address_country, address_zip)
);