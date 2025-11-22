CREATE TABLE department(
    id INT AUTO_INCREMENT,
    name VARCHAR(32) NOT NULL UNIQUE,
    building VARCHAR(32) NOT NULL,
    PRIMARY KEY (id)
);

CREATE TABLE doctor(
    id INT AUTO_INCREMENT,
    password VARCHAR(32) NOT NULL,
    first_name VARCHAR(32) NOT NULL,
    last_name VARCHAR(32) NOT NULL,
    phone VARCHAR(20) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    department_id INT,
    is_active BOOL NOT NULL DEFAULT 1,
    PRIMARY KEY (id),
    FOREIGN KEY (department_id) REFERENCES department(id)
);

CREATE TABLE address_info (
    -- have two attributes: country, zip, state as primary key
    address_country varchar(50),
    address_zip varchar(15),
    address_state varchar(10),
    PRIMARY KEY (address_country, address_zip)
);

CREATE TABLE patient(
    id int auto_increment primary key,
    password varchar(50),
    first_name varchar(50),
    last_name varchar(50),
    dob date,
    phone varchar(20) UNIQUE,
    email varchar(100) UNIQUE,
    address_street varchar(100),
    address_country varchar(50),
    address_zip varchar(15),
    insurance varchar(100),
    pharmacy_address varchar(100),

    FOREIGN KEY (address_country, address_zip) REFERENCES address_info(address_country, address_zip)
);

CREATE TABLE appointment(
    id INT AUTO_INCREMENT,
    start DATETIME NOT NULL,
    end DATETIME NOT NULL,
    notes TINYTEXT DEFAULT "",
    patient_id INT NOT NULL,
    doctor_id INT NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (patient_id) REFERENCES patient(id),
    FOREIGN KEY (doctor_id) REFERENCES doctor(id),
    UNIQUE KEY unique_doctor_time (doctor_id, start, end),
    UNIQUE KEY unique_patient_time (patient_id, start, end),
    CHECK (end > start)
);

CREATE TABLE prescription(
	id INT AUTO_INCREMENT,
	name VARCHAR(64) NOT NULL,
	dosage VARCHAR(16) NOT NULL,
	expiration DATETIME NOT NULL,
    appointment_id INT NOT NULL,
    PRIMARY KEY (id),
	FOREIGN KEY (appointment_id) REFERENCES appointment(id)
);