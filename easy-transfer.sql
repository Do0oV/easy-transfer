-- MySQL Script generated by MySQL Workbench
-- Thu Jun 14 18:08:58 2018
-- Model: New Model    Version: 1.0
-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema easy-transfer
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema easy-transfer
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `easy-transfer` DEFAULT CHARACTER SET utf8 ;
USE `easy-transfer` ;

-- -----------------------------------------------------
-- Table `easy-transfer`.`transfer`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `easy-transfer`.`transfer` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `exp_email` VARCHAR(255) NOT NULL,
  `dest_email` VARCHAR(255) NOT NULL,
  `path` VARCHAR(255) NOT NULL,
  `message` TEXT NULL,
  `creation_date` DATETIME NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;