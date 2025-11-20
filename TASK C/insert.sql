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


insert into Patients
(password,first_name,last_name,dob,phone,email,address_street,address_country,address_zip,insurance,pharmacy_address)
values
('pw201','Mason','Turner','1989-03-14','555-220-1001','mason.t@example.com','12 Elm St','USA','10001','Aetna','CVS Manhattan'),
('pw202','Aiden','Brooks','1992-10-18','555-220-1002','aiden.b@example.com','44 West St','USA','10002','BlueShield','Walgreens Chelsea'),
('pw203','Scarlett','Reed','1984-06-29','555-220-1003','scarlett.r@example.com','88 Harbor Rd','USA','30301','UnitedHealth','CVS Atlanta'),
('pw204','Lillian','Cole','1990-11-03','555-220-1004','lillian.c@example.com','101 Lakeview Dr','USA','60601','Medicare','Walgreens Chicago'),
('pw205','Elijah','Perry','1987-09-15','555-220-1005','elijah.p@example.com','9 Sunrise Ave','USA','85001','Aetna','CVS Phoenix'),
('pw206','Hannah','Miller','1995-02-11','555-220-1006','hannah.m@example.com','33 Valley Rd','USA','94102','Kaiser','RiteAid San Francisco'),
('pw207','Zoe','Ward','1982-07-21','555-220-1007','zoe.w@example.com','177 Pacific St','USA','98101','BlueShield','Walgreens Seattle'),
('pw208','Leo','Sanchez','1996-01-12','555-220-1008','leo.s@example.com','18 Forest Ln','USA','73301','Aetna','H-E-B Pharmacy'),
('pw209','Charlie','Bryant','1983-08-27','555-220-1009','charlie.b@example.com','29 Ocean Blvd','USA','33101','UnitedHealth','CVS Miami'),
('pw210','Savannah','Hughes','1999-09-02','555-220-1010','sav.h@example.com','700 Cedar St','USA','15201','BlueShield','RiteAid Pittsburgh'),

('pw211','Victoria','Hale','1986-05-04','555-220-1011','victoria.h@example.com','12 Oakwood St','USA','10001','Aetna','CVS Manhattan'),
('pw212','Gabriel','Knight','1991-07-13','555-220-1012','gabriel.k@example.com','441 2nd Ave','USA','10002','Medicare','Walgreens Chelsea'),
('pw213','Ruby','Foster','1988-12-19','555-220-1013','ruby.f@example.com','10 North Ave','USA','30301','UnitedHealth','CVS Atlanta'),
('pw214','Evan','Greer','1985-04-22','555-220-1014','evan.g@example.com','87 Pine Creek Rd','USA','60601','Aetna','Walgreens Chicago'),
('pw215','Hazel','Choi','1997-03-03','555-220-1015','hazel.c@example.com','155 Ridge Rd','USA','85001','BlueShield','CVS Phoenix'),
('pw216','Wyatt','Gibson','1994-09-16','555-220-1016','wyatt.g@example.com','67 Maple Way','USA','94102','Kaiser','RiteAid San Francisco'),
('pw217','Nora','Lane','1983-03-18','555-220-1017','nora.l@example.com','144 South St','USA','98101','Aetna','Walgreens Seattle'),
('pw218','Jack','Haynes','1981-10-09','555-220-1018','jack.h@example.com','8 Bridge Ln','USA','73301','UnitedHealth','H-E-B Pharmacy'),
('pw219','Ellie','Quinn','1998-05-25','555-220-1019','ellie.q@example.com','900 Grand Ave','USA','33101','Aetna','CVS Miami'),
('pw220','Nathan','Ford','1993-11-12','555-220-1020','nathan.f@example.com','243 Center St','USA','15201','BlueShield','RiteAid Pittsburgh'),

('pw221','Chase','Barker','1986-06-28','555-220-1021','chase.b@example.com','19 Glenview Rd','USA','10001','Aetna','CVS Manhattan'),
('pw222','Madelyn','Ross','1990-02-05','555-220-1022','madelyn.r@example.com','700 5th Ave','USA','10002','Medicaid','Walgreens Chelsea'),
('pw223','Piper','Dean','1985-01-17','555-220-1023','piper.d@example.com','488 Pine Ave','USA','30301','UnitedHealth','CVS Atlanta'),
('pw224','Hudson','Little','1999-07-30','555-220-1024','hudson.l@example.com','301 Clark St','USA','60601','Aetna','Walgreens Chicago'),
('pw225','Addison','Beck','1984-09-06','555-220-1025','addison.b@example.com','1101 Highland Rd','USA','85001','BlueShield','CVS Phoenix'),
('pw226','Joseph','Price','1991-04-03','555-220-1026','joseph.p@example.com','19 Oakridge Ave','USA','94102','Kaiser','RiteAid San Francisco'),
('pw227','Ariana','Scott','1996-08-22','555-220-1027','ariana.s@example.com','55 Lincoln Rd','USA','98101','UnitedHealth','Walgreens Seattle'),
('pw228','Elias','Henderson','1982-05-27','555-220-1028','elias.h@example.com','20 Marsh St','USA','73301','Aetna','H-E-B Pharmacy'),
('pw229','Claire','Walsh','1997-12-11','555-220-1029','claire.w@example.com','433 River Ave','USA','33101','Medicare','CVS Miami'),
('pw230','Gavin','Barrett','1981-03-31','555-220-1030','gavin.b@example.com','900 Franklin St','USA','15201','BlueShield','RiteAid Pittsburgh'),

