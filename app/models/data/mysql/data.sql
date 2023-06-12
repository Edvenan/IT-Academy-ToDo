-- MySQL Workbench Synchronization
-- Generated: 2023-05-30 12:36
-- Model: New Model
-- Version: 1.0
-- Project: Name of the project
-- Author: e_ven

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE="ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION";

CREATE SCHEMA IF NOT EXISTS `ToDo` DEFAULT CHARACTER SET utf8 ;

CREATE TABLE IF NOT EXISTS `ToDo`.`Tasks` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  `start_time` DATETIME NULL DEFAULT NULL,
  `end_time` DATETIME NULL DEFAULT NULL,
  `author` VARCHAR(45) NOT NULL,
  `status` ENUM("Pending", "Ongoing", "Finished") NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

USE todo;

INSERT INTO tasks (
    id,
    name,
    start_time,
    end_time,
    author,
    status
  )
VALUES
    (NULL, "Table delivery to Ikea", NULL, NULL, "James Campbell", "Pending"),
    (NULL, "Parcel collection at Macy's", NULL, NULL, "Jong Smith", "Pending"),
    (NULL, "Complete Sprint-3 FullStak PHP", NULL, NULL, "Joana", "Pending"),
    (NULL, "Complete Sprint-3 FullStak PHP", NULL, NULL, "Pupa", "Pending"),
    (NULL, "Complete Sprint-3 FullStak PHP", NULL, NULL, "Eduard", "Pending"),
    (NULL, "Hiring process for new employee", NULL, NULL, "Allison Baxter", "Pending"),
    (NULL, "Seafreight shipment to China", NULL, NULL, "Carol Johnson", "Pending"),
    (NULL, "Project A - Task 1", NULL, NULL, "Rubén Salander", "Pending"),
    (NULL, "Project A - Task 2", NULL, NULL, "Joan Java", "Pending"),
    (NULL, "Project A - Task 3", NULL, NULL, "Oriol Go", "Pending"),
    (NULL, "Project B - Task 1", NULL, NULL, "Mark Fletcher", "Pending");


