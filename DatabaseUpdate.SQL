SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

ALTER TABLE `gliding`.`aircraft` 
ADD COLUMN `aircraft_particle_id` VARCHAR(24) NULL DEFAULT NULL AFTER `spot_id`,
ADD COLUMN `aircraft_track_udp_server` VARCHAR(16) NULL DEFAULT NULL AFTER `aircraft_particle_id`,
ADD COLUMN `aircraft_track_timer_1` INT(11) NULL DEFAULT NULL AFTER `aircraft_track_udp_server`,
ADD COLUMN `aircraft_track_timer_2` INT(11) NULL DEFAULT NULL AFTER `aircraft_track_timer_1`,
ADD COLUMN `aircraft_track_timer_3` INT(11) NULL DEFAULT NULL AFTER `aircraft_track_timer_2`,
ADD COLUMN `aircraft_track_timer_4` INT(11) NULL DEFAULT NULL AFTER `aircraft_track_timer_3`,
ADD COLUMN `aircraft_track_timer_5` INT(11) NULL DEFAULT NULL AFTER `aircraft_track_timer_4`,
ADD COLUMN `aircraft_track_debug` TINYINT(1) NULL DEFAULT 0 AFTER `aircraft_track_timer_5`,
ADD COLUMN `aircraft_track_last_status` ENUM('nofix','pos','hello') NULL DEFAULT NULL AFTER `aircraft_track_debug`,
ADD COLUMN `aircraft_track_status_timestamp` DATETIME NULL DEFAULT NULL AFTER `aircraft_track_last_status`,
ADD COLUMN `aircraft_track_battery` FLOAT(11) NULL DEFAULT NULL AFTER `aircraft_track_status_timestamp`,
ADD COLUMN `aircraft_track_battery_timestamp` DATETIME NULL DEFAULT NULL AFTER `aircraft_track_battery`,
ADD UNIQUE INDEX `aircraft_particle_id_UNIQUE` (`aircraft_particle_id` ASC);


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
