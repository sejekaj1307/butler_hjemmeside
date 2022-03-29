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

#### machines
~~~~sql
CREATE TABLE `butler_db`.`machines` ( `id` INT(11) NOT NULL AUTO_INCREMENT , `name` VARCHAR(50) NOT NULL , `name_nordic` VARCHAR(50) NOT NULL , `link` VARCHAR(50) NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;
~~~~


#### externals
~~~~sql
CREATE TABLE `butler_db`.`external` ( `id` INT(11) NOT NULL AUTO_INCREMENT , `first_name` VARCHAR(50) NOT NULL , `last_name` VARCHAR(50) NOT NULL , `email` VARCHAR(50) NOT NULL , `phone` VARCHAR(50) NOT NULL , `phone_private` VARCHAR(50) NOT NULL , `contact_type` VARCHAR(50) NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;
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


