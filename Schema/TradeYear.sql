CREATE TABLE `TradeYear` (
  `TYid` int NOT NULL AUTO_INCREMENT,
  `Tid` int NOT NULL,
  `Year` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `Days` tinyint NOT NULL,
  `Insurance` tinyint NOT NULL,
  `RiskAssessment` tinyint NOT NULL,
  `HealthChecked` tinyint NOT NULL,
  `PitchSize0` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `PitchSize1` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `PitchSize2` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `Power0` int NOT NULL,
  `Power1` int NOT NULL,
  `Power2` int NOT NULL,
  `PitchNum0` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `PitchNum1` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `PitchNum2` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `PitchLoc0` smallint NOT NULL,
  `PitchLoc1` smallint NOT NULL,
  `PitchLoc2` smallint NOT NULL,
  `BookingState` tinyint NOT NULL,
  `Fee` int NOT NULL,
  `TotalPaid` int NOT NULL,
  `YNotes` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `PNotes` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `Date` int NOT NULL,
  `History` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `SentInvite` int NOT NULL,
  `SentConfirm` int NOT NULL,
  `SentLocation` int NOT NULL,
  `SentArrive` int NOT NULL,
  `DepositCode` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `BalanceCode` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `OtherCode` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `DateChange` int NOT NULL,
  PRIMARY KEY (`TYid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;