('pw231','Noah','Page','1992-01-07','555-220-1031','noah.p@example.com','80 Birchwood Dr','Canada','M5H 2N2','SunLife','Shoppers Toronto'),
('pw232','Lila','Desjardins','1988-02-25','555-220-1032','lila.d@example.ca','88 Howe St','Canada','V6B 1A1','Manulife','London Drugs Vancouver'),
('pw233','Owen','Gauthier','1995-10-12','555-220-1033','owen.g@example.ca','21 Roy St','Canada','H2X 1Y4','RAMQ','Jean Coutu Montreal'),
('pw234','Amelia','Roy','1986-08-01','555-220-1034','amelia.r@example.ca','15 Whyte Ave','Canada','T5J 2N3','BlueCross','Rexall Edmonton'),
('pw235','Julian','Andrews','1984-04-14','555-220-1035','julian.a@example.ca','9 Carlton St','Canada','R3C 4T3','Great-West','Shoppers Winnipeg'),
('pw236','Margot','Pelletier','1999-12-09','555-220-1036','margot.p@example.ca','400 Victoria St','Canada','S7K 3J8','Manulife','Shoppers Saskatoon'),
('pw237','Felix','Cormier','1983-06-16','555-220-1037','felix.c@example.ca','88 King George St','Canada','E3B 1A1','BlueCross','Lawtons Fredericton'),
('pw238','Sophie','LeBlanc','1994-02-03','555-220-1038','sophie.l@example.ca','44 Duke St','Canada','B3J 2K9','Medavie','Shoppers Halifax'),
('pw239','Isaac','Gallant','1991-07-29','555-220-1039','isaac.g@example.ca','12 Grafton St','Canada','C1A 7N8','SunLife','Murphy’s Pharmacy PEI'),
('pw240','Chloe','Ng','1997-10-19','555-220-1040','chloe.ng@example.ca','30 Main St','Canada','Y1A 2C6','NorthCross','Shoppers Whitehorse'),

('pw241','Damian','Fuller','1982-03-06','555-220-1041','damian.f@example.com','78 Hillcrest Rd','USA','10001','Aetna','CVS Manhattan'),
('pw242','Ivy','Bates','1996-09-18','555-220-1042','ivy.b@example.com','19 Meadow St','USA','10002','UnitedHealth','Walgreens Chelsea'),
('pw243','Zach','Park','1987-12-27','555-220-1043','zach.p@example.com','33 Stone Rd','USA','30301','Aetna','CVS Atlanta'),
('pw244','Riley','Morton','1990-05-13','555-220-1044','riley.m@example.com','57 Elmton Ave','USA','60601','Medicaid','Walgreens Chicago'),
('pw245','Maya','Steele','1988-11-09','555-220-1045','maya.s@example.com','144 Sunset Ridge','USA','85001','BlueShield','CVS Phoenix'),
('pw246','Jordan','Vega','1992-04-08','555-220-1046','jordan.v@example.com','12 Pacific Dr','USA','94102','Kaiser','RiteAid San Francisco'),
('pw247','Emily','Sharpe','1983-10-24','555-220-1047','em.sharpe@example.com','90 Easton St','USA','98101','UnitedHealth','Walgreens Seattle'),
('pw248','Reid','Bennett','1997-03-15','555-220-1048','reid.b@example.com','55 Horizon Way','USA','73301','Aetna','H-E-B Pharmacy'),
('pw249','Zoey','Nash','1985-07-08','555-220-1049','zoey.n@example.com','300 Grove Ln','USA','33101','BlueShield','CVS Miami'),
('pw250','Logan','Carter','1981-04-26','555-220-1050','logan.c@example.com','66 Summit Blvd','USA','15201','UnitedHealth','RiteAid Pittsburgh'),

('pw251','Faith','Robertson','1994-12-21','555-220-1051','faith.r@example.com','18 Windermere Rd','USA','10001','Aetna','CVS Manhattan'),
('pw252','Troy','Becker','1986-02-02','555-220-1052','troy.b@example.com','76 Broadway Pl','USA','10002','Medicare','Walgreens Chelsea'),
('pw253','Gemma','Lowe','1989-08-30','555-220-1053','gemma.l@example.com','100 Harbor St','USA','30301','UnitedHealth','CVS Atlanta'),
('pw254','Xavier','Holmes','1982-11-05','555-220-1054','xavier.h@example.com','15 Briar Hill','USA','60601','Aetna','Walgreens Chicago'),
('pw255','Kate','O’Connell','1993-07-12','555-220-1055','kate.oc@example.com','210 High St','USA','85001','BlueShield','CVS Phoenix'),
('pw256','Marcus','Day','1985-03-09','555-220-1056','marcus.d@example.com','42 Mason Rd','USA','94102','Kaiser','RiteAid San Francisco'),
('pw257','Violet','Patton','1991-09-29','555-220-1057','violet.p@example.com','202 Riverbend Rd','USA','98101','Aetna','Walgreens Seattle'),
('pw258','Jasper','Klein','1997-01-18','555-220-1058','jasper.k@example.com','88 Country Club Rd','USA','73301','UnitedHealth','H-E-B Pharmacy'),
('pw259','Holly','Shepherd','1984-06-06','555-220-1059','holly.s@example.com','109 Pinecrest Ln','USA','33101','Medicare','CVS Miami'),
('pw260','Connor','Maxwell','1980-12-30','555-220-1060','connor.m@example.com','77 Lakeshore Blvd','USA','15201','Aetna','RiteAid Pittsburgh');
