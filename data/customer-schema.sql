
DROP TABLE IF EXISTS `customer_Customer`;

CREATE TABLE `customer_Customer` (
  `customerID` int(12) NOT NULL AUTO_INCREMENT,
  `siteID` int(12) NOT NULL,
  `creator` int(12) NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  `deleted` int(1) NOT NULL,
  `customerNameEnglish` varchar(100) NOT NULL,
  `customerNameJapanese` varchar(100) NOT NULL,
  `customerPersonInCharge` varchar(100) NOT NULL,
  `customerRepresentativeDepartment` varchar(100) NOT NULL,
  `customerRepresentativeTitle` varchar(100) NOT NULL,
  `customerHonorarySuffix` varchar(10) NOT NULL,
  `customerNotes` varchar(255) NOT NULL,
  `customerTelephone` varchar(50) NOT NULL,
  `customerFax` varchar(50) NOT NULL,
  `customerEmail` varchar(50) NOT NULL,
  `priceLevel` int(2) NOT NULL,
  PRIMARY KEY (`customerID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
