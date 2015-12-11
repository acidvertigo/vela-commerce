
DROP TABLE IF EXISTS `Config`;
		
CREATE TABLE `Config` (
  `id` INTEGER NOT NULL AUTO_INCREMENT,
  `Name` VARCHAR (64) NOT NULL COMMENT 'Configuration name',
  `Value` VARCHAR(255) NOT NULL COMMENT 'Configuration Value',
  `Description` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Configuration description',
  `Config_group_id` INTEGER NOT NULL COMMENT 'Configuration group foreign key',
  PRIMARY KEY (`id`)
) COMMENT 'Configuration table';

DROP TABLE IF EXISTS `Config_group`;
		
CREATE TABLE `Config_group` (
  `id` INTEGER NOT NULL AUTO_INCREMENT,
  `Name` VARCHAR(64) NOT NULL COMMENT 'Configuration group name',
  `Description` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Configuration group description',
  PRIMARY KEY (`id`)
) COMMENT 'Configuration Group table';


# Foreign Keys

ALTER TABLE `Config` ADD FOREIGN KEY (Config_group_id) REFERENCES `Config_group` (`id`);

# Table Properties

-- ALTER TABLE `Config` ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
-- ALTER TABLE `Config_group` ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

# Config Data
INSERT INTO `Config` (`Name`,`Value`,`Description`,`Config_group_id`) VALUES
('id','vela_id','Session Name','1');
INSERT INTO `Config` (`Name`,`Value`,`Description`,`Config_group_id`) VALUES
('path', '/', 'Cookie Path','2');
INSERT INTO `Config` (`Name`,`Value`,`Description`,`Config_group_id`) VALUES
('domain', 'example.org', 'Cookie Domain','2');
INSERT INTO `Config` (`Name`,`Value`,`Description`,`Config_group_id`) VALUES
('secure', false, 'Secure Cookie','2');
INSERT INTO `Config` (`Name`,`Value`,`Description`,`Config_group_id`) VALUES
('lifetime', 3600, 'Cookie Lifetime','2');
INSERT INTO `Config` (`Name`,`Value`,`Description`,`Config_group_id`) VALUES
('httponly', true, 'httponly cookie','2');

# Config Group
INSERT INTO `Config_group` (`Name`,`Description`) VALUES
('session','General Session settings');
INSERT INTO `Config_group` (`Name`,`Description`) VALUES
('cookie','Cookie settings');
