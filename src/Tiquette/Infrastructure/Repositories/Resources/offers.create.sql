CREATE TABLE `offers` (
  `uuid` char(36) NOT NULL DEFAULT '',
  `proposed_price` int(10) unsigned NOT NULL,
  `price_currency` char(3) NOT NULL DEFAULT '',
  `buyer_message` text NOT NULL,
  `ticket_uuid` char(36) NOT NULL DEFAULT '',
  `buyer_uuid` char(36) NOT NULL DEFAULT '',
  `accepted_on` datetime DEFAULT NULL,
  PRIMARY KEY (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
