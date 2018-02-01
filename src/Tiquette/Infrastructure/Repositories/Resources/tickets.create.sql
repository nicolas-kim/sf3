CREATE TABLE `tickets` (
  `uuid` char(36) NOT NULL,
  `event_name` varchar(255) NOT NULL DEFAULT '',
  `event_description` text NOT NULL,
  `event_date` datetime NOT NULL,
  `bought_at_price` int(10) unsigned NOT NULL,
  `price_currency` char(3) NOT NULL DEFAULT '',
  `submitted_on` datetime NOT NULL,
  `accepted_offer_id` char(36) DEFAULT NULL,
  `seller_id` char(36) DEFAULT NULL,
  PRIMARY KEY (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
