
--
-- Dumping data for table `plan_master`
--

INSERT INTO `plans` (`id`, `name`, `sub_name`, `type`, `no_of_contact`, `amount`, `days`, `description`) VALUES
(1, 'FREE', 'Rooky', 'limited', '25', '0.00', '30 days', 'When they sign up, this allows them be able\r\nTo send requests to only 5 contacts daily.'),
(2, 'ELITES', 'Upgrade to ELITES', 'limited', '100', '726.35', '30 days', 'Request upto 25 contacts daily'),
(3, 'PRO', 'Upgrade to PRO', 'limited', '500', '2555.00', '30 days', 'Request upto 100 contacts daily.'),
(4, 'DIAMOND', 'Upgrade to DIAMOND', 'limited', '1000', '4380.00', '30 days', 'Request up to 500 Contacts Daily'),
(5, 'UNLIMITED', 'BLACK', 'unlimited', '3650000', '17885.00', '365 days', 'Unlimited Contacts');
