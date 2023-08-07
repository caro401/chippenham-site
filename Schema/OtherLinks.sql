CREATE TABLE `OtherLinks` (
  `id` int NOT NULL AUTO_INCREMENT,
  `LinkType` int NOT NULL,
  `SN` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci,
  `URL` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `Image` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `Year` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;
