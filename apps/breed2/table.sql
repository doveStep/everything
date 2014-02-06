/* SYNTAX: mysql -useandb -pPASSWORD < ~/proj/mobile/ej/table.sql */
USE seandb;

CREATE TABLE `breed_spawn_types` (
    `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT, 
	`breed_type` varchar(6) NOT NULL,
	`attributes` varchar(120) NOT NULL,
    `premium` tinyint(1) NOT NULL,
    PRIMARY KEY `id` (`id`)
) ENGINE=InnoDB;

CREATE TABLE `breed_attributes_by_type` (
    `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT, 
    `breed_type` int(6) UNSIGNED NOT NULL,
    `position` varchar(20) NOT NULL,
    `attribute_list` varchar(120) NOT NULL,
    PRIMARY KEY `id` (`id`),
    KEY (`type`),
    KEY (`position`)
) ENGINE=InnoDB;

/* ******************SEED DATA******************** */

INSERT INTO `breed_attributes_by_type` (`breed_type`, `position`, `attribute_list`) VALUES
    (1,`facial_feature`,``),
    (1,`eye`,`dejected,happy`),
    (1,`mouth`,`tongue,frown,smile`)
;

/* *****************END SEED DATA***************** */