CREATE TABLE `logs` (
  `id` int(11) UNSIGNED NOT NULL,
  `action` int(11) NOT NULL DEFAULT '0',
  `ip` varchar(255) CHARACTER SET utf8 COLLATE utf8 NOT NULL,
  `stamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `packages` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8 NOT NULL,
  `profile` varchar(255) COLLATE utf8 NOT NULL,
  `duration` int(11) NOT NULL,
  `price` decimal(4,2) NOT NULL,
  `transfer` int(11) NOT NULL,
  `position` int(11) NOT NULL DEFAULT '0',
  `active` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8;

INSERT INTO `packages` (`id`, `name`, `profile`, `duration`, `price`, `transfer`, `position`, `active`) VALUES
(1, 'Internet Day', 'InternetDay', 86400, '1.20', 2000, 1, 1),
(2, 'Internet Weekend', 'InternetWeekend', 259200, '3.30', 5000, 2, 1),
(3, 'Internet Week', 'InternetWeek', 604800, '7.40', 10000, 3, 1);

CREATE TABLE `statistics` (
  `id` int(11) UNSIGNED NOT NULL,
  `transaction_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `bytes_in` int(11) NOT NULL,
  `bytes_out` int(11) NOT NULL,
  `packets_in` int(11) NOT NULL,
  `packets_out` int(11) NOT NULL,
  `uptime` varchar(100) COLLATE utf8 NOT NULL,
  `stamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8;

CREATE TABLE `statistics_active` (
  `id` int(11) UNSIGNED NOT NULL,
  `transaction_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `address` varchar(100) COLLATE utf8 NOT NULL,
  `mac_address` varchar(100) COLLATE utf8 NOT NULL,
  `uptime` varchar(100) COLLATE utf8 NOT NULL,
  `session_time_left` varchar(100) COLLATE utf8 NOT NULL,
  `bytes_in` int(11) NOT NULL,
  `bytes_out` int(11) NOT NULL,
  `limit_bytes_total` int(11) NOT NULL,
  `stamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8;

CREATE TABLE `transactions` (
  `id` int(11) UNSIGNED NOT NULL,
  `transaction` varchar(255) COLLATE utf8 NOT NULL,
  `paypal` varchar(255) COLLATE utf8 NOT NULL,
  `confirmed` tinyint(4) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL DEFAULT '0',
  `package_id` int(11) NOT NULL DEFAULT '0',
  `confirmation` text COLLATE utf8 NOT NULL,
  `created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `stamp_start` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `stamp_end` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8;

CREATE TABLE `users` (
  `id` int(11) UNSIGNED NOT NULL,
  `username` varchar(50) COLLATE utf8 NOT NULL,
  `email` varchar(255) COLLATE utf8 NOT NULL,
  `router_id` varchar(255) COLLATE utf8 NOT NULL,
  `traceability` text COLLATE utf8 NOT NULL,
  `mac` varchar(255) COLLATE utf8 NOT NULL,
  `stamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `active` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8;