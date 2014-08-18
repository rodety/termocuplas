CREATE DATABASE  IF NOT EXISTS `sensor` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `sensor`;
-- MySQL dump 10.13  Distrib 5.5.38, for debian-linux-gnu (x86_64)
--
-- Host: 127.0.0.1    Database: sensor
-- ------------------------------------------------------
-- Server version	5.6.12

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `Sensor`
--

DROP TABLE IF EXISTS `Sensor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Sensor` (
  `idSensor` int(11) NOT NULL AUTO_INCREMENT,
  `ubicacion` varchar(45) NOT NULL,
  `tipo` varchar(45) NOT NULL,
  `unidadMedida` varchar(45) NOT NULL,
  `habilitado` bit(1) NOT NULL DEFAULT b'1',
  PRIMARY KEY (`idSensor`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Temperatura`
--

DROP TABLE IF EXISTS `Temperatura`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Temperatura` (
  `idTemperatura` int(11) NOT NULL AUTO_INCREMENT,
  `valor` double NOT NULL,
  `Sensor_idSensor` int(11) NOT NULL,
  `fecha` datetime NOT NULL,
  PRIMARY KEY (`idTemperatura`,`Sensor_idSensor`),
  KEY `fk_Temperatura_Sensor_idx` (`Sensor_idSensor`) USING BTREE,
  KEY `fecha_index` (`fecha`) USING BTREE,
  CONSTRAINT `fk_Temperatura_Sensor` FOREIGN KEY (`Sensor_idSensor`) REFERENCES `Sensor` (`idSensor`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=2210284 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `Sensor` WRITE;
/*!40000 ALTER TABLE `Sensor` DISABLE KEYS */;
INSERT INTO `Sensor` VALUES (1,'arequipa','1','grados',''),(2,'cusco','1','grados','\0'),(3,'lima','1','grados',''),(4,'ica','1','grados','\0'),(5,'chiclayo','1','grados','\0'),(6,'piura','1','grados','\0'),(7,'tacna','1','grados','\0'),(8,'puno','1','grados','\0'),(9,'abancay','1','grados','\0'),(10,'moquegua','1','grados','\0'),(11,'cajamarca','1','grados','\0'),(12,'trujillo','1','grados','\0');
/*!40000 ALTER TABLE `Sensor` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

--
-- Dumping routines for database 'sensor'
--
/*!50003 DROP PROCEDURE IF EXISTS `InsertRand` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `InsertRand`(IN NumRows INT)
BEGIN
        DECLARE i INT;
        SET i = 1;
        START TRANSACTION;
        WHILE i <= NumRows DO
			INSERT INTO `sensor`.`Temperatura`(`valor`,`Sensor_idSensor`,`fecha`)
			VALUES (
				FLOOR(10 + (RAND() * 50)),
				FLOOR(1 + (RAND() * 12)),
				FROM_UNIXTIME(UNIX_TIMESTAMP('2014-01-01 00:00:00') + FLOOR(0 + (RAND() * 31536000)))
			);
            SET i = i + 1;
        END WHILE;
        COMMIT;
    END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-08-17  6:03:33
