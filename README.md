# butler_hjemmeside

## Start integration test - locally
node_modules/.bin/cypress open

## Start unit test - locally
./vendor/bin/phpunit

## butler_db
### employees tabeller
#### employees
~~~~sql
CREATE TABLE `butler_db`.`employees` ( `id` INT(11) NOT NULL AUTO_INCREMENT , `first_name` VARCHAR(50) NOT NULL , `last_name` VARCHAR(50) NOT NULL , `initials` VARCHAR(50) NOT NULL , `email` VARCHAR(50) NOT NULL , `email_private` VARCHAR(50) NOT NULL , `phone` VARCHAR(50) NOT NULL , `phone_private` VARCHAR(50) NOT NULL , `emergency_name` VARCHAR(50) NOT NULL , `emergency_phone` VARCHAR(50) NOT NULL , `picture` VARCHAR(50) NOT NULL , `colour` VARCHAR(50) NOT NULL , `admin_status` VARCHAR(50) NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;
~~~~

#### employee data
~~~~sql
INSERT INTO `employees` (`id`, `first_name`, `last_name`, `initials`, `email`, `email_private`, `phone`, `phone_private`, `emergency_name`, `emergency_phone`, `picture`, `colour`, `admin_status`) VALUES ('1', 'Steen', 'Ibsen', 'SI', 'steen@butlerboreteknik@gmail.com', 'victo@hotmail.dk', '40583800', '42361822', 'Lene Ibsen', '22351306', '', '', ''), ('2', 'Tom', 'Butler', 'TB', 'butlerboreteknik@gmail.com', 'butlerboreteknik@gmail.com', '61603800', '61603800', 'Helle Butler', '30132190', '', '', ''), ('3', 'Lea', 'Butler', 'LB', 'Lea.butlerboreteknik@gmail.com', '', '41133800', '60168697', 'Helle Butler', '30132190', '', '', ''), ('4', 'Brian', 'Larsen', 'BL', 'bl.butlerboreteknik@gmail.com', '', '42373800', '23309331', 'Kjeld Larsen', '21465361', '', '', ''), ('5', 'Jimmy', 'Sørensen', 'JS', 'jimmy@butlerboreteknik@gmail.com', '', '', '41429327', 'Majbritt', '40299929', '', '', ''), ('6', 'Peter', 'Bisgaard', 'PB', 'Pbbutlerboreteknik@gmail.com', '', '25380038', '', 'Katrine', '23487906', '', '', ''), ('7', 'Mads', 'Sperling', 'MS', 'Mas.butlerboreteknik@gmail.dom', 'mads4141@hotmail.dk', '22803800', '42414138', 'Iben Sperling', '22928341', '', '', ''), ('8', 'Bo', 'Guldberg', 'BG', 'Bo.butlerboreteknik@gmail.com', 'teamguldberg@hotmail.dk', '41183800', '40748492', 'Else Guldberg ', '25478492', '', '', ''), ('9', 'Mads', 'Hansen', 'MH', 'Mads.butlerboreteknik@gmail.com', '', '41603800', '22399329', 'Anna Johannesen', '30266729 ', '', '', ''), ('10', 'Brian', 'Lau Andersen', 'BA', 'brianlauandersen@icloud.com', '', '', '40635720', 'Camilla Nielsen', '40617227 ', '', '', '');
~~~~


#### machines
~~~~sql
CREATE TABLE `butler_db`.`machines` ( `id` INT(11) NOT NULL AUTO_INCREMENT , `name` VARCHAR(50) NOT NULL , `name_nordic` VARCHAR(50) NOT NULL , `link` VARCHAR(50) NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;
~~~~


#### externals
~~~~sql
CREATE TABLE `butler_db`.`externals` ( `id` INT(11) NOT NULL AUTO_INCREMENT , `first_name` VARCHAR(50) NOT NULL , `last_name` VARCHAR(50) NOT NULL , `email` VARCHAR(50) NOT NULL , `phone` VARCHAR(50) NOT NULL , `phone_private` VARCHAR(50) NOT NULL , `contact_type` VARCHAR(50) NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;
~~~~

#### externals data
~~~~sql
INSERT INTO `externals` (`id`, `first_name`, `last_name`, `email`, `phone`, `phone_private`, `contact_type`) VALUES ('1', 'Fornavn', 'Efternavn', 'emailAddresse@gmail.com', '12345678', '12345678', 'Noget første'), ('2', 'Fornavn', 'Efternavn', 'emailAddresse@gmail.com', '12345678', '12345678', 'Noget andet'), ('3', 'Fornavn', 'Efternavn', 'emailAddresse@gmail.com', '12345678', '12345678', 'Noget tredje'), ('4', 'Fornavn', 'Efternavn', 'emailAddresse@gmail.com', '12345678', '12345678', 'Noget fjerde'), ('5', 'Fornavn', 'Efternavn', 'emailAddresse@gmail.com', '12345678', '12345678', 'Noget femte');
~~~~


#### suppliers
~~~~sql
CREATE TABLE `butler_db`.`suppliers` ( `id` INT(11) NOT NULL AUTO_INCREMENT , `first_name` VARCHAR(50) NOT NULL , `last_name` VARCHAR(50) NOT NULL , `email` VARCHAR(50) NOT NULL , `phone` VARCHAR(50) NOT NULL , `address` VARCHAR(50) NOT NULL , `product` VARCHAR(50) NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;
~~~~

### cases tabeller
#### cases
~~~~sql
CREATE TABLE `butler_db`.`cases` ( `id` INT(11) NOT NULL AUTO_INCREMENT , `case_nr` VARCHAR(50) NOT NULL , `client` VARCHAR(50) NOT NULL , `client_case_nr` VARCHAR(50) NOT NULL , `location` VARCHAR(50) NOT NULL , `zip_code` VARCHAR(50) NOT NULL , `case_responsible` VARCHAR(50) NOT NULL , `est_start_date` DATETIME NOT NULL , `est_end_date` DATETIME NOT NULL , `status` VARCHAR(50) NOT NULL , `job_type` VARCHAR(50) NOT NULL , `machines` VARCHAR(50) NOT NULL , `employees` VARCHAR(50) NOT NULL , `comment_road_info` VARCHAR(50) NOT NULL , `comment_extra_work` VARCHAR(50) NOT NULL , `created_at` DATETIME NOT NULL , `created_initials` VARCHAR(50) NOT NULL , `archived_initials` VARCHAR(50) NOT NULL , `archived_at` DATETIME NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;
~~~~

### tasks tabeller
#### tasks
~~~~sql
CREATE TABLE `butler_db`.`tasks` ( `id` INT(11) NOT NULL AUTO_INCREMENT , `task_title` VARCHAR(50) NOT NULL , `prority` VARCHAR(50) NOT NULL , `status` VARCHAR(50) NOT NULL , `deadline` DATETIME NOT NULL , `created_at` DATETIME NOT NULL , `created_initials` VARCHAR(50) NOT NULL , `updated_at` DATETIME NOT NULL , `updated_initials` VARCHAR(50) NOT NULL , `archived_at` DATETIME NOT NULL , `archived_initials` VARCHAR(50) NOT NULL , `comment` VARCHAR(50) NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;
~~~~


#### tasks_planned_service
~~~~sql
CREATE TABLE `butler_db`.`tasks_service` ( `id` INT(11) NOT NULL AUTO_INCREMENT , `task_title` VARCHAR(50) NOT NULL , `prority` VARCHAR(50) NOT NULL , `status` VARCHAR(50) NOT NULL , `deadline` DATETIME NOT NULL , `created_at` DATETIME NOT NULL , `created_initials` VARCHAR(50) NOT NULL , `updated_at` DATETIME NOT NULL , `updated_initials` VARCHAR(50) NOT NULL , `archived_at` DATETIME NOT NULL , `archived_initials` VARCHAR(50) NOT NULL , `last_service` DATETIME NOT NULL , `comment` VARCHAR(50) NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;
~~~~


#### daily_reports
~~~~sql
CREATE TABLE `butler_db`.`daily_reports` ( `id` INT NOT NULL AUTO_INCREMENT , `time_reg_field_id` INT NOT NULL , `time_reg_data` VARCHAR(255) NOT NULL , `user_id` INT NOT NULL , `date` DATETIME NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;
~~~~

#### time_reg_fields
~~~~sql
CREATE TABLE `butler_db`.`time_reg_fields` ( `id` INT NOT NULL AUTO_INCREMENT , `input_type` VARCHAR(255) NOT NULL , `input_lable` VARCHAR(255) NOT NULL , `job_type` VARCHAR(255) NOT NULL , `children` VARCHAR(255) NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;
~~~~


