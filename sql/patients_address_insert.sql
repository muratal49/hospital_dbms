--Inserting Adresses:
insert into Address_info (address_country, address_zip, address_state) values
('USA','10001','NY'),
('USA','10002','NY'),
('USA','30301','GA'),
('USA','60601','IL'),
('USA','85001','AZ'),
('USA','94102','CA'),
('USA','98101','WA'),
('USA','73301','TX'),
('USA','33101','FL'),
('USA','15201','PA');
insert into Address_info (address_country, address_zip, address_state) values
('Canada','M5H 2N2','ON'),
('Canada','V6B 1A1','BC'),
('Canada','H2X 1Y4','QC'),
('Canada','T5J 2N3','AB'),
('Canada','R3C 4T3','MB'),
('Canada','S7K 3J8','SK'),
('Canada','E3B 1A1','NB'),
('Canada','B3J 2K9','NS'),
('Canada','C1A 7N8','PE'),
('Canada','Y1A 2C6','YT');



--Inserting Patients with Address IDs:

insert into Patients
(password,first_name,last_name,dob,phone,email,address_street,address_country,address_zip,insurance,pharmacy_address)
values
('pw123','Ellen','Milfrey','1989-05-12','555-123-1111','ellen@example.com','101 Main St','USA','10001','BlueShield','CVS Manhattan'),
('pw234','John','Carson','1992-11-23','555-234-1112','johnc@example.com','22 Broadway Ave','USA','10002','Aetna','Walgreens Chelsea'),
('pw345','Maria','Lopez','1985-07-09','555-345-1113','maria@example.com','77 Pine St','USA','30301','UnitedHealth','CVS Atlanta'),
('pw456','David','Nguyen','1990-02-14','555-456-1114','dnguyen@example.com','450 Lakeshore Dr','USA','60601','Medicare','Walgreens Chicago'),
('pw567','Sara','Kim','1996-08-25','555-567-1115','sk@example.com','19 Desert St','USA','85001','BlueShield','CVS Phoenix'),
('pw678','Michael','Adams','1980-03-17','555-678-1116','madams@example.com','800 Sunset Blvd','USA','94102','Aetna','RiteAid San Francisco'),
('pw789','Laura','Patel','1999-10-02','555-789-1117','laurap@example.com','90 King St','USA','98101','UnitedHealth','Walgreens Seattle'),
('pw890','Chris','Johnson','1983-12-30','555-890-1118','cjohnson@example.com','14 River Rd','USA','73301','Medicare','H-E-B Pharmacy'),
('pw901','Nina','Sanders','1994-01-04','555-901-1119','nina@example.com','555 Ocean Ave','USA','33101','Aetna','CVS Miami'),
('pw012','Robert','Fitz','1981-04-27','555-012-1120','rfitz@example.com','303 Liberty St','USA','15201','BlueShield','RiteAid Pittsburgh'),
('pw132','Olivia','Chen','1997-03-14','555-132-1121','olivia@example.com','12 Maple St','USA','10001','Aetna','CVS Manhattan'),
('pw243','Daniel','Smith','1988-06-11','555-243-1122','dsmith@example.com','29 North Rd','USA','10002','Medicare','Walgreens Chelsea'),
('pw354','Priya','Sharma','1993-09-21','555-354-1123','priya@example.com','140 Oak St','USA','30301','UnitedHealth','CVS Atlanta'),
('pw465','Henry','Olsen','1986-12-03','555-465-1124','henry@example.com','455 Hill St','USA','60601','Anthem','Walgreens Chicago'),
('pw576','Emily','Watson','1992-10-10','555-576-1125','emily@example.com','800 Valley Rd','USA','85001','BlueShield','CVS Phoenix'),
('pw687','Liam','Brown','1984-05-05','555-687-1126','liam@example.com','18 Mission St','USA','94102','Aetna','RiteAid San Francisco'),
('pw798','Grace','Taylor','1991-07-18','555-798-1127','grace@example.com','90 Wall St','USA','98101','UnitedHealth','Walgreens Seattle'),
('pw809','Kevin','Lee','1982-11-12','555-809-1128','klee@example.com','11 Round Tree Ct','USA','73301','Medicare','H-E-B Pharmacy'),
('pw820','Isabella','Martinez','1998-02-09','555-820-1129','isa@example.com','500 Grove St','USA','33101','Aetna','CVS Miami'),
('pw931','Jason','Green','1987-08-28','555-931-1130','jgreen@example.com','700 Fifth Ave','USA','15201','BlueShield','RiteAid Pittsburgh'),
('pw100','Ava','Bennett','1993-04-22','416-555-8821','ava.bennett@example.ca','12 King St W','Canada','M5H 2N2','SunLife','Shoppers Toronto'),
('pw101','Lucas','MacDonald','1987-09-14','604-555-3388','lucas.macd@example.ca','80 Granville St','Canada','V6B 1A1','Manulife','London Drugs Vancouver'),
('pw102','Chloe','Ducharme','1995-12-05','514-555-9400','chloe.duc@example.ca','420 St Laurent Blvd','Canada','H2X 1Y4','RAMQ','Jean Coutu Montreal'),
('pw103','Ethan','Sawyer','1982-07-18','780-555-2922','ethan.sawyer@example.ca','55 Jasper Ave','Canada','T5J 2N3','Alberta Blue Cross','Rexall Edmonton'),
('pw104','Sophie','Lambert','1998-11-30','204-555-7712','sophie.lamb@example.ca','19 Portage Ave','Canada','R3C 4T3','Great-West Life','Shoppers Winnipeg');
