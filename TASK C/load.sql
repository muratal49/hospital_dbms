LOAD DATA LOCAL INFILE './data/appointment_data.csv'
INTO TABLE Appointment
FIELDS TERMINATED BY ','
ENCLOSED BY '"'
LINES TERMINATED BY '\n'
IGNORE 1 ROWS -- skip CSV header
(id, start, end, notes, patient_id, doctor_id);

LOAD DATA LOCAL INFILE './data/address_info_data.csv'
INTO TABLE Address_info
FIELDS TERMINATED BY ','
LINES TERMINATED BY '\n'
IGNORE 1 ROWS
(address_country, address_zip, address_state);

LOAD DATA LOCAL INFILE './data/patient_data.csv'
INTO TABLE Patients
FIELDS TERMINATED BY ','
ENCLOSED BY '"'
LINES TERMINATED BY '\n'
IGNORE 1 ROWS
(password,first_name,last_name,dob,phone,email,address_street,
address_country,address_zip,insurance,pharmacy_address);
