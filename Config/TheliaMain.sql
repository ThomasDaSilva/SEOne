
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- seone
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `seone`;

CREATE TABLE `seone`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `object_id` INTEGER NOT NULL,
    `object_type` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- robots
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `robots`;

CREATE TABLE `robots`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `domain_name` VARCHAR(255) NOT NULL,
    `robots_content` TEXT NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- seone_i18n
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `seone_i18n`;

CREATE TABLE `seone_i18n`
(
    `id` INTEGER NOT NULL,
    `locale` VARCHAR(5) DEFAULT 'en_US' NOT NULL,
    `noindex` TINYINT(4) DEFAULT 0 NOT NULL,
    `nofollow` TINYINT(4) DEFAULT 0 NOT NULL,
    `canonical_field` TEXT,
    `h1` TEXT,
    `mesh_text_1` TEXT,
    `mesh_url_1` TEXT,
    `mesh_text_2` TEXT,
    `mesh_url_2` TEXT,
    `mesh_text_3` TEXT,
    `mesh_url_3` TEXT,
    `mesh_text_4` TEXT,
    `mesh_url_4` TEXT,
    `mesh_text_5` TEXT,
    `mesh_url_5` TEXT,
    `mesh_1` TEXT,
    `mesh_2` TEXT,
    `mesh_3` TEXT,
    `mesh_4` TEXT,
    `mesh_5` TEXT,
    `json_data` TEXT,
    PRIMARY KEY (`id`,`locale`),
    CONSTRAINT `seone_i18n_fk_9fab70`
        FOREIGN KEY (`id`)
        REFERENCES `seone` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
