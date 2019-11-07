-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Sep 05, 2019 at 10:34 PM
-- Server version: 10.3.15-MariaDB
-- PHP Version: 7.2.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `testDB`
--

-- --------------------------------------------------------

--
-- Table structure for table `countries`
--

CREATE TABLE `countries` (
  `id` int(10) UNSIGNED NOT NULL,
  `sortname` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phonecode` int(11) NOT NULL,
  `flag` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `currency_symbol` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `currency_code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `countries`
--

INSERT INTO `countries` (`id`, `sortname`, `name`, `phonecode`, `flag`, `currency_symbol`, `currency_code`) VALUES
(1, 'AF', 'Afghanistan', 93, 'af.png', '؋', 'AFN'),
(2, 'AL', 'Albania', 355, 'al.png', 'L', 'ALL'),
(3, 'DZ', 'Algeria', 213, 'dz.png', 'د.ج', 'DZD'),
(4, 'AS', 'American Samoa', 1684, 'as.png', '', 'USD'),
(5, 'AD', 'Andorra', 376, 'ad.png', '€', 'EUR'),
(6, 'AO', 'Angola', 244, 'ao.png', 'Kz', 'AOA'),
(7, 'AI', 'Anguilla', 1264, 'ai.png', '$', 'XCD'),
(8, 'AQ', 'Antarctica', 672, 'aq.png', '', ''),
(9, 'AG', 'Antigua And Barbuda', 1268, 'ag.png', '$', 'XCD'),
(10, 'AR', 'Argentina', 54, 'ar.png', '$', 'ARS'),
(11, 'AM', 'Armenia', 374, 'am.png', '', 'AMD'),
(12, 'AW', 'Aruba', 297, 'aw.png', 'ƒ', 'AWG'),
(13, 'AU', 'Australia', 61, 'au.png', '$', 'AUD'),
(14, 'AT', 'Austria', 43, 'at.png', '€', 'EUR'),
(15, 'AZ', 'Azerbaijan', 994, 'az.png', '', 'AZN'),
(16, 'BS', 'Bahamas The', 1242, 'bs.png', '', 'BSD'),
(17, 'BH', 'Bahrain', 973, 'bh.png', '.د.ب', 'BHD'),
(18, 'BD', 'Bangladesh', 880, 'bd.png', '৳', 'BDT'),
(19, 'BB', 'Barbados', 1246, 'bb.png', '$', 'BBD'),
(20, 'BY', 'Belarus', 375, 'by.png', 'Br', 'BYR'),
(21, 'BE', 'Belgium', 32, 'be.png', '€', 'EUR'),
(22, 'BZ', 'Belize', 501, 'bz.png', '$', 'BZD'),
(23, 'BJ', 'Benin', 229, 'bj.png', 'Fr', 'XOF'),
(24, 'BM', 'Bermuda', 1441, 'bm.png', '', 'BMD'),
(25, 'BT', 'Bhutan', 975, 'bt.png', '', 'BTN'),
(26, 'BO', 'Bolivia', 591, 'bo.png', '', 'BOB'),
(27, 'BA', 'Bosnia and Herzegovina', 387, 'ba.png', '', 'BAM'),
(28, 'BW', 'Botswana', 267, 'bw.png', '', 'BWP'),
(29, 'BV', 'Bouvet Island', 47, 'bv.png', '', 'NOK'),
(30, 'BR', 'Brazil', 55, 'br.png', '', 'BRL'),
(31, 'IO', 'British Indian Ocean Territory', 246, 'io.png', '', 'USD'),
(32, 'BN', 'Brunei', 673, 'bn.png', '', 'BND'),
(33, 'BG', 'Bulgaria', 359, 'bg.png', '', 'BGN'),
(34, 'BF', 'Burkina Faso', 226, 'bf.png', '', 'XOF'),
(35, 'BI', 'Burundi', 257, 'bi.png', '', 'BIF'),
(36, 'KH', 'Cambodia', 855, 'kh.png', '', 'KHR'),
(37, 'CM', 'Cameroon', 237, 'cm.png', '', 'XAF'),
(38, 'CA', 'Canada', 1, 'ca.png', '', 'CAD'),
(39, 'CV', 'Cape Verde', 238, 'cv.png', '', ''),
(40, 'KY', 'Cayman Islands', 1345, 'ky.png', '', 'KYD'),
(41, 'CF', 'Central African Republic', 236, 'cf.png', '', 'XAF'),
(42, 'TD', 'Chad', 235, 'td.png', '', 'XAF'),
(43, 'CL', 'Chile', 56, 'cl.png', '', 'CLF'),
(44, 'CN', 'China', 86, 'cn.png', '', 'CNY'),
(45, 'CX', 'Christmas Island', 61, 'cx.png', '', 'AUD'),
(46, 'CC', 'Cocos (Keeling) Islands', 672, 'cc.png', '', 'AUD'),
(47, 'CO', 'Colombia', 57, 'co.png', '', 'COP'),
(48, 'KM', 'Comoros', 269, 'km.png', '', 'KMF'),
(49, 'CG', 'Congo', 242, 'cg.png', '', 'CDF'),
(50, 'CD', 'Congo The Democratic Republic Of The', 242, 'cd.png', '', 'CDF'),
(51, 'CK', 'Cook Islands', 682, 'ck.png', '', 'NZD'),
(52, 'CR', 'Costa Rica', 506, 'cr.png', '', 'CRC'),
(53, 'CI', 'Cote D\'Ivoire (Ivory Coast)', 225, 'ci.png', '', 'XOF'),
(54, 'HR', 'Croatia (Hrvatska)', 385, 'hr.png', '', 'HRK'),
(55, 'CU', 'Cuba', 53, 'cu.png', '', 'CUC'),
(56, 'CY', 'Cyprus', 357, 'cy.png', '', 'EUR'),
(57, 'CZ', 'Czech Republic', 420, 'cz.png', '', 'CZK'),
(58, 'DK', 'Denmark', 45, 'dk.png', '', 'DKK'),
(59, 'DJ', 'Djibouti', 253, 'dj.png', '', 'DJF'),
(60, 'DM', 'Dominica', 1767, 'dm.png', '', 'XCD'),
(61, 'DO', 'Dominican Republic', 1809, 'do.png', '', 'DOP'),
(62, 'TP', 'East Timor', 670, 'tl.png', '', ''),
(63, 'EC', 'Ecuador', 593, 'ec.png', '', 'USD'),
(64, 'EG', 'Egypt', 20, 'eg.png', '', 'EGP'),
(65, 'SV', 'El Salvador', 503, 'sv.png', '', 'SVC'),
(66, 'GQ', 'Equatorial Guinea', 240, 'gq.png', '', 'XAF'),
(67, 'ER', 'Eritrea', 291, 'er.png', '', 'ERN'),
(68, 'EE', 'Estonia', 372, 'ee.png', '', 'EUR'),
(69, 'ET', 'Ethiopia', 251, 'et.png', '', 'ETB'),
(70, 'XA', 'External Territories of Australia', 61, 'cc.png', '', ''),
(71, 'FK', 'Falkland Islands', 500, 'fk.png', '', 'FKP'),
(72, 'FO', 'Faroe Islands', 298, 'fo.png', '', 'DKK'),
(73, 'FJ', 'Fiji Islands', 679, 'fj.png', '', 'FJD'),
(74, 'FI', 'Finland', 358, 'fi.png', '', 'EUR'),
(75, 'FR', 'France', 33, 'fr.png', '', 'EUR'),
(76, 'GF', 'French Guiana', 594, 'gf.png', '', 'EUR'),
(77, 'PF', 'French Polynesia', 689, 'pf.png', '', 'XPF'),
(78, 'TF', 'French Southern Territories', 33, 'tf.png', '', 'EUR'),
(79, 'GA', 'Gabon', 241, 'ga.png', '', 'XAF'),
(80, 'GM', 'Gambia The', 220, 'gm.png', '', 'GMD'),
(81, 'GE', 'Georgia', 995, 'ge.png', '', 'GEL'),
(82, 'DE', 'Germany', 49, 'de.png', '', 'EUR'),
(83, 'GH', 'Ghana', 233, 'gh.png', '', 'GHS'),
(84, 'GI', 'Gibraltar', 350, 'gi.png', '', 'GIP'),
(85, 'GR', 'Greece', 30, 'gr.png', '', 'EUR'),
(86, 'GL', 'Greenland', 299, 'gl.png', '', 'DKK'),
(87, 'GD', 'Grenada', 1473, 'gd.png', '', 'XCD'),
(88, 'GP', 'Guadeloupe', 590, 'gp.png', '', 'EUR'),
(89, 'GU', 'Guam', 1671, 'gu.png', '', 'USD'),
(90, 'GT', 'Guatemala', 502, 'gt.png', '', 'GTQ'),
(91, 'XU', 'Guernsey and Alderney', 44, 'gg.png', '', 'GBP'),
(92, 'GN', 'Guinea', 224, 'gn.png', '', 'GNF'),
(93, 'GW', 'Guinea-Bissau', 245, 'gw.png', '', 'XOF'),
(94, 'GY', 'Guyana', 592, 'gy.png', '', 'GYD'),
(95, 'HT', 'Haiti', 509, 'ht.png', '', 'HTG'),
(96, 'HM', 'Heard and McDonald Islands', 672, 'hm.png', '', 'AUD'),
(97, 'HN', 'Honduras', 504, 'hn.png', '', 'EUR'),
(98, 'HK', 'Hong Kong S.A.R.', 852, 'hk.png', '', 'HKD'),
(99, 'HU', 'Hungary', 36, 'hu.png', '', 'HUF'),
(100, 'IS', 'Iceland', 354, 'is.png', '', 'ISK'),
(101, 'IN', 'India', 91, 'in.png', '', 'INR'),
(102, 'ID', 'Indonesia', 62, 'id.png', '', 'IDR'),
(103, 'IR', 'Iran', 98, 'ir.png', '', 'IRR'),
(104, 'IQ', 'Iraq', 964, 'iq.png', '', 'IQD'),
(105, 'IE', 'Ireland', 353, 'ie.png', '', 'Euro'),
(106, 'IL', 'Israel', 972, 'il.png', '', 'ILS'),
(107, 'IT', 'Italy', 39, 'it.png', '', 'EUR'),
(108, 'JM', 'Jamaica', 1876, 'jm.png', '', 'JMD'),
(109, 'JP', 'Japan', 81, 'jp.png', '', 'JPY'),
(110, 'XJ', 'Jersey', 44, 'je.png', '', 'GBP'),
(111, 'JO', 'Jordan', 962, 'jo.png', '', 'JOD'),
(112, 'KZ', 'Kazakhstan', 7, 'kz.png', '', 'KZT'),
(113, 'KE', 'Kenya', 254, 'ke.png', '', 'KES'),
(114, 'KI', 'Kiribati', 686, 'ki.png', '', 'AUD'),
(115, 'KP', 'Korea North', 850, 'kp.png', '', 'KPW'),
(116, 'KR', 'Korea South', 82, 'kr.png', '', 'KRW'),
(117, 'KW', 'Kuwait', 965, 'kw.png', '', 'KWD'),
(118, 'KG', 'Kyrgyzstan', 996, 'kg.png', '', 'KGS'),
(119, 'LA', 'Laos', 856, 'la.png', '', ''),
(120, 'LV', 'Latvia', 371, 'lv.png', '', 'EUR'),
(121, 'LB', 'Lebanon', 961, 'lb.png', '', 'LBP'),
(122, 'LS', 'Lesotho', 266, 'ls.png', '', 'LSL'),
(123, 'LR', 'Liberia', 231, 'lr.png', '', 'LRD'),
(124, 'LY', 'Libya', 218, 'ly.png', '', 'LYD'),
(125, 'LI', 'Liechtenstein', 423, 'li.png', '', 'CHF'),
(126, 'LT', 'Lithuania', 370, 'lt.png', '', 'EUR'),
(127, 'LU', 'Luxembourg', 352, 'lu.png', '', 'EUR'),
(128, 'MO', 'Macau S.A.R.', 853, 'mo.png', '', 'MOP'),
(129, 'MK', 'Macedonia', 389, 'mk.png', '', 'MKD'),
(130, 'MG', 'Madagascar', 261, 'mg.png', '', 'MGA'),
(131, 'MW', 'Malawi', 265, 'mw.png', '', 'MWK'),
(132, 'MY', 'Malaysia', 60, 'my.png', '', 'MYR'),
(133, 'MV', 'Maldives', 960, 'mv.png', '', 'MVR'),
(134, 'ML', 'Mali', 223, 'ml.png', '', 'XOF'),
(135, 'MT', 'Malta', 356, 'mt.png', '', 'EUR'),
(136, 'XM', 'Man (Isle of)', 44, 'xm.png', '', ''),
(137, 'MH', 'Marshall Islands', 692, 'mh.png', '', 'USD'),
(138, 'MQ', 'Martinique', 596, 'mq.png', '', 'EUR'),
(139, 'MR', 'Mauritania', 222, 'mr.png', '', 'MRU'),
(140, 'MU', 'Mauritius', 230, 'mu.png', '', 'MUR'),
(141, 'YT', 'Mayotte', 269, 'yt.png', '', 'EUR'),
(142, 'MX', 'Mexico', 52, 'mx.png', '', 'MXN'),
(143, 'FM', 'Micronesia', 691, 'fm.png', '', 'USD'),
(144, 'MD', 'Moldova', 373, 'md.png', '', 'MDL'),
(145, 'MC', 'Monaco', 377, 'mc.png', '', 'EUR'),
(146, 'MN', 'Mongolia', 976, 'mn.png', '', 'MNT'),
(147, 'MS', 'Montserrat', 1664, 'ms.png', '', 'XCD'),
(148, 'MA', 'Morocco', 212, 'ma.png', '', 'MAD'),
(149, 'MZ', 'Mozambique', 258, 'mz.png', '', 'MZN'),
(150, 'MM', 'Myanmar', 95, 'mm.png', '', 'MMK'),
(151, 'NA', 'Namibia', 264, 'na.png', '', 'NAD'),
(152, 'NR', 'Nauru', 674, 'nr.png', '', 'Nauru'),
(153, 'NP', 'Nepal', 977, 'np.png', '', 'NPR'),
(154, 'AN', 'Netherlands Antilles', 599, 'an.png', '', ''),
(155, 'NL', 'Netherlands The', 31, 'nl.png', '', 'EUR'),
(156, 'NC', 'New Caledonia', 687, 'nc.png', '', 'XPF'),
(157, 'NZ', 'New Zealand', 64, 'nz.png', '', 'NZD'),
(158, 'NI', 'Nicaragua', 505, 'ni.png', '', 'NIO'),
(159, 'NE', 'Niger', 227, 'ne.png', '', 'XOF'),
(160, 'NG', 'Nigeria', 234, 'ng.png', '', 'NGN'),
(161, 'NU', 'Niue', 683, 'nu.png', '', 'NZD'),
(162, 'NF', 'Norfolk Island', 672, 'nf.png', '', 'AUD'),
(163, 'MP', 'Northern Mariana Islands', 1670, 'mp.png', '', 'USD'),
(164, 'NO', 'Norway', 47, 'no.png', '', 'NOK'),
(165, 'OM', 'Oman', 968, 'om.png', '', 'OMR'),
(166, 'PK', 'Pakistan', 92, 'pk.png', '', 'PKR'),
(167, 'PW', 'Palau', 680, 'pw.png', '', 'USD'),
(168, 'PS', 'Palestinian Territory Occupied', 970, 'ps.png', '', ''),
(169, 'PA', 'Panama', 507, 'pa.png', '', 'PAB'),
(170, 'PG', 'Papua new Guinea', 675, 'pg.png', '', 'PGK'),
(171, 'PY', 'Paraguay', 595, 'py.png', '', 'PYG'),
(172, 'PE', 'Peru', 51, 'pe.png', '', 'PEN'),
(173, 'PH', 'Philippines', 63, 'ph.png', '', 'PHP'),
(174, 'PN', 'Pitcairn Island', 64, 'pn.png', '', 'NZD'),
(175, 'PL', 'Poland', 48, 'pl.png', '', 'PLN'),
(176, 'PT', 'Portugal', 351, 'pt.png', '', 'EUR'),
(177, 'PR', 'Puerto Rico', 1787, 'pr.png', '', 'USD'),
(178, 'QA', 'Qatar', 974, 'qa.png', '', 'QAR'),
(179, 'RE', 'Reunion', 262, 're.png', '', 'EUR'),
(180, 'RO', 'Romania', 40, 'ro.png', '', 'RON'),
(181, 'RU', 'Russia', 70, 'ru.png', '', 'RUB'),
(182, 'RW', 'Rwanda', 250, 'rw.png', '', 'RWF'),
(183, 'SH', 'Saint Helena', 290, 'sq.png', '', 'SHP'),
(184, 'KN', 'Saint Kitts And Nevis', 1869, 'kn.png', '', 'XCD'),
(185, 'LC', 'Saint Lucia', 1758, 'lc.png', '', 'XCD'),
(186, 'PM', 'Saint Pierre and Miquelon', 508, 'pm.png', '', 'EUR'),
(187, 'VC', 'Saint Vincent And The Grenadines', 1784, 'vc.png', '', 'XCD'),
(188, 'WS', 'Samoa', 684, 'ws.png', '', 'WST'),
(189, 'SM', 'San Marino', 378, 'sm.png', '', 'EUR'),
(190, 'ST', 'Sao Tome and Principe', 239, 'st.png', '', 'STN'),
(191, 'SA', 'Saudi Arabia', 966, 'sa.png', '', 'SAR'),
(192, 'SN', 'Senegal', 221, 'sn.png', '', 'XOF'),
(193, 'RS', 'Serbia', 381, 'rs.png', '', 'RSD'),
(194, 'SC', 'Seychelles', 248, 'sc.png', '', 'SCR'),
(195, 'SL', 'Sierra Leone', 232, 'sl.png', '', 'SLL'),
(196, 'SG', 'Singapore', 65, 'sg.png', '', 'SGD'),
(197, 'SK', 'Slovakia', 421, 'sk.png', '', 'EUR'),
(198, 'SI', 'Slovenia', 386, 'si.png', '', 'EUR'),
(199, 'XG', 'Smaller Territories of the UK', 44, 'sh.png', '', ''),
(200, 'SB', 'Solomon Islands', 677, 'sb.png', '', 'SBD'),
(201, 'SO', 'Somalia', 252, 'so.png', '', 'SOS'),
(202, 'ZA', 'South Africa', 27, 'za.png', '', 'ZAR'),
(203, 'GS', 'South Georgia', 0, 'gs.png', '', ''),
(204, 'SS', 'South Sudan', 211, 'ss.png', '', 'SSP'),
(205, 'ES', 'Spain', 34, 'es.png', '', 'EUR'),
(206, 'LK', 'Sri Lanka', 94, 'lk.png', '', 'LKR'),
(207, 'SD', 'Sudan', 249, 'sd.png', '', 'SDG'),
(208, 'SR', 'Suriname', 597, 'sr.png', '', 'SRD'),
(209, 'SJ', 'Svalbard And Jan Mayen Islands', 47, 'sj.png', '', 'NOK'),
(210, 'SZ', 'Swaziland', 268, 'sz.png', '', 'SZL'),
(211, 'SE', 'Sweden', 46, 'se.png', '', 'SEK'),
(212, 'CH', 'Switzerland', 41, 'ch.png ', '', 'CHE'),
(213, 'SY', 'Syria', 963, 'sy.png', '', 'SYP'),
(214, 'TW', 'Taiwan', 886, 'tw.png', '', 'TWD'),
(215, 'TJ', 'Tajikistan', 992, 'tj.png', '', 'TJS'),
(216, 'TZ', 'Tanzania', 255, 'tz.png', '', 'TZS'),
(217, 'TH', 'Thailand', 66, 'th.png', '', 'THB'),
(218, 'TG', 'Togo', 228, 'tg.png', '', 'XOF'),
(219, 'TK', 'Tokelau', 690, 'tk.png', '', 'NZD'),
(220, 'TO', 'Tonga', 676, 'to.png', '', 'TOP'),
(221, 'TT', 'Trinidad And Tobago', 1868, 'tt.png', '', 'TTD'),
(222, 'TN', 'Tunisia', 216, 'tn.png', '', 'TND'),
(223, 'TR', 'Turkey', 90, 'tr.png', '', 'TRY'),
(224, 'TM', 'Turkmenistan', 7370, 'tm.png', '', 'TMT'),
(225, 'TC', 'Turks And Caicos Islands', 1649, 'tc.png', '', 'USD'),
(226, 'TV', 'Tuvalu', 688, 'tv.png', '', 'AUD'),
(227, 'UG', 'Uganda', 256, 'ug.png', '', 'UGX'),
(228, 'UA', 'Ukraine', 380, 'ua.png', '', 'UAH'),
(229, 'AE', 'United Arab Emirates', 971, 'ae.png', '', 'AED'),
(230, 'GB', 'United Kingdom', 44, 'gb.png', '', 'GBP'),
(231, 'US', 'United States', 1, 'us.png', '', 'USD'),
(232, 'UM', 'United States Minor Outlying Islands', 1, 'um.png', '', 'USD'),
(233, 'UY', 'Uruguay', 598, 'uy.png', '', 'UYI'),
(234, 'UZ', 'Uzbekistan', 998, 'uz.png', '', 'UZS'),
(235, 'VU', 'Vanuatu', 678, 'vu.png', '', 'VUV'),
(236, 'VA', 'Vatican City State (Holy See)', 39, 'va.png', '', ''),
(237, 'VE', 'Venezuela', 58, 've.png', '', 'VEF'),
(238, 'VN', 'Vietnam', 84, 'vn.png', '', 'VND'),
(239, 'VG', 'Virgin Islands (British)', 1284, 'vg.png', '', 'USD'),
(240, 'VI', 'Virgin Islands (US)', 1340, 'vi.png', '', 'USD'),
(241, 'WF', 'Wallis And Futuna Islands', 681, 'wf.png', '', 'XPF'),
(242, 'EH', 'Western Sahara', 212, 'eh.png', '', 'MAD'),
(243, 'YE', 'Yemen', 967, 'ye.png', '', 'YER'),
(244, 'YU', 'Yugoslavia', 38, 'yu.png', '', ''),
(245, 'ZM', 'Zambia', 260, 'zm.png', '', 'ZMW'),
(246, 'ZW', 'Zimbabwe', 263, 'zw.png', '', 'ZWL');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `countries`
--
ALTER TABLE `countries`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `countries`
--
ALTER TABLE `countries`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=247;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
