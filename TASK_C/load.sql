LOAD DATA LOCAL INFILE './data/department_data.csv'
INTO TABLE department
FIELDS TERMINATED BY ','
LINES TERMINATED BY '\n'
IGNORE 1 ROWS
(id, name, building);

LOAD DATA LOCAL INFILE './data/doctor_data.csv'
INTO TABLE doctor
FIELDS TERMINATED BY ','
ENCLOSED BY '"'
LINES TERMINATED BY '\n'
IGNORE 1 ROWS
(id, password, first_name, last_name, phone, email, department_id, is_active);

LOAD DATA LOCAL INFILE './data/address_info_data.csv'
INTO TABLE address_info
FIELDS TERMINATED BY ','
LINES TERMINATED BY '\n'
IGNORE 1 ROWS
(address_country, address_zip, address_state);

LOAD DATA LOCAL INFILE './data/patients_data.csv'
INTO TABLE patient
FIELDS TERMINATED BY ','
ENCLOSED BY '"'
LINES TERMINATED BY '\n'
IGNORE 1 ROWS
(password,first_name,last_name,dob,phone,email,address_street,
address_country,address_zip,insurance,pharmacy_address);

LOAD DATA LOCAL INFILE './data/appointment_data.csv'
INTO TABLE appointment
FIELDS TERMINATED BY ','
ENCLOSED BY '"'
LINES TERMINATED BY '\n'
IGNORE 1 ROWS
(id, start, end, notes, patient_id, doctor_id);

LOAD DATA LOCAL INFILE './data/prescription_data.csv'
INTO TABLE prescription
FIELDS TERMINATED BY ','
ENCLOSED BY '"'
LINES TERMINATED BY '\n'
IGNORE 1 ROWS
(id, name, dosage, expiration, appointment_id);
