use esm_v3_develop;
SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `adverse_event`;
CREATE TABLE `adverse_event` (
                                 `id` int(11) NOT NULL AUTO_INCREMENT,
                                 `patient_id` int(11) NOT NULL,
                                 `serious` tinyint(1) NOT NULL,
                                 `number` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
                                 `occured_at` datetime NOT NULL,
                                 `deleted_at` datetime DEFAULT NULL,
                                 `source_id` int(11) DEFAULT NULL,
                                 PRIMARY KEY (`id`),
                                 KEY `IDX_F34C1C506B899279` (`patient_id`),
                                 CONSTRAINT `FK_F34C1C506B899279` FOREIGN KEY (`patient_id`) REFERENCES `patient` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `adverse_event_audit_trail`;
CREATE TABLE `adverse_event_audit_trail` (
                                             `id` int(11) NOT NULL AUTO_INCREMENT,
                                             `entity_id` int(11) NOT NULL,
                                             `user_id` int(11) DEFAULT NULL,
                                             `date` datetime NOT NULL,
                                             `reason` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                             `modif_type` smallint(6) NOT NULL COMMENT '1=insert, 2=update, 3=delete',
                                             `details` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:json)',
                                             PRIMARY KEY (`id`),
                                             KEY `IDX_58F447F781257D5D` (`entity_id`),
                                             KEY `IDX_58F447F7A76ED395` (`user_id`),
                                             CONSTRAINT `FK_58F447F781257D5D` FOREIGN KEY (`entity_id`) REFERENCES `adverse_event` (`id`),
                                             CONSTRAINT `FK_58F447F7A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `application`;
CREATE TABLE `application` (
                               `id` int(11) NOT NULL AUTO_INCREMENT,
                               `type_id` int(11) NOT NULL,
                               `name` varchar(55) COLLATE utf8mb4_unicode_ci NOT NULL,
                               `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                               `img` varchar(55) COLLATE utf8mb4_unicode_ci NOT NULL,
                               `api_token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                               `deleted_at` datetime DEFAULT NULL,
                               PRIMARY KEY (`id`),
                               KEY `IDX_A45BDDC1C54C8C93` (`type_id`),
                               CONSTRAINT `FK_A45BDDC1C54C8C93` FOREIGN KEY (`type_id`) REFERENCES `application_type` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `application` (`id`, `type_id`, `name`, `url`, `img`, `api_token`, `deleted_at`) VALUES
(1,	5,	'ESM',	'http://esm-template-v3.localhost',	'/img/app/portal.png',	'c0ebe23d7702b1fe4869fb0b6f8d8154',	NULL);

DROP TABLE IF EXISTS `application_type`;
CREATE TABLE `application_type` (
                                    `id` int(11) NOT NULL AUTO_INCREMENT,
                                    `name` varchar(55) COLLATE utf8mb4_unicode_ci NOT NULL,
                                    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `application_type` (`id`, `name`) VALUES
(1,	'Portal v2.0'),
(2,	'eTMF v1.0'),
(3,	'ICC v1.0'),
(4,	'ERDC v6.1'),
(5,	'ESM v3.0'),
(6,	'EDMS v2.1');

DROP TABLE IF EXISTS `application_user`;
CREATE TABLE `application_user` (
                                    `application_id` int(11) NOT NULL,
                                    `user_id` int(11) NOT NULL,
                                    PRIMARY KEY (`application_id`,`user_id`),
                                    KEY `IDX_7A7FBEC13E030ACD` (`application_id`),
                                    KEY `IDX_7A7FBEC1A76ED395` (`user_id`),
                                    CONSTRAINT `FK_7A7FBEC13E030ACD` FOREIGN KEY (`application_id`) REFERENCES `application` (`id`) ON DELETE CASCADE,
                                    CONSTRAINT `FK_7A7FBEC1A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `center`;
CREATE TABLE `center` (
                          `id` int(11) NOT NULL AUTO_INCREMENT,
                          `center_status_id` int(11) DEFAULT NULL,
                          `project_id` int(11) NOT NULL,
                          `number` varchar(55) COLLATE utf8mb4_unicode_ci NOT NULL,
                          `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                          `deleted_at` datetime DEFAULT NULL,
                          `source_id` int(11) DEFAULT NULL,
                          PRIMARY KEY (`id`),
                          KEY `IDX_40F0EB2463DE7C0F` (`center_status_id`),
                          KEY `IDX_40F0EB24166D1F9C` (`project_id`),
                          CONSTRAINT `FK_40F0EB24166D1F9C` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`),
                          CONSTRAINT `FK_40F0EB2463DE7C0F` FOREIGN KEY (`center_status_id`) REFERENCES `dl_center_status` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `center` (`id`, `center_status_id`, `project_id`, `number`, `name`, `deleted_at`, `source_id`) VALUES
(1,	1,	3,	'Un',	'CHU Un',	NULL,	NULL),
(2,	1,	2,	'0A',	'Numero Uno',	NULL,	NULL);

DROP TABLE IF EXISTS `center_audit_trail`;
CREATE TABLE `center_audit_trail` (
                                      `id` int(11) NOT NULL AUTO_INCREMENT,
                                      `entity_id` int(11) NOT NULL,
                                      `user_id` int(11) DEFAULT NULL,
                                      `date` datetime NOT NULL,
                                      `reason` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                      `modif_type` smallint(6) NOT NULL COMMENT '1=insert, 2=update, 3=delete',
                                      `details` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:json)',
                                      PRIMARY KEY (`id`),
                                      KEY `IDX_3A63187C81257D5D` (`entity_id`),
                                      KEY `IDX_3A63187CA76ED395` (`user_id`),
                                      CONSTRAINT `FK_3A63187C81257D5D` FOREIGN KEY (`entity_id`) REFERENCES `center` (`id`),
                                      CONSTRAINT `FK_3A63187CA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `center_audit_trail` (`id`, `entity_id`, `user_id`, `date`, `reason`, `modif_type`, `details`) VALUES
(1,	1,	5,	'2021-04-08 15:00:30',	NULL,	1,	'{\"number\":\"Un\",\"name\":\"CHU Un\",\"centerStatus\":\"En Pr\\u00e9s\\u00e9lection (1)\",\"institutions\":[\"CHU Saint-Hilaire (1)\"]}'),
(2,	2,	13,	'2021-04-12 11:08:21',	NULL,	1,	'{\"number\":\"0A\",\"name\":\"Numero Uno\",\"centerStatus\":\"En Pr\\u00e9s\\u00e9lection (1)\",\"institutions\":[\"CHU Saint-Hilaire (1)\"]}');

DROP TABLE IF EXISTS `condition_patient_data`;
CREATE TABLE `condition_patient_data` (
                                          `id` int(11) NOT NULL AUTO_INCREMENT,
                                          `patient_data_id` int(11) DEFAULT NULL,
                                          `schema_condition_id` int(11) DEFAULT NULL,
                                          `executed_at` datetime NOT NULL,
                                          PRIMARY KEY (`id`),
                                          KEY `IDX_84571A086A96F4D7` (`patient_data_id`),
                                          KEY `IDX_84571A08A88A5DEE` (`schema_condition_id`),
                                          CONSTRAINT `FK_84571A086A96F4D7` FOREIGN KEY (`patient_data_id`) REFERENCES `patient_data` (`id`),
                                          CONSTRAINT `FK_84571A08A88A5DEE` FOREIGN KEY (`schema_condition_id`) REFERENCES `schema_condition` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `condition_visit_patient`;
CREATE TABLE `condition_visit_patient` (
                                           `id` int(11) NOT NULL AUTO_INCREMENT,
                                           `visit_patient_id` int(11) DEFAULT NULL,
                                           `schema_condition_id` int(11) DEFAULT NULL,
                                           `executed_at` datetime NOT NULL,
                                           PRIMARY KEY (`id`),
                                           KEY `IDX_32C14DAF61EC6A` (`visit_patient_id`),
                                           KEY `IDX_32C14DAA88A5DEE` (`schema_condition_id`),
                                           CONSTRAINT `FK_32C14DAA88A5DEE` FOREIGN KEY (`schema_condition_id`) REFERENCES `schema_condition` (`id`),
                                           CONSTRAINT `FK_32C14DAF61EC6A` FOREIGN KEY (`visit_patient_id`) REFERENCES `visit_patient` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `connection_audit_trail`;
CREATE TABLE `connection_audit_trail` (
                                          `id` int(11) NOT NULL AUTO_INCREMENT,
                                          `user_id` int(11) NOT NULL,
                                          `application_id` int(11) NOT NULL,
                                          `started_at` datetime NOT NULL,
                                          `ended_at` datetime DEFAULT NULL,
                                          `user_agent` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                                          `device_session_token` varchar(55) COLLATE utf8mb4_unicode_ci NOT NULL,
                                          `ip` varchar(55) COLLATE utf8mb4_unicode_ci NOT NULL,
                                          `reason` int(11) DEFAULT NULL,
                                          `last_ping` datetime DEFAULT NULL,
                                          `last_refresh` datetime DEFAULT NULL,
                                          PRIMARY KEY (`id`),
                                          UNIQUE KEY `UNIQ_97F30D8475EC8E48` (`device_session_token`),
                                          KEY `IDX_97F30D84A76ED395` (`user_id`),
                                          KEY `IDX_97F30D843E030ACD` (`application_id`),
                                          CONSTRAINT `FK_97F30D843E030ACD` FOREIGN KEY (`application_id`) REFERENCES `application` (`id`),
                                          CONSTRAINT `FK_97F30D84A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `connection_audit_trail` (`id`, `user_id`, `application_id`, `started_at`, `ended_at`, `user_agent`, `device_session_token`, `ip`, `reason`, `last_ping`, `last_refresh`) VALUES
(1,	1,	1,	'2021-04-07 19:15:44',	'2021-04-07 19:18:24',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36',	'DST_35468e9c7bf63a33d58bf60942a2fa14',	'10.88.66.135',	1,	'2021-04-07 19:18:22',	'2021-04-07 19:16:12'),
(2,	6,	1,	'2021-04-07 19:24:05',	'2021-04-07 20:47:15',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:87.0) Gecko/20100101 Firefox/87.0',	'DST_d3c6fc1387d9567680a777bccccaf21b',	'10.88.66.135',	2,	'2021-04-07 20:17:54',	'2021-04-07 20:17:15'),
(3,	1,	1,	'2021-04-08 09:05:18',	'2021-04-08 09:35:58',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36',	'DST_90da349b1397e0e4a5449ed34afc2e3a',	'10.88.66.135',	2,	'2021-04-08 09:32:38',	'2021-04-08 09:05:58'),
(4,	5,	1,	'2021-04-08 09:28:05',	'2021-04-08 11:47:25',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36',	'DST_c7174c1e48b846aac713e47b1d2ebed5',	'10.88.66.135',	2,	'2021-04-08 11:46:25',	'2021-04-08 11:16:55'),
(5,	6,	1,	'2021-04-08 10:37:17',	'2021-04-08 11:08:06',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:87.0) Gecko/20100101 Firefox/87.0',	'DST_88e9815ed44e9cf6ccda5d5102e22987',	'10.88.66.135',	2,	'2021-04-08 11:07:55',	'2021-04-08 10:38:04'),
(6,	1,	1,	'2021-04-08 11:27:34',	'2021-04-08 12:01:12',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36',	'DST_ac8fe168b8546d5a98a130886fe67407',	'10.88.66.135',	2,	'2021-04-08 11:30:52',	'2021-04-08 11:31:12'),
(7,	6,	1,	'2021-04-08 13:55:16',	'2021-04-08 14:34:34',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36',	'DST_363a2b06318504983349ae18a26d656e',	'10.88.66.135',	2,	'2021-04-08 14:33:34',	'2021-04-08 14:04:12'),
(8,	1,	1,	'2021-04-08 14:06:17',	'2021-04-08 14:41:21',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36',	'DST_bdaac901ccf7eb4066bda1f82876def9',	'10.88.66.135',	2,	'2021-04-08 14:06:17',	'2021-04-08 14:11:21'),
(9,	6,	1,	'2021-04-08 14:35:37',	'2021-04-08 14:38:33',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36',	'DST_c55ad2ba43cccea9f98876d3c94e56f1',	'10.88.66.135',	3,	'2021-04-08 14:38:15',	'2021-04-08 14:38:20'),
(10,	6,	1,	'2021-04-08 14:38:33',	'2021-04-08 15:34:28',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:87.0) Gecko/20100101 Firefox/87.0',	'DST_a66a585ecc2da060c63bb00f89c83cd6',	'10.88.66.135',	2,	'2021-04-08 15:34:18',	'2021-04-08 15:04:25'),
(11,	5,	1,	'2021-04-08 14:39:49',	'2021-04-08 17:37:19',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36',	'DST_ec40dc7460d042661cf5030794ce01f4',	'10.88.66.135',	2,	'2021-04-08 17:37:09',	'2021-04-08 17:07:18'),
(12,	10,	1,	'2021-04-08 14:55:41',	'2021-04-08 15:42:59',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36',	'DST_24569164fef87acd7526e79eeddd6247',	'10.88.66.135',	2,	'2021-04-08 14:59:47',	'2021-04-08 14:59:57'),
(13,	10,	1,	'2021-04-08 15:44:10',	'2021-04-08 17:15:24',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36',	'DST_1eb511a4b497f023bb3cfc399531c201',	'10.88.66.135',	2,	'2021-04-08 16:37:23',	'2021-04-08 16:37:34'),
(14,	6,	1,	'2021-04-08 15:51:09',	'2021-04-08 16:27:52',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:87.0) Gecko/20100101 Firefox/87.0',	'DST_0a53c0f905f13b9e1d5ea6547db57e63',	'10.88.66.135',	2,	'2021-04-08 16:27:42',	'2021-04-08 15:57:50'),
(15,	1,	1,	'2021-04-08 16:19:33',	'2021-04-08 17:05:10',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36',	'DST_5c4f74933a961b6220a5b5e7741171e4',	'10.88.66.135',	2,	'2021-04-08 17:04:10',	'2021-04-08 16:34:27'),
(16,	11,	1,	'2021-04-08 16:31:59',	'2021-04-08 16:32:09',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:87.0) Gecko/20100101 Firefox/87.0',	'DST_6bed4b34016fa1a17d90345f7f80e8a8',	'10.88.66.135',	1,	'2021-04-08 16:31:59',	'2021-04-08 16:32:05'),
(17,	11,	1,	'2021-04-08 16:32:18',	'2021-04-08 17:03:36',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:87.0) Gecko/20100101 Firefox/87.0',	'DST_4abd459fb0b76c5d4424fe71c0bdda9c',	'10.88.66.135',	2,	'2021-04-08 17:03:16',	'2021-04-08 16:33:24'),
(18,	6,	1,	'2021-04-08 16:57:53',	'2021-04-08 17:52:50',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:87.0) Gecko/20100101 Firefox/87.0',	'DST_4d98d2bfaa2bec171eb2669764c73144',	'10.88.66.135',	2,	'2021-04-08 17:52:40',	'2021-04-08 17:22:48'),
(19,	10,	1,	'2021-04-08 17:15:44',	'2021-04-08 17:49:34',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36',	'DST_c61de7857025d05a07ddffd1c1e3f41c',	'10.88.66.135',	2,	'2021-04-08 17:48:34',	'2021-04-08 17:18:46'),
(20,	5,	1,	'2021-04-08 17:41:25',	'2021-04-08 18:23:06',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36',	'DST_ff662757549026ca236d7016ab7c8d4c',	'10.88.66.135',	2,	'2021-04-08 18:07:25',	'2021-04-08 17:53:06'),
(21,	11,	1,	'2021-04-08 17:42:00',	'2021-04-08 17:42:12',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:87.0) Gecko/20100101 Firefox/87.0',	'DST_efd78f90c9eb7349f780eb8384f0b38d',	'10.88.66.135',	1,	'2021-04-08 17:42:12',	'2021-04-08 17:42:02'),
(22,	11,	1,	'2021-04-08 17:42:19',	'2021-04-08 17:43:01',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:87.0) Gecko/20100101 Firefox/87.0',	'DST_b8f28fa9f73ee86bdf75eec0d483389f',	'10.88.66.135',	3,	'2021-04-08 17:42:31',	'2021-04-08 17:42:20'),
(23,	11,	1,	'2021-04-08 17:43:04',	'2021-04-08 18:15:52',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:87.0) Gecko/20100101 Firefox/87.0',	'DST_b103c4b89757a7c76d5263d8c14607cc',	'10.88.66.135',	2,	'2021-04-08 17:53:33',	'2021-04-08 17:45:52'),
(24,	10,	1,	'2021-04-08 20:07:41',	'2021-04-08 20:40:39',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36',	'DST_e5b97bc06c4ec1b65bc7e3baa7772748',	'10.88.66.135',	2,	'2021-04-08 20:24:34',	'2021-04-08 20:10:39'),
(25,	5,	1,	'2021-04-09 09:07:02',	'2021-04-09 09:37:02',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36',	'DST_eff19dbcc1197c5f4e01735cc833eae1',	'10.88.66.135',	2,	'2021-04-09 09:07:02',	'2021-04-09 09:07:02'),
(26,	6,	1,	'2021-04-09 09:25:59',	'2021-04-09 10:24:21',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:87.0) Gecko/20100101 Firefox/87.0',	'DST_22aed8c70e140ac94069b385b4d30459',	'10.88.66.135',	2,	'2021-04-09 10:24:11',	'2021-04-09 09:54:19'),
(27,	5,	1,	'2021-04-09 09:41:20',	'2021-04-09 10:23:37',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36',	'DST_300c60d80b56c48b80dca76b24dec0de',	'10.88.66.135',	2,	'2021-04-09 10:23:27',	'2021-04-09 09:53:36'),
(28,	5,	1,	'2021-04-09 10:38:02',	'2021-04-09 11:18:21',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36',	'DST_d91564321cade5b3c414c3ff5a739e0a',	'10.88.66.135',	2,	'2021-04-09 11:17:21',	'2021-04-09 10:47:45'),
(29,	1,	1,	'2021-04-09 10:39:05',	'2021-04-09 10:39:18',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36',	'DST_5e4a9e58f00589f83c2342dc0775d213',	'10.88.66.135',	1,	'2021-04-09 10:39:15',	'2021-04-09 10:39:05'),
(30,	6,	1,	'2021-04-09 10:39:32',	'2021-04-09 11:13:00',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36',	'DST_d8a617696a3d617b04f80011bc72239a',	'10.88.66.135',	2,	'2021-04-09 11:11:59',	'2021-04-09 10:42:57'),
(31,	5,	1,	'2021-04-09 12:18:57',	'2021-04-09 12:51:21',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36',	'DST_b12249d1b0503869d5d95742422eafb9',	'10.88.66.135',	2,	'2021-04-09 12:50:21',	'2021-04-09 12:20:48'),
(32,	5,	1,	'2021-04-09 13:56:07',	'2021-04-09 14:52:21',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36',	'DST_ffde4322b99bc3d5cda1c2193c56afc2',	'10.88.66.135',	2,	'2021-04-09 14:51:21',	'2021-04-09 14:21:39'),
(33,	5,	1,	'2021-04-09 15:00:52',	'2021-04-09 17:04:21',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36',	'DST_19909a06bade319f4f95daa2cb7430c0',	'10.88.66.135',	2,	'2021-04-09 17:03:21',	'2021-04-09 16:34:20'),
(34,	6,	1,	'2021-04-09 15:03:38',	'2021-04-09 15:33:40',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:87.0) Gecko/20100101 Firefox/87.0',	'DST_69ef9d5daa267f4a765c2e1d5f95b054',	'10.88.66.135',	2,	'2021-04-09 15:33:29',	'2021-04-09 15:03:38'),
(35,	11,	1,	'2021-04-09 16:26:58',	'2021-04-09 17:04:33',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:87.0) Gecko/20100101 Firefox/87.0',	'DST_67b2c3f499042a6fc904ca22643ac128',	'10.88.66.135',	2,	'2021-04-09 17:04:23',	'2021-04-09 16:34:31'),
(36,	11,	1,	'2021-04-09 17:15:30',	'2021-04-09 17:20:57',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:87.0) Gecko/20100101 Firefox/87.0',	'DST_bb9c1e5c1c2ca4454b679e79bc87ed50',	'10.88.66.135',	1,	'2021-04-09 17:20:57',	'2021-04-09 17:15:56'),
(37,	5,	1,	'2021-04-09 17:21:03',	'2021-04-09 18:20:21',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36',	'DST_c43046ee0758481978913727fda81281',	'10.88.66.135',	2,	'2021-04-09 18:19:21',	'2021-04-09 17:49:45'),
(38,	11,	1,	'2021-04-09 17:21:24',	'2021-04-09 17:26:02',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:87.0) Gecko/20100101 Firefox/87.0',	'DST_ce6cef1506253fb95f4ef3dd8b8b2e14',	'10.88.66.135',	3,	'2021-04-09 17:25:49',	'2021-04-09 17:25:09'),
(39,	11,	1,	'2021-04-09 17:26:03',	'2021-04-09 17:27:42',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:87.0) Gecko/20100101 Firefox/87.0',	'DST_3ab4bd07aa2075164abb1ce92e93528b',	'10.88.66.135',	3,	'2021-04-09 17:27:29',	'2021-04-09 17:26:09'),
(40,	11,	1,	'2021-04-09 17:27:44',	'2021-04-09 17:59:18',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:87.0) Gecko/20100101 Firefox/87.0',	'DST_bdd2b4911d272a3a8322e9a94c7ca35e',	'10.88.66.135',	2,	'2021-04-09 17:59:08',	'2021-04-09 17:29:16'),
(41,	6,	1,	'2021-04-12 09:11:00',	'2021-04-12 10:02:31',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:87.0) Gecko/20100101 Firefox/87.0',	'DST_53ec6220d2dfa214edbebd2ffc154df6',	'10.88.66.135',	2,	'2021-04-12 10:02:21',	'2021-04-12 09:32:29'),
(42,	5,	1,	'2021-04-12 09:18:44',	'2021-04-12 09:49:20',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36',	'DST_5b161f952a73d78d209a7ff7d3355e4d',	'10.88.66.135',	2,	'2021-04-12 09:49:13',	'2021-04-12 09:19:20'),
(43,	13,	1,	'2021-04-12 09:49:54',	'2021-04-12 11:56:38',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36',	'DST_74e9e1a4a4d774c75f895fe192af769f',	'10.88.66.135',	2,	'2021-04-12 11:55:38',	'2021-04-12 11:26:04'),
(44,	6,	1,	'2021-04-12 11:21:08',	'2021-04-12 11:55:49',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:87.0) Gecko/20100101 Firefox/87.0',	'DST_123cad136f7bd1cb0b4fe1bd3c7e18cd',	'10.88.66.135',	2,	'2021-04-12 11:55:39',	'2021-04-12 11:25:47'),
(45,	5,	1,	'2021-04-12 11:24:04',	'2021-04-12 11:56:13',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36',	'DST_512aedc8cf6275c123b972812a89dcdf',	'10.88.66.135',	2,	'2021-04-12 11:55:13',	'2021-04-12 11:25:43'),
(46,	6,	1,	'2021-04-12 12:01:45',	'2021-04-12 12:31:57',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:87.0) Gecko/20100101 Firefox/87.0',	'DST_ee21a824722f3c1e17fd6a51c26ca6eb',	'10.88.66.135',	2,	'2021-04-12 12:31:47',	'2021-04-12 12:01:56'),
(47,	5,	1,	'2021-04-12 14:22:50',	'2021-04-12 14:53:13',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36',	'DST_b3407cbbdd2a0b83a49c1e472d65bdc8',	'10.88.66.135',	2,	'2021-04-12 14:52:13',	'2021-04-12 14:22:55'),
(48,	6,	1,	'2021-04-12 14:24:13',	'2021-04-12 15:34:55',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:87.0) Gecko/20100101 Firefox/87.0',	'DST_54839eb735df663bea45807ca38c5be8',	'10.88.66.135',	2,	'2021-04-12 15:34:45',	'2021-04-12 15:04:48'),
(49,	5,	1,	'2021-04-12 15:29:16',	'2021-04-12 16:02:13',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36',	'DST_f7009b3c7a1086f41a3e5e77877882ae',	'10.88.66.135',	2,	'2021-04-12 16:01:13',	'2021-04-12 15:31:49'),
(50,	13,	1,	'2021-04-12 15:43:10',	'2021-04-12 16:34:25',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36',	'DST_4ba8ff6d5344addbba73a0353e9c2af9',	'10.88.66.135',	2,	'2021-04-12 16:05:56',	'2021-04-12 16:04:25'),
(51,	1,	1,	'2021-04-12 15:58:14',	'2021-04-12 16:44:51',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36',	'DST_82138ba7043430c317c3f7e505a39b6b',	'10.88.66.135',	2,	'2021-04-12 16:43:51',	'2021-04-12 16:13:58'),
(52,	5,	1,	'2021-04-12 16:08:08',	'2021-04-12 17:00:48',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36',	'DST_0a1a2141f0a3021f75b4ccb023b51a6c',	'10.88.66.135',	2,	'2021-04-12 17:00:38',	'2021-04-12 16:30:38'),
(53,	6,	1,	'2021-04-12 16:10:35',	'2021-04-12 16:53:55',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:87.0) Gecko/20100101 Firefox/87.0',	'DST_b0878a9f64bfa6c4aa3901566036cc79',	'10.88.66.135',	2,	'2021-04-12 16:53:45',	'2021-04-12 16:23:53'),
(54,	13,	1,	'2021-04-13 09:15:53',	'2021-04-13 09:28:09',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36',	'DST_852ecd622bedc5bbb8a31d636a13abf4',	'10.88.66.135',	1,	'2021-04-13 09:27:57',	'2021-04-13 09:28:00'),
(55,	5,	1,	'2021-04-13 09:16:31',	'2021-04-13 09:47:09',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36',	'DST_85a00fef5096ece3f61aac1c4f8ca38a',	'10.88.66.135',	2,	'2021-04-13 09:46:09',	'2021-04-13 09:16:33'),
(56,	14,	1,	'2021-04-13 09:22:40',	'2021-04-13 09:27:18',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:87.0) Gecko/20100101 Firefox/87.0',	'DST_de77f592defc8ad543675e11f83fb578',	'10.88.66.135',	3,	'2021-04-13 09:27:00',	'2021-04-13 09:26:20'),
(57,	14,	1,	'2021-04-13 09:27:24',	'2021-04-13 09:28:12',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:87.0) Gecko/20100101 Firefox/87.0',	'DST_dcc497d8edcfbfe376ee0e5523b698a1',	'10.88.66.135',	1,	'2021-04-13 09:28:03',	'2021-04-13 09:27:32'),
(58,	13,	1,	'2021-04-13 09:28:18',	'2021-04-13 09:29:23',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:87.0) Gecko/20100101 Firefox/87.0',	'DST_4f8b45ea98cc00f4dbb135248ab478c8',	'10.88.66.135',	1,	'2021-04-13 09:29:14',	'2021-04-13 09:28:24'),
(59,	13,	1,	'2021-04-13 09:29:37',	'2021-04-13 09:30:11',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36',	'DST_d2c3dd6d887acaffb379be55908592e7',	'10.88.66.135',	1,	'2021-04-13 09:30:00',	'2021-04-13 09:30:04'),
(60,	13,	1,	'2021-04-13 09:30:20',	'2021-04-13 09:51:08',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:87.0) Gecko/20100101 Firefox/87.0',	'DST_e9ebdeb140a4f29bbdc23a8b0eca9930',	'10.88.66.135',	3,	'2021-04-13 09:49:54',	'2021-04-13 09:50:01'),
(61,	14,	1,	'2021-04-13 09:31:12',	'2021-04-13 09:32:21',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36',	'DST_d3132f29250d335443088aefeaa0c2b3',	'10.88.66.135',	3,	'2021-04-13 09:31:55',	'2021-04-13 09:31:14'),
(62,	14,	1,	'2021-04-13 09:32:29',	'2021-04-13 09:41:26',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36',	'DST_54cd7f809fb07f2317f43f9d63dcdb73',	'10.88.66.135',	1,	'2021-04-13 09:41:20',	'2021-04-13 09:32:29'),
(63,	13,	1,	'2021-04-13 09:51:09',	'2021-04-13 10:35:34',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36',	'DST_8bb09baf377d32b54d18bceb7fbdb958',	'10.88.66.135',	2,	'2021-04-13 10:08:05',	'2021-04-13 10:05:34'),
(64,	5,	1,	'2021-04-13 10:02:52',	'2021-04-13 10:33:23',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36',	'DST_83dd0592463e8f693b317f4452d27e38',	'10.88.66.135',	2,	'2021-04-13 10:33:13',	'2021-04-13 10:03:22'),
(65,	6,	1,	'2021-04-13 14:44:56',	'2021-04-13 15:15:18',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:87.0) Gecko/20100101 Firefox/87.0',	'DST_0b1ec3480e90c045ff700769f19762de',	'10.88.66.135',	2,	'2021-04-13 15:15:08',	'2021-04-13 14:45:16'),
(66,	5,	1,	'2021-04-13 15:48:19',	'2021-04-13 16:42:09',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36',	'DST_f5420f2e9b6c9de5570985f59664f6d9',	'10.88.66.135',	2,	'2021-04-13 16:41:09',	'2021-04-13 16:11:37'),
(67,	7,	1,	'2021-04-13 15:59:36',	'2021-04-13 16:33:15',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36',	'DST_79a9518d44c2ce5f92067b2e60135e4e',	'10.88.66.135',	2,	'2021-04-13 16:04:26',	'2021-04-13 16:03:15'),
(68,	5,	1,	'2021-04-13 17:09:23',	'2021-04-13 17:40:48',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36',	'DST_ace340eee1003ce90947164fc96baebd',	'10.88.66.135',	2,	'2021-04-13 17:40:38',	'2021-04-13 17:10:38');

DROP TABLE IF EXISTS `connection_error_audit_trail`;
CREATE TABLE `connection_error_audit_trail` (
                                                `id` int(11) NOT NULL AUTO_INCREMENT,
                                                `user_id` int(11) DEFAULT NULL,
                                                `created_at` datetime NOT NULL,
                                                `device` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                                                `ip` varchar(55) COLLATE utf8mb4_unicode_ci NOT NULL,
                                                `error` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                                                `username` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                                PRIMARY KEY (`id`),
                                                KEY `IDX_BAFB082AA76ED395` (`user_id`),
                                                CONSTRAINT `FK_BAFB082AA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `connection_error_audit_trail` (`id`, `user_id`, `created_at`, `device`, `ip`, `error`, `username`) VALUES
(1,	NULL,	'2021-04-08 10:24:03',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:87.0) Gecko/20100101 Firefox/87.0',	'10.88.66.135',	'security.connexion.err.csrf_token',	'rdurville@clinfile.com'),
(2,	6,	'2021-04-08 10:24:08',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:87.0) Gecko/20100101 Firefox/87.0',	'10.88.66.135',	'security.connexion.err.bad_credentials',	NULL),
(3,	6,	'2021-04-08 14:06:07',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36',	'10.88.66.135',	'',	NULL),
(4,	6,	'2021-04-08 14:35:28',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36',	'10.88.66.135',	'security.connexion.err.bad_credentials',	NULL),
(5,	6,	'2021-04-08 14:35:31',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36',	'10.88.66.135',	'security.connexion.err.bad_credentials',	NULL),
(6,	6,	'2021-04-08 14:54:57',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36',	'10.88.66.135',	'',	NULL),
(7,	12,	'2021-04-08 14:55:26',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36',	'10.88.66.135',	'security.connexion.err.bad_credentials',	NULL),
(8,	NULL,	'2021-04-08 15:43:12',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36',	'10.88.66.135',	'security.connexion.err.bad_credentials',	'khadar.yonis@clinfile.com'),
(9,	NULL,	'2021-04-08 15:43:20',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36',	'10.88.66.135',	'security.connexion.err.bad_credentials',	'khadar.yonis@clinfile.com'),
(10,	NULL,	'2021-04-08 15:43:31',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36',	'10.88.66.135',	'security.connexion.err.bad_credentials',	'khadar.yonis@clinfile.com'),
(11,	NULL,	'2021-04-08 15:43:44',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36',	'10.88.66.135',	'security.connexion.err.bad_credentials',	'khadar.yonis@clinfile.com'),
(12,	NULL,	'2021-04-08 15:43:45',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36',	'10.88.66.135',	'security.connexion.err.bad_credentials',	'khadar.yonis@clinfile.com'),
(13,	NULL,	'2021-04-08 15:43:48',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36',	'10.88.66.135',	'security.connexion.err.bad_credentials',	'khadar.yonis@clinfile.com'),
(14,	6,	'2021-04-08 15:51:01',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:87.0) Gecko/20100101 Firefox/87.0',	'10.88.66.135',	'security.connexion.err.bad_credentials',	NULL),
(15,	10,	'2021-04-08 17:15:37',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36',	'10.88.66.135',	'security.connexion.err.bad_credentials',	NULL),
(16,	NULL,	'2021-04-12 09:10:52',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:87.0) Gecko/20100101 Firefox/87.0',	'10.88.66.135',	'security.connexion.err.bad_credentials',	'rdurville'),
(17,	6,	'2021-04-12 16:10:29',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:87.0) Gecko/20100101 Firefox/87.0',	'10.88.66.135',	'security.connexion.err.bad_credentials',	NULL),
(18,	14,	'2021-04-13 09:31:05',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36',	'10.88.66.135',	'security.connexion.err.bad_credentials',	NULL),
(19,	14,	'2021-04-13 09:32:04',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36',	'10.88.66.135',	'',	NULL),
(20,	13,	'2021-04-13 09:49:50',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36',	'10.88.66.135',	'',	NULL),
(21,	6,	'2021-04-13 14:45:01',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:87.0) Gecko/20100101 Firefox/87.0',	'10.88.66.135',	'',	NULL),
(22,	NULL,	'2021-04-13 15:59:31',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36',	'10.88.66.135',	'security.connexion.err.bad_credentials',	'clin_adm@orange.fr');

DROP TABLE IF EXISTS `contact`;
CREATE TABLE `contact` (
                           `id` int(11) NOT NULL AUTO_INCREMENT,
                           `object_id` int(11) NOT NULL,
                           `project_id` int(11) NOT NULL,
                           `type_id` int(11) NOT NULL,
                           `phase_id` int(11) NOT NULL,
                           `type_recipient_id` int(11) NOT NULL,
                           `intervenant_id` int(11) NOT NULL,
                           `detail` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                           `cc` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                           `date` datetime NOT NULL,
                           `hour` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL,
                           `completed` tinyint(1) DEFAULT NULL,
                           PRIMARY KEY (`id`),
                           KEY `IDX_4C62E638232D562B` (`object_id`),
                           KEY `IDX_4C62E638166D1F9C` (`project_id`),
                           KEY `IDX_4C62E638C54C8C93` (`type_id`),
                           KEY `IDX_4C62E63899091188` (`phase_id`),
                           KEY `IDX_4C62E6386E6C357D` (`type_recipient_id`),
                           KEY `IDX_4C62E638AB9A1716` (`intervenant_id`),
                           CONSTRAINT `FK_4C62E638166D1F9C` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`),
                           CONSTRAINT `FK_4C62E638232D562B` FOREIGN KEY (`object_id`) REFERENCES `dl_contact_object` (`id`),
                           CONSTRAINT `FK_4C62E6386E6C357D` FOREIGN KEY (`type_recipient_id`) REFERENCES `dl_contact_type_recipient` (`id`),
                           CONSTRAINT `FK_4C62E63899091188` FOREIGN KEY (`phase_id`) REFERENCES `dl_contact_phase` (`id`),
                           CONSTRAINT `FK_4C62E638AB9A1716` FOREIGN KEY (`intervenant_id`) REFERENCES `user` (`id`),
                           CONSTRAINT `FK_4C62E638C54C8C93` FOREIGN KEY (`type_id`) REFERENCES `dl_contact_type` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `contact_interlocutor`;
CREATE TABLE `contact_interlocutor` (
                                        `contact_id` int(11) NOT NULL,
                                        `interlocutor_id` int(11) NOT NULL,
                                        PRIMARY KEY (`contact_id`,`interlocutor_id`),
                                        KEY `IDX_DF4909C1E7A1254A` (`contact_id`),
                                        KEY `IDX_DF4909C1B3F944DB` (`interlocutor_id`),
                                        CONSTRAINT `FK_DF4909C1B3F944DB` FOREIGN KEY (`interlocutor_id`) REFERENCES `interlocutor` (`id`) ON DELETE CASCADE,
                                        CONSTRAINT `FK_DF4909C1E7A1254A` FOREIGN KEY (`contact_id`) REFERENCES `contact` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `contact_user`;
CREATE TABLE `contact_user` (
                                `contact_id` int(11) NOT NULL,
                                `user_id` int(11) NOT NULL,
                                PRIMARY KEY (`contact_id`,`user_id`),
                                KEY `IDX_A56C54B6E7A1254A` (`contact_id`),
                                KEY `IDX_A56C54B6A76ED395` (`user_id`),
                                CONSTRAINT `FK_A56C54B6A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE,
                                CONSTRAINT `FK_A56C54B6E7A1254A` FOREIGN KEY (`contact_id`) REFERENCES `contact` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `courbe_setting`;
CREATE TABLE `courbe_setting` (
                                  `id` int(11) NOT NULL AUTO_INCREMENT,
                                  `project_id` int(11) NOT NULL,
                                  `datestrat` date DEFAULT NULL,
                                  `unit` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                                  PRIMARY KEY (`id`),
                                  UNIQUE KEY `UNIQ_5FDB57A2166D1F9C` (`project_id`),
                                  CONSTRAINT `FK_5FDB57A2166D1F9C` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `courbe_setting_audit_trail`;
CREATE TABLE `courbe_setting_audit_trail` (
                                              `id` int(11) NOT NULL AUTO_INCREMENT,
                                              `entity_id` int(11) NOT NULL,
                                              `user_id` int(11) DEFAULT NULL,
                                              `date` datetime NOT NULL,
                                              `reason` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                              `modif_type` smallint(6) NOT NULL COMMENT '1=insert, 2=update, 3=delete',
                                              `details` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:json)',
                                              PRIMARY KEY (`id`),
                                              KEY `IDX_24CC4BBF81257D5D` (`entity_id`),
                                              KEY `IDX_24CC4BBFA76ED395` (`user_id`),
                                              CONSTRAINT `FK_24CC4BBF81257D5D` FOREIGN KEY (`entity_id`) REFERENCES `courbe_setting` (`id`),
                                              CONSTRAINT `FK_24CC4BBFA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `date`;
CREATE TABLE `date` (
                        `id` int(11) NOT NULL AUTO_INCREMENT,
                        `project_id` int(11) NOT NULL,
                        `submission_at` datetime DEFAULT NULL,
                        `feasibility_committee_at` datetime DEFAULT NULL,
                        `review_committee_at` datetime DEFAULT NULL,
                        `registration_at` datetime DEFAULT NULL,
                        `subscription_started_at` datetime DEFAULT NULL,
                        `subscription_ended_at` datetime DEFAULT NULL,
                        `certification_at` datetime DEFAULT NULL,
                        `kiff_of_meeting_at` datetime DEFAULT NULL,
                        `mep_forecast_at` datetime DEFAULT NULL,
                        `mep_at` datetime DEFAULT NULL,
                        `forecast_inclusion_started_at` datetime DEFAULT NULL,
                        `first_consent_at` datetime DEFAULT NULL,
                        `inclusion_patient_started_at` datetime DEFAULT NULL,
                        `forecast_inclusion_ended_at` datetime DEFAULT NULL,
                        `inclusion_patient_ended_at` datetime DEFAULT NULL,
                        `study_declaration_ended_at` datetime DEFAULT NULL,
                        `validation_commmittee_review_ended_at` datetime DEFAULT NULL,
                        `amendments_befor_first_inclusion` tinyint(1) DEFAULT NULL,
                        `number_expected_patients` int(11) DEFAULT NULL,
                        `number_screened_patients` int(11) DEFAULT NULL,
                        `number_patients_included` int(11) DEFAULT NULL,
                        `number_randomized_patients` int(11) DEFAULT NULL,
                        `expected_duration_inclusion_at` datetime DEFAULT NULL,
                        `expected_duration_follow_up_after_inclusion_at` datetime DEFAULT NULL,
                        `expected_lplvat` datetime DEFAULT NULL,
                        `actual_lplvat` datetime DEFAULT NULL,
                        `expected_report_analysed_at` datetime DEFAULT NULL,
                        `actual_report_analysedt_at` datetime DEFAULT NULL,
                        `final_report_analysed_at` datetime DEFAULT NULL,
                        `final_expected_lplvat` datetime DEFAULT NULL,
                        `final_actual_lplvat` datetime DEFAULT NULL,
                        `final_expected_report_at` datetime DEFAULT NULL,
                        `final_actual_report_at` datetime DEFAULT NULL,
                        `final_actual_report_clinical_at` datetime DEFAULT NULL,
                        `depot_clinical_trials_at` datetime DEFAULT NULL,
                        `depot_eudra_ct_at` datetime DEFAULT NULL,
                        PRIMARY KEY (`id`),
                        KEY `IDX_AA9E377A166D1F9C` (`project_id`),
                        CONSTRAINT `FK_AA9E377A166D1F9C` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `date` (`id`, `project_id`, `submission_at`, `feasibility_committee_at`, `review_committee_at`, `registration_at`, `subscription_started_at`, `subscription_ended_at`, `certification_at`, `kiff_of_meeting_at`, `mep_forecast_at`, `mep_at`, `forecast_inclusion_started_at`, `first_consent_at`, `inclusion_patient_started_at`, `forecast_inclusion_ended_at`, `inclusion_patient_ended_at`, `study_declaration_ended_at`, `validation_commmittee_review_ended_at`, `amendments_befor_first_inclusion`, `number_expected_patients`, `number_screened_patients`, `number_patients_included`, `number_randomized_patients`, `expected_duration_inclusion_at`, `expected_duration_follow_up_after_inclusion_at`, `expected_lplvat`, `actual_lplvat`, `expected_report_analysed_at`, `actual_report_analysedt_at`, `final_report_analysed_at`, `final_expected_lplvat`, `final_actual_lplvat`, `final_expected_report_at`, `final_actual_report_at`, `final_actual_report_clinical_at`, `depot_clinical_trials_at`, `depot_eudra_ct_at`) VALUES
(1,	1,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL),
(2,	2,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL),
(3,	3,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL);

DROP TABLE IF EXISTS `date_audit_trail`;
CREATE TABLE `date_audit_trail` (
                                    `id` int(11) NOT NULL AUTO_INCREMENT,
                                    `entity_id` int(11) NOT NULL,
                                    `user_id` int(11) DEFAULT NULL,
                                    `date` datetime NOT NULL,
                                    `reason` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                    `modif_type` smallint(6) NOT NULL COMMENT '1=insert, 2=update, 3=delete',
                                    `details` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:json)',
                                    PRIMARY KEY (`id`),
                                    KEY `IDX_8A5AD4A481257D5D` (`entity_id`),
                                    KEY `IDX_8A5AD4A4A76ED395` (`user_id`),
                                    CONSTRAINT `FK_8A5AD4A481257D5D` FOREIGN KEY (`entity_id`) REFERENCES `date` (`id`),
                                    CONSTRAINT `FK_8A5AD4A4A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `date_audit_trail` (`id`, `entity_id`, `user_id`, `date`, `reason`, `modif_type`, `details`) VALUES
(1,	1,	6,	'2021-04-07 19:51:20',	NULL,	1,	'{\"project\":\"Projet test 01 (1)\"}'),
(2,	2,	6,	'2021-04-07 20:15:49',	NULL,	1,	'{\"project\":\"FFF (2)\"}'),
(3,	3,	5,	'2021-04-08 09:32:19',	NULL,	1,	'{\"project\":\"Projet Cadmus (3)\"}');

DROP TABLE IF EXISTS `delegation`;
CREATE TABLE `delegation` (
                              `id` int(11) NOT NULL AUTO_INCREMENT,
                              `rep_sponsor` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                              `regulatory` tinyint(1) NOT NULL,
                              `manitoring` tinyint(1) NOT NULL,
                              `pharmacovigilance` tinyint(1) NOT NULL,
                              `dsur` tinyint(1) NOT NULL,
                              `susar` tinyint(1) NOT NULL,
                              PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `delegation` (`id`, `rep_sponsor`, `regulatory`, `manitoring`, `pharmacovigilance`, `dsur`, `susar`) VALUES
(1,	NULL,	0,	0,	0,	0,	0),
(2,	NULL,	0,	0,	0,	0,	0),
(3,	NULL,	0,	0,	0,	0,	0);

DROP TABLE IF EXISTS `delegation_audit_trail`;
CREATE TABLE `delegation_audit_trail` (
                                          `id` int(11) NOT NULL AUTO_INCREMENT,
                                          `entity_id` int(11) NOT NULL,
                                          `user_id` int(11) DEFAULT NULL,
                                          `date` datetime NOT NULL,
                                          `reason` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                          `modif_type` smallint(6) NOT NULL COMMENT '1=insert, 2=update, 3=delete',
                                          `details` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:json)',
                                          PRIMARY KEY (`id`),
                                          KEY `IDX_82CD2D2B81257D5D` (`entity_id`),
                                          KEY `IDX_82CD2D2BA76ED395` (`user_id`),
                                          CONSTRAINT `FK_82CD2D2B81257D5D` FOREIGN KEY (`entity_id`) REFERENCES `delegation` (`id`),
                                          CONSTRAINT `FK_82CD2D2BA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `delegation_audit_trail` (`id`, `entity_id`, `user_id`, `date`, `reason`, `modif_type`, `details`) VALUES
(1,	1,	6,	'2021-04-07 19:51:19',	NULL,	1,	'{\"regulatory\":\"\",\"manitoring\":\"\",\"pharmacovigilance\":\"\",\"dsur\":\"\",\"susar\":\"\"}'),
(2,	2,	6,	'2021-04-07 20:15:49',	NULL,	1,	'{\"regulatory\":\"\",\"manitoring\":\"\",\"pharmacovigilance\":\"\",\"dsur\":\"\",\"susar\":\"\"}'),
(3,	3,	5,	'2021-04-08 09:32:18',	NULL,	1,	'{\"regulatory\":\"\",\"manitoring\":\"\",\"pharmacovigilance\":\"\",\"dsur\":\"\",\"susar\":\"\"}');

DROP TABLE IF EXISTS `dl_call_project`;
CREATE TABLE `dl_call_project` (
                                   `id` int(11) NOT NULL AUTO_INCREMENT,
                                   `label` varchar(55) COLLATE utf8mb4_unicode_ci NOT NULL,
                                   `delete_at` datetime DEFAULT NULL,
                                   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `dl_call_project` (`id`, `label`, `delete_at`) VALUES
(1,	'AcSé',	NULL),
(2,	'Affectation MERRI',	NULL),
(3,	'Appel à projet générique',	NULL),
(4,	'Cohortes',	NULL),
(5,	'ERA-NET FLAG-ERA',	NULL),
(6,	'ERA-NET TRANSCAN',	NULL),
(7,	'ETA-PerMed',	NULL),
(8,	'H2020',	NULL),
(9,	'Horizon Europe',	NULL),
(10,	'PAIR',	NULL),
(11,	'PHRC-K',	NULL),
(12,	'PLBIO',	NULL),
(13,	'PRME',	NULL),
(14,	'PRT-K',	NULL),
(15,	'RHU',	NULL),
(16,	'SHS-E-SP',	NULL),
(17,	'SIRIC',	NULL),
(18,	'Autre',	NULL);

DROP TABLE IF EXISTS `dl_center_status`;
CREATE TABLE `dl_center_status` (
                                    `id` int(11) NOT NULL AUTO_INCREMENT,
                                    `label` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                                    `type` smallint(6) NOT NULL,
                                    `position` int(11) NOT NULL,
                                    `deleted_at` datetime DEFAULT NULL,
                                    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `dl_center_status` (`id`, `label`, `type`, `position`, `deleted_at`) VALUES
(1,	'En Présélection',	1,	10,	NULL),
(2,	'Non-sélectionné',	1,	20,	NULL),
(3,	'Sélectionné',	2,	30,	NULL),
(4,	'Retrait de participation',	3,	40,	NULL),
(5,	'Avis favorable des autorités',	3,	50,	NULL),
(6,	'Mise en place effectuée',	3,	60,	NULL),
(7,	'Activé',	3,	70,	NULL),
(8,	'Centre actif',	3,	80,	NULL),
(9,	'Centre en suivi',	3,	90,	NULL),
(10,	'A clôturer',	3,	100,	NULL),
(11,	'Clôturé',	4,	110,	NULL);

DROP TABLE IF EXISTS `dl_civility`;
CREATE TABLE `dl_civility` (
                               `id` int(11) NOT NULL AUTO_INCREMENT,
                               `label` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
                               `deleted_at` datetime DEFAULT NULL,
                               PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `dl_civility` (`id`, `label`, `deleted_at`) VALUES
(1,	'Ms.',	NULL),
(2,	'Mr.',	NULL),
(3,	'Dr.',	NULL),
(4,	'Pr.',	NULL);

DROP TABLE IF EXISTS `dl_communication_type`;
CREATE TABLE `dl_communication_type` (
                                         `id` int(11) NOT NULL AUTO_INCREMENT,
                                         `label` varchar(55) COLLATE utf8mb4_unicode_ci NOT NULL,
                                         `delete_at` datetime DEFAULT NULL,
                                         PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `dl_communication_type` (`id`, `label`, `delete_at`) VALUES
(1,	'Publication',	NULL),
(2,	'Congrès',	NULL);

DROP TABLE IF EXISTS `dl_congress`;
CREATE TABLE `dl_congress` (
                               `id` int(11) NOT NULL AUTO_INCREMENT,
                               `label` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                               `delete_at` datetime DEFAULT NULL,
                               PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `dl_congress` (`id`, `label`, `delete_at`) VALUES
(1,	'AACR',	NULL),
(2,	'ABC',	NULL),
(3,	'ASCO',	NULL),
(4,	'ASCO GI',	NULL),
(5,	'ASCO GU',	NULL),
(6,	'ASH',	NULL),
(7,	'ASTRO',	NULL),
(8,	'Autres',	NULL),
(9,	'Assises de Génétique Humaine et Médicale',	NULL),
(10,	'BRCA Symposium',	NULL),
(11,	'CPLF',	NULL),
(12,	'CTOS',	NULL),
(13,	'Congrès du collège des humanités médicales (Colhum)',	NULL),
(14,	'EBCC',	NULL),
(15,	'ECCO',	NULL),
(16,	'EHTG',	NULL),
(17,	'EMSOS',	NULL),
(18,	'EPICLIN',	NULL),
(19,	'EQALM',	NULL),
(20,	'ESGO',	NULL),
(21,	'ESMO',	NULL),
(22,	'ESMO IO',	NULL),
(23,	'ESMO GI',	NULL),
(24,	'ESTRO',	NULL),
(25,	'EUROMAR',	NULL),
(26,	'IAGG',	NULL),
(27,	'ICCTF',	NULL),
(28,	'IFCC-EFLM ',	NULL),
(29,	'ISMRC',	NULL),
(30,	'MASCC',	NULL),
(31,	'JFHOD',	NULL),
(32,	'JDP',	NULL),
(33,	'SABCS',	NULL),
(34,	'SFH',	NULL),
(35,	'SFSPM',	NULL),
(36,	'SFRO',	NULL),
(37,	'SIOG',	NULL),
(38,	'SIOP',	NULL),
(39,	'SNMMI',	NULL),
(40,	'SOFOG',	NULL),
(41,	'UICC',	NULL),
(42,	'WCLC',	NULL);

DROP TABLE IF EXISTS `dl_contact_object`;
CREATE TABLE `dl_contact_object` (
                                     `id` int(11) NOT NULL AUTO_INCREMENT,
                                     `label` varchar(55) COLLATE utf8mb4_unicode_ci NOT NULL,
                                     `deleted_at` datetime DEFAULT NULL,
                                     PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `dl_contact_object` (`id`, `label`, `deleted_at`) VALUES
(1,	'Objet1',	NULL),
(2,	'Objet2',	NULL),
(3,	'Objet3',	NULL),
(4,	'Objet4',	NULL);

DROP TABLE IF EXISTS `dl_contact_phase`;
CREATE TABLE `dl_contact_phase` (
                                    `id` int(11) NOT NULL AUTO_INCREMENT,
                                    `label` varchar(55) COLLATE utf8mb4_unicode_ci NOT NULL,
                                    `deleted_at` datetime DEFAULT NULL,
                                    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `dl_contact_phase` (`id`, `label`, `deleted_at`) VALUES
(1,	'Pré-selection',	NULL),
(2,	'Suivi',	NULL);

DROP TABLE IF EXISTS `dl_contact_type`;
CREATE TABLE `dl_contact_type` (
                                   `id` int(11) NOT NULL AUTO_INCREMENT,
                                   `label` varchar(55) COLLATE utf8mb4_unicode_ci NOT NULL,
                                   `deleted_at` datetime DEFAULT NULL,
                                   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `dl_contact_type` (`id`, `label`, `deleted_at`) VALUES
(1,	'Téléphone',	NULL),
(2,	'Email',	NULL),
(3,	'Fax',	NULL),
(4,	'Courrier',	NULL);

DROP TABLE IF EXISTS `dl_contact_type_recipient`;
CREATE TABLE `dl_contact_type_recipient` (
                                             `id` int(11) NOT NULL AUTO_INCREMENT,
                                             `label` varchar(55) COLLATE utf8mb4_unicode_ci NOT NULL,
                                             `deleted_at` datetime DEFAULT NULL,
                                             PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `dl_contact_type_recipient` (`id`, `label`, `deleted_at`) VALUES
(1,	'Interlocuteur(s)',	NULL),
(2,	'Intervenant(s)',	NULL);

DROP TABLE IF EXISTS `dl_cooperator`;
CREATE TABLE `dl_cooperator` (
                                 `id` int(11) NOT NULL AUTO_INCREMENT,
                                 `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                                 `delete_at` datetime DEFAULT NULL,
                                 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `dl_cooperator` (`id`, `title`, `delete_at`) VALUES
(1,	'BREAST',	NULL),
(2,	'EORTC',	NULL),
(3,	'FFCD',	NULL),
(4,	'GEFPICS',	NULL),
(5,	'Génétique et Cancer',	NULL),
(6,	'GEP',	NULL),
(7,	'GERCOR',	NULL),
(8,	'GERICO',	NULL),
(9,	'GETUG',	NULL),
(10,	'Groupe Belge',	NULL),
(11,	'GRT',	NULL),
(12,	'GSF',	NULL),
(13,	'GYNECO',	NULL),
(14,	'IFCT',	NULL),
(15,	'Médecine Personnalisée',	NULL),
(16,	'NCCTG',	NULL),
(17,	'NCIC',	NULL),
(18,	'ORL',	NULL),
(19,	'PETACC',	NULL),
(20,	'POUMON',	NULL),
(21,	'PRODIGE',	NULL),
(22,	'SARCOME',	NULL),
(23,	'SFCE',	NULL),
(24,	'UCBG',	NULL),
(25,	'UCGI',	NULL),
(26,	'BIG',	NULL),
(27,	'CANTO',	NULL),
(28,	'UNITRAD',	NULL),
(29,	'SFRO',	NULL),
(30,	'ICORG',	NULL),
(31,	'GRECCAR',	NULL),
(32,	'SAKK',	NULL),
(33,	'LLC',	NULL),
(34,	'IFM',	NULL),
(35,	'NETSARC',	NULL),
(36,	'ARCAGY GINECO',	NULL),
(37,	'TUTHYREF',	NULL),
(38,	'GCC',	NULL),
(39,	'SFD',	NULL),
(40,	'LYSARC',	NULL),
(41,	'ANOCEF',	NULL),
(42,	'UNICANCER-AFSOS Soins de Support',	NULL),
(43,	'AFU',	NULL),
(44,	'DIALOG',	NULL),
(45,	'SLO',	NULL),
(46,	'German Breast Group',	NULL),
(47,	'IBCSG',	NULL),
(48,	'TROG Cancer Researchv',	NULL),
(49,	'EORTC - BCG',	NULL),
(50,	'ABCSG',	NULL),
(51,	'GAICO',	NULL),
(52,	'Success',	NULL),
(53,	'IOSG',	NULL),
(54,	'BOOG',	NULL),
(55,	'HeCOGG',	NULL),
(56,	'Fondazione Michelangelo',	NULL),
(57,	'Velindre Hospital',	NULL),
(58,	'Latin American Cooperative Oncology Group',	NULL),
(59,	'I . T . M . O . Group(Italian Trials in Medical Oncology)',	NULL),
(60,	'ANZBCTG',	NULL),
(61,	'SOLTI',	NULL),
(62,	'The Icelandic Breast Cancer Group',	NULL),
(63,	'Hong Kong Breast Oncology Group',	NULL),
(64,	'SABO',	NULL),
(65,	'GOCCHI',	NULL),
(66,	'TCOG',	NULL),
(67,	'BGICS',	NULL),
(68,	'GEICAM',	NULL),
(69,	'NCIC CTG',	NULL),
(70,	'Hellenic Oncology Research Group',	NULL),
(71,	'CTRG',	NULL),
(72,	'GECOPERU',	NULL),
(73,	'GORTEC',	NULL),
(74,	'GETTEC',	NULL),
(75,	'REFCOR',	NULL),
(76,	'INTERSARC',	NULL);

DROP TABLE IF EXISTS `dl_country`;
CREATE TABLE `dl_country` (
                              `id` int(11) NOT NULL AUTO_INCREMENT,
                              `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                              `name_english` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                              `code` varchar(2) COLLATE utf8mb4_unicode_ci NOT NULL,
                              `deleted_at` datetime DEFAULT NULL,
                              PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `dl_country` (`id`, `name`, `name_english`, `code`, `deleted_at`) VALUES
(1,	'France',	'La France',	'FR',	NULL),
(2,	'Belgium',	'La Belgique',	'BE',	NULL),
(3,	'Canada',	'Le Cananda',	'CA',	NULL),
(4,	'Estonia',	'L\'Estonie',	'EE',	NULL),
(5,	'Germany',	'L\'Allemagne',	'DE',	NULL),
(6,	'Ireland',	'L\'Irlande',	'IE',	NULL),
(7,	'Israel',	'Israël',	'IL',	NULL),
(8,	'Italy',	'L\'Italie',	'IT',	NULL),
(9,	'Monaco',	'Monaco',	'MC',	NULL),
(10,	'Portugal',	'Le Portugal',	'PT',	NULL),
(11,	'Romania',	'La Roumanie',	'RO',	NULL),
(12,	'Slovakia',	'La Slovaquie',	'SK',	NULL),
(13,	'Spain',	'L\'Espagne',	'ES',	NULL),
(14,	'Sweden',	'La Suède',	'SE',	NULL),
(15,	'Switzerland',	'La Suisse',	'CH',	NULL),
(16,	'United Kingdom of Great Britain and Northern Ireland (the)',	'Royaume-Uni de Grande-Bretagne et d\'Irlande du Nord (le)',	'UK',	NULL),
(17,	'United States of America (the)',	'Les États-Unis d\'Amérique ',	'US',	NULL);

DROP TABLE IF EXISTS `dl_country_department`;
CREATE TABLE `dl_country_department` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT NULL,
  `country_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `position` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_36328999727ACA70` (`parent_id`),
  KEY `IDX_36328999F92F3E70` (`country_id`),
  CONSTRAINT `FK_36328999727ACA70` FOREIGN KEY (`parent_id`) REFERENCES `dl_country_department` (`id`),
  CONSTRAINT `FK_36328999F92F3E70` FOREIGN KEY (`country_id`) REFERENCES `dl_country` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `dl_country_department` (`id`, `parent_id`, `country_id`, `name`, `code`, `deleted_at`, `position`) VALUES
(1,	NULL,	1,	'CLARA',	NULL,	NULL,	10),
(2,	1,	NULL,	'Ain',	'01',	NULL,	15),
(3,	1,	NULL,	'Allier',	'03',	NULL,	20),
(4,	1,	NULL,	'Ardèche',	'07',	NULL,	25),
(5,	1,	NULL,	'Cantal',	'15',	NULL,	30),
(6,	1,	NULL,	'Drome',	'26',	NULL,	35),
(7,	1,	NULL,	'Isère',	'38',	NULL,	40),
(8,	1,	NULL,	'Loire',	'42',	NULL,	45),
(9,	1,	NULL,	'Puy-de-Dôme',	'63',	NULL,	50),
(10,	1,	NULL,	'Rhône',	'69',	NULL,	55),
(11,	1,	NULL,	'Savoie',	'73',	NULL,	60),
(12,	1,	NULL,	'Haute Savoie',	'74',	NULL,	65),
(13,	NULL,	1,	'Grand Est',	NULL,	NULL,	70),
(14,	13,	NULL,	'Ardennes',	'08',	NULL,	75),
(15,	13,	NULL,	'Aube',	'10',	NULL,	80),
(16,	13,	NULL,	'Côte d\'Or',	'21',	NULL,	85),
(17,	13,	NULL,	'Doubs',	'25',	NULL,	90),
(18,	13,	NULL,	'Marne',	'51',	NULL,	95),
(19,	13,	NULL,	'Meurthe-et-Moselle',	'54',	NULL,	100),
(20,	13,	NULL,	'Moselle',	'57',	NULL,	105),
(21,	13,	NULL,	'Bas Rhin',	'67',	NULL,	110),
(22,	13,	NULL,	'Haut-Rhin',	'68',	NULL,	115),
(23,	13,	NULL,	'Haute Saône',	'70',	NULL,	120),
(24,	13,	NULL,	'Saône-et-Loire',	'71',	NULL,	125),
(25,	13,	NULL,	'Vosges',	'88',	NULL,	130),
(26,	13,	NULL,	'Yonne',	'89',	NULL,	135),
(27,	13,	NULL,	'Belfort',	'90',	NULL,	140),
(28,	NULL,	1,	'Grand Ouest',	NULL,	NULL,	145),
(29,	28,	NULL,	'Charente',	'16',	NULL,	150),
(30,	28,	NULL,	'Charente-Maritime',	'17',	NULL,	155),
(31,	28,	NULL,	'Cher',	'18',	NULL,	160),
(32,	28,	NULL,	'Côtes d\'Armor',	'22',	NULL,	165),
(33,	28,	NULL,	'Eure et Loir',	'28',	NULL,	170),
(34,	28,	NULL,	'Finistère',	'29',	NULL,	175),
(35,	28,	NULL,	'Ille et Vilaine',	'35',	NULL,	180),
(36,	28,	NULL,	'Indre',	'36',	NULL,	185),
(37,	28,	NULL,	'Indre et Loire',	'37',	NULL,	190),
(38,	28,	NULL,	'Loir et Cher',	'41',	NULL,	195),
(39,	28,	NULL,	'Loiret',	'45',	NULL,	200),
(40,	28,	NULL,	'Maine-et-Loire',	'49',	NULL,	205),
(41,	28,	NULL,	'Morbihan',	'56',	NULL,	210),
(42,	28,	NULL,	'Sarthe',	'72',	NULL,	215),
(43,	28,	NULL,	'Deux Sevres',	'79',	NULL,	220),
(44,	28,	NULL,	'Vendee',	'85',	NULL,	225),
(45,	28,	NULL,	'Vienne',	'86',	NULL,	230),
(46,	NULL,	1,	'Grand Sud-Ouest',	NULL,	NULL,	235),
(47,	46,	NULL,	'Ariège',	'09',	NULL,	240),
(48,	46,	NULL,	'Aude',	'11',	NULL,	245),
(49,	46,	NULL,	'Aveyron',	'12',	NULL,	250),
(50,	46,	NULL,	'Correze',	'19',	NULL,	255),
(51,	46,	NULL,	'Creuse',	'23',	NULL,	260),
(52,	46,	NULL,	'Dordogne',	'24',	NULL,	265),
(53,	46,	NULL,	'Gard',	'30',	NULL,	270),
(54,	46,	NULL,	'Haute Garonne',	'31',	NULL,	275),
(55,	46,	NULL,	'Herault',	'34',	NULL,	280),
(56,	46,	NULL,	'Gironde',	'33',	NULL,	285),
(57,	46,	NULL,	'Landes',	'40',	NULL,	290),
(58,	46,	NULL,	'Lot',	'46',	NULL,	295),
(59,	46,	NULL,	'Lot et Garenne',	'47',	NULL,	300),
(60,	46,	NULL,	'Pyrénées-Atlantiques',	'64',	NULL,	305),
(61,	46,	NULL,	'Hautes-Pyrénées',	'65',	NULL,	310),
(62,	46,	NULL,	'Pyrénées-Orientales',	'66',	NULL,	315),
(63,	46,	NULL,	'Tarn',	'81',	NULL,	320),
(64,	46,	NULL,	'Tarn et Garonne',	'82',	NULL,	325),
(65,	46,	NULL,	'Haute Vienne',	'87',	NULL,	330),
(66,	NULL,	1,	'Ile-de-France',	NULL,	NULL,	335),
(67,	66,	NULL,	'Paris',	'75',	NULL,	340),
(68,	66,	NULL,	'Seine et Marne',	'77',	NULL,	345),
(69,	66,	NULL,	'Yvelines',	'78',	NULL,	350),
(70,	66,	NULL,	'Essonne',	'91',	NULL,	355),
(71,	66,	NULL,	'Hauts-de-Seine',	'92',	NULL,	360),
(72,	66,	NULL,	'Seine-Saint-Denis',	'93',	NULL,	365),
(73,	66,	NULL,	'Val-de-Marne',	'94',	NULL,	370),
(74,	66,	NULL,	'Val-d\'Oise',	'95',	NULL,	375),
(75,	NULL,	1,	'Nord-Ouest',	NULL,	NULL,	380),
(76,	75,	NULL,	'Aisne',	'02',	NULL,	385),
(77,	75,	NULL,	'Calvados',	'14',	NULL,	390),
(78,	75,	NULL,	'Eure',	'27',	NULL,	395),
(79,	75,	NULL,	'Nord',	'59',	NULL,	400),
(80,	75,	NULL,	'Oise',	'60',	NULL,	405),
(81,	75,	NULL,	'Pas-de-Calais',	'62',	NULL,	410),
(82,	75,	NULL,	'Seine-Maritime',	'76',	NULL,	415),
(83,	75,	NULL,	'Somme',	'80',	NULL,	420),
(84,	NULL,	1,	'PACA',	NULL,	NULL,	425),
(85,	84,	NULL,	'Alpes de Haute Provence',	'04',	NULL,	430),
(86,	84,	NULL,	'Hautes Alpes',	'05',	NULL,	435),
(87,	84,	NULL,	'Alpes Maritimes',	'06',	NULL,	440),
(88,	84,	NULL,	'Bouches-du-Rhône',	'13',	NULL,	445),
(89,	84,	NULL,	'Var',	'83',	NULL,	450),
(90,	84,	NULL,	'Vaucluse',	'84',	NULL,	455),
(91,	NULL,	1,	'Non applicable',	NULL,	NULL,	460),
(92,	91,	NULL,	'Corse-du-Sud',	'2A',	NULL,	465),
(93,	91,	NULL,	'Haute-Corse',	'2B',	NULL,	470),
(94,	91,	NULL,	'Guadeloupe',	'971',	NULL,	475),
(95,	91,	NULL,	'Martinique ',	'972',	NULL,	480),
(96,	91,	NULL,	'Réunion',	'974',	NULL,	485);

DROP TABLE IF EXISTS `dl_crf_type`;
CREATE TABLE `dl_crf_type` (
                               `id` int(11) NOT NULL AUTO_INCREMENT,
                               `label` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
                               `deleted_at` datetime DEFAULT NULL,
                               PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `dl_crf_type` (`id`, `label`, `deleted_at`) VALUES
(1,	'Clinfile',	NULL),
(2,	'Papier',	NULL),
(3,	'Ennov',	NULL),
(4,	'Autre',	NULL);

DROP TABLE IF EXISTS `dl_department`;
CREATE TABLE `dl_department` (
                                 `id` int(11) NOT NULL AUTO_INCREMENT,
                                 `label` varchar(55) COLLATE utf8mb4_unicode_ci NOT NULL,
                                 `deleted_at` datetime DEFAULT NULL,
                                 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `dl_department` (`id`, `label`, `deleted_at`) VALUES
(1,	'Compta',	NULL),
(2,	'Juridique',	NULL),
(3,	'IT',	NULL);

DROP TABLE IF EXISTS `dl_device`;
CREATE TABLE `dl_device` (
                             `id` int(11) NOT NULL AUTO_INCREMENT,
                             `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                             PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `dl_device` (`id`, `name`) VALUES
(1,	'€'),
(2,	'$'),
(3,	'F'),
(4,	'¥'),
(5,	'£');

DROP TABLE IF EXISTS `dl_exam_type`;
CREATE TABLE `dl_exam_type` (
                                `id` int(11) NOT NULL AUTO_INCREMENT,
                                `label` varchar(55) COLLATE utf8mb4_unicode_ci NOT NULL,
                                `delete_at` datetime DEFAULT NULL,
                                PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `dl_exam_type` (`id`, `label`, `delete_at`) VALUES
(1,	'IRM',	NULL),
(2,	'Scanner',	NULL),
(3,	'Echographie',	NULL),
(4,	'Bilan biologique',	NULL),
(5,	'Biopsie',	NULL);

DROP TABLE IF EXISTS `dl_formality_rule`;
CREATE TABLE `dl_formality_rule` (
                                     `id` int(11) NOT NULL AUTO_INCREMENT,
                                     `label` varchar(55) COLLATE utf8mb4_unicode_ci NOT NULL,
                                     `delete_at` datetime DEFAULT NULL,
                                     PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `dl_formality_rule` (`id`, `label`, `delete_at`) VALUES
(1,	'MR01',	NULL),
(2,	'MR02',	NULL),
(3,	'MR03',	NULL),
(4,	'MR04',	NULL),
(5,	'MR05',	NULL),
(6,	'MR06',	NULL),
(7,	'Demande d\'autorisation',	NULL),
(8,	'Health Data Hub',	NULL);

DROP TABLE IF EXISTS `dl_funder`;
CREATE TABLE `dl_funder` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(55) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `dl_funder` (`id`, `label`, `deleted_at`) VALUES
(1,	'Agendia',	NULL),
(2,	'Alliance',	NULL),
(3,	'Amgen',	NULL),
(4,	'ANR',	NULL),
(5,	'Aquilab',	NULL),
(6,	'Astra Zeneca',	NULL),
(7,	'Bayer',	NULL),
(8,	'BeiGene',	NULL),
(9,	'Boehringer Ingelheim',	NULL),
(10,	'Breast International Group (BIG)',	NULL),
(11,	'Bristol Myers Squibb',	NULL),
(12,	'Cancer Research UK',	NULL),
(13,	'Chugai Pharma',	NULL),
(14,	'Clovis',	NULL),
(15,	'Daiichi',	NULL),
(16,	'DGOS',	NULL),
(17,	'DGOS-INCa',	NULL),
(18,	'European Organisation for Research',	NULL),
(19,	'Ferring Pharmaceuticals A/S',	NULL),
(20,	'Fondation ARC pour la recherche',	NULL),
(21,	'Fondation de France',	NULL),
(22,	'Genentech',	NULL),
(23,	'Genomic Health',	NULL),
(24,	'German Breast Group (GBG)',	NULL),
(25,	'GlaxosmithKline',	NULL),
(26,	'Grünenthal',	NULL),
(27,	'HalioDX',	NULL),
(28,	'Immunomedics',	NULL),
(29,	'Institut Gustave Roussy',	NULL),
(30,	'Institut Jules Bordet',	NULL),
(31,	'Institut National du Cancer (INCa)',	NULL),
(32,	'Institut of Cancer research (ICR)',	NULL),
(33,	'International Breast Cancer Study',	NULL),
(34,	'Ipsen',	NULL),
(35,	'Ipsogen',	NULL),
(36,	'Janssen',	NULL),
(37,	'Kiowarkirin',	NULL),
(38,	'Ligue Nationale Contre le Cancer',	NULL),
(39,	'Lilly',	NULL),
(40,	'Malakoff Mederic',	NULL),
(41,	'MedImmune',	NULL),
(42,	'Merck',	NULL),
(43,	'MSD',	NULL),
(44,	'Myriad',	NULL),
(45,	'Nanostring',	NULL),
(46,	'Natsuca',	NULL),
(47,	'Nektar',	NULL),
(48,	'Novartis',	NULL),
(49,	'Pfizer',	NULL),
(50,	'Pierre Fabre',	NULL),
(51,	'Puma',	NULL),
(52,	'Rhône Poulenc Rorer',	NULL),
(53,	'Roche',	NULL),
(54,	'Roche Diagnostics France',	NULL),
(55,	'SAKK',	NULL),
(56,	'Sanofi',	NULL),
(57,	'Sanofi - Aventis',	NULL),
(58,	'Schweizerische Arbeitsgemeinschaft',	NULL),
(59,	'Seattle Genetics',	NULL),
(60,	'Servier',	NULL),
(61,	'Takeda Pharmaceuticals International',	NULL),
(62,	'Tesaro',	NULL),
(63,	'The Royal Marsden NHS Foundation',	NULL),
(64,	'TheraPanacea',	NULL),
(65,	'Union Européenne',	NULL),
(66,	'Zenica',	NULL);

DROP TABLE IF EXISTS `dl_institution_type`;
CREATE TABLE `dl_institution_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(55) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `dl_institution_type` (`id`, `label`, `deleted_at`) VALUES
(1,	'AP',	NULL),
(2,	'Autorité Compétente',	NULL),
(3,	'Autre',	NULL),
(4,	'Centre étranger',	NULL),
(5,	'CH',	NULL),
(6,	'CLCC',	NULL),
(7,	'Comité éthique',	NULL),
(8,	'CRB',	NULL),
(9,	'Economiste',	NULL),
(10,	'Entité juridique',	NULL),
(11,	'Etablissement pharmaceutique',	NULL),
(12,	'Etablissement privé',	NULL),
(13,	'Fabricant',	NULL),
(14,	'Fournisseur',	NULL),
(15,	'Gestionnaire de données',	NULL),
(16,	'Groupe coopérateur',	NULL),
(17,	'Industriel',	NULL),
(18,	'Inserm',	NULL),
(19,	'Institutionnel',	NULL),
(20,	'Medical Writer',	NULL),
(21,	'Monitoring',	NULL);

DROP TABLE IF EXISTS `dl_is_congress`;
CREATE TABLE `dl_is_congress` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(55) COLLATE utf8mb4_unicode_ci NOT NULL,
  `delete_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `dl_is_congress` (`id`, `label`, `delete_at`) VALUES
(1,	'Oral',	NULL),
(2,	'Poster',	NULL);

DROP TABLE IF EXISTS `dl_journals`;
CREATE TABLE `dl_journals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `delete_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `dl_journals` (`id`, `label`, `delete_at`) VALUES
(1,	'Annals of Oncology',	NULL),
(2,	'Annals of Oncology Advance Acces',	NULL),
(3,	'Annals of Surgical Oncology',	NULL),
(4,	'Autres',	NULL),
(5,	'BMC Cancer',	NULL),
(6,	'British Medical Journal (BMJ)',	NULL),
(7,	'Breast Cancer Res',	NULL),
(8,	'Breast Cancer Research and Treatment',	NULL),
(9,	'Breast J',	NULL),
(10,	'British Journal of Cancer',	NULL),
(11,	'Bull Cancer',	NULL),
(12,	'Cancer',	NULL),
(13,	'Cancer Chemother Pharmacol',	NULL),
(14,	'Cancer Epidemiology',	NULL),
(15,	'Cancer Epidemiol Biomark. and Prev.',	NULL),
(16,	'Cancer Genetics',	NULL),
(17,	'Cancer Medicine',	NULL),
(18,	'Cancer/Radiotherapie',	NULL),
(19,	'Cancer Res',	NULL),
(20,	'Cancérologie digestive',	NULL),
(21,	'Cells',	NULL),
(22,	'Cell Death Dis',	NULL),
(23,	'Clin Breast Cancer',	NULL),
(24,	'Clin Sarcoma Res',	NULL),
(25,	'Clinical Colorectal Cancer',	NULL),
(26,	'Eur J Cancer',	NULL),
(27,	'Eur J Hum Genet',	NULL),
(28,	'Eur Urol',	NULL),
(29,	'Front Oncol',	NULL),
(30,	'Innovations & Thérapeutiques en Oncologie',	NULL),
(31,	'Int J Cancer',	NULL),
(32,	'Int. J. Mol. Sci.',	NULL),
(33,	'Int J Radiat Oncol Biol Phys',	NULL),
(34,	'ITO',	NULL),
(35,	'JAMA Oncol',	NULL),
(36,	'J Pharm Biomed Annal',	NULL),
(37,	'Journal of Clinical Oncology',	NULL),
(38,	'Journal of Medical Case Report',	NULL),
(39,	'Journal of Pharmacology',	NULL),
(40,	'JNCCN',	NULL),
(41,	'JNCI Cancer Spectr',	NULL),
(42,	'Lancet Oncol',	NULL),
(43,	'Mol Cancer Research',	NULL),
(44,	'Med Decis Making',	NULL),
(45,	'Nat Rev Clin Oncol',	NULL),
(46,	'Nature',	NULL),
(47,	'N Engl J Med',	NULL),
(48,	'OncoImmunology',	NULL),
(49,	'Oncologie',	NULL),
(50,	'Oncology',	NULL),
(51,	'Oncology Hematology',	NULL),
(52,	'Oncol Ther',	NULL),
(53,	'Oncotarget',	NULL),
(54,	'PLoS Med',	NULL),
(55,	'Psychology, Health & Medicine ',	NULL),
(56,	'Psycho-Oncology',	NULL),
(57,	'Radiother Oncol',	NULL),
(58,	'Scientific Reports',	NULL),
(59,	'Social Health Illn',	NULL),
(60,	'Thérapie',	NULL);

DROP TABLE IF EXISTS `dl_meeting_type`;
CREATE TABLE `dl_meeting_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(55) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `dl_meeting_type` (`id`, `label`, `deleted_at`) VALUES
(1,	'Réunion 1',	NULL),
(2,	'Réunion 2',	NULL),
(3,	'Réunion 3',	NULL);

DROP TABLE IF EXISTS `dl_membership_group`;
CREATE TABLE `dl_membership_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(55) COLLATE utf8mb4_unicode_ci NOT NULL,
  `delete_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `dl_membership_group` (`id`, `label`, `delete_at`) VALUES
(1,	'UCGI',	NULL),
(2,	'ORL',	NULL),
(3,	'GMP',	NULL),
(4,	'SARCOME',	NULL),
(5,	'UCBG',	NULL),
(6,	'UNITRAD',	NULL),
(7,	'GIO',	NULL),
(8,	'GERICO',	NULL),
(9,	'SDS',	NULL),
(10,	'GETUG',	NULL),
(11,	'GEP',	NULL),
(12,	'FEDEGYN',	NULL),
(13,	'AUTRE',	NULL);

DROP TABLE IF EXISTS `dl_monitoring_report_type`;
CREATE TABLE `dl_monitoring_report_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(55) COLLATE utf8mb4_unicode_ci NOT NULL,
  `position` smallint(6) NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `dl_monitoring_report_type` (`id`, `label`, `position`, `deleted_at`) VALUES
(1,	'Selected',	10,	NULL),
(2,	'Setting up',	20,	NULL),
(3,	'Monitoring',	30,	NULL),
(4,	'Quality control',	40,	NULL),
(5,	'Closure',	50,	NULL);

DROP TABLE IF EXISTS `dl_participant_job`;
CREATE TABLE `dl_participant_job` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(55) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `dl_participant_job` (`id`, `label`, `deleted_at`) VALUES
(1,	'Administratif',	NULL),
(2,	'ARC',	NULL),
(3,	'ARC Coordonnateur',	NULL),
(4,	'Autre',	NULL),
(5,	'Cadre de Santé',	NULL),
(6,	'Chef de Projet',	NULL),
(7,	'Chercheur',	NULL),
(8,	'Diététicien',	NULL),
(9,	'Dosimétriste',	NULL),
(10,	'Infirmier',	NULL),
(11,	'Informaticien',	NULL),
(12,	'Manipulateur',	NULL),
(13,	'Médecin',	NULL),
(14,	'Médecin chercheur',	NULL),
(15,	'Pharmacien',	NULL),
(16,	'Physicien',	NULL),
(17,	'Secrétaire',	NULL),
(18,	'Statisticien',	NULL),
(19,	'Study Nurse',	NULL),
(20,	'TRC',	NULL),
(21,	'Technicien',	NULL),
(22,	'Coordinateur',	NULL);

DROP TABLE IF EXISTS `dl_participant_project_role`;
CREATE TABLE `dl_participant_project_role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(55) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(8) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `dl_participant_project_role` (`id`, `label`, `code`, `deleted_at`) VALUES
(1,	'Investigateur',	'INV',	NULL),
(2,	'ARC sur site',	'TEC',	NULL),
(3,	'Infirmière',	'IDR',	NULL),
(4,	'Pharmacien',	'PH',	NULL),
(5,	'Biologiste',	'BIO',	NULL),
(6,	'Technicien en laboratoire',	'LAB',	NULL),
(7,	'Anatomopathologiste',	'PA',	NULL);

DROP TABLE IF EXISTS `dl_participant_speciality`;
CREATE TABLE `dl_participant_speciality` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(55) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `dl_participant_speciality` (`id`, `label`, `deleted_at`) VALUES
(1,	'Assurance',	NULL),
(2,	'Autorité Compétente',	NULL),
(3,	'Autre',	NULL),
(4,	'Cancérologie Cervico-faciale',	NULL),
(5,	'Cardiologie',	NULL),
(6,	'Chirurgie',	NULL),
(7,	'Comité Ethique',	NULL),
(8,	'Conditionnement',	NULL),
(9,	'Cytologie',	NULL),
(10,	'Data management',	NULL),
(11,	'Dermatologie',	NULL),
(12,	'Distribution',	NULL),
(13,	'Endocrinologie',	NULL),
(14,	'Gastro-entérologie',	NULL),
(15,	'Génétique',	NULL),
(16,	'Génomique',	NULL),
(17,	'Gériatrie',	NULL),
(18,	'Groupe Coopérateur',	NULL),
(19,	'Gynécologie Médicale',	NULL),
(20,	'Gynécologie Obstétrique',	NULL),
(21,	'Hématologie',	NULL),
(22,	'Hépatologie',	NULL),
(23,	'Histologie',	NULL),
(24,	'Immunologie',	NULL),
(25,	'Imprimeur',	NULL),
(26,	'Industriel',	NULL),
(27,	'Institutionnel',	NULL),
(28,	'Médecine interne',	NULL),
(29,	'Médecine Nucléaire',	NULL),
(30,	'Métabolisme',	NULL),
(31,	'Neurologie',	NULL),
(32,	'Nutrition',	NULL),
(33,	'Oncologie',	NULL),
(34,	'Oncologie Médicale',	NULL),
(35,	'ORL',	NULL),
(36,	'Pédiatrie',	NULL),
(37,	'PharmacoCinétique',	NULL),
(38,	'Pharmacogénétique',	NULL),
(39,	'Pharmacologie',	NULL),
(40,	'Pneumologie',	NULL),
(41,	'Production',	NULL),
(42,	'Protéomique',	NULL),
(43,	'Radiopharmacien',	NULL),
(44,	'Radiothérapie',	NULL),
(45,	'Randomisation',	NULL),
(46,	'Rhumatologie',	NULL),
(47,	'Santé publique et Médecine Soc',	NULL),
(48,	'Sénologie',	NULL),
(49,	'Statistiques',	NULL),
(50,	'Stomatologie',	NULL),
(51,	'Transcriptomique',	NULL),
(52,	'Transport',	NULL),
(53,	'Urologie',	NULL),
(54,	'Médecine générale',	NULL),
(55,	'CIC',	NULL),
(56,	'URC',	NULL),
(57,	'Recherche clinique',	NULL),
(58,	'NA',	NULL),
(59,	'Néphrologie',	NULL),
(60,	'Orthopédie',	NULL),
(61,	'Virologie',	NULL);

DROP TABLE IF EXISTS `dl_patient_number`;
CREATE TABLE `dl_patient_number` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(55) COLLATE utf8mb4_unicode_ci NOT NULL,
  `delete_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `dl_patient_number` (`id`, `label`, `delete_at`) VALUES
(1,	'Par étude',	NULL),
(2,	'par centre',	NULL);

DROP TABLE IF EXISTS `dl_payment_unit`;
CREATE TABLE `dl_payment_unit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(55) COLLATE utf8mb4_unicode_ci NOT NULL,
  `delete_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `dl_payment_unit` (`id`, `label`, `delete_at`) VALUES
(1,	'Patient',	NULL),
(2,	'Patient/Visite',	NULL);

DROP TABLE IF EXISTS `dl_post_type`;
CREATE TABLE `dl_post_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(55) COLLATE utf8mb4_unicode_ci NOT NULL,
  `delete_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `dl_post_type` (`id`, `label`, `delete_at`) VALUES
(1,	'Princeps',	NULL),
(2,	'Ancillaire',	NULL),
(3,	'Secondaire',	NULL);

DROP TABLE IF EXISTS `dl_project_database_freeze_reason`;
CREATE TABLE `dl_project_database_freeze_reason` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(55) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `dl_project_database_freeze_reason` (`id`, `label`, `deleted_at`) VALUES
(1,	'IDMC',	NULL),
(2,	'Translationnel',	NULL),
(3,	'DSMB',	NULL),
(4,	'Autre (ex: congrès)',	NULL);

DROP TABLE IF EXISTS `dl_project_status`;
CREATE TABLE `dl_project_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(55) COLLATE utf8mb4_unicode_ci NOT NULL,
  `delete_at` datetime DEFAULT NULL,
  `is_auto` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `dl_project_status` (`id`, `label`, `delete_at`, `is_auto`) VALUES
(1,	'En discussion',	NULL,	0),
(2,	'En initiation',	NULL,	0),
(3,	'En recrutement',	NULL,	0),
(4,	'En suivi',	NULL,	0),
(5,	'A clôturer',	NULL,	0),
(6,	'Clôturé',	NULL,	0);

DROP TABLE IF EXISTS `dl_rule_transfer_territory`;
CREATE TABLE `dl_rule_transfer_territory` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `delete_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `dl_rule_transfer_territory` (`id`, `label`, `delete_at`) VALUES
(1,	'UE',	NULL),
(2,	'Hors UE',	NULL);

DROP TABLE IF EXISTS `dl_society`;
CREATE TABLE `dl_society` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `delete_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `dl_society` (`id`, `name`, `delete_at`) VALUES
(1,	'SociétéA',	NULL),
(2,	'SociétéB',	NULL),
(3,	'SociétéC',	NULL);

DROP TABLE IF EXISTS `dl_sponsor`;
CREATE TABLE `dl_sponsor` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(55) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `dl_sponsor` (`id`, `label`, `deleted_at`) VALUES
(1,	'IBCSG',	NULL),
(2,	'EORTC',	NULL),
(3,	'ALLIANCE',	NULL),
(4,	'SAKK',	NULL),
(5,	'ROCHE',	NULL),
(6,	'CURIE',	NULL),
(7,	'GBG',	NULL),
(8,	'SWOG',	NULL),
(9,	'Promoteur hollandais',	NULL),
(10,	'PFIZER',	NULL),
(11,	'Gustave Roussy',	NULL),
(12,	'The Clatterbridge Cancer NHs foundation trust',	NULL),
(13,	'Centre médical de l’université de Leiden',	NULL),
(14,	'UNICANCER',	NULL);

DROP TABLE IF EXISTS `dl_study_population`;
CREATE TABLE `dl_study_population` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(55) COLLATE utf8mb4_unicode_ci NOT NULL,
  `delete_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `dl_study_population` (`id`, `label`, `delete_at`) VALUES
(1,	'Pédiatrique',	NULL),
(2,	'Adulte',	NULL);

DROP TABLE IF EXISTS `dl_submission_type`;
CREATE TABLE `dl_submission_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(55) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `dl_submission_type` (`id`, `label`, `deleted_at`) VALUES
(1,	'Initiale',	NULL),
(2,	'Amendement',	NULL),
(3,	'Déclaration',	NULL);

DROP TABLE IF EXISTS `dl_territory`;
CREATE TABLE `dl_territory` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(55) COLLATE utf8mb4_unicode_ci NOT NULL,
  `delete_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `dl_territory` (`id`, `label`, `delete_at`) VALUES
(1,	'France',	NULL),
(2,	'International',	NULL);

DROP TABLE IF EXISTS `dl_trail_phase`;
CREATE TABLE `dl_trail_phase` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(55) COLLATE utf8mb4_unicode_ci NOT NULL,
  `delete_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `dl_trail_phase` (`id`, `label`, `delete_at`) VALUES
(1,	'I',	NULL),
(2,	'II',	NULL),
(3,	'III',	NULL),
(4,	'IV',	NULL),
(5,	'Cohorte',	NULL),
(6,	'HPS',	NULL),
(7,	'NA',	NULL);

DROP TABLE IF EXISTS `dl_trail_treatment`;
CREATE TABLE `dl_trail_treatment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(55) COLLATE utf8mb4_unicode_ci NOT NULL,
  `delete_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `dl_trail_treatment` (`id`, `label`, `delete_at`) VALUES
(1,	'Médicament expérimental',	NULL),
(2,	'Médicament auxiliaire',	NULL),
(3,	'Placebo',	NULL),
(4,	'Radiothérapie',	NULL),
(5,	'Chirurgie',	NULL),
(6,	'NA',	NULL);

DROP TABLE IF EXISTS `dl_trail_type`;
CREATE TABLE `dl_trail_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(55) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `dl_trail_type` (`id`, `label`, `deleted_at`) VALUES
(1,	'RIPH1',	NULL),
(2,	'RIPH2',	NULL),
(3,	'RIPH3',	NULL),
(4,	'HORS RIPH',	NULL),
(5,	'Recherche biomédicale',	NULL),
(6,	'Soins courants',	NULL),
(7,	'Sur données',	NULL),
(8,	'Collections biologiques',	NULL);

DROP TABLE IF EXISTS `dl_training_type`;
CREATE TABLE `dl_training_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(55) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `dl_training_type` (`id`, `label`, `deleted_at`) VALUES
(1,	'Type A',	NULL),
(2,	'Type B',	NULL);

DROP TABLE IF EXISTS `dl_trl_indice`;
CREATE TABLE `dl_trl_indice` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(55) COLLATE utf8mb4_unicode_ci NOT NULL,
  `delete_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `dl_trl_indice` (`id`, `label`, `delete_at`) VALUES
(1,	'1',	NULL),
(2,	'2',	NULL),
(3,	'3',	NULL),
(4,	'4',	NULL),
(5,	'5',	NULL),
(6,	'6',	NULL),
(7,	'7',	NULL),
(8,	'8',	NULL);

DROP TABLE IF EXISTS `dl_type_declaration`;
CREATE TABLE `dl_type_declaration` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(55) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `dl_type_declaration` (`id`, `label`, `deleted_at`) VALUES
(1,	'1er patient',	NULL),
(2,	'Fin d\'étude',	NULL),
(3,	'Rapport final',	NULL);

DROP TABLE IF EXISTS `dl_user_job`;
CREATE TABLE `dl_user_job` (
                               `id` int(11) NOT NULL AUTO_INCREMENT,
                               `label` varchar(55) COLLATE utf8mb4_unicode_ci NOT NULL,
                               `deleted_at` datetime DEFAULT NULL,
                               PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `dl_user_job` (`id`, `label`, `deleted_at`) VALUES
(1,	'ARC',	NULL),
(2,	'AP',	NULL),
(3,	'Direction Réglementaire',	NULL),
(4,	'Rédacteur Médical',	NULL),
(5,	'CEC',	NULL),
(6,	'RPC',	NULL),
(7,	'REGLEMENTAIRE',	NULL),
(8,	'CDP',	NULL),
(9,	'Assistante de Direction',	NULL),
(10,	'TEC / ARC',	NULL),
(11,	'Direction des Opérations Cliniques',	NULL),
(12,	'Direction R&D',	NULL),
(13,	'Direction Partenariats',	NULL),
(14,	'ARC Manager',	NULL),
(15,	'Responsable AQ',	NULL),
(16,	'Ref Déviations',	NULL);

DROP TABLE IF EXISTS `dl_visit_type`;
CREATE TABLE `dl_visit_type` (
                                 `id` int(11) NOT NULL AUTO_INCREMENT,
                                 `label` varchar(55) COLLATE utf8mb4_unicode_ci NOT NULL,
                                 `deleted_at` datetime DEFAULT NULL,
                                 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `dl_visit_type` (`id`, `label`, `deleted_at`) VALUES
(1,	'Screening',	NULL),
(2,	'Inclusion',	NULL),
(3,	'Follow up',	NULL),
(4,	'End of study',	NULL),
(5,	'Extra visit',	NULL),
(6,	'Rando',	NULL);

DROP TABLE IF EXISTS `document_tracking`;
CREATE TABLE `document_tracking` (
                                     `id` int(11) NOT NULL AUTO_INCREMENT,
                                     `project_id` int(11) DEFAULT NULL,
                                     `country_id` int(11) DEFAULT NULL,
                                     `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                                     `version` varchar(8) COLLATE utf8mb4_unicode_ci NOT NULL,
                                     `level` smallint(6) NOT NULL COMMENT '1: center, 2: interlocutor',
                                     `to_be_sent` tinyint(1) NOT NULL,
                                     `to_be_received` tinyint(1) NOT NULL,
                                     `disabled_at` datetime DEFAULT NULL,
                                     PRIMARY KEY (`id`),
                                     KEY `IDX_27BD9345166D1F9C` (`project_id`),
                                     KEY `IDX_27BD9345F92F3E70` (`country_id`),
                                     CONSTRAINT `FK_27BD9345166D1F9C` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`),
                                     CONSTRAINT `FK_27BD9345F92F3E70` FOREIGN KEY (`country_id`) REFERENCES `dl_country` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `document_tracking_audit_trail`;
CREATE TABLE `document_tracking_audit_trail` (
                                                 `id` int(11) NOT NULL AUTO_INCREMENT,
                                                 `entity_id` int(11) NOT NULL,
                                                 `user_id` int(11) DEFAULT NULL,
                                                 `date` datetime NOT NULL,
                                                 `reason` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                                 `modif_type` smallint(6) NOT NULL COMMENT '1=insert, 2=update, 3=delete',
                                                 `details` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:json)',
                                                 PRIMARY KEY (`id`),
                                                 KEY `IDX_9E33EBD081257D5D` (`entity_id`),
                                                 KEY `IDX_9E33EBD0A76ED395` (`user_id`),
                                                 CONSTRAINT `FK_9E33EBD081257D5D` FOREIGN KEY (`entity_id`) REFERENCES `document_tracking` (`id`),
                                                 CONSTRAINT `FK_9E33EBD0A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `document_tracking_center`;
CREATE TABLE `document_tracking_center` (
                                            `id` int(11) NOT NULL AUTO_INCREMENT,
                                            `document_tracking_id` int(11) DEFAULT NULL,
                                            `center_id` int(11) DEFAULT NULL,
                                            `sent_at` datetime DEFAULT NULL,
                                            `received_at` datetime DEFAULT NULL,
                                            PRIMARY KEY (`id`),
                                            KEY `IDX_E1B069B6FFAC9CD4` (`document_tracking_id`),
                                            KEY `IDX_E1B069B65932F377` (`center_id`),
                                            CONSTRAINT `FK_E1B069B65932F377` FOREIGN KEY (`center_id`) REFERENCES `center` (`id`),
                                            CONSTRAINT `FK_E1B069B6FFAC9CD4` FOREIGN KEY (`document_tracking_id`) REFERENCES `document_tracking` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `document_tracking_center_audit_trail`;
CREATE TABLE `document_tracking_center_audit_trail` (
                                                        `id` int(11) NOT NULL AUTO_INCREMENT,
                                                        `entity_id` int(11) NOT NULL,
                                                        `user_id` int(11) DEFAULT NULL,
                                                        `date` datetime NOT NULL,
                                                        `reason` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                                        `modif_type` smallint(6) NOT NULL COMMENT '1=insert, 2=update, 3=delete',
                                                        `details` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:json)',
                                                        PRIMARY KEY (`id`),
                                                        KEY `IDX_F86F19AE81257D5D` (`entity_id`),
                                                        KEY `IDX_F86F19AEA76ED395` (`user_id`),
                                                        CONSTRAINT `FK_F86F19AE81257D5D` FOREIGN KEY (`entity_id`) REFERENCES `document_tracking_center` (`id`),
                                                        CONSTRAINT `FK_F86F19AEA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `document_tracking_interlocutor`;
CREATE TABLE `document_tracking_interlocutor` (
                                                  `id` int(11) NOT NULL AUTO_INCREMENT,
                                                  `document_tracking_id` int(11) DEFAULT NULL,
                                                  `interlocutor_id` int(11) DEFAULT NULL,
                                                  `sent_at` datetime DEFAULT NULL,
                                                  `received_at` datetime DEFAULT NULL,
                                                  PRIMARY KEY (`id`),
                                                  KEY `IDX_2BD80025FFAC9CD4` (`document_tracking_id`),
                                                  KEY `IDX_2BD80025B3F944DB` (`interlocutor_id`),
                                                  CONSTRAINT `FK_2BD80025B3F944DB` FOREIGN KEY (`interlocutor_id`) REFERENCES `interlocutor` (`id`),
                                                  CONSTRAINT `FK_2BD80025FFAC9CD4` FOREIGN KEY (`document_tracking_id`) REFERENCES `document_tracking` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `document_tracking_interlocutor_audit_trail`;
CREATE TABLE `document_tracking_interlocutor_audit_trail` (
                                                              `id` int(11) NOT NULL AUTO_INCREMENT,
                                                              `entity_id` int(11) NOT NULL,
                                                              `user_id` int(11) DEFAULT NULL,
                                                              `date` datetime NOT NULL,
                                                              `reason` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                                              `modif_type` smallint(6) NOT NULL COMMENT '1=insert, 2=update, 3=delete',
                                                              `details` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:json)',
                                                              PRIMARY KEY (`id`),
                                                              KEY `IDX_276E2CAD81257D5D` (`entity_id`),
                                                              KEY `IDX_276E2CADA76ED395` (`user_id`),
                                                              CONSTRAINT `FK_276E2CAD81257D5D` FOREIGN KEY (`entity_id`) REFERENCES `document_tracking_interlocutor` (`id`),
                                                              CONSTRAINT `FK_276E2CADA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `document_transverse`;
CREATE TABLE `document_transverse` (
                                       `id` int(11) NOT NULL AUTO_INCREMENT,
                                       `portee_document_id` int(11) DEFAULT NULL,
                                       `type_document_id` int(11) DEFAULT NULL,
                                       `institution_id` int(11) DEFAULT NULL,
                                       `interlocutor_id` int(11) DEFAULT NULL,
                                       `drug_id` int(11) DEFAULT NULL,
                                       `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                                       `filename` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                       `valid_start_at` datetime DEFAULT NULL,
                                       `valid_end_at` datetime DEFAULT NULL,
                                       `is_valid` tinyint(1) NOT NULL,
                                       `deleted_at` datetime DEFAULT NULL,
                                       `created_at` datetime NOT NULL,
                                       `updated_at` datetime DEFAULT NULL,
                                       PRIMARY KEY (`id`),
                                       KEY `IDX_3D15F4E1320792C4` (`portee_document_id`),
                                       KEY `IDX_3D15F4E18826AFA6` (`type_document_id`),
                                       KEY `IDX_3D15F4E110405986` (`institution_id`),
                                       KEY `IDX_3D15F4E1B3F944DB` (`interlocutor_id`),
                                       KEY `IDX_3D15F4E1AABCA765` (`drug_id`),
                                       CONSTRAINT `FK_3D15F4E110405986` FOREIGN KEY (`institution_id`) REFERENCES `institution` (`id`),
                                       CONSTRAINT `FK_3D15F4E1320792C4` FOREIGN KEY (`portee_document_id`) REFERENCES `portee_document_transverse` (`id`),
                                       CONSTRAINT `FK_3D15F4E18826AFA6` FOREIGN KEY (`type_document_id`) REFERENCES `type_document_transverse` (`id`),
                                       CONSTRAINT `FK_3D15F4E1AABCA765` FOREIGN KEY (`drug_id`) REFERENCES `drug` (`id`),
                                       CONSTRAINT `FK_3D15F4E1B3F944DB` FOREIGN KEY (`interlocutor_id`) REFERENCES `interlocutor` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `document_transverse` (`id`, `portee_document_id`, `type_document_id`, `institution_id`, `interlocutor_id`, `drug_id`, `name`, `filename`, `valid_start_at`, `valid_end_at`, `is_valid`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1,	3,	3,	NULL,	NULL,	2,	'Notice',	'test test.txt',	'2021-04-08 00:00:00',	'2021-05-08 00:00:00',	1,	NULL,	'2021-04-08 10:13:02',	'2021-04-08 10:13:02'),
(2,	3,	2,	NULL,	NULL,	2,	'Notice bis',	'test test.txt',	'2021-03-18 00:00:00',	'2021-04-17 00:00:00',	1,	NULL,	'2021-04-08 10:50:45',	'2021-04-08 10:50:45'),
(4,	2,	4,	1,	NULL,	NULL,	'Norme lab',	'TEST EN PDF.pdf',	'2021-04-15 00:00:00',	'2021-05-09 00:00:00',	1,	NULL,	'2021-04-08 14:40:40',	'2021-04-08 14:40:40'),
(5,	1,	1,	NULL,	1,	NULL,	'CV Ghasan',	'TEST EN PDF.pdf',	'2021-02-01 00:00:00',	'2021-01-31 00:00:00',	1,	NULL,	'2021-04-08 14:41:46',	'2021-04-12 15:48:59'),
(6,	1,	1,	NULL,	1,	NULL,	'test valid',	'TEST EN PDF.pdf',	'2021-04-15 00:00:00',	'2021-05-15 00:00:00',	1,	NULL,	'2021-04-08 14:45:46',	'2021-04-08 14:45:46'),
(8,	1,	1,	NULL,	1,	NULL,	'Doc invalide',	'TEST EN PDF.pdf',	'2021-02-17 00:00:00',	'2021-03-19 00:00:00',	1,	NULL,	'2021-04-08 15:41:48',	'2021-04-08 15:41:48'),
(9,	3,	2,	NULL,	NULL,	3,	'BI Spasfon',	NULL,	'2021-04-01 00:00:00',	'2021-05-01 00:00:00',	1,	NULL,	'2021-04-08 15:54:14',	NULL),
(10,	3,	2,	NULL,	NULL,	3,	'BI Spasfon invalide',	NULL,	'2021-01-01 00:00:00',	'2021-01-31 00:00:00',	1,	NULL,	'2021-04-08 15:55:08',	NULL),
(11,	3,	2,	NULL,	NULL,	4,	'Chloriquine - notice',	'test.pdf',	'2021-04-13 00:00:00',	'2021-05-13 00:00:00',	1,	NULL,	'2021-04-08 16:15:45',	'2021-04-08 16:15:45'),
(12,	2,	4,	1,	NULL,	NULL,	'Test',	'TEST EN PDF.pdf',	'2021-04-16 00:00:00',	'2021-05-16 00:00:00',	1,	NULL,	'2021-04-08 16:32:56',	'2021-04-08 16:32:56'),
(13,	3,	2,	NULL,	NULL,	4,	'notice en anglais',	NULL,	'2021-01-13 00:00:00',	'2021-02-12 00:00:00',	1,	NULL,	'2021-04-08 20:08:44',	NULL),
(14,	3,	2,	NULL,	NULL,	4,	'Notice - italien',	NULL,	'2021-04-19 00:00:00',	'2021-05-19 00:00:00',	1,	NULL,	'2021-04-08 20:09:12',	NULL),
(15,	3,	2,	NULL,	NULL,	4,	'notice - russe',	NULL,	'2021-04-06 00:00:00',	'2021-05-06 00:00:00',	1,	NULL,	'2021-04-08 20:10:12',	NULL),
(16,	3,	2,	NULL,	NULL,	1,	'Notice Doli',	NULL,	'2021-04-08 00:00:00',	'2021-05-08 00:00:00',	1,	NULL,	'2021-04-09 09:44:01',	NULL),
(17,	3,	3,	NULL,	NULL,	1,	'Deux',	NULL,	'2021-04-13 00:00:00',	'2021-05-13 00:00:00',	1,	NULL,	'2021-04-09 09:44:36',	NULL),
(18,	1,	1,	NULL,	1,	NULL,	'Changement date',	'TEST EN PDF.pdf',	'2021-04-12 00:00:00',	'2021-05-09 00:00:00',	1,	NULL,	'2021-04-09 09:49:56',	'2021-04-09 09:49:56'),
(19,	2,	4,	2,	NULL,	NULL,	'TestMR',	'TEST EN PDF.pdf',	'2021-04-04 00:00:00',	'2021-05-04 00:00:00',	1,	NULL,	'2021-04-09 10:42:57',	'2021-04-09 10:42:57'),
(20,	2,	4,	1,	NULL,	NULL,	'Norme lab bis',	'TEST EN PDF.pdf',	'2021-04-06 00:00:00',	'2021-05-07 00:00:00',	1,	NULL,	'2021-04-09 16:06:50',	'2021-04-09 16:07:13'),
(21,	3,	2,	NULL,	NULL,	3,	'notice sans pdf',	NULL,	'2021-04-01 00:00:00',	'2021-05-01 00:00:00',	1,	NULL,	'2021-04-12 16:03:55',	NULL),
(22,	2,	4,	2,	NULL,	NULL,	'Test MI',	NULL,	'2021-04-01 00:00:00',	'2021-05-01 00:00:00',	1,	NULL,	'2021-04-13 09:52:42',	NULL);

DROP TABLE IF EXISTS `document_transverse_audit_trail`;
CREATE TABLE `document_transverse_audit_trail` (
                                                   `id` int(11) NOT NULL AUTO_INCREMENT,
                                                   `entity_id` int(11) NOT NULL,
                                                   `user_id` int(11) DEFAULT NULL,
                                                   `date` datetime NOT NULL,
                                                   `reason` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                                   `modif_type` smallint(6) NOT NULL COMMENT '1=insert, 2=update, 3=delete',
                                                   `details` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:json)',
                                                   PRIMARY KEY (`id`),
                                                   KEY `IDX_1F4D65C681257D5D` (`entity_id`),
                                                   KEY `IDX_1F4D65C6A76ED395` (`user_id`),
                                                   CONSTRAINT `FK_1F4D65C681257D5D` FOREIGN KEY (`entity_id`) REFERENCES `document_transverse` (`id`),
                                                   CONSTRAINT `FK_1F4D65C6A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `document_transverse_audit_trail` (`id`, `entity_id`, `user_id`, `date`, `reason`, `modif_type`, `details`) VALUES
(1,	1,	5,	'2021-04-08 10:13:02',	NULL,	1,	'{\"name\":\"Notice\",\"filename\":\"test test.txt\",\"validStartAt\":\"2021-04-08 00:00:00\",\"validEndAt\":\"2021-05-08 00:00:00\",\"isValid\":\"1\",\"createdAt\":\"2021-04-08 10:13:02\",\"porteeDocument\":\"Medicament (3)\",\"TypeDocument\":\"RCP (3)\"}'),
(2,	2,	5,	'2021-04-08 10:50:45',	NULL,	1,	'{\"name\":\"Notice bis\",\"filename\":\"test test.txt\",\"validStartAt\":\"2021-03-18 00:00:00\",\"validEndAt\":\"2021-04-17 00:00:00\",\"isValid\":\"1\",\"createdAt\":\"2021-04-08 10:50:45\",\"porteeDocument\":\"Medicament (3)\",\"TypeDocument\":\"BI (2)\"}'),
(3,	4,	5,	'2021-04-08 14:40:40',	NULL,	1,	'{\"name\":\"Norme lab\",\"filename\":\"TEST EN PDF.pdf\",\"validStartAt\":\"2021-04-10 00:00:00\",\"validEndAt\":\"2021-05-10 00:00:00\",\"isValid\":\"1\",\"createdAt\":\"2021-04-08 14:40:40\",\"porteeDocument\":\"\\u00c9tablissement (2)\",\"TypeDocument\":\"Normes de laboratoires (4)\",\"institution\":\"CHU Saint-Hilaire (1)\"}'),
(4,	5,	6,	'2021-04-08 14:41:46',	NULL,	1,	'{\"name\":\"CV Ghasan\",\"filename\":\"Output.pdf\",\"validStartAt\":\"2021-04-06 00:00:00\",\"validEndAt\":\"2021-05-06 00:00:00\",\"isValid\":\"1\",\"createdAt\":\"2021-04-08 14:41:46\",\"porteeDocument\":\"Interlocuteur (1)\",\"TypeDocument\":\"CV (1)\",\"interlocutor\":\"Ghasan SUCCAR (1)\"}'),
(5,	4,	5,	'2021-04-08 14:45:27',	NULL,	2,	'{\"validEndAt\":[\"2021-05-10 00:00:00\",\"2021-05-10 00:00:00\"]}'),
(6,	6,	5,	'2021-04-08 14:45:46',	NULL,	1,	'{\"name\":\"test valid\",\"filename\":\"TEST EN PDF.pdf\",\"validStartAt\":\"2021-04-15 00:00:00\",\"validEndAt\":\"2021-05-15 00:00:00\",\"isValid\":\"1\",\"createdAt\":\"2021-04-08 14:45:46\",\"porteeDocument\":\"Interlocuteur (1)\",\"TypeDocument\":\"CV (1)\",\"interlocutor\":\"Ghasan SUCCAR (1)\"}'),
(7,	4,	5,	'2021-04-08 14:55:05',	NULL,	2,	'{\"validStartAt\":[\"2021-04-10 00:00:00\",\"2021-04-09 00:00:00\"],\"validEndAt\":[\"2021-05-10 00:00:00\",\"2021-05-09 00:00:00\"]}'),
(8,	5,	6,	'2021-04-08 15:00:30',	NULL,	2,	'{\"validStartAt\":[\"2021-04-06 00:00:00\",\"2021-01-01 00:00:00\"],\"validEndAt\":[\"2021-05-06 00:00:00\",\"2021-01-31 00:00:00\"]}'),
(9,	8,	5,	'2021-04-08 15:41:48',	NULL,	1,	'{\"name\":\"Doc invalide\",\"filename\":\"TEST EN PDF.pdf\",\"validStartAt\":\"2021-02-17 00:00:00\",\"validEndAt\":\"2021-03-19 00:00:00\",\"isValid\":\"1\",\"createdAt\":\"2021-04-08 15:41:48\",\"porteeDocument\":\"Interlocuteur (1)\",\"TypeDocument\":\"CV (1)\",\"interlocutor\":\"Ghasan SUCCAR (1)\"}'),
(10,	9,	6,	'2021-04-08 15:54:14',	NULL,	1,	'{\"name\":\"BI Spasfon\",\"validStartAt\":\"2021-04-01 00:00:00\",\"validEndAt\":\"2021-05-01 00:00:00\",\"isValid\":\"1\",\"createdAt\":\"2021-04-08 15:54:14\",\"porteeDocument\":\"Medicament (3)\",\"TypeDocument\":\"BI (2)\"}'),
(11,	10,	6,	'2021-04-08 15:55:08',	NULL,	1,	'{\"name\":\"BI Spasfon invalide\",\"validStartAt\":\"2021-01-01 00:00:00\",\"validEndAt\":\"2021-01-31 00:00:00\",\"isValid\":\"1\",\"createdAt\":\"2021-04-08 15:55:08\",\"porteeDocument\":\"Medicament (3)\",\"TypeDocument\":\"BI (2)\"}'),
(12,	5,	5,	'2021-04-08 16:10:10',	NULL,	2,	'{\"filename\":[\"Output.pdf\",\"\"],\"validEndAt\":[\"2021-01-31 00:00:00\",\"2021-01-31 00:00:00\"]}'),
(13,	5,	5,	'2021-04-08 16:10:29',	NULL,	2,	'{\"validEndAt\":[\"2021-01-31 00:00:00\",\"2021-01-31 00:00:00\"]}'),
(14,	5,	5,	'2021-04-08 16:10:39',	NULL,	2,	'{\"filename\":[\"TEST EN PDF.pdf\",\"\"],\"validEndAt\":[\"2021-01-31 00:00:00\",\"2021-01-31 00:00:00\"]}'),
(15,	5,	5,	'2021-04-08 16:10:54',	NULL,	2,	'{\"filename\":[\"TEST EN PDF.pdf\",\"\"],\"validEndAt\":[\"2021-01-31 00:00:00\",\"2021-01-31 00:00:00\"]}'),
(16,	5,	5,	'2021-04-08 16:11:25',	NULL,	2,	'{\"validEndAt\":[\"2021-01-31 00:00:00\",\"2021-01-31 00:00:00\"]}'),
(17,	5,	5,	'2021-04-08 16:11:39',	NULL,	2,	'{\"filename\":[\"TEST EN PDF.pdf\",\"\"],\"validEndAt\":[\"2021-01-31 00:00:00\",\"2021-01-31 00:00:00\"]}'),
(18,	5,	5,	'2021-04-08 16:12:00',	NULL,	2,	'{\"validEndAt\":[\"2021-01-31 00:00:00\",\"2021-01-31 00:00:00\"]}'),
(19,	11,	10,	'2021-04-08 16:15:45',	NULL,	1,	'{\"name\":\"Chloriquine - notice\",\"filename\":\"test.pdf\",\"validStartAt\":\"2021-04-13 00:00:00\",\"validEndAt\":\"2021-05-13 00:00:00\",\"isValid\":\"1\",\"createdAt\":\"2021-04-08 16:15:45\",\"porteeDocument\":\"Medicament (3)\",\"TypeDocument\":\"BI (2)\"}'),
(20,	12,	11,	'2021-04-08 16:32:56',	NULL,	1,	'{\"name\":\"Test\",\"filename\":\"TEST EN PDF.pdf\",\"validStartAt\":\"2021-04-16 00:00:00\",\"validEndAt\":\"2021-05-16 00:00:00\",\"isValid\":\"1\",\"createdAt\":\"2021-04-08 16:32:56\",\"porteeDocument\":\"\\u00c9tablissement (2)\",\"TypeDocument\":\"Normes de laboratoires (4)\",\"institution\":\"CHU Saint-Hilaire (1)\"}'),
(21,	13,	10,	'2021-04-08 20:08:44',	NULL,	1,	'{\"name\":\"notice en anglais\",\"validStartAt\":\"2021-01-13 00:00:00\",\"validEndAt\":\"2021-02-12 00:00:00\",\"isValid\":\"1\",\"createdAt\":\"2021-04-08 20:08:44\",\"porteeDocument\":\"Medicament (3)\",\"TypeDocument\":\"BI (2)\"}'),
(22,	14,	10,	'2021-04-08 20:09:12',	NULL,	1,	'{\"name\":\"Notice - italien\",\"validStartAt\":\"2021-04-19 00:00:00\",\"validEndAt\":\"2021-05-19 00:00:00\",\"isValid\":\"1\",\"createdAt\":\"2021-04-08 20:09:12\",\"porteeDocument\":\"Medicament (3)\",\"TypeDocument\":\"BI (2)\"}'),
(23,	15,	10,	'2021-04-08 20:10:12',	NULL,	1,	'{\"name\":\"notice - russe\",\"validStartAt\":\"2021-04-06 00:00:00\",\"validEndAt\":\"2021-05-06 00:00:00\",\"isValid\":\"1\",\"createdAt\":\"2021-04-08 20:10:12\",\"porteeDocument\":\"Medicament (3)\",\"TypeDocument\":\"BI (2)\"}'),
(24,	16,	5,	'2021-04-09 09:44:01',	NULL,	1,	'{\"name\":\"Notice Doli\",\"validStartAt\":\"2021-04-08 00:00:00\",\"validEndAt\":\"2021-05-08 00:00:00\",\"isValid\":\"1\",\"createdAt\":\"2021-04-09 09:44:01\",\"porteeDocument\":\"Medicament (3)\",\"TypeDocument\":\"BI (2)\"}'),
(25,	17,	5,	'2021-04-09 09:44:36',	NULL,	1,	'{\"name\":\"Deux\",\"validStartAt\":\"2021-04-13 00:00:00\",\"validEndAt\":\"2021-05-13 00:00:00\",\"isValid\":\"1\",\"createdAt\":\"2021-04-09 09:44:36\",\"porteeDocument\":\"Medicament (3)\",\"TypeDocument\":\"RCP (3)\"}'),
(26,	18,	5,	'2021-04-09 09:49:56',	NULL,	1,	'{\"name\":\"Changement date\",\"filename\":\"TEST EN PDF.pdf\",\"validStartAt\":\"2021-04-09 00:00:00\",\"validEndAt\":\"2021-05-09 00:00:00\",\"isValid\":\"1\",\"createdAt\":\"2021-04-09 09:49:56\",\"porteeDocument\":\"Interlocuteur (1)\",\"TypeDocument\":\"CV (1)\",\"interlocutor\":\"Ghasan SUCCAR (1)\"}'),
(27,	18,	5,	'2021-04-09 09:50:08',	NULL,	2,	'{\"validStartAt\":[\"2021-04-09 00:00:00\",\"2021-04-12 00:00:00\"]}'),
(28,	4,	5,	'2021-04-09 09:53:31',	NULL,	2,	'{\"validStartAt\":[\"2021-04-09 00:00:00\",\"2021-04-15 00:00:00\"]}'),
(29,	19,	6,	'2021-04-09 10:42:57',	NULL,	1,	'{\"name\":\"TestMR\",\"filename\":\"TEST EN PDF.pdf\",\"validStartAt\":\"2021-04-04 00:00:00\",\"validEndAt\":\"2021-05-04 00:00:00\",\"isValid\":\"1\",\"createdAt\":\"2021-04-09 10:42:57\",\"porteeDocument\":\"\\u00c9tablissement (2)\",\"TypeDocument\":\"Normes de laboratoires (4)\",\"institution\":\"EtablisementA (2)\"}'),
(30,	20,	5,	'2021-04-09 16:06:50',	NULL,	1,	'{\"name\":\"Norme lab bis\",\"filename\":\"TEST EN PDF.pdf\",\"validStartAt\":\"2021-04-07 00:00:00\",\"validEndAt\":\"2021-05-07 00:00:00\",\"isValid\":\"1\",\"createdAt\":\"2021-04-09 16:06:50\",\"porteeDocument\":\"\\u00c9tablissement (2)\",\"TypeDocument\":\"Normes de laboratoires (4)\",\"institution\":\"CHU Saint-Hilaire (1)\"}'),
(31,	20,	5,	'2021-04-09 16:07:02',	NULL,	2,	'{\"filename\":[\"TEST EN PDF.pdf\",\"\"]}'),
(32,	20,	5,	'2021-04-09 16:07:13',	NULL,	2,	'{\"validStartAt\":[\"2021-04-07 00:00:00\",\"2021-04-06 00:00:00\"]}'),
(33,	5,	13,	'2021-04-12 11:24:04',	NULL,	2,	'{\"validStartAt\":[\"2021-01-01 00:00:00\",\"2021-02-01 00:00:00\"]}'),
(34,	5,	5,	'2021-04-12 15:29:57',	NULL,	2,	'{\"filename\":[\"Output.pdf\",\"\"]}'),
(35,	5,	13,	'2021-04-12 15:47:57',	NULL,	2,	'{\"filename\":[\"TEST EN PDF.pdf\",\"\"]}'),
(36,	11,	13,	'2021-04-12 16:00:13',	NULL,	2,	'{\"TypeDocument\":[\"BI (2)\",\"RCP (3)\"]}'),
(37,	11,	13,	'2021-04-12 16:00:22',	NULL,	2,	'{\"TypeDocument\":[\"RCP (3)\",\"BI (2)\"]}'),
(38,	21,	13,	'2021-04-12 16:03:55',	NULL,	1,	'{\"name\":\"notice sans pdf\",\"validStartAt\":\"2021-04-01 00:00:00\",\"validEndAt\":\"2021-05-01 00:00:00\",\"isValid\":\"1\",\"createdAt\":\"2021-04-12 16:03:55\",\"porteeDocument\":\"Medicament (3)\",\"TypeDocument\":\"RCP (3)\"}'),
(39,	21,	13,	'2021-04-12 16:04:18',	NULL,	2,	'{\"TypeDocument\":[\"RCP (3)\",\"BI (2)\"]}'),
(40,	22,	13,	'2021-04-13 09:52:42',	NULL,	1,	'{\"name\":\"Test MI\",\"validStartAt\":\"2021-04-01 00:00:00\",\"validEndAt\":\"2021-05-01 00:00:00\",\"isValid\":\"1\",\"createdAt\":\"2021-04-13 09:52:42\",\"porteeDocument\":\"\\u00c9tablissement (2)\",\"TypeDocument\":\"Normes de laboratoires (4)\",\"institution\":\"EtablisementA (2)\"}');

DROP TABLE IF EXISTS `drug`;
CREATE TABLE `drug` (
                        `id` int(11) NOT NULL AUTO_INCREMENT,
                        `treatment_type_id` int(11) DEFAULT NULL,
                        `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                        `is_valid` tinyint(1) NOT NULL,
                        `deleted_at` datetime DEFAULT NULL,
                        `created_at` datetime NOT NULL,
                        PRIMARY KEY (`id`),
                        KEY `IDX_43EB7A3EBD8A4E5F` (`treatment_type_id`),
                        CONSTRAINT `FK_43EB7A3EBD8A4E5F` FOREIGN KEY (`treatment_type_id`) REFERENCES `treatment_type` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `drug` (`id`, `treatment_type_id`, `name`, `is_valid`, `deleted_at`, `created_at`) VALUES
(1,	2,	'Doliprane',	1,	NULL,	'2021-04-08 09:45:09'),
(2,	2,	'Ibuprophène',	1,	NULL,	'2021-04-08 09:45:22'),
(3,	3,	'Spasfon',	1,	NULL,	'2021-04-08 15:03:11'),
(4,	1,	'chloroquine',	1,	NULL,	'2021-04-08 16:15:08'),
(5,	5,	'Cortisone',	0,	NULL,	'2021-04-09 10:38:15'),
(6,	1,	'Medoc test',	0,	NULL,	'2021-04-09 17:25:09');

DROP TABLE IF EXISTS `drug_audit_trail`;
CREATE TABLE `drug_audit_trail` (
                                    `id` int(11) NOT NULL AUTO_INCREMENT,
                                    `entity_id` int(11) NOT NULL,
                                    `user_id` int(11) DEFAULT NULL,
                                    `date` datetime NOT NULL,
                                    `reason` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                    `modif_type` smallint(6) NOT NULL COMMENT '1=insert, 2=update, 3=delete',
                                    `details` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:json)',
                                    PRIMARY KEY (`id`),
                                    KEY `IDX_221675AF81257D5D` (`entity_id`),
                                    KEY `IDX_221675AFA76ED395` (`user_id`),
                                    CONSTRAINT `FK_221675AF81257D5D` FOREIGN KEY (`entity_id`) REFERENCES `drug` (`id`),
                                    CONSTRAINT `FK_221675AFA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `drug_audit_trail` (`id`, `entity_id`, `user_id`, `date`, `reason`, `modif_type`, `details`) VALUES
(1,	1,	5,	'2021-04-08 09:45:09',	NULL,	1,	'{\"name\":\"Doliprane\",\"isValid\":\"\",\"createdAt\":\"2021-04-08 09:45:09\",\"TreatmentType\":\"M\\u00e9dicament exp\\u00e9rimental (1)\"}'),
(2,	2,	5,	'2021-04-08 09:45:22',	NULL,	1,	'{\"name\":\"Ibuproph\\u00e8ne\",\"isValid\":\"\",\"createdAt\":\"2021-04-08 09:45:22\",\"TreatmentType\":\"M\\u00e9dicament auxiliaire (2)\"}'),
(3,	2,	5,	'2021-04-08 10:13:02',	NULL,	2,	'{\"isValid\":[\"\",\"1\"]}'),
(5,	3,	6,	'2021-04-08 15:03:11',	NULL,	1,	'{\"name\":\"Spasfon\",\"isValid\":\"\",\"createdAt\":\"2021-04-08 15:03:11\",\"TreatmentType\":\"NA (6)\"}'),
(7,	3,	6,	'2021-04-08 15:53:23',	NULL,	2,	'{\"TreatmentType\":[\"NA (6)\",\"M\\u00e9dicament exp\\u00e9rimental (1)\"]}'),
(8,	3,	6,	'2021-04-08 15:54:14',	NULL,	2,	'{\"isValid\":[\"\",\"1\"]}'),
(9,	4,	10,	'2021-04-08 16:15:08',	NULL,	1,	'{\"name\":\"chloroquine\",\"isValid\":\"\",\"createdAt\":\"2021-04-08 16:15:08\",\"TreatmentType\":\"M\\u00e9dicament exp\\u00e9rimental (1)\"}'),
(10,	4,	10,	'2021-04-08 16:15:45',	NULL,	2,	'{\"isValid\":[\"\",\"1\"]}'),
(11,	1,	5,	'2021-04-09 09:44:01',	NULL,	2,	'{\"isValid\":[\"\",\"1\"]}'),
(12,	5,	5,	'2021-04-09 10:38:15',	NULL,	1,	'{\"name\":\"Cortisone\",\"isValid\":\"\",\"createdAt\":\"2021-04-09 10:38:15\",\"TreatmentType\":\"Chirurgie (5)\"}'),
(13,	1,	11,	'2021-04-09 17:22:43',	NULL,	2,	'{\"TreatmentType\":[\"M\\u00e9dicament exp\\u00e9rimental (1)\",\"M\\u00e9dicament auxiliaire (2)\"]}'),
(14,	6,	11,	'2021-04-09 17:25:09',	NULL,	1,	'{\"name\":\"Medoc test\",\"isValid\":\"\",\"createdAt\":\"2021-04-09 17:25:09\",\"TreatmentType\":\"M\\u00e9dicament exp\\u00e9rimental (1)\"}'),
(15,	3,	13,	'2021-04-12 16:03:01',	NULL,	2,	'{\"TreatmentType\":[\"M\\u00e9dicament exp\\u00e9rimental (1)\",\"NA (6)\"]}'),
(16,	3,	13,	'2021-04-13 10:01:24',	NULL,	2,	'{\"TreatmentType\":[\"NA (6)\",\"Placebo (3)\"]}');

DROP TABLE IF EXISTS `exam`;
CREATE TABLE `exam` (
                        `id` int(11) NOT NULL AUTO_INCREMENT,
                        `type_id` int(11) NOT NULL,
                        `project_id` int(11) NOT NULL,
                        `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                        `position` smallint(6) DEFAULT NULL,
                        `source_id` int(11) DEFAULT NULL,
                        `price` double DEFAULT NULL,
                        `ordre` smallint(6) NOT NULL,
                        `deleted_at` datetime DEFAULT NULL,
                        PRIMARY KEY (`id`),
                        KEY `IDX_38BBA6C6C54C8C93` (`type_id`),
                        KEY `IDX_38BBA6C6166D1F9C` (`project_id`),
                        CONSTRAINT `FK_38BBA6C6166D1F9C` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`),
                        CONSTRAINT `FK_38BBA6C6C54C8C93` FOREIGN KEY (`type_id`) REFERENCES `dl_exam_type` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `exam_audit_trail`;
CREATE TABLE `exam_audit_trail` (
                                    `id` int(11) NOT NULL AUTO_INCREMENT,
                                    `entity_id` int(11) NOT NULL,
                                    `user_id` int(11) DEFAULT NULL,
                                    `date` datetime NOT NULL,
                                    `reason` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                    `modif_type` smallint(6) NOT NULL COMMENT '1=insert, 2=update, 3=delete',
                                    `details` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:json)',
                                    PRIMARY KEY (`id`),
                                    KEY `IDX_AE304C7981257D5D` (`entity_id`),
                                    KEY `IDX_AE304C79A76ED395` (`user_id`),
                                    CONSTRAINT `FK_AE304C7981257D5D` FOREIGN KEY (`entity_id`) REFERENCES `exam` (`id`),
                                    CONSTRAINT `FK_AE304C79A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `exam_variable`;
CREATE TABLE `exam_variable` (
                                 `exam_id` int(11) NOT NULL,
                                 `patient_variable_id` int(11) NOT NULL,
                                 PRIMARY KEY (`exam_id`,`patient_variable_id`),
                                 KEY `IDX_8F29C8BE578D5E91` (`exam_id`),
                                 KEY `IDX_8F29C8BEC30041C1` (`patient_variable_id`),
                                 CONSTRAINT `FK_8F29C8BE578D5E91` FOREIGN KEY (`exam_id`) REFERENCES `exam` (`id`) ON DELETE CASCADE,
                                 CONSTRAINT `FK_8F29C8BEC30041C1` FOREIGN KEY (`patient_variable_id`) REFERENCES `patient_variable` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `funding`;
CREATE TABLE `funding` (
                           `id` int(11) NOT NULL AUTO_INCREMENT,
                           `call_project_id` int(11) NOT NULL,
                           `devise_id` int(11) NOT NULL,
                           `funder_id` int(11) NOT NULL,
                           `project_id` int(11) NOT NULL,
                           `public_funding` tinyint(1) DEFAULT NULL,
                           `comment` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                           `obtained_at` datetime NOT NULL,
                           `amount` double NOT NULL,
                           `demanded_at` datetime NOT NULL,
                           `deleted_at` datetime DEFAULT NULL,
                           PRIMARY KEY (`id`),
                           KEY `IDX_D30DD1D632F25E06` (`call_project_id`),
                           KEY `IDX_D30DD1D6F4445056` (`devise_id`),
                           KEY `IDX_D30DD1D66CC88588` (`funder_id`),
                           KEY `IDX_D30DD1D6166D1F9C` (`project_id`),
                           CONSTRAINT `FK_D30DD1D6166D1F9C` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`),
                           CONSTRAINT `FK_D30DD1D632F25E06` FOREIGN KEY (`call_project_id`) REFERENCES `dl_call_project` (`id`),
                           CONSTRAINT `FK_D30DD1D66CC88588` FOREIGN KEY (`funder_id`) REFERENCES `dl_funder` (`id`),
                           CONSTRAINT `FK_D30DD1D6F4445056` FOREIGN KEY (`devise_id`) REFERENCES `dl_device` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `funding_audit_trail`;
CREATE TABLE `funding_audit_trail` (
                                       `id` int(11) NOT NULL AUTO_INCREMENT,
                                       `entity_id` int(11) NOT NULL,
                                       `user_id` int(11) DEFAULT NULL,
                                       `date` datetime NOT NULL,
                                       `reason` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                       `modif_type` smallint(6) NOT NULL COMMENT '1=insert, 2=update, 3=delete',
                                       `details` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:json)',
                                       PRIMARY KEY (`id`),
                                       KEY `IDX_4EBA02BD81257D5D` (`entity_id`),
                                       KEY `IDX_4EBA02BDA76ED395` (`user_id`),
                                       CONSTRAINT `FK_4EBA02BD81257D5D` FOREIGN KEY (`entity_id`) REFERENCES `funding` (`id`),
                                       CONSTRAINT `FK_4EBA02BDA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `institution`;
CREATE TABLE `institution` (
                               `id` int(11) NOT NULL AUTO_INCREMENT,
                               `country_id` int(11) NOT NULL,
                               `country_department_id` int(11) DEFAULT NULL,
                               `institution_type_id` int(11) DEFAULT NULL,
                               `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                               `address1` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                               `address2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                               `city` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                               `finess` varchar(9) COLLATE utf8mb4_unicode_ci NOT NULL,
                               `siret` varchar(14) COLLATE utf8mb4_unicode_ci NOT NULL,
                               `postal_code` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                               `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                               `fax` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                               `email` varchar(55) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                               `deleted_at` datetime DEFAULT NULL,
                               PRIMARY KEY (`id`),
                               KEY `IDX_3A9F98E5F92F3E70` (`country_id`),
                               KEY `IDX_3A9F98E55D35271B` (`country_department_id`),
                               KEY `IDX_3A9F98E5A1B27A01` (`institution_type_id`),
                               CONSTRAINT `FK_3A9F98E55D35271B` FOREIGN KEY (`country_department_id`) REFERENCES `dl_country_department` (`id`),
                               CONSTRAINT `FK_3A9F98E5A1B27A01` FOREIGN KEY (`institution_type_id`) REFERENCES `dl_institution_type` (`id`),
                               CONSTRAINT `FK_3A9F98E5F92F3E70` FOREIGN KEY (`country_id`) REFERENCES `dl_country` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `institution` (`id`, `country_id`, `country_department_id`, `institution_type_id`, `name`, `address1`, `address2`, `city`, `finess`, `siret`, `postal_code`, `phone`, `fax`, `email`, `deleted_at`) VALUES
(1,	1,	8,	1,	'CHU Saint-Hilaire',	'52 avenue Leclerc',	'ASC B',	'Nantes',	'123456789',	'12345678912345',	'44000',	'0699999999',	'0699999999',	'chu_st_hilaire@yopmail.com',	NULL),
(2,	1,	32,	1,	'EtablisementA',	'1 Rue de la FAISANDERIE',	NULL,	'Poissy',	'123245643',	'45612387401204',	'78300',	NULL,	NULL,	NULL,	NULL);

DROP TABLE IF EXISTS `institution_audit_trail`;
CREATE TABLE `institution_audit_trail` (
                                           `id` int(11) NOT NULL AUTO_INCREMENT,
                                           `entity_id` int(11) NOT NULL,
                                           `user_id` int(11) DEFAULT NULL,
                                           `date` datetime NOT NULL,
                                           `reason` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                           `modif_type` smallint(6) NOT NULL COMMENT '1=insert, 2=update, 3=delete',
                                           `details` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:json)',
                                           PRIMARY KEY (`id`),
                                           KEY `IDX_D3C3AE4781257D5D` (`entity_id`),
                                           KEY `IDX_D3C3AE47A76ED395` (`user_id`),
                                           CONSTRAINT `FK_D3C3AE4781257D5D` FOREIGN KEY (`entity_id`) REFERENCES `institution` (`id`),
                                           CONSTRAINT `FK_D3C3AE47A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `institution_audit_trail` (`id`, `entity_id`, `user_id`, `date`, `reason`, `modif_type`, `details`) VALUES
(1,	1,	6,	'2021-04-07 19:47:40',	NULL,	1,	'{\"name\":\"CHU Saint-Hilaire\",\"address1\":\"52 avenue Leclerc\",\"address2\":\"ASC B\",\"city\":\"Nantes\",\"finess\":\"123456789\",\"siret\":\"12345678912345\",\"postalCode\":\"44000\",\"phone\":\"0699999999\",\"fax\":\"0699999999\",\"email\":\"chu_st_hilaire@yopmail.com\",\"country\":\"FR (1)\",\"countryDepartment\":\"Loire (8)\",\"institutionType\":\"AP (1)\"}'),
(2,	2,	10,	'2021-04-08 15:47:12',	NULL,	1,	'{\"name\":\"EtablisementA\",\"address1\":\"1 Rue de la FAISANDERIE\",\"city\":\"Poissy\",\"finess\":\"123245643\",\"siret\":\"45612387401204\",\"postalCode\":\"78300\",\"country\":\"FR (1)\",\"countryDepartment\":\"C\\u00f4tes d\'Armor (32)\",\"institutionType\":\"AP (1)\"}');

DROP TABLE IF EXISTS `institution_center`;
CREATE TABLE `institution_center` (
                                      `center_id` int(11) NOT NULL,
                                      `institution_id` int(11) NOT NULL,
                                      PRIMARY KEY (`center_id`,`institution_id`),
                                      KEY `IDX_D5AEB93D5932F377` (`center_id`),
                                      KEY `IDX_D5AEB93D10405986` (`institution_id`),
                                      CONSTRAINT `FK_D5AEB93D10405986` FOREIGN KEY (`institution_id`) REFERENCES `institution` (`id`) ON DELETE CASCADE,
                                      CONSTRAINT `FK_D5AEB93D5932F377` FOREIGN KEY (`center_id`) REFERENCES `center` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `institution_center` (`center_id`, `institution_id`) VALUES
(1,	1),
(2,	1);

DROP TABLE IF EXISTS `interlocutor`;
CREATE TABLE `interlocutor` (
                                `id` int(11) NOT NULL AUTO_INCREMENT,
                                `civility_id` int(11) NOT NULL,
                                `cooperator_id` int(11) DEFAULT NULL,
                                `job_id` int(11) NOT NULL,
                                `specialty_one_id` int(11) DEFAULT NULL,
                                `specialty_two_id` int(11) DEFAULT NULL,
                                `first_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
                                `last_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
                                `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
                                `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                `fax` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                `deleted_at` datetime DEFAULT NULL,
                                `rpps_number` varchar(11) COLLATE utf8mb4_unicode_ci NOT NULL,
                                `am_number` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                `created_at` datetime NOT NULL,
                                PRIMARY KEY (`id`),
                                UNIQUE KEY `UNIQ_F3FE3BF5E7927C74` (`email`),
                                KEY `IDX_F3FE3BF523D6A298` (`civility_id`),
                                KEY `IDX_F3FE3BF5D5492A24` (`cooperator_id`),
                                KEY `IDX_F3FE3BF5BE04EA9` (`job_id`),
                                KEY `IDX_F3FE3BF5C9E1D4D5` (`specialty_one_id`),
                                KEY `IDX_F3FE3BF5A2BD331A` (`specialty_two_id`),
                                CONSTRAINT `FK_F3FE3BF523D6A298` FOREIGN KEY (`civility_id`) REFERENCES `dl_civility` (`id`),
                                CONSTRAINT `FK_F3FE3BF5A2BD331A` FOREIGN KEY (`specialty_two_id`) REFERENCES `dl_participant_speciality` (`id`),
                                CONSTRAINT `FK_F3FE3BF5BE04EA9` FOREIGN KEY (`job_id`) REFERENCES `dl_participant_job` (`id`),
                                CONSTRAINT `FK_F3FE3BF5C9E1D4D5` FOREIGN KEY (`specialty_one_id`) REFERENCES `dl_participant_speciality` (`id`),
                                CONSTRAINT `FK_F3FE3BF5D5492A24` FOREIGN KEY (`cooperator_id`) REFERENCES `dl_cooperator` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `interlocutor` (`id`, `civility_id`, `cooperator_id`, `job_id`, `specialty_one_id`, `specialty_two_id`, `first_name`, `last_name`, `email`, `phone`, `fax`, `deleted_at`, `rpps_number`, `am_number`, `created_at`) VALUES
(1,	3,	3,	13,	5,	7,	'Ghasan',	'SUCCAR',	'gsuccar@yopmail.com',	'0699999999',	'0699999999',	NULL,	'12345678912',	NULL,	'2021-04-07 19:47:55');

DROP TABLE IF EXISTS `interlocutor_audit_trail`;
CREATE TABLE `interlocutor_audit_trail` (
                                            `id` int(11) NOT NULL AUTO_INCREMENT,
                                            `entity_id` int(11) NOT NULL,
                                            `user_id` int(11) DEFAULT NULL,
                                            `date` datetime NOT NULL,
                                            `reason` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                            `modif_type` smallint(6) NOT NULL COMMENT '1=insert, 2=update, 3=delete',
                                            `details` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:json)',
                                            PRIMARY KEY (`id`),
                                            KEY `IDX_9D0545AF81257D5D` (`entity_id`),
                                            KEY `IDX_9D0545AFA76ED395` (`user_id`),
                                            CONSTRAINT `FK_9D0545AF81257D5D` FOREIGN KEY (`entity_id`) REFERENCES `interlocutor` (`id`),
                                            CONSTRAINT `FK_9D0545AFA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `interlocutor_audit_trail` (`id`, `entity_id`, `user_id`, `date`, `reason`, `modif_type`, `details`) VALUES
(1,	1,	6,	'2021-04-07 19:47:55',	NULL,	1,	'{\"firstName\":\"Ghasan\",\"lastName\":\"SUCCAR\",\"email\":\"gsuccar@yopmail.com\",\"phone\":\"0699999999\",\"fax\":\"0699999999\",\"rppsNumber\":\"12345678912\",\"civility\":\"Dr. (3)\",\"cooperator\":\"FFCD (3)\",\"job\":\"M\\u00e9decin (13)\",\"specialtyOne\":\"Cardiologie (5)\",\"specialtyTwo\":\"Comit\\u00e9 Ethique (7)\",\"institutions\":[\"CHU Saint-Hilaire (1)\"]}');

DROP TABLE IF EXISTS `interlocutor_center`;
CREATE TABLE `interlocutor_center` (
                                       `id` int(11) NOT NULL AUTO_INCREMENT,
                                       `center_id` int(11) DEFAULT NULL,
                                       `interlocutor_id` int(11) NOT NULL,
                                       `service_id` int(11) DEFAULT NULL,
                                       `enabled_at` datetime DEFAULT NULL,
                                       `disabled_at` datetime DEFAULT NULL,
                                       `metas` longtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:json)',
                                       `source_id` int(11) DEFAULT NULL,
                                       `is_principal_investigator` tinyint(1) NOT NULL,
                                       PRIMARY KEY (`id`),
                                       KEY `IDX_82EFB9C25932F377` (`center_id`),
                                       KEY `IDX_82EFB9C2B3F944DB` (`interlocutor_id`),
                                       KEY `IDX_82EFB9C2ED5CA9E6` (`service_id`),
                                       CONSTRAINT `FK_82EFB9C25932F377` FOREIGN KEY (`center_id`) REFERENCES `center` (`id`),
                                       CONSTRAINT `FK_82EFB9C2B3F944DB` FOREIGN KEY (`interlocutor_id`) REFERENCES `interlocutor` (`id`),
                                       CONSTRAINT `FK_82EFB9C2ED5CA9E6` FOREIGN KEY (`service_id`) REFERENCES `service` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `interlocutor_center` (`id`, `center_id`, `interlocutor_id`, `service_id`, `enabled_at`, `disabled_at`, `metas`, `source_id`, `is_principal_investigator`) VALUES
(1,	1,	1,	1,	'2021-04-08 15:00:39',	NULL,	'[]',	NULL,	1),
(2,	2,	1,	1,	'2021-04-12 11:08:32',	NULL,	'[]',	NULL,	1);

DROP TABLE IF EXISTS `interlocutor_center_audit_trail`;
CREATE TABLE `interlocutor_center_audit_trail` (
                                                   `id` int(11) NOT NULL AUTO_INCREMENT,
                                                   `entity_id` int(11) NOT NULL,
                                                   `user_id` int(11) DEFAULT NULL,
                                                   `date` datetime NOT NULL,
                                                   `reason` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                                   `modif_type` smallint(6) NOT NULL COMMENT '1=insert, 2=update, 3=delete',
                                                   `details` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:json)',
                                                   PRIMARY KEY (`id`),
                                                   KEY `IDX_FD8D2B3A81257D5D` (`entity_id`),
                                                   KEY `IDX_FD8D2B3AA76ED395` (`user_id`),
                                                   CONSTRAINT `FK_FD8D2B3A81257D5D` FOREIGN KEY (`entity_id`) REFERENCES `interlocutor_center` (`id`),
                                                   CONSTRAINT `FK_FD8D2B3AA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `interlocutor_center_audit_trail` (`id`, `entity_id`, `user_id`, `date`, `reason`, `modif_type`, `details`) VALUES
(1,	1,	5,	'2021-04-08 15:00:39',	NULL,	1,	'{\"enabledAt\":\"2021-04-08 15:00:39\",\"metas\":\"\",\"isPrincipalInvestigator\":\"1\",\"service\":\"Cardiologie (1)\"}'),
(2,	2,	13,	'2021-04-12 11:08:32',	NULL,	1,	'{\"enabledAt\":\"2021-04-12 11:08:32\",\"metas\":\"\",\"isPrincipalInvestigator\":\"1\",\"service\":\"Cardiologie (1)\"}');

DROP TABLE IF EXISTS `interlocutor_institution`;
CREATE TABLE `interlocutor_institution` (
                                            `interlocutor_id` int(11) NOT NULL,
                                            `institution_id` int(11) NOT NULL,
                                            PRIMARY KEY (`interlocutor_id`,`institution_id`),
                                            KEY `IDX_12B93C32B3F944DB` (`interlocutor_id`),
                                            KEY `IDX_12B93C3210405986` (`institution_id`),
                                            CONSTRAINT `FK_12B93C3210405986` FOREIGN KEY (`institution_id`) REFERENCES `institution` (`id`) ON DELETE CASCADE,
                                            CONSTRAINT `FK_12B93C32B3F944DB` FOREIGN KEY (`interlocutor_id`) REFERENCES `interlocutor` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `interlocutor_institution` (`interlocutor_id`, `institution_id`) VALUES
(1,	1);

DROP TABLE IF EXISTS `meeting`;
CREATE TABLE `meeting` (
                           `id` int(11) NOT NULL AUTO_INCREMENT,
                           `object_id` int(11) NOT NULL,
                           `created_by_id` int(11) NOT NULL,
                           `project_id` int(11) NOT NULL,
                           `type` varchar(55) COLLATE utf8mb4_unicode_ci NOT NULL,
                           `started_at` datetime NOT NULL,
                           `report` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                           `updated_at` datetime DEFAULT NULL,
                           `duration` smallint(6) DEFAULT NULL,
                           `started_hour` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
                           `created_at` datetime NOT NULL,
                           `closed_at` datetime DEFAULT NULL,
                           PRIMARY KEY (`id`),
                           KEY `IDX_F515E139232D562B` (`object_id`),
                           KEY `IDX_F515E139B03A8386` (`created_by_id`),
                           KEY `IDX_F515E139166D1F9C` (`project_id`),
                           CONSTRAINT `FK_F515E139166D1F9C` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`),
                           CONSTRAINT `FK_F515E139232D562B` FOREIGN KEY (`object_id`) REFERENCES `dl_meeting_type` (`id`),
                           CONSTRAINT `FK_F515E139B03A8386` FOREIGN KEY (`created_by_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `meeting_user`;
CREATE TABLE `meeting_user` (
                                `meeting_id` int(11) NOT NULL,
                                `user_id` int(11) NOT NULL,
                                PRIMARY KEY (`meeting_id`,`user_id`),
                                KEY `IDX_61622E9B67433D9C` (`meeting_id`),
                                KEY `IDX_61622E9BA76ED395` (`user_id`),
                                CONSTRAINT `FK_61622E9B67433D9C` FOREIGN KEY (`meeting_id`) REFERENCES `meeting` (`id`) ON DELETE CASCADE,
                                CONSTRAINT `FK_61622E9BA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `messenger_messages`;
CREATE TABLE `messenger_messages` (
                                      `id` bigint(20) NOT NULL AUTO_INCREMENT,
                                      `body` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
                                      `headers` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
                                      `queue_name` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
                                      `created_at` datetime NOT NULL,
                                      `available_at` datetime NOT NULL,
                                      `delivered_at` datetime DEFAULT NULL,
                                      PRIMARY KEY (`id`),
                                      KEY `IDX_75EA56E0FB7336F0` (`queue_name`),
                                      KEY `IDX_75EA56E0E3BD61CE` (`available_at`),
                                      KEY `IDX_75EA56E016BA31DB` (`delivered_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `monitoring_report`;
CREATE TABLE `monitoring_report` (
                                     `id` int(11) NOT NULL AUTO_INCREMENT,
                                     `template_version_id` int(11) NOT NULL,
                                     `status_id` int(11) NOT NULL,
                                     `center_id` int(11) NOT NULL,
                                     `created_by_id` int(11) NOT NULL,
                                     `created_at` datetime NOT NULL,
                                     PRIMARY KEY (`id`),
                                     KEY `IDX_D7115FED697762CA` (`template_version_id`),
                                     KEY `IDX_D7115FED6BF700BD` (`status_id`),
                                     KEY `IDX_D7115FED5932F377` (`center_id`),
                                     KEY `IDX_D7115FEDB03A8386` (`created_by_id`),
                                     CONSTRAINT `FK_D7115FED5932F377` FOREIGN KEY (`center_id`) REFERENCES `center` (`id`),
                                     CONSTRAINT `FK_D7115FED697762CA` FOREIGN KEY (`template_version_id`) REFERENCES `monitoring_report_template_version` (`id`),
                                     CONSTRAINT `FK_D7115FED6BF700BD` FOREIGN KEY (`status_id`) REFERENCES `monitoring_report_status` (`id`),
                                     CONSTRAINT `FK_D7115FEDB03A8386` FOREIGN KEY (`created_by_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `monitoring_report_audit_trail`;
CREATE TABLE `monitoring_report_audit_trail` (
                                                 `id` int(11) NOT NULL AUTO_INCREMENT,
                                                 `entity_id` int(11) NOT NULL,
                                                 `user_id` int(11) DEFAULT NULL,
                                                 `date` datetime NOT NULL,
                                                 `reason` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                                 `modif_type` smallint(6) NOT NULL COMMENT '1=insert, 2=update, 3=delete',
                                                 `details` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:json)',
                                                 PRIMARY KEY (`id`),
                                                 KEY `IDX_6EE8BE4481257D5D` (`entity_id`),
                                                 KEY `IDX_6EE8BE44A76ED395` (`user_id`),
                                                 CONSTRAINT `FK_6EE8BE4481257D5D` FOREIGN KEY (`entity_id`) REFERENCES `monitoring_report` (`id`),
                                                 CONSTRAINT `FK_6EE8BE44A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `monitoring_report_status`;
CREATE TABLE `monitoring_report_status` (
                                            `id` int(11) NOT NULL AUTO_INCREMENT,
                                            `label` varchar(55) COLLATE utf8mb4_unicode_ci NOT NULL,
                                            `code` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
                                            `position` smallint(6) NOT NULL,
                                            PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `monitoring_report_status` (`id`, `label`, `code`, `position`) VALUES
(1,	'Status1',	'S1',	10),
(2,	'Status2',	'S2',	20);

DROP TABLE IF EXISTS `monitoring_report_template`;
CREATE TABLE `monitoring_report_template` (
                                              `id` int(11) NOT NULL AUTO_INCREMENT,
                                              `type_id` int(11) NOT NULL,
                                              `project_id` int(11) NOT NULL,
                                              PRIMARY KEY (`id`),
                                              KEY `IDX_304D66D8C54C8C93` (`type_id`),
                                              KEY `IDX_304D66D8166D1F9C` (`project_id`),
                                              CONSTRAINT `FK_304D66D8166D1F9C` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`),
                                              CONSTRAINT `FK_304D66D8C54C8C93` FOREIGN KEY (`type_id`) REFERENCES `dl_monitoring_report_type` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `monitoring_report_template_version`;
CREATE TABLE `monitoring_report_template_version` (
                                                      `id` int(11) NOT NULL AUTO_INCREMENT,
                                                      `monitoring_report_template_id` int(11) NOT NULL,
                                                      `status_id` int(11) NOT NULL,
                                                      `version` smallint(6) NOT NULL,
                                                      `created_at` datetime NOT NULL,
                                                      PRIMARY KEY (`id`),
                                                      KEY `IDX_E42D87FCDE115084` (`monitoring_report_template_id`),
                                                      KEY `IDX_E42D87FC6BF700BD` (`status_id`),
                                                      CONSTRAINT `FK_E42D87FC6BF700BD` FOREIGN KEY (`status_id`) REFERENCES `monitoring_report_template_version_status` (`id`),
                                                      CONSTRAINT `FK_E42D87FCDE115084` FOREIGN KEY (`monitoring_report_template_id`) REFERENCES `monitoring_report_template` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `monitoring_report_template_version_audit_trail`;
CREATE TABLE `monitoring_report_template_version_audit_trail` (
                                                                  `id` int(11) NOT NULL AUTO_INCREMENT,
                                                                  `entity_id` int(11) NOT NULL,
                                                                  `user_id` int(11) DEFAULT NULL,
                                                                  `date` datetime NOT NULL,
                                                                  `reason` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                                                  `modif_type` smallint(6) NOT NULL COMMENT '1=insert, 2=update, 3=delete',
                                                                  `details` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:json)',
                                                                  PRIMARY KEY (`id`),
                                                                  KEY `IDX_29BA888881257D5D` (`entity_id`),
                                                                  KEY `IDX_29BA8888A76ED395` (`user_id`),
                                                                  CONSTRAINT `FK_29BA888881257D5D` FOREIGN KEY (`entity_id`) REFERENCES `monitoring_report_template_version` (`id`),
                                                                  CONSTRAINT `FK_29BA8888A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `monitoring_report_template_version_status`;
CREATE TABLE `monitoring_report_template_version_status` (
                                                             `id` int(11) NOT NULL AUTO_INCREMENT,
                                                             `label` varchar(55) COLLATE utf8mb4_unicode_ci NOT NULL,
                                                             `code` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
                                                             `position` smallint(6) NOT NULL,
                                                             PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `monitoring_report_template_version_status` (`id`, `label`, `code`, `position`) VALUES
(1,	'Status1',	'S1',	10),
(2,	'Status2',	'S2',	20);

DROP TABLE IF EXISTS `patient`;
CREATE TABLE `patient` (
                           `id` int(11) NOT NULL AUTO_INCREMENT,
                           `center_id` int(11) DEFAULT NULL,
                           `project_id` int(11) NOT NULL,
                           `number` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
                           `consent_at` datetime DEFAULT NULL,
                           `inclusion_at` datetime DEFAULT NULL,
                           `deleted_at` datetime DEFAULT NULL,
                           PRIMARY KEY (`id`),
                           KEY `IDX_1ADAD7EB5932F377` (`center_id`),
                           KEY `IDX_1ADAD7EB166D1F9C` (`project_id`),
                           CONSTRAINT `FK_1ADAD7EB166D1F9C` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`),
                           CONSTRAINT `FK_1ADAD7EB5932F377` FOREIGN KEY (`center_id`) REFERENCES `center` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `patient_audit_trail`;
CREATE TABLE `patient_audit_trail` (
                                       `id` int(11) NOT NULL AUTO_INCREMENT,
                                       `entity_id` int(11) NOT NULL,
                                       `user_id` int(11) DEFAULT NULL,
                                       `date` datetime NOT NULL,
                                       `reason` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                       `modif_type` smallint(6) NOT NULL COMMENT '1=insert, 2=update, 3=delete',
                                       `details` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:json)',
                                       PRIMARY KEY (`id`),
                                       KEY `IDX_8520DE3781257D5D` (`entity_id`),
                                       KEY `IDX_8520DE37A76ED395` (`user_id`),
                                       CONSTRAINT `FK_8520DE3781257D5D` FOREIGN KEY (`entity_id`) REFERENCES `patient` (`id`),
                                       CONSTRAINT `FK_8520DE37A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `patient_data`;
CREATE TABLE `patient_data` (
                                `id` int(11) NOT NULL AUTO_INCREMENT,
                                `patient_id` int(11) DEFAULT NULL,
                                `variable_id` int(11) DEFAULT NULL,
                                `variable_value` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                                `iteration` smallint(6) NOT NULL,
                                `value` varchar(55) COLLATE utf8mb4_unicode_ci NOT NULL,
                                `importing` tinyint(1) NOT NULL DEFAULT 0,
                                `ordre` smallint(6) NOT NULL,
                                `deleted_at` datetime DEFAULT NULL,
                                `disabled_at` datetime DEFAULT NULL,
                                PRIMARY KEY (`id`),
                                KEY `IDX_2462BEAB6B899279` (`patient_id`),
                                KEY `IDX_2462BEABF3037E8E` (`variable_id`),
                                CONSTRAINT `FK_2462BEAB6B899279` FOREIGN KEY (`patient_id`) REFERENCES `patient` (`id`),
                                CONSTRAINT `FK_2462BEABF3037E8E` FOREIGN KEY (`variable_id`) REFERENCES `patient_variable` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `patient_data_audit_trail`;
CREATE TABLE `patient_data_audit_trail` (
                                            `id` int(11) NOT NULL AUTO_INCREMENT,
                                            `entity_id` int(11) NOT NULL,
                                            `user_id` int(11) DEFAULT NULL,
                                            `date` datetime NOT NULL,
                                            `reason` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                            `modif_type` smallint(6) NOT NULL COMMENT '1=insert, 2=update, 3=delete',
                                            `details` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:json)',
                                            PRIMARY KEY (`id`),
                                            KEY `IDX_E50811F781257D5D` (`entity_id`),
                                            KEY `IDX_E50811F7A76ED395` (`user_id`),
                                            CONSTRAINT `FK_E50811F781257D5D` FOREIGN KEY (`entity_id`) REFERENCES `patient_data` (`id`),
                                            CONSTRAINT `FK_E50811F7A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `patient_variable`;
CREATE TABLE `patient_variable` (
                                    `id` int(11) NOT NULL AUTO_INCREMENT,
                                    `project_id` int(11) DEFAULT NULL,
                                    `variable_type_id` int(11) NOT NULL,
                                    `exam_id` int(11) DEFAULT NULL,
                                    `variable_list_id` int(11) DEFAULT NULL,
                                    `label` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                                    `position` smallint(6) DEFAULT NULL,
                                    `source_id` varchar(55) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                    `has_patient` tinyint(1) DEFAULT NULL,
                                    `has_visit` tinyint(1) DEFAULT NULL,
                                    `is_visit` tinyint(1) DEFAULT NULL,
                                    `is_exam` tinyint(1) DEFAULT NULL,
                                    `is_variable` tinyint(1) DEFAULT NULL,
                                    `sys` tinyint(1) NOT NULL,
                                    `deleted_at` datetime DEFAULT NULL,
                                    PRIMARY KEY (`id`),
                                    KEY `IDX_FBC8DF28166D1F9C` (`project_id`),
                                    KEY `IDX_FBC8DF28ABA835F1` (`variable_type_id`),
                                    KEY `IDX_FBC8DF28578D5E91` (`exam_id`),
                                    KEY `IDX_FBC8DF28534AAFE9` (`variable_list_id`),
                                    CONSTRAINT `FK_FBC8DF28166D1F9C` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`),
                                    CONSTRAINT `FK_FBC8DF28534AAFE9` FOREIGN KEY (`variable_list_id`) REFERENCES `variable_list` (`id`),
                                    CONSTRAINT `FK_FBC8DF28578D5E91` FOREIGN KEY (`exam_id`) REFERENCES `exam` (`id`),
                                    CONSTRAINT `FK_FBC8DF28ABA835F1` FOREIGN KEY (`variable_type_id`) REFERENCES `variable_type` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `patient_variable` (`id`, `project_id`, `variable_type_id`, `exam_id`, `variable_list_id`, `label`, `position`, `source_id`, `has_patient`, `has_visit`, `is_visit`, `is_exam`, `is_variable`, `sys`, `deleted_at`) VALUES
(1,	1,	2,	NULL,	NULL,	'Date de signature du consentement',	NULL,	NULL,	1,	NULL,	NULL,	NULL,	1,	1,	NULL),
(2,	1,	2,	NULL,	NULL,	'Date d\'inclusion',	NULL,	NULL,	1,	NULL,	NULL,	NULL,	1,	1,	NULL),
(3,	2,	2,	NULL,	NULL,	'Date de signature du consentement',	NULL,	NULL,	1,	NULL,	NULL,	NULL,	1,	1,	NULL),
(4,	2,	2,	NULL,	NULL,	'Date d\'inclusion',	NULL,	NULL,	1,	NULL,	NULL,	NULL,	1,	1,	NULL),
(5,	3,	2,	NULL,	NULL,	'Date de signature du consentement',	NULL,	NULL,	1,	NULL,	NULL,	NULL,	1,	1,	NULL),
(6,	3,	2,	NULL,	NULL,	'Date d\'inclusion',	NULL,	NULL,	1,	NULL,	NULL,	NULL,	1,	1,	NULL);

DROP TABLE IF EXISTS `patient_variable_audit_trail`;
CREATE TABLE `patient_variable_audit_trail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entity_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `date` datetime NOT NULL,
  `reason` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `modif_type` smallint(6) NOT NULL COMMENT '1=insert, 2=update, 3=delete',
  `details` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:json)',
  PRIMARY KEY (`id`),
  KEY `IDX_16CFF1EF81257D5D` (`entity_id`),
  KEY `IDX_16CFF1EFA76ED395` (`user_id`),
  CONSTRAINT `FK_16CFF1EF81257D5D` FOREIGN KEY (`entity_id`) REFERENCES `patient_variable` (`id`),
  CONSTRAINT `FK_16CFF1EFA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `patient_variable_audit_trail` (`id`, `entity_id`, `user_id`, `date`, `reason`, `modif_type`, `details`) VALUES
(1,	1,	6,	'2021-04-07 19:51:20',	NULL,	1,	'{\"label\":\"Date de signature du consentement\",\"sys\":\"1\",\"variableType\":\"date (2)\"}'),
    (2,	2,	6,	'2021-04-07 19:51:20',	NULL,	1,	'{\"label\":\"Date d\'inclusion\",\"sys\":\"1\",\"variableType\":\"date (2)\"}'),
    (3,	3,	6,	'2021-04-07 20:15:49',	NULL,	1,	'{\"label\":\"Date de signature du consentement\",\"sys\":\"1\",\"variableType\":\"date (2)\"}'),
    (4,	4,	6,	'2021-04-07 20:15:49',	NULL,	1,	'{\"label\":\"Date d\'inclusion\",\"sys\":\"1\",\"variableType\":\"date (2)\"}'),
    (5,	5,	5,	'2021-04-08 09:32:19',	NULL,	1,	'{\"label\":\"Date de signature du consentement\",\"sys\":\"1\",\"variableType\":\"date (2)\"}'),
    (6,	6,	5,	'2021-04-08 09:32:19',	NULL,	1,	'{\"label\":\"Date d\'inclusion\",\"sys\":\"1\",\"variableType\":\"date (2)\"}');

    DROP TABLE IF EXISTS `phase_setting`;
    CREATE TABLE `phase_setting` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `project_id` int(11) NOT NULL,
    `ordre` smallint(6) NOT NULL,
    `position` int(11) NOT NULL,
    `label` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `deleted_at` datetime DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `IDX_8F1015F4166D1F9C` (`project_id`),
    CONSTRAINT `FK_8F1015F4166D1F9C` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


    DROP TABLE IF EXISTS `phase_setting_audit_trail`;
    CREATE TABLE `phase_setting_audit_trail` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `entity_id` int(11) NOT NULL,
    `user_id` int(11) DEFAULT NULL,
    `date` datetime NOT NULL,
    `reason` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `modif_type` smallint(6) NOT NULL COMMENT '1=insert, 2=update, 3=delete',
    `details` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:json)',
    PRIMARY KEY (`id`),
    KEY `IDX_EDF6A01281257D5D` (`entity_id`),
    KEY `IDX_EDF6A012A76ED395` (`user_id`),
    CONSTRAINT `FK_EDF6A01281257D5D` FOREIGN KEY (`entity_id`) REFERENCES `phase_setting` (`id`),
    CONSTRAINT `FK_EDF6A012A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


    DROP TABLE IF EXISTS `point`;
    CREATE TABLE `point` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `courbe_setting_id` int(11) DEFAULT NULL,
    `x` int(11) NOT NULL,
    `y` int(11) NOT NULL,
    PRIMARY KEY (`id`),
    KEY `IDX_B7A5F32458468CAE` (`courbe_setting_id`),
    CONSTRAINT `FK_B7A5F32458468CAE` FOREIGN KEY (`courbe_setting_id`) REFERENCES `courbe_setting` (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


    DROP TABLE IF EXISTS `point_audit_trail`;
    CREATE TABLE `point_audit_trail` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `entity_id` int(11) NOT NULL,
    `user_id` int(11) DEFAULT NULL,
    `date` datetime NOT NULL,
    `reason` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `modif_type` smallint(6) NOT NULL COMMENT '1=insert, 2=update, 3=delete',
    `details` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:json)',
    PRIMARY KEY (`id`),
    KEY `IDX_83FCDEAD81257D5D` (`entity_id`),
    KEY `IDX_83FCDEADA76ED395` (`user_id`),
    CONSTRAINT `FK_83FCDEAD81257D5D` FOREIGN KEY (`entity_id`) REFERENCES `point` (`id`),
    CONSTRAINT `FK_83FCDEADA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


    DROP TABLE IF EXISTS `portee_document_transverse`;
    CREATE TABLE `portee_document_transverse` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

    INSERT INTO `portee_document_transverse` (`id`, `name`, `code`) VALUES
    (1,	'Interlocuteur',	'INTERLOCUTOR'),
    (2,	'Établissement',	'INSTITUTION'),
    (3,	'Medicament',	'MEDICAMENT');

    DROP TABLE IF EXISTS `profile`;
    CREATE TABLE `profile` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `deleted_at` datetime DEFAULT NULL,
    `acronyme` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
    `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

    INSERT INTO `profile` (`id`, `deleted_at`, `acronyme`, `name`) VALUES
    (1,	NULL,	'ADM',	'Admin'),
    (2,	NULL,	'ARC',	'Attaché de Recherche Clinique'),
    (3,	NULL,	'CP',	'Chef de Projet'),
    (4,	NULL,	'QA',	'Assurance Qualité'),
    (5,	NULL,	'DM',	'Data Manager'),
    (6,	NULL,	'Dir OP',	'Directeur des Opérations'),
    (7,	NULL,	'INV',	'Investigateur'),
    (8,	NULL,	'VISU',	'Visualisation'),
    (9,	NULL,	'TMI',	'Test MI');

    DROP TABLE IF EXISTS `profile_audit_trail`;
    CREATE TABLE `profile_audit_trail` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `entity_id` int(11) NOT NULL,
    `user_id` int(11) DEFAULT NULL,
    `date` datetime NOT NULL,
    `reason` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `modif_type` smallint(6) NOT NULL COMMENT '1=insert, 2=update, 3=delete',
    `details` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:json)',
    PRIMARY KEY (`id`),
    KEY `IDX_A03EA70681257D5D` (`entity_id`),
    KEY `IDX_A03EA706A76ED395` (`user_id`),
    CONSTRAINT `FK_A03EA70681257D5D` FOREIGN KEY (`entity_id`) REFERENCES `profile` (`id`),
    CONSTRAINT `FK_A03EA706A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

    INSERT INTO `profile_audit_trail` (`id`, `entity_id`, `user_id`, `date`, `reason`, `modif_type`, `details`) VALUES
    (1,	1,	NULL,	'2021-04-07 19:14:07',	NULL,	1,	'{\"acronyme\":\"ADM\",\"name\":\"Admin\",\"roles\":[\"ROLE_PROJECT_READ (1)\",\"ROLE_PROJECT_WRITE (2)\",\"ROLE_PROJECT_CLOSE_DEMAND (3)\",\"ROLE_PROJECT_CLOSE (4)\",\"ROLE_PROJECT_READ_CLOSED (5)\",\"ROLE_USER_READ (6)\",\"ROLE_USER_WRITE (7)\",\"ROLE_USER_ACCESS (8)\",\"ROLE_USER_ARCHIVE (9)\",\"ROLE_PROFILE_READ (10)\",\"ROLE_PROFILE_WRITE (11)\",\"ROLE_INSTITUTION_READ (12)\",\"ROLE_INSTITUTION_WRITE (13)\",\"ROLE_INSTITUTION_ARCHIVE (14)\",\"ROLE_INTERLOCUTOR_READ (15)\",\"ROLE_INTERLOCUTOR_WRITE (16)\",\"ROLE_INTERLOCUTOR_ARCHIVE (17)\",\"ROLE_SHOW_AUDIT_TRAIL (18)\",\"ROLE_PROJECT_SETTINGS_READ (19)\",\"ROLE_PROJECT_SETTINGS_WRITE (20)\",\"ROLE_DIAGRAMVISIT_READ (21)\",\"ROLE_DIAGRAMVISIT_WRITE (22)\",\"ROLE_ECRF_READ (23)\",\"ROLE_PROJECT_INTERVENANT_READ (24)\",\"ROLE_PROJECT_INTERVENANT_WRITE (25)\",\"ROLE_COMMUNICATION_READ (26)\",\"ROLE_COMMUNICATION_WRITE (27)\",\"ROLE_COMMUNICATION_DELETE (28)\",\"ROLE_PUBLICATION_READ (29)\",\"ROLE_PUBLICATION_WRITE (30)\",\"ROLE_PUBLICATION_ARCHIVE (31)\",\"ROLE_FUNDING_READ (32)\",\"ROLE_FUNDING_WRITE (33)\",\"ROLE_FUNDING_ARCHIVE (34)\",\"ROLE_DATE_READ (35)\",\"ROLE_DATE_WRITE (36)\",\"ROLE_SUBMISSION_READ (37)\",\"ROLE_SUBMISSION_WRITE (38)\",\"ROLE_SUBMISSION_ARCHIVE (39)\",\"ROLE_PROJECT_RULE_READ (40)\",\"ROLE_PROJECT_RULE_WRITE (41)\",\"ROLE_CENTER_READ (42)\",\"ROLE_CENTER_WRITE (43)\",\"ROLE_CENTER_ARCHIVE (44)\",\"ROLE_DOCUMENTTRACKING_READ (45)\",\"ROLE_DOCUMENTTRACKING_WRITE (46)\",\"ROLE_DOCUMENTTRACKING_ARCHIVE (47)\",\"ROLE_PATIENTTRACKING_READ (48)\",\"ROLE_PATIENTTRACKING_WRITE (49)\",\"ROLE_PATIENT_ARCHIVE (50)\",\"ROLE_VISIT_STATUS_WRITE (51)\",\"ROLE_PROJECT_INTERLOCUTOR_READ (52)\",\"ROLE_PROJECT_INTERLOCUTOR_WRITE (53)\",\"ROLE_DOCUMENTTRANSVERSE_READ (54)\",\"ROLE_DOCUMENTTRANSVERSE_WRITE (55)\",\"ROLE_DOCUMENTTRANSVERSE_ARCHIVE (56)\",\"ROLE_ECRF_READ (57)\",\"ROLE_DRUG_READ (58)\",\"ROLE_DRUG_WRITE (59)\",\"ROLE_DRUG_ARCHIVE (60)\"]}'),
    (2,	2,	NULL,	'2021-04-07 19:14:07',	NULL,	1,	'{\"acronyme\":\"ARC\",\"name\":\"Attach\\u00e9 de Recherche Clinique\",\"roles\":[\"ROLE_PROJECT_READ (1)\",\"ROLE_PROJECT_WRITE (2)\"]}'),
    (3,	3,	NULL,	'2021-04-07 19:14:07',	NULL,	1,	'{\"acronyme\":\"CP\",\"name\":\"Chef de Projet\",\"roles\":[\"ROLE_PROJECT_READ (1)\",\"ROLE_PROJECT_WRITE (2)\",\"ROLE_PROJECT_CLOSE_DEMAND (3)\",\"ROLE_PROJECT_READ_CLOSED (5)\"]}'),
    (4,	4,	NULL,	'2021-04-07 19:14:07',	NULL,	1,	'{\"acronyme\":\"QA\",\"name\":\"Assurance Qualit\\u00e9\",\"roles\":[\"ROLE_PROJECT_READ (1)\",\"ROLE_PROJECT_WRITE (2)\"]}'),
    (5,	5,	NULL,	'2021-04-07 19:14:07',	NULL,	1,	'{\"acronyme\":\"DM\",\"name\":\"Data Manager\",\"roles\":[\"ROLE_PROJECT_READ (1)\",\"ROLE_PROJECT_WRITE (2)\"]}'),
    (6,	6,	NULL,	'2021-04-07 19:14:07',	NULL,	1,	'{\"acronyme\":\"Dir OP\",\"name\":\"Directeur des Op\\u00e9rations\",\"roles\":[\"ROLE_PROJECT_READ (1)\",\"ROLE_PROJECT_WRITE (2)\"]}'),
    (7,	7,	NULL,	'2021-04-07 19:14:07',	NULL,	1,	'{\"acronyme\":\"INV\",\"name\":\"Investigateur\",\"roles\":[\"ROLE_PROJECT_READ (1)\",\"ROLE_PROJECT_WRITE (2)\"]}'),
    (8,	8,	5,	'2021-04-08 09:29:05',	NULL,	1,	'{\"acronyme\":\"VISU\",\"name\":\"Visualisation\",\"roles\":[\"ROLE_PROJECT_READ (1)\",\"ROLE_USER_READ (6)\",\"ROLE_PROFILE_READ (10)\",\"ROLE_INSTITUTION_READ (12)\",\"ROLE_INTERLOCUTOR_READ (15)\",\"ROLE_SHOW_AUDIT_TRAIL (18)\",\"ROLE_PROJECT_SETTINGS_READ (19)\",\"ROLE_DIAGRAMVISIT_READ (21)\",\"ROLE_PROJECT_INTERVENANT_READ (24)\",\"ROLE_COMMUNICATION_READ (26)\",\"ROLE_PUBLICATION_READ (29)\",\"ROLE_FUNDING_READ (32)\",\"ROLE_DATE_READ (35)\",\"ROLE_SUBMISSION_READ (37)\",\"ROLE_PROJECT_RULE_READ (40)\",\"ROLE_CENTER_READ (42)\",\"ROLE_DOCUMENTTRACKING_READ (45)\",\"ROLE_PATIENTTRACKING_READ (48)\",\"ROLE_PROJECT_INTERLOCUTOR_READ (52)\",\"ROLE_DOCUMENTTRANSVERSE_READ (54)\",\"ROLE_ECRF_READ (57)\",\"ROLE_DRUG_READ (58)\"]}'),
    (9,	8,	5,	'2021-04-08 16:29:45',	NULL,	2,	'{\"roles\":{\"added\":[],\"removed\":[\"ROLE_DOCUMENTTRANSVERSE_READ (54)\"]}}'),
    (10,	8,	5,	'2021-04-08 16:31:45',	NULL,	2,	'{\"roles\":{\"added\":[],\"removed\":[\"ROLE_INSTITUTION_READ (12)\"]}}'),
    (11,	8,	5,	'2021-04-08 16:32:15',	NULL,	2,	'{\"roles\":{\"added\":[\"ROLE_DOCUMENTTRANSVERSE_READ (54)\",\"ROLE_DOCUMENTTRANSVERSE_WRITE (55)\"],\"removed\":[]}}'),
    (12,	8,	5,	'2021-04-08 17:41:54',	NULL,	2,	'{\"roles\":{\"added\":[],\"removed\":[\"ROLE_PROJECT_SETTINGS_READ (19)\",\"ROLE_DIAGRAMVISIT_READ (21)\",\"ROLE_PROJECT_INTERVENANT_READ (24)\",\"ROLE_COMMUNICATION_READ (26)\",\"ROLE_PUBLICATION_READ (29)\",\"ROLE_FUNDING_READ (32)\",\"ROLE_DATE_READ (35)\",\"ROLE_SUBMISSION_READ (37)\",\"ROLE_PROJECT_RULE_READ (40)\",\"ROLE_CENTER_READ (42)\",\"ROLE_DOCUMENTTRACKING_READ (45)\",\"ROLE_PATIENTTRACKING_READ (48)\",\"ROLE_PROJECT_INTERLOCUTOR_READ (52)\",\"ROLE_DOCUMENTTRANSVERSE_READ (54)\",\"ROLE_DOCUMENTTRANSVERSE_WRITE (55)\",\"ROLE_ECRF_READ (57)\"]}}'),
    (13,	8,	5,	'2021-04-08 17:42:16',	NULL,	2,	'{\"roles\":{\"added\":[],\"removed\":[\"ROLE_PROJECT_READ (1)\"]}}'),
    (14,	8,	5,	'2021-04-08 17:42:35',	NULL,	2,	'{\"roles\":{\"added\":[\"ROLE_PROJECT_READ (1)\"],\"removed\":[]}}'),
    (15,	8,	5,	'2021-04-08 17:42:59',	NULL,	2,	'{\"roles\":{\"added\":[\"ROLE_DOCUMENTTRANSVERSE_READ (54)\"],\"removed\":[]}}'),
    (16,	8,	5,	'2021-04-09 17:21:17',	NULL,	2,	'{\"roles\":{\"added\":[\"ROLE_INSTITUTION_READ (12)\",\"ROLE_DRUG_WRITE (59)\"],\"removed\":[]}}'),
    (17,	8,	5,	'2021-04-09 17:25:56',	NULL,	2,	'{\"roles\":{\"added\":[\"ROLE_DRUG_ARCHIVE (60)\"],\"removed\":[\"ROLE_DRUG_WRITE (59)\"]}}'),
    (18,	8,	5,	'2021-04-09 17:27:37',	NULL,	2,	'{\"roles\":{\"added\":[\"ROLE_DRUG_WRITE (59)\"],\"removed\":[]}}'),
    (19,	9,	13,	'2021-04-13 09:21:40',	NULL,	1,	'{\"acronyme\":\"TMI\",\"name\":\"Test MI\",\"roles\":[\"ROLE_PROJECT_READ (1)\",\"ROLE_USER_READ (6)\",\"ROLE_PROFILE_READ (10)\",\"ROLE_INSTITUTION_READ (12)\",\"ROLE_INTERLOCUTOR_READ (15)\",\"ROLE_SHOW_AUDIT_TRAIL (18)\",\"ROLE_PROJECT_SETTINGS_READ (19)\",\"ROLE_PROJECT_INTERVENANT_READ (24)\",\"ROLE_COMMUNICATION_READ (26)\",\"ROLE_PUBLICATION_READ (29)\",\"ROLE_FUNDING_READ (32)\",\"ROLE_DATE_READ (35)\",\"ROLE_SUBMISSION_READ (37)\",\"ROLE_PROJECT_RULE_READ (40)\",\"ROLE_CENTER_READ (42)\",\"ROLE_DOCUMENTTRANSVERSE_READ (54)\",\"ROLE_ECRF_READ (57)\",\"ROLE_DRUG_READ (58)\"]}'),
    (20,	9,	13,	'2021-04-13 09:27:07',	NULL,	2,	'{\"roles\":{\"added\":[\"ROLE_DOCUMENTTRANSVERSE_WRITE (55)\"],\"removed\":[]}}'),
    (21,	9,	13,	'2021-04-13 09:30:55',	NULL,	2,	'{\"roles\":{\"added\":[],\"removed\":[\"ROLE_DOCUMENTTRANSVERSE_WRITE (55)\"]}}'),
    (22,	9,	13,	'2021-04-13 09:31:56',	NULL,	2,	'{\"roles\":{\"added\":[],\"removed\":[\"ROLE_DOCUMENTTRANSVERSE_READ (54)\"]}}');

    DROP TABLE IF EXISTS `profile_role`;
    CREATE TABLE `profile_role` (
    `profile_id` int(11) NOT NULL,
    `role_id` int(11) NOT NULL,
    PRIMARY KEY (`profile_id`,`role_id`),
    KEY `IDX_E1A105FECCFA12B8` (`profile_id`),
    KEY `IDX_E1A105FED60322AC` (`role_id`),
    CONSTRAINT `FK_E1A105FECCFA12B8` FOREIGN KEY (`profile_id`) REFERENCES `profile` (`id`) ON DELETE CASCADE,
    CONSTRAINT `FK_E1A105FED60322AC` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

    INSERT INTO `profile_role` (`profile_id`, `role_id`) VALUES
    (1,	1),
    (1,	2),
    (1,	3),
    (1,	4),
    (1,	5),
    (1,	6),
    (1,	7),
    (1,	8),
    (1,	9),
    (1,	10),
    (1,	11),
    (1,	12),
    (1,	13),
    (1,	14),
    (1,	15),
    (1,	16),
    (1,	17),
    (1,	18),
    (1,	19),
    (1,	20),
    (1,	21),
    (1,	22),
    (1,	23),
    (1,	24),
    (1,	25),
    (1,	26),
    (1,	27),
    (1,	28),
    (1,	29),
    (1,	30),
    (1,	31),
    (1,	32),
    (1,	33),
    (1,	34),
    (1,	35),
    (1,	36),
    (1,	37),
    (1,	38),
    (1,	39),
    (1,	40),
    (1,	41),
    (1,	42),
    (1,	43),
    (1,	44),
    (1,	45),
    (1,	46),
    (1,	47),
    (1,	48),
    (1,	49),
    (1,	50),
    (1,	51),
    (1,	52),
    (1,	53),
    (1,	54),
    (1,	55),
    (1,	56),
    (1,	57),
    (1,	58),
    (1,	59),
    (1,	60),
    (2,	1),
    (2,	2),
    (3,	1),
    (3,	2),
    (3,	3),
    (3,	5),
    (4,	1),
    (4,	2),
    (5,	1),
    (5,	2),
    (6,	1),
    (6,	2),
    (7,	1),
    (7,	2),
    (8,	1),
    (8,	6),
    (8,	10),
    (8,	12),
    (8,	15),
    (8,	18),
    (8,	54),
    (8,	58),
    (8,	59),
    (8,	60),
    (9,	1),
    (9,	6),
    (9,	10),
    (9,	12),
    (9,	15),
    (9,	18),
    (9,	19),
    (9,	24),
    (9,	26),
    (9,	29),
    (9,	32),
    (9,	35),
    (9,	37),
    (9,	40),
    (9,	42),
    (9,	57),
    (9,	58);

    DROP TABLE IF EXISTS `project`;
    CREATE TABLE `project` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `sponsor_id` int(11) DEFAULT NULL,
    `responsible_pm_id` int(11) DEFAULT NULL,
    `responsible_cra_id` int(11) DEFAULT NULL,
    `crf_type_id` int(11) NOT NULL,
    `closed_demand_by_id` int(11) DEFAULT NULL,
    `project_status_id` int(11) DEFAULT NULL,
    `study_population_id` int(11) DEFAULT NULL,
    `trail_type_id` int(11) NOT NULL,
    `trail_phase_id` int(11) NOT NULL,
    `patient_number_id` int(11) DEFAULT NULL,
    `trl_indice_id` int(11) DEFAULT NULL,
    `territory_id` int(11) DEFAULT NULL,
    `membership_group_id` int(11) DEFAULT NULL,
    `payment_unit_id` int(11) DEFAULT NULL,
    `delegation_id` int(11) DEFAULT NULL,
    `courbe_id` int(11) DEFAULT NULL,
    `app_token` varchar(55) COLLATE utf8mb4_unicode_ci NOT NULL,
    `name` varchar(55) COLLATE utf8mb4_unicode_ci NOT NULL,
    `protocole` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `has_etmf` tinyint(1) DEFAULT NULL,
    `crf_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `etmf_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `ref` varchar(55) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `created_at` datetime NOT NULL,
    `close_demanded_at` datetime DEFAULT NULL,
    `closed_at` datetime DEFAULT NULL,
    `logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `font_color` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL,
    `background_color` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL,
    `updated_at` datetime NOT NULL,
    `acronyme` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
    `name_english` varchar(55) COLLATE utf8mb4_unicode_ci NOT NULL,
    `nct_number` varchar(55) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `eudract_number` varchar(55) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `coordinating_investigators` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `metas_participant` longtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:json)',
    `metas_user` longtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:json)',
    PRIMARY KEY (`id`),
    UNIQUE KEY `UNIQ_2FB3D0EE5E237E06` (`name`),
    UNIQUE KEY `UNIQ_2FB3D0EEF3E736B0` (`acronyme`),
    UNIQUE KEY `UNIQ_2FB3D0EE24E847BE` (`name_english`),
    UNIQUE KEY `UNIQ_2FB3D0EE49C77A0` (`courbe_id`),
    KEY `IDX_2FB3D0EE12F7FB51` (`sponsor_id`),
    KEY `IDX_2FB3D0EE1DFA41B8` (`responsible_pm_id`),
    KEY `IDX_2FB3D0EEBF3493A8` (`responsible_cra_id`),
    KEY `IDX_2FB3D0EEAB525ED9` (`crf_type_id`),
    KEY `IDX_2FB3D0EE2E48DEE0` (`closed_demand_by_id`),
    KEY `IDX_2FB3D0EE7ACB456A` (`project_status_id`),
    KEY `IDX_2FB3D0EE79AA6B8A` (`study_population_id`),
    KEY `IDX_2FB3D0EE6F91E98E` (`trail_type_id`),
    KEY `IDX_2FB3D0EEFAA5A034` (`trail_phase_id`),
    KEY `IDX_2FB3D0EE962A0C2F` (`patient_number_id`),
    KEY `IDX_2FB3D0EE8150335D` (`trl_indice_id`),
    KEY `IDX_2FB3D0EE73F74AD4` (`territory_id`),
    KEY `IDX_2FB3D0EEE2B115B3` (`membership_group_id`),
    KEY `IDX_2FB3D0EEE1F47EE7` (`payment_unit_id`),
    KEY `IDX_2FB3D0EE56CBBCF5` (`delegation_id`),
    CONSTRAINT `FK_2FB3D0EE12F7FB51` FOREIGN KEY (`sponsor_id`) REFERENCES `dl_sponsor` (`id`),
    CONSTRAINT `FK_2FB3D0EE1DFA41B8` FOREIGN KEY (`responsible_pm_id`) REFERENCES `user` (`id`),
    CONSTRAINT `FK_2FB3D0EE2E48DEE0` FOREIGN KEY (`closed_demand_by_id`) REFERENCES `user` (`id`),
    CONSTRAINT `FK_2FB3D0EE49C77A0` FOREIGN KEY (`courbe_id`) REFERENCES `courbe_setting` (`id`),
    CONSTRAINT `FK_2FB3D0EE56CBBCF5` FOREIGN KEY (`delegation_id`) REFERENCES `delegation` (`id`),
    CONSTRAINT `FK_2FB3D0EE6F91E98E` FOREIGN KEY (`trail_type_id`) REFERENCES `dl_trail_type` (`id`),
    CONSTRAINT `FK_2FB3D0EE73F74AD4` FOREIGN KEY (`territory_id`) REFERENCES `dl_territory` (`id`),
    CONSTRAINT `FK_2FB3D0EE79AA6B8A` FOREIGN KEY (`study_population_id`) REFERENCES `dl_study_population` (`id`),
    CONSTRAINT `FK_2FB3D0EE7ACB456A` FOREIGN KEY (`project_status_id`) REFERENCES `dl_project_status` (`id`),
    CONSTRAINT `FK_2FB3D0EE8150335D` FOREIGN KEY (`trl_indice_id`) REFERENCES `dl_trl_indice` (`id`),
    CONSTRAINT `FK_2FB3D0EE962A0C2F` FOREIGN KEY (`patient_number_id`) REFERENCES `dl_patient_number` (`id`),
    CONSTRAINT `FK_2FB3D0EEAB525ED9` FOREIGN KEY (`crf_type_id`) REFERENCES `dl_crf_type` (`id`),
    CONSTRAINT `FK_2FB3D0EEBF3493A8` FOREIGN KEY (`responsible_cra_id`) REFERENCES `user` (`id`),
    CONSTRAINT `FK_2FB3D0EEE1F47EE7` FOREIGN KEY (`payment_unit_id`) REFERENCES `dl_payment_unit` (`id`),
    CONSTRAINT `FK_2FB3D0EEE2B115B3` FOREIGN KEY (`membership_group_id`) REFERENCES `dl_membership_group` (`id`),
    CONSTRAINT `FK_2FB3D0EEFAA5A034` FOREIGN KEY (`trail_phase_id`) REFERENCES `dl_trail_phase` (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

    INSERT INTO `project` (`id`, `sponsor_id`, `responsible_pm_id`, `responsible_cra_id`, `crf_type_id`, `closed_demand_by_id`, `project_status_id`, `study_population_id`, `trail_type_id`, `trail_phase_id`, `patient_number_id`, `trl_indice_id`, `territory_id`, `membership_group_id`, `payment_unit_id`, `delegation_id`, `courbe_id`, `app_token`, `name`, `protocole`, `has_etmf`, `crf_url`, `etmf_url`, `ref`, `created_at`, `close_demanded_at`, `closed_at`, `logo`, `font_color`, `background_color`, `updated_at`, `acronyme`, `name_english`, `nct_number`, `eudract_number`, `coordinating_investigators`, `metas_participant`, `metas_user`) VALUES
    (1,	3,	4,	3,	2,	NULL,	1,	2,	1,	1,	NULL,	NULL,	1,	NULL,	NULL,	1,	NULL,	'606df117e1d2a',	'Projet test 02',	NULL,	0,	NULL,	NULL,	NULL,	'2021-04-07 19:51:19',	NULL,	NULL,	NULL,	'#2c4d7a',	'#ffffff',	'2021-04-07 19:51:56',	'PT1',	'Projet test 02 EN',	NULL,	NULL,	NULL,	'[]',	'[]'),
    (2,	8,	8,	9,	2,	NULL,	1,	1,	1,	1,	NULL,	NULL,	1,	NULL,	NULL,	2,	NULL,	'606df6d5a702c',	'FFF',	NULL,	0,	NULL,	NULL,	'F',	'2021-04-07 20:15:49',	NULL,	NULL,	NULL,	'#2c4d7a',	'#ffffff',	'2021-04-07 20:15:49',	'F',	'F',	NULL,	NULL,	NULL,	'[]',	'[]'),
    (3,	6,	4,	2,	2,	NULL,	1,	NULL,	1,	1,	2,	NULL,	1,	NULL,	1,	3,	NULL,	'606eb182deab0',	'Projet Cadmus',	NULL,	0,	NULL,	NULL,	NULL,	'2021-04-08 09:32:18',	NULL,	NULL,	NULL,	'#2c4d7a',	'#ffffff',	'2021-04-08 09:32:42',	'CAD',	'Project Cadmus',	NULL,	NULL,	NULL,	'[]',	'[]');

    DROP TABLE IF EXISTS `project_audit_trail`;
    CREATE TABLE `project_audit_trail` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `entity_id` int(11) NOT NULL,
    `user_id` int(11) DEFAULT NULL,
    `date` datetime NOT NULL,
    `reason` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `modif_type` smallint(6) NOT NULL COMMENT '1=insert, 2=update, 3=delete',
    `details` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:json)',
    PRIMARY KEY (`id`),
    KEY `IDX_7DEC168381257D5D` (`entity_id`),
    KEY `IDX_7DEC1683A76ED395` (`user_id`),
    CONSTRAINT `FK_7DEC168381257D5D` FOREIGN KEY (`entity_id`) REFERENCES `project` (`id`),
    CONSTRAINT `FK_7DEC1683A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

    INSERT INTO `project_audit_trail` (`id`, `entity_id`, `user_id`, `date`, `reason`, `modif_type`, `details`) VALUES
    (1,	1,	6,	'2021-04-07 19:51:19',	NULL,	1,	'{\"name\":\"Projet test 01\",\"hasETMF\":\"\",\"fontColor\":\"#2c4d7a\",\"backgroundColor\":\"#ffffff\",\"acronyme\":\"PT1\",\"nameEnglish\":\"Projet test 01 EN\",\"sponsor\":\"ALLIANCE (3)\",\"responsiblePM\":\"CEC Cinfile CEC (4)\",\"responsibleCRA\":\"CDP Cinfile CDP (3)\",\"crfType\":\"Papier (2)\",\"projectStatus\":\"En discussion (1)\",\"studyPopulation\":\"Adulte (2)\",\"trailType\":\"RIPH1 (1)\",\"trailPhase\":\"I (1)\",\"territory\":\"France (1)\",\"delegation\":\" ()\",\"countries\":[\"FR (1)\"],\"trailTreatments\":[\"M\\u00e9dicament exp\\u00e9rimental (1)\"]}'),
    (2,	1,	6,	'2021-04-07 19:51:56',	NULL,	2,	'{\"name\":[\"Projet test 01\",\"Projet test 02\"],\"nameEnglish\":[\"Projet test 01 EN\",\"Projet test 02 EN\"],\"countries\":[\"FR (1)\"]}'),
    (3,	2,	6,	'2021-04-07 20:15:49',	NULL,	1,	'{\"name\":\"FFF\",\"hasETMF\":\"\",\"ref\":\"F\",\"fontColor\":\"#2c4d7a\",\"backgroundColor\":\"#ffffff\",\"acronyme\":\"F\",\"nameEnglish\":\"F\",\"sponsor\":\"SWOG (8)\",\"responsiblePM\":\"Matthias Perret (8)\",\"responsibleCRA\":\"Miary Rabehasy (9)\",\"crfType\":\"Papier (2)\",\"projectStatus\":\"En discussion (1)\",\"studyPopulation\":\"P\\u00e9diatrique (1)\",\"trailType\":\"RIPH1 (1)\",\"trailPhase\":\"I (1)\",\"territory\":\"France (1)\",\"delegation\":\" ()\",\"countries\":[\"FR (1)\"],\"trailTreatments\":[\"M\\u00e9dicament auxiliaire (2)\"]}'),
    (4,	3,	5,	'2021-04-08 09:32:18',	NULL,	1,	'{\"name\":\"Projet Cadmus\",\"hasETMF\":\"\",\"fontColor\":\"#2c4d7a\",\"backgroundColor\":\"#ffffff\",\"acronyme\":\"CAD\",\"nameEnglish\":\"Project Cadmus\",\"sponsor\":\"CURIE (6)\",\"responsiblePM\":\"CEC Cinfile CEC (4)\",\"responsibleCRA\":\"Dev2 Cinfile2 (2)\",\"crfType\":\"Papier (2)\",\"projectStatus\":\"En discussion (1)\",\"trailType\":\"RIPH1 (1)\",\"trailPhase\":\"I (1)\",\"patientNumber\":\"par centre (2)\",\"territory\":\"France (1)\",\"paymentUnit\":\"Patient (1)\",\"delegation\":\" ()\",\"countries\":[\"FR (1)\"],\"trailTreatments\":[\"M\\u00e9dicament auxiliaire (2)\"]}'),
    (5,	3,	5,	'2021-04-08 09:32:42',	NULL,	2,	'{\"countries\":{\"added\":[\"FR (1)\"],\"removed\":[]}}');

    DROP TABLE IF EXISTS `project_country`;
    CREATE TABLE `project_country` (
    `project_id` int(11) NOT NULL,
    `country_id` int(11) NOT NULL,
    PRIMARY KEY (`project_id`,`country_id`),
    KEY `IDX_C56DC503166D1F9C` (`project_id`),
    KEY `IDX_C56DC503F92F3E70` (`country_id`),
    CONSTRAINT `FK_C56DC503166D1F9C` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`) ON DELETE CASCADE,
    CONSTRAINT `FK_C56DC503F92F3E70` FOREIGN KEY (`country_id`) REFERENCES `dl_country` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

    INSERT INTO `project_country` (`project_id`, `country_id`) VALUES
    (1,	1),
    (2,	1),
    (3,	1);

    DROP TABLE IF EXISTS `project_database_freeze`;
    CREATE TABLE `project_database_freeze` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `project_date_id` int(11) DEFAULT NULL,
    `reason_id` int(11) DEFAULT NULL,
    `frozen_at` datetime NOT NULL,
    `other_reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `report_date` datetime DEFAULT NULL,
    `deleted_at` datetime DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `IDX_BABED3222E893A0E` (`project_date_id`),
    KEY `IDX_BABED32259BB1592` (`reason_id`),
    CONSTRAINT `FK_BABED3222E893A0E` FOREIGN KEY (`project_date_id`) REFERENCES `date` (`id`),
    CONSTRAINT `FK_BABED32259BB1592` FOREIGN KEY (`reason_id`) REFERENCES `dl_project_database_freeze_reason` (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


    DROP TABLE IF EXISTS `project_database_freeze_audit_trail`;
    CREATE TABLE `project_database_freeze_audit_trail` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `entity_id` int(11) NOT NULL,
    `user_id` int(11) DEFAULT NULL,
    `date` datetime NOT NULL,
    `reason` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `modif_type` smallint(6) NOT NULL COMMENT '1=insert, 2=update, 3=delete',
    `details` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:json)',
    PRIMARY KEY (`id`),
    KEY `IDX_C45C69D281257D5D` (`entity_id`),
    KEY `IDX_C45C69D2A76ED395` (`user_id`),
    CONSTRAINT `FK_C45C69D281257D5D` FOREIGN KEY (`entity_id`) REFERENCES `project_database_freeze` (`id`),
    CONSTRAINT `FK_C45C69D2A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


    DROP TABLE IF EXISTS `project_drug`;
    CREATE TABLE `project_drug` (
    `drug_id` int(11) NOT NULL,
    `project_id` int(11) NOT NULL,
    PRIMARY KEY (`drug_id`,`project_id`),
    KEY `IDX_7A7AB226AABCA765` (`drug_id`),
    KEY `IDX_7A7AB226166D1F9C` (`project_id`),
    CONSTRAINT `FK_7A7AB226166D1F9C` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`) ON DELETE CASCADE,
    CONSTRAINT `FK_7A7AB226AABCA765` FOREIGN KEY (`drug_id`) REFERENCES `drug` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


    DROP TABLE IF EXISTS `project_trail_treatment`;
    CREATE TABLE `project_trail_treatment` (
    `project_id` int(11) NOT NULL,
    `trail_treatment_id` int(11) NOT NULL,
    PRIMARY KEY (`project_id`,`trail_treatment_id`),
    KEY `IDX_62841AD7166D1F9C` (`project_id`),
    KEY `IDX_62841AD722DA564` (`trail_treatment_id`),
    CONSTRAINT `FK_62841AD7166D1F9C` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`) ON DELETE CASCADE,
    CONSTRAINT `FK_62841AD722DA564` FOREIGN KEY (`trail_treatment_id`) REFERENCES `dl_trail_treatment` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

    INSERT INTO `project_trail_treatment` (`project_id`, `trail_treatment_id`) VALUES
    (1,	1),
    (2,	2),
    (3,	2);

    DROP TABLE IF EXISTS `publication`;
    CREATE TABLE `publication` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `project_id` int(11) NOT NULL,
    `post_type_id` int(11) DEFAULT NULL,
    `is_congress_id` int(11) DEFAULT NULL,
    `journals_id` int(11) DEFAULT NULL,
    `congress_id` int(11) DEFAULT NULL,
    `communication_type_id` int(11) DEFAULT NULL,
    `comment` tinytext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `date` datetime NOT NULL,
    `post_number` varchar(55) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `deleted_at` datetime DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `IDX_AF3C6779166D1F9C` (`project_id`),
    KEY `IDX_AF3C6779F8A43BA0` (`post_type_id`),
    KEY `IDX_AF3C67796082566D` (`is_congress_id`),
    KEY `IDX_AF3C67799385281` (`journals_id`),
    KEY `IDX_AF3C6779B1E43B18` (`congress_id`),
    KEY `IDX_AF3C6779B09DA5C9` (`communication_type_id`),
    CONSTRAINT `FK_AF3C6779166D1F9C` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`),
    CONSTRAINT `FK_AF3C67796082566D` FOREIGN KEY (`is_congress_id`) REFERENCES `dl_is_congress` (`id`),
    CONSTRAINT `FK_AF3C67799385281` FOREIGN KEY (`journals_id`) REFERENCES `dl_journals` (`id`),
    CONSTRAINT `FK_AF3C6779B09DA5C9` FOREIGN KEY (`communication_type_id`) REFERENCES `dl_communication_type` (`id`),
    CONSTRAINT `FK_AF3C6779B1E43B18` FOREIGN KEY (`congress_id`) REFERENCES `dl_congress` (`id`),
    CONSTRAINT `FK_AF3C6779F8A43BA0` FOREIGN KEY (`post_type_id`) REFERENCES `dl_post_type` (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


    DROP TABLE IF EXISTS `publication_audit_trail`;
    CREATE TABLE `publication_audit_trail` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `entity_id` int(11) NOT NULL,
    `user_id` int(11) DEFAULT NULL,
    `date` datetime NOT NULL,
    `reason` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `modif_type` smallint(6) NOT NULL COMMENT '1=insert, 2=update, 3=delete',
    `details` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:json)',
    PRIMARY KEY (`id`),
    KEY `IDX_624044D381257D5D` (`entity_id`),
    KEY `IDX_624044D3A76ED395` (`user_id`),
    CONSTRAINT `FK_624044D381257D5D` FOREIGN KEY (`entity_id`) REFERENCES `publication` (`id`),
    CONSTRAINT `FK_624044D3A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


    DROP TABLE IF EXISTS `role`;
    CREATE TABLE `role` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `parent_id` int(11) DEFAULT NULL,
    `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `deleted_at` datetime DEFAULT NULL,
    `position` int(11) NOT NULL,
    PRIMARY KEY (`id`),
    KEY `IDX_57698A6A727ACA70` (`parent_id`),
    CONSTRAINT `FK_57698A6A727ACA70` FOREIGN KEY (`parent_id`) REFERENCES `role` (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

    INSERT INTO `role` (`id`, `parent_id`, `code`, `deleted_at`, `position`) VALUES
    (1,	NULL,	'ROLE_PROJECT_READ',	NULL,	10),
    (2,	1,	'ROLE_PROJECT_WRITE',	NULL,	15),
    (3,	1,	'ROLE_PROJECT_CLOSE_DEMAND',	NULL,	20),
    (4,	1,	'ROLE_PROJECT_CLOSE',	NULL,	25),
    (5,	1,	'ROLE_PROJECT_READ_CLOSED',	NULL,	30),
    (6,	NULL,	'ROLE_USER_READ',	NULL,	15),
    (7,	6,	'ROLE_USER_WRITE',	NULL,	20),
    (8,	6,	'ROLE_USER_ACCESS',	NULL,	25),
    (9,	6,	'ROLE_USER_ARCHIVE',	NULL,	30),
    (10,	NULL,	'ROLE_PROFILE_READ',	NULL,	20),
    (11,	10,	'ROLE_PROFILE_WRITE',	NULL,	25),
    (12,	NULL,	'ROLE_INSTITUTION_READ',	NULL,	25),
    (13,	12,	'ROLE_INSTITUTION_WRITE',	NULL,	30),
    (14,	12,	'ROLE_INSTITUTION_ARCHIVE',	NULL,	35),
    (15,	NULL,	'ROLE_INTERLOCUTOR_READ',	NULL,	30),
    (16,	15,	'ROLE_INTERLOCUTOR_WRITE',	NULL,	35),
    (17,	15,	'ROLE_INTERLOCUTOR_ARCHIVE',	NULL,	40),
    (18,	NULL,	'ROLE_SHOW_AUDIT_TRAIL',	NULL,	35),
    (19,	NULL,	'ROLE_PROJECT_SETTINGS_READ',	NULL,	40),
    (20,	19,	'ROLE_PROJECT_SETTINGS_WRITE',	NULL,	45),
    (21,	19,	'ROLE_DIAGRAMVISIT_READ',	NULL,	50),
    (22,	21,	'ROLE_DIAGRAMVISIT_WRITE',	NULL,	55),
    (23,	21,	'ROLE_ECRF_READ',	NULL,	60),
    (24,	NULL,	'ROLE_PROJECT_INTERVENANT_READ',	NULL,	45),
    (25,	24,	'ROLE_PROJECT_INTERVENANT_WRITE',	NULL,	50),
    (26,	NULL,	'ROLE_COMMUNICATION_READ',	NULL,	50),
    (27,	26,	'ROLE_COMMUNICATION_WRITE',	NULL,	55),
    (28,	26,	'ROLE_COMMUNICATION_DELETE',	NULL,	60),
    (29,	NULL,	'ROLE_PUBLICATION_READ',	NULL,	55),
    (30,	29,	'ROLE_PUBLICATION_WRITE',	NULL,	60),
    (31,	29,	'ROLE_PUBLICATION_ARCHIVE',	NULL,	65),
    (32,	NULL,	'ROLE_FUNDING_READ',	NULL,	60),
    (33,	32,	'ROLE_FUNDING_WRITE',	NULL,	65),
    (34,	32,	'ROLE_FUNDING_ARCHIVE',	NULL,	70),
    (35,	NULL,	'ROLE_DATE_READ',	NULL,	65),
    (36,	35,	'ROLE_DATE_WRITE',	NULL,	70),
    (37,	NULL,	'ROLE_SUBMISSION_READ',	NULL,	70),
    (38,	37,	'ROLE_SUBMISSION_WRITE',	NULL,	75),
    (39,	37,	'ROLE_SUBMISSION_ARCHIVE',	NULL,	80),
    (40,	NULL,	'ROLE_PROJECT_RULE_READ',	NULL,	75),
    (41,	40,	'ROLE_PROJECT_RULE_WRITE',	NULL,	80),
    (42,	NULL,	'ROLE_CENTER_READ',	NULL,	80),
    (43,	42,	'ROLE_CENTER_WRITE',	NULL,	85),
    (44,	42,	'ROLE_CENTER_ARCHIVE',	NULL,	90),
    (45,	42,	'ROLE_DOCUMENTTRACKING_READ',	NULL,	95),
    (46,	45,	'ROLE_DOCUMENTTRACKING_WRITE',	NULL,	100),
    (47,	45,	'ROLE_DOCUMENTTRACKING_ARCHIVE',	NULL,	105),
    (48,	42,	'ROLE_PATIENTTRACKING_READ',	NULL,	100),
    (49,	48,	'ROLE_PATIENTTRACKING_WRITE',	NULL,	105),
    (50,	48,	'ROLE_PATIENT_ARCHIVE',	NULL,	110),
    (51,	48,	'ROLE_VISIT_STATUS_WRITE',	NULL,	115),
    (52,	42,	'ROLE_PROJECT_INTERLOCUTOR_READ',	NULL,	105),
    (53,	52,	'ROLE_PROJECT_INTERLOCUTOR_WRITE',	NULL,	110),
    (54,	NULL,	'ROLE_DOCUMENTTRANSVERSE_READ',	NULL,	85),
    (55,	54,	'ROLE_DOCUMENTTRANSVERSE_WRITE',	NULL,	90),
    (56,	54,	'ROLE_DOCUMENTTRANSVERSE_ARCHIVE',	NULL,	95),
    (57,	NULL,	'ROLE_ECRF_READ',	NULL,	90),
    (58,	NULL,	'ROLE_DRUG_READ',	NULL,	95),
    (59,	58,	'ROLE_DRUG_WRITE',	NULL,	100),
    (60,	58,	'ROLE_DRUG_ARCHIVE',	NULL,	105);

    DROP TABLE IF EXISTS `rule`;
    CREATE TABLE `rule` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `formality_id` int(11) DEFAULT NULL,
    `study_transfer_territory_id` int(11) DEFAULT NULL,
    `out_transfer_territory_id` int(11) DEFAULT NULL,
    `project_id` int(11) NOT NULL,
    `conformity` tinyint(1) NOT NULL,
    `study_transfer` tinyint(1) NOT NULL,
    `out_study_transfer` tinyint(1) NOT NULL,
    `post` tinyint(1) NOT NULL,
    `partner` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `mapping` tinyint(1) NOT NULL,
    `reference` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `validate_mapping` tinyint(1) NOT NULL,
    `data_protection` tinyint(1) NOT NULL,
    `data_access` tinyint(1) NOT NULL,
    `e_tmf` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `IDX_46D8ACCCDCCF3332` (`formality_id`),
    KEY `IDX_46D8ACCCC0ADCACC` (`study_transfer_territory_id`),
    KEY `IDX_46D8ACCC8AD97712` (`out_transfer_territory_id`),
    KEY `IDX_46D8ACCC166D1F9C` (`project_id`),
    CONSTRAINT `FK_46D8ACCC166D1F9C` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`),
    CONSTRAINT `FK_46D8ACCC8AD97712` FOREIGN KEY (`out_transfer_territory_id`) REFERENCES `dl_rule_transfer_territory` (`id`),
    CONSTRAINT `FK_46D8ACCCC0ADCACC` FOREIGN KEY (`study_transfer_territory_id`) REFERENCES `dl_rule_transfer_territory` (`id`),
    CONSTRAINT `FK_46D8ACCCDCCF3332` FOREIGN KEY (`formality_id`) REFERENCES `dl_formality_rule` (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

    INSERT INTO `rule` (`id`, `formality_id`, `study_transfer_territory_id`, `out_transfer_territory_id`, `project_id`, `conformity`, `study_transfer`, `out_study_transfer`, `post`, `partner`, `mapping`, `reference`, `validate_mapping`, `data_protection`, `data_access`, `e_tmf`) VALUES
    (1,	NULL,	NULL,	NULL,	1,	0,	0,	0,	0,	NULL,	0,	NULL,	0,	0,	0,	NULL),
    (2,	NULL,	NULL,	NULL,	2,	0,	0,	0,	0,	NULL,	0,	NULL,	0,	0,	0,	NULL),
    (3,	1,	NULL,	NULL,	3,	0,	0,	0,	0,	NULL,	1,	'02415',	0,	0,	0,	'htpps://plop');

    DROP TABLE IF EXISTS `rule_audit_trail`;
    CREATE TABLE `rule_audit_trail` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `entity_id` int(11) NOT NULL,
    `user_id` int(11) DEFAULT NULL,
    `date` datetime NOT NULL,
    `reason` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `modif_type` smallint(6) NOT NULL COMMENT '1=insert, 2=update, 3=delete',
    `details` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:json)',
    PRIMARY KEY (`id`),
    KEY `IDX_A4195BA881257D5D` (`entity_id`),
    KEY `IDX_A4195BA8A76ED395` (`user_id`),
    CONSTRAINT `FK_A4195BA881257D5D` FOREIGN KEY (`entity_id`) REFERENCES `rule` (`id`),
    CONSTRAINT `FK_A4195BA8A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

    INSERT INTO `rule_audit_trail` (`id`, `entity_id`, `user_id`, `date`, `reason`, `modif_type`, `details`) VALUES
    (1,	1,	6,	'2021-04-07 19:51:20',	NULL,	1,	'{\"conformity\":\"\",\"studyTransfer\":\"\",\"outStudyTransfer\":\"\",\"post\":\"\",\"mapping\":\"\",\"validateMapping\":\"\",\"dataProtection\":\"\",\"dataAccess\":\"\",\"project\":\"Projet test 01 (1)\"}'),
    (2,	2,	6,	'2021-04-07 20:15:49',	NULL,	1,	'{\"conformity\":\"\",\"studyTransfer\":\"\",\"outStudyTransfer\":\"\",\"post\":\"\",\"mapping\":\"\",\"validateMapping\":\"\",\"dataProtection\":\"\",\"dataAccess\":\"\",\"project\":\"FFF (2)\"}'),
    (3,	3,	5,	'2021-04-08 09:32:19',	NULL,	1,	'{\"conformity\":\"\",\"studyTransfer\":\"\",\"outStudyTransfer\":\"\",\"post\":\"\",\"mapping\":\"\",\"validateMapping\":\"\",\"dataProtection\":\"\",\"dataAccess\":\"\",\"project\":\"Projet Cadmus (3)\"}'),
    (4,	3,	5,	'2021-04-13 15:50:52',	NULL,	2,	'{\"mapping\":[\"\",\"1\"],\"reference\":[\"\",\"02415\"],\"eTmf\":[\"\",\"htpps:\\/\\/plop\"],\"formality\":[\"\",\"MR01 (1)\"]}');

    DROP TABLE IF EXISTS `schema_condition`;
    CREATE TABLE `schema_condition` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `project_id` int(11) NOT NULL,
    `phase_id` int(11) DEFAULT NULL,
    `visit_id` int(11) DEFAULT NULL,
    `label` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `condition` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `deleted_at` datetime DEFAULT NULL,
    `disabled` tinyint(1) DEFAULT NULL,
    `phase_visit` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
    PRIMARY KEY (`id`),
    KEY `IDX_D169FE40166D1F9C` (`project_id`),
    KEY `IDX_D169FE4099091188` (`phase_id`),
    KEY `IDX_D169FE4075FA0FF2` (`visit_id`),
    CONSTRAINT `FK_D169FE40166D1F9C` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`),
    CONSTRAINT `FK_D169FE4075FA0FF2` FOREIGN KEY (`visit_id`) REFERENCES `visit` (`id`),
    CONSTRAINT `FK_D169FE4099091188` FOREIGN KEY (`phase_id`) REFERENCES `phase_setting` (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


    DROP TABLE IF EXISTS `schema_condition_patient_variable`;
    CREATE TABLE `schema_condition_patient_variable` (
    `schema_condition_id` int(11) NOT NULL,
    `patient_variable_id` int(11) NOT NULL,
    PRIMARY KEY (`schema_condition_id`,`patient_variable_id`),
    KEY `IDX_A16DF0ABA88A5DEE` (`schema_condition_id`),
    KEY `IDX_A16DF0ABC30041C1` (`patient_variable_id`),
    CONSTRAINT `FK_A16DF0ABA88A5DEE` FOREIGN KEY (`schema_condition_id`) REFERENCES `schema_condition` (`id`) ON DELETE CASCADE,
    CONSTRAINT `FK_A16DF0ABC30041C1` FOREIGN KEY (`patient_variable_id`) REFERENCES `patient_variable` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


    DROP TABLE IF EXISTS `service`;
    CREATE TABLE `service` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `institution_id` int(11) NOT NULL,
    `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `address2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `address_inherited` tinyint(1) NOT NULL,
    `city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `postal_code` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `deleted_at` datetime DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `IDX_E19D9AD210405986` (`institution_id`),
    CONSTRAINT `FK_E19D9AD210405986` FOREIGN KEY (`institution_id`) REFERENCES `institution` (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

    INSERT INTO `service` (`id`, `institution_id`, `name`, `address`, `address2`, `address_inherited`, `city`, `postal_code`, `deleted_at`) VALUES
    (1,	1,	'Cardiologie',	NULL,	NULL,	1,	NULL,	NULL,	NULL),
    (2,	1,	'Oncologie',	NULL,	NULL,	1,	NULL,	NULL,	NULL);

    DROP TABLE IF EXISTS `service_audit_trail`;
    CREATE TABLE `service_audit_trail` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `entity_id` int(11) NOT NULL,
    `user_id` int(11) DEFAULT NULL,
    `date` datetime NOT NULL,
    `reason` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `modif_type` smallint(6) NOT NULL COMMENT '1=insert, 2=update, 3=delete',
    `details` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:json)',
    PRIMARY KEY (`id`),
    KEY `IDX_79B9C7D481257D5D` (`entity_id`),
    KEY `IDX_79B9C7D4A76ED395` (`user_id`),
    CONSTRAINT `FK_79B9C7D481257D5D` FOREIGN KEY (`entity_id`) REFERENCES `service` (`id`),
    CONSTRAINT `FK_79B9C7D4A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

    INSERT INTO `service_audit_trail` (`id`, `entity_id`, `user_id`, `date`, `reason`, `modif_type`, `details`) VALUES
    (1,	1,	6,	'2021-04-07 19:47:41',	NULL,	1,	'{\"name\":\"Cardiologie\",\"addressInherited\":\"1\"}'),
    (2,	2,	6,	'2021-04-07 19:47:43',	NULL,	1,	'{\"name\":\"Oncologie\",\"addressInherited\":\"1\"}');

    DROP TABLE IF EXISTS `settings`;
    CREATE TABLE `settings` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `value` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

    INSERT INTO `settings` (`id`, `name`, `value`, `description`) VALUES
    (1,	'variable_date_id',	'2',	NULL),
    (2,	'job_cdp_id',	'8',	NULL),
    (3,	'job_cec_id',	'5',	NULL);

    DROP TABLE IF EXISTS `submission`;
    CREATE TABLE `submission` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `project_id` int(11) NOT NULL,
    `country_id` int(11) NOT NULL,
    `type_submission_regulatory_id` int(11) NOT NULL,
    `type_submission_id` int(11) NOT NULL,
    `type_declaration_id` int(11) DEFAULT NULL,
    `amendment_number` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
    `estimated_submission_at` datetime NOT NULL,
    `submission_at` datetime DEFAULT NULL,
    `question` tinyint(1) NOT NULL,
    `comment` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `file_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `admissibility_at` datetime DEFAULT NULL,
    `authorization_deadline_at` datetime DEFAULT NULL,
    `authorization_at` datetime DEFAULT NULL,
    `started_at` datetime DEFAULT NULL,
    `deleted_at` datetime DEFAULT NULL,
    `nameSubmissionRegulatory_id` int(11) DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `IDX_DB055AF3BC7F88FA` (`nameSubmissionRegulatory_id`),
    KEY `IDX_DB055AF3166D1F9C` (`project_id`),
    KEY `IDX_DB055AF3F92F3E70` (`country_id`),
    KEY `IDX_DB055AF3280F7253` (`type_submission_regulatory_id`),
    KEY `IDX_DB055AF39110FE06` (`type_submission_id`),
    KEY `IDX_DB055AF396A17137` (`type_declaration_id`),
    CONSTRAINT `FK_DB055AF3166D1F9C` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`),
    CONSTRAINT `FK_DB055AF3280F7253` FOREIGN KEY (`type_submission_regulatory_id`) REFERENCES `dl_submission_type_authority` (`id`),
    CONSTRAINT `FK_DB055AF39110FE06` FOREIGN KEY (`type_submission_id`) REFERENCES `dl_submission_type` (`id`),
    CONSTRAINT `FK_DB055AF396A17137` FOREIGN KEY (`type_declaration_id`) REFERENCES `dl_type_declaration` (`id`),
    CONSTRAINT `FK_DB055AF3BC7F88FA` FOREIGN KEY (`nameSubmissionRegulatory_id`) REFERENCES `dl_submission_name` (`id`),
    CONSTRAINT `FK_DB055AF3F92F3E70` FOREIGN KEY (`country_id`) REFERENCES `dl_country` (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

    INSERT INTO `submission` (`id`, `project_id`, `country_id`, `type_submission_regulatory_id`, `type_submission_id`, `type_declaration_id`, `amendment_number`, `estimated_submission_at`, `submission_at`, `question`, `comment`, `file_number`, `admissibility_at`, `authorization_deadline_at`, `authorization_at`, `started_at`, `deleted_at`, `nameSubmissionRegulatory_id`) VALUES
    (1,	3,	1,	1,	2,	NULL,	'155',	'2021-04-02 00:00:00',	'2021-04-01 00:00:00',	0,	'',	'6484145',	'2021-04-20 00:00:00',	'2021-05-30 00:00:00',	NULL,	NULL,	NULL,	1);

    DROP TABLE IF EXISTS `submission_audit_trail`;
    CREATE TABLE `submission_audit_trail` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `entity_id` int(11) NOT NULL,
    `user_id` int(11) DEFAULT NULL,
    `date` datetime NOT NULL,
    `reason` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `modif_type` smallint(6) NOT NULL COMMENT '1=insert, 2=update, 3=delete',
    `details` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:json)',
    PRIMARY KEY (`id`),
    KEY `IDX_32E7FE6281257D5D` (`entity_id`),
    KEY `IDX_32E7FE62A76ED395` (`user_id`),
    CONSTRAINT `FK_32E7FE6281257D5D` FOREIGN KEY (`entity_id`) REFERENCES `submission` (`id`),
    CONSTRAINT `FK_32E7FE62A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

    INSERT INTO `submission_audit_trail` (`id`, `entity_id`, `user_id`, `date`, `reason`, `modif_type`, `details`) VALUES
    (1,	1,	5,	'2021-04-13 15:53:25',	NULL,	1,	'{\"amendmentNumber\":\"155\",\"estimatedSubmissionAt\":\"2021-04-02 00:00:00\",\"submissionAt\":\"2021-04-01 00:00:00\",\"question\":\"\",\"comment\":\"\",\"fileNumber\":\"6484145\",\"admissibilityAt\":\"2021-04-20 00:00:00\",\"authorizationDeadlineAt\":\"2021-05-30 00:00:00\",\"nameSubmissionRegulatory\":\"TypeAuthorit\\u00e91 authorit\\u00e91 FR (1)\",\"project\":\"Projet Cadmus (3)\",\"country\":\"FR (1)\",\"typeSubmissionRegulatory\":\"TypeAuthorit\\u00e91 FR (1)\",\"typeSubmission\":\"Amendement (2)\"}');

    DROP TABLE IF EXISTS `terms_of_service`;
    CREATE TABLE `terms_of_service` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `published_at` date NOT NULL,
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

    INSERT INTO `terms_of_service` (`id`, `published_at`) VALUES
    (1,	'2021-04-07');

    DROP TABLE IF EXISTS `terms_of_service_signature`;
    CREATE TABLE `terms_of_service_signature` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    `terms_of_service_id` int(11) NOT NULL,
    `signed_at` datetime NOT NULL,
    PRIMARY KEY (`id`),
    KEY `IDX_AD1D461A76ED395` (`user_id`),
    KEY `IDX_AD1D461A5DEBC29` (`terms_of_service_id`),
    CONSTRAINT `FK_AD1D461A5DEBC29` FOREIGN KEY (`terms_of_service_id`) REFERENCES `terms_of_service` (`id`),
    CONSTRAINT `FK_AD1D461A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

    INSERT INTO `terms_of_service_signature` (`id`, `user_id`, `terms_of_service_id`, `signed_at`) VALUES
    (1,	1,	1,	'2021-04-07 19:15:51'),
    (2,	6,	1,	'2021-04-07 19:24:11'),
    (3,	5,	1,	'2021-04-08 09:28:12'),
    (4,	10,	1,	'2021-04-08 14:55:47'),
    (5,	11,	1,	'2021-04-08 16:32:05'),
    (6,	13,	1,	'2021-04-12 09:50:21'),
    (7,	14,	1,	'2021-04-13 09:22:46'),
    (8,	7,	1,	'2021-04-13 16:03:15');

    DROP TABLE IF EXISTS `training`;
    CREATE TABLE `training` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `project_id` int(11) NOT NULL,
    `type_id` int(11) DEFAULT NULL,
    `teacher_id` int(11) NOT NULL,
    `created_by_id` int(11) NOT NULL,
    `title` varchar(55) COLLATE utf8mb4_unicode_ci NOT NULL,
    `started_at` datetime NOT NULL,
    `ended_at` datetime NOT NULL,
    `duration` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `updated_at` datetime DEFAULT NULL,
    `material` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `started_hour` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL,
    `ended_hour` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL,
    `created_at` datetime NOT NULL,
    `closed_at` datetime DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `IDX_D5128A8F166D1F9C` (`project_id`),
    KEY `IDX_D5128A8FC54C8C93` (`type_id`),
    KEY `IDX_D5128A8F41807E1D` (`teacher_id`),
    KEY `IDX_D5128A8FB03A8386` (`created_by_id`),
    CONSTRAINT `FK_D5128A8F166D1F9C` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`),
    CONSTRAINT `FK_D5128A8F41807E1D` FOREIGN KEY (`teacher_id`) REFERENCES `user` (`id`),
    CONSTRAINT `FK_D5128A8FB03A8386` FOREIGN KEY (`created_by_id`) REFERENCES `user` (`id`),
    CONSTRAINT `FK_D5128A8FC54C8C93` FOREIGN KEY (`type_id`) REFERENCES `dl_training_type` (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


    DROP TABLE IF EXISTS `training_user`;
    CREATE TABLE `training_user` (
    `training_id` int(11) NOT NULL,
    `user_id` int(11) NOT NULL,
    PRIMARY KEY (`training_id`,`user_id`),
    KEY `IDX_8209910ABEFD98D1` (`training_id`),
    KEY `IDX_8209910AA76ED395` (`user_id`),
    CONSTRAINT `FK_8209910AA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE,
    CONSTRAINT `FK_8209910ABEFD98D1` FOREIGN KEY (`training_id`) REFERENCES `training` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


    DROP TABLE IF EXISTS `treatment_type`;
    CREATE TABLE `treatment_type` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

    INSERT INTO `treatment_type` (`id`, `name`) VALUES
    (1,	'Médicament expérimental'),
    (2,	'Médicament auxiliaire'),
    (3,	'Placebo'),
    (4,	'Radiothérapie'),
    (5,	'Chirurgie'),
    (6,	'NA');

    DROP TABLE IF EXISTS `type_document_transverse`;
    CREATE TABLE `type_document_transverse` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `days_count_valid` int(11) NOT NULL,
    `code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

    INSERT INTO `type_document_transverse` (`id`, `name`, `days_count_valid`, `code`) VALUES
    (1,	'CV',	30,	'CV'),
    (2,	'BI',	30,	'BI'),
    (3,	'RCP',	30,	'RCP'),
    (4,	'Normes de laboratoires',	30,	'NORMES_LABORATOIRE');

    DROP TABLE IF EXISTS `user`;
    CREATE TABLE `user` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `civility_id` int(11) DEFAULT NULL,
    `profile_id` int(11) NOT NULL,
    `job_id` int(11) NOT NULL,
    `department_id` int(11) DEFAULT NULL,
    `society_id` int(11) NOT NULL,
    `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
    `roles` longtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:json)',
    `password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `first_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
    `last_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
    `locale` varchar(2) COLLATE utf8mb4_unicode_ci NOT NULL,
    `is_super_admin` tinyint(1) DEFAULT NULL,
    `deleted_at` datetime DEFAULT NULL,
    `has_access_esm` tinyint(1) NOT NULL,
    `has_access_etmf` tinyint(1) NOT NULL,
    `phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `note` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `password_updated_at` datetime DEFAULT NULL,
    `reset_password_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `reset_password_at` datetime DEFAULT NULL,
    `is_bot` tinyint(1) NOT NULL,
    `two_factor_secret` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `UNIQ_8D93D649E7927C74` (`email`),
    KEY `IDX_8D93D64923D6A298` (`civility_id`),
    KEY `IDX_8D93D649CCFA12B8` (`profile_id`),
    KEY `IDX_8D93D649BE04EA9` (`job_id`),
    KEY `IDX_8D93D649AE80F5DF` (`department_id`),
    KEY `IDX_8D93D649E6389D24` (`society_id`),
    CONSTRAINT `FK_8D93D64923D6A298` FOREIGN KEY (`civility_id`) REFERENCES `dl_civility` (`id`),
    CONSTRAINT `FK_8D93D649AE80F5DF` FOREIGN KEY (`department_id`) REFERENCES `dl_department` (`id`),
    CONSTRAINT `FK_8D93D649BE04EA9` FOREIGN KEY (`job_id`) REFERENCES `dl_user_job` (`id`),
    CONSTRAINT `FK_8D93D649CCFA12B8` FOREIGN KEY (`profile_id`) REFERENCES `profile` (`id`),
    CONSTRAINT `FK_8D93D649E6389D24` FOREIGN KEY (`society_id`) REFERENCES `dl_society` (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

    INSERT INTO `user` (`id`, `civility_id`, `profile_id`, `job_id`, `department_id`, `society_id`, `email`, `roles`, `password`, `first_name`, `last_name`, `locale`, `is_super_admin`, `deleted_at`, `has_access_esm`, `has_access_etmf`, `phone`, `note`, `password_updated_at`, `reset_password_token`, `reset_password_at`, `is_bot`, `two_factor_secret`) VALUES
    (1,	2,	1,	1,	1,	1,	'dev@clinfile.com',	'[]',	'$argon2id$v=19$m=65536,t=4,p=1$CAnJbiJWdouz+nIlpNADRA$ibvbA3znJ0eifClU80HRv0BVXD3Sp5DAB5wQsnCMzp8',	'Dev',	'Cinfile',	'fr',	1,	NULL,	1,	1,	'0102030405',	'remarque...',	'2021-04-07 19:14:07',	NULL,	NULL,	0,	NULL),
    (2,	1,	3,	5,	1,	1,	'dev2@clinfile.com',	'[]',	'$argon2id$v=19$m=65536,t=4,p=1$7wzKRKfp8lV8DiUrp8King$mhyXYKk6X86V/8KfyaBv6fD9MP4ojejfu59aZLfJcr0',	'Dev2',	'Cinfile2',	'fr',	NULL,	NULL,	1,	0,	'0102030405',	'remarque...',	'2021-04-07 19:14:07',	NULL,	NULL,	0,	NULL),
    (3,	1,	3,	5,	1,	1,	'cdp@clinfile.com',	'[]',	'$argon2id$v=19$m=65536,t=4,p=1$4e555mBrR7L2oUd/qIcvYw$UoL4gS6iWCsl//fXBsSQiqeAa3mHiBb13M7+0923zns',	'CDP',	'Cinfile CDP',	'fr',	NULL,	NULL,	1,	0,	'0102030405',	'remarque...',	'2021-04-07 19:14:07',	NULL,	NULL,	0,	NULL),
    (4,	1,	3,	8,	1,	1,	'cec@clinfile.com',	'[]',	'$argon2id$v=19$m=65536,t=4,p=1$TZTWB+YumOi34xJUHfjUBQ$qjFplpC8Im05CFgss6YrTesOBOF1wLoPFFGq93BsBa0',	'CEC',	'Cinfile CEC',	'fr',	NULL,	NULL,	1,	0,	'0102030405',	'remarque...',	'2021-04-07 19:14:07',	NULL,	NULL,	0,	NULL),
    (5,	1,	1,	1,	1,	1,	'clinfile-stagiaire3@clinfile.com',	'[]',	'$argon2id$v=19$m=65536,t=4,p=1$7wzKRKfp8lV8DiUrp8King$mhyXYKk6X86V/8KfyaBv6fD9MP4ojejfu59aZLfJcr0',	'Neïla',	'Sebaïs',	'fr',	NULL,	NULL,	1,	0,	'0102030405',	NULL,	'2021-04-07 19:14:07',	NULL,	NULL,	0,	NULL),
    (6,	2,	1,	1,	1,	1,	'rdurville@clinfile.com',	'[]',	'$argon2id$v=19$m=65536,t=4,p=1$7wzKRKfp8lV8DiUrp8King$mhyXYKk6X86V/8KfyaBv6fD9MP4ojejfu59aZLfJcr0',	'Raphaël',	'Durville',	'fr',	NULL,	NULL,	1,	0,	'0102030405',	NULL,	'2021-04-07 19:14:07',	NULL,	NULL,	0,	NULL),
    (7,	2,	1,	1,	1,	1,	'jgoncalves@clinfile.com',	'[]',	'$argon2id$v=19$m=65536,t=4,p=1$7wzKRKfp8lV8DiUrp8King$mhyXYKk6X86V/8KfyaBv6fD9MP4ojejfu59aZLfJcr0',	'Jordan',	'Gonçalves',	'fr',	NULL,	NULL,	1,	0,	'0102030405',	NULL,	'2021-04-07 19:14:07',	NULL,	NULL,	0,	NULL),
    (8,	2,	1,	8,	1,	1,	'mperret@clinfile.com',	'[]',	'$argon2id$v=19$m=65536,t=4,p=1$7wzKRKfp8lV8DiUrp8King$mhyXYKk6X86V/8KfyaBv6fD9MP4ojejfu59aZLfJcr0',	'Matthias',	'Perret',	'fr',	NULL,	NULL,	1,	0,	'0102030405',	NULL,	'2021-04-07 19:14:07',	NULL,	NULL,	0,	NULL),
    (9,	2,	1,	5,	1,	1,	'mrabehasy@clinfile.com',	'[]',	'$argon2id$v=19$m=65536,t=4,p=1$7wzKRKfp8lV8DiUrp8King$mhyXYKk6X86V/8KfyaBv6fD9MP4ojejfu59aZLfJcr0',	'Miary',	'Rabehasy',	'fr',	NULL,	NULL,	1,	0,	'0102030405',	NULL,	'2021-04-07 19:14:07',	NULL,	NULL,	0,	NULL),
    (10,	2,	1,	1,	1,	1,	'kyonis@clinfile.com',	'[]',	'$argon2id$v=19$m=65536,t=4,p=1$7wzKRKfp8lV8DiUrp8King$mhyXYKk6X86V/8KfyaBv6fD9MP4ojejfu59aZLfJcr0',	'Khadar',	'Yonis',	'fr',	NULL,	NULL,	1,	0,	'0102030405',	NULL,	'2021-04-07 19:14:07',	NULL,	NULL,	0,	NULL),
    (11,	1,	8,	1,	1,	1,	'nsebais@clinfile.com',	'[]',	'$argon2id$v=19$m=65536,t=4,p=1$7wzKRKfp8lV8DiUrp8King$mhyXYKk6X86V/8KfyaBv6fD9MP4ojejfu59aZLfJcr0',	'Neïla2',	'Sebaïs2',	'fr',	NULL,	NULL,	1,	0,	'0102030405',	NULL,	'2021-04-07 19:14:07',	NULL,	NULL,	0,	NULL),
    (12,	2,	3,	1,	1,	1,	'rdurville2@clinfile.com',	'[]',	'$argon2id$v=19$m=65536,t=4,p=1$7wzKRKfp8lV8DiUrp8King$mhyXYKk6X86V/8KfyaBv6fD9MP4ojejfu59aZLfJcr0',	'Raphaël2',	'Durville2',	'fr',	NULL,	NULL,	1,	0,	'0102030405',	NULL,	'2021-04-07 19:14:07',	NULL,	NULL,	0,	NULL),
    (13,	1,	1,	1,	1,	1,	'mipuy@clinfile.com',	'[]',	'$argon2id$v=19$m=65536,t=4,p=1$7wzKRKfp8lV8DiUrp8King$mhyXYKk6X86V/8KfyaBv6fD9MP4ojejfu59aZLfJcr0',	'Martin',	'IPUY',	'fr',	NULL,	NULL,	1,	0,	'0102030405',	NULL,	'2021-04-07 19:14:07',	NULL,	NULL,	0,	NULL),
    (14,	2,	9,	1,	1,	1,	'mipuy2@clinfile.com',	'[]',	'$argon2id$v=19$m=65536,t=4,p=1$7wzKRKfp8lV8DiUrp8King$mhyXYKk6X86V/8KfyaBv6fD9MP4ojejfu59aZLfJcr0',	'Martin2',	'IPUY2',	'fr',	NULL,	NULL,	1,	0,	'0102030405',	NULL,	'2021-04-07 19:14:07',	NULL,	NULL,	0,	NULL);

    DROP TABLE IF EXISTS `user_audit_trail`;
    CREATE TABLE `user_audit_trail` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `entity_id` int(11) NOT NULL,
    `user_id` int(11) DEFAULT NULL,
    `date` datetime NOT NULL,
    `reason` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `modif_type` smallint(6) NOT NULL COMMENT '1=insert, 2=update, 3=delete',
    `details` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:json)',
    PRIMARY KEY (`id`),
    KEY `IDX_1C3828ED81257D5D` (`entity_id`),
    KEY `IDX_1C3828EDA76ED395` (`user_id`),
    CONSTRAINT `FK_1C3828ED81257D5D` FOREIGN KEY (`entity_id`) REFERENCES `user` (`id`),
    CONSTRAINT `FK_1C3828EDA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

    INSERT INTO `user_audit_trail` (`id`, `entity_id`, `user_id`, `date`, `reason`, `modif_type`, `details`) VALUES
    (1,	1,	NULL,	'2021-04-07 19:14:07',	NULL,	1,	'{\"email\":\"dev@clinfile.com\",\"firstName\":\"Dev\",\"lastName\":\"Cinfile\",\"locale\":\"fr\",\"hasAccessEsm\":\"1\",\"hasAccessEtmf\":\"1\",\"phone\":\"0102030405\",\"note\":\"remarque...\",\"password_updated_at\":\"2021-04-07 19:14:07\",\"is_bot\":\"\",\"civility\":\"Mr. (2)\",\"profile\":\"Admin (1)\",\"job\":\"ARC (1)\",\"department\":\"Compta (1)\",\"society\":\"Soci\\u00e9t\\u00e9A (1)\"}'),
    (2,	2,	NULL,	'2021-04-07 19:14:07',	NULL,	1,	'{\"email\":\"dev2@clinfile.com\",\"firstName\":\"Dev2\",\"lastName\":\"Cinfile2\",\"locale\":\"fr\",\"hasAccessEsm\":\"1\",\"hasAccessEtmf\":\"\",\"phone\":\"0102030405\",\"note\":\"remarque...\",\"password_updated_at\":\"2021-04-07 19:14:07\",\"is_bot\":\"\",\"civility\":\"Ms. (1)\",\"profile\":\"Chef de Projet (3)\",\"job\":\"CEC (5)\",\"department\":\"Compta (1)\",\"society\":\"Soci\\u00e9t\\u00e9A (1)\"}'),
    (3,	3,	NULL,	'2021-04-07 19:14:07',	NULL,	1,	'{\"email\":\"cdp@clinfile.com\",\"firstName\":\"CDP\",\"lastName\":\"Cinfile CDP\",\"locale\":\"fr\",\"hasAccessEsm\":\"1\",\"hasAccessEtmf\":\"\",\"phone\":\"0102030405\",\"note\":\"remarque...\",\"password_updated_at\":\"2021-04-07 19:14:07\",\"is_bot\":\"\",\"civility\":\"Ms. (1)\",\"profile\":\"Chef de Projet (3)\",\"job\":\"CEC (5)\",\"department\":\"Compta (1)\",\"society\":\"Soci\\u00e9t\\u00e9A (1)\"}'),
    (4,	4,	NULL,	'2021-04-07 19:14:07',	NULL,	1,	'{\"email\":\"cec@clinfile.com\",\"firstName\":\"CEC\",\"lastName\":\"Cinfile CEC\",\"locale\":\"fr\",\"hasAccessEsm\":\"1\",\"hasAccessEtmf\":\"\",\"phone\":\"0102030405\",\"note\":\"remarque...\",\"password_updated_at\":\"2021-04-07 19:14:07\",\"is_bot\":\"\",\"civility\":\"Ms. (1)\",\"profile\":\"Chef de Projet (3)\",\"job\":\"CDP (8)\",\"department\":\"Compta (1)\",\"society\":\"Soci\\u00e9t\\u00e9A (1)\"}'),
    (5,	5,	NULL,	'2021-04-07 19:14:07',	NULL,	1,	'{\"email\":\"clinfile-stagiaire3@clinfile.com\",\"firstName\":\"Ne\\u00efla\",\"lastName\":\"Seba\\u00efs\",\"locale\":\"fr\",\"hasAccessEsm\":\"1\",\"hasAccessEtmf\":\"\",\"phone\":\"0102030405\",\"password_updated_at\":\"2021-04-07 19:14:07\",\"is_bot\":\"\",\"civility\":\"Ms. (1)\",\"profile\":\"Admin (1)\",\"job\":\"ARC (1)\",\"department\":\"Compta (1)\",\"society\":\"Soci\\u00e9t\\u00e9A (1)\"}'),
    (6,	6,	NULL,	'2021-04-07 19:14:07',	NULL,	1,	'{\"email\":\"rdurville@clinfile.com\",\"firstName\":\"Rapha\\u00ebl\",\"lastName\":\"Durville\",\"locale\":\"fr\",\"hasAccessEsm\":\"1\",\"hasAccessEtmf\":\"\",\"phone\":\"0102030405\",\"password_updated_at\":\"2021-04-07 19:14:07\",\"is_bot\":\"\",\"civility\":\"Mr. (2)\",\"profile\":\"Admin (1)\",\"job\":\"ARC (1)\",\"department\":\"Compta (1)\",\"society\":\"Soci\\u00e9t\\u00e9A (1)\"}'),
    (7,	7,	NULL,	'2021-04-07 19:14:07',	NULL,	1,	'{\"email\":\"jgoncalves@clinfile.com\",\"firstName\":\"Jordan\",\"lastName\":\"Gon\\u00e7alves\",\"locale\":\"fr\",\"hasAccessEsm\":\"1\",\"hasAccessEtmf\":\"\",\"phone\":\"0102030405\",\"password_updated_at\":\"2021-04-07 19:14:07\",\"is_bot\":\"\",\"civility\":\"Mr. (2)\",\"profile\":\"Admin (1)\",\"job\":\"ARC (1)\",\"department\":\"Compta (1)\",\"society\":\"Soci\\u00e9t\\u00e9A (1)\"}'),
    (8,	8,	NULL,	'2021-04-07 19:14:07',	NULL,	1,	'{\"email\":\"mperret@clinfile.com\",\"firstName\":\"Matthias\",\"lastName\":\"Perret\",\"locale\":\"fr\",\"hasAccessEsm\":\"1\",\"hasAccessEtmf\":\"\",\"phone\":\"0102030405\",\"password_updated_at\":\"2021-04-07 19:14:07\",\"is_bot\":\"\",\"civility\":\"Mr. (2)\",\"profile\":\"Admin (1)\",\"job\":\"ARC (1)\",\"department\":\"Compta (1)\",\"society\":\"Soci\\u00e9t\\u00e9A (1)\"}'),
    (9,	9,	NULL,	'2021-04-07 19:14:07',	NULL,	1,	'{\"email\":\"mrabehasy@clinfile.com\",\"firstName\":\"Miary\",\"lastName\":\"Rabehasy\",\"locale\":\"fr\",\"hasAccessEsm\":\"1\",\"hasAccessEtmf\":\"\",\"phone\":\"0102030405\",\"password_updated_at\":\"2021-04-07 19:14:07\",\"is_bot\":\"\",\"civility\":\"Mr. (2)\",\"profile\":\"Admin (1)\",\"job\":\"ARC (1)\",\"department\":\"Compta (1)\",\"society\":\"Soci\\u00e9t\\u00e9A (1)\"}'),
    (10,	10,	NULL,	'2021-04-07 19:14:07',	NULL,	1,	'{\"email\":\"kyonis@clinfile.com\",\"firstName\":\"Khadar\",\"lastName\":\"Yonis\",\"locale\":\"fr\",\"hasAccessEsm\":\"1\",\"hasAccessEtmf\":\"\",\"phone\":\"0102030405\",\"password_updated_at\":\"2021-04-07 19:14:07\",\"is_bot\":\"\",\"civility\":\"Mr. (2)\",\"profile\":\"Admin (1)\",\"job\":\"ARC (1)\",\"department\":\"Compta (1)\",\"society\":\"Soci\\u00e9t\\u00e9A (1)\"}'),
    (11,	11,	NULL,	'2021-04-07 19:14:07',	NULL,	1,	'{\"email\":\"nsebais@clinfile.com\",\"firstName\":\"Ne\\u00efla2\",\"lastName\":\"Seba\\u00efs2\",\"locale\":\"fr\",\"hasAccessEsm\":\"1\",\"hasAccessEtmf\":\"\",\"phone\":\"0102030405\",\"password_updated_at\":\"2021-04-07 19:14:07\",\"is_bot\":\"\",\"civility\":\"Ms. (1)\",\"profile\":\"Chef de Projet (3)\",\"job\":\"ARC (1)\",\"department\":\"Compta (1)\",\"society\":\"Soci\\u00e9t\\u00e9A (1)\"}'),
    (12,	12,	NULL,	'2021-04-07 19:14:07',	NULL,	1,	'{\"email\":\"rdurville2@clinfile.com\",\"firstName\":\"Rapha\\u00ebl2\",\"lastName\":\"Durville2\",\"locale\":\"fr\",\"hasAccessEsm\":\"1\",\"hasAccessEtmf\":\"\",\"phone\":\"0102030405\",\"password_updated_at\":\"2021-04-07 19:14:07\",\"is_bot\":\"\",\"civility\":\"Mr. (2)\",\"profile\":\"Chef de Projet (3)\",\"job\":\"ARC (1)\",\"department\":\"Compta (1)\",\"society\":\"Soci\\u00e9t\\u00e9A (1)\"}'),
    (13,	13,	NULL,	'2021-04-07 19:14:07',	NULL,	1,	'{\"email\":\"mipuy@clinfile.com\",\"firstName\":\"Martin\",\"lastName\":\"IPUY\",\"locale\":\"fr\",\"hasAccessEsm\":\"1\",\"hasAccessEtmf\":\"\",\"phone\":\"0102030405\",\"password_updated_at\":\"2021-04-07 19:14:07\",\"is_bot\":\"\",\"civility\":\"Ms. (1)\",\"profile\":\"Admin (1)\",\"job\":\"ARC (1)\",\"department\":\"Compta (1)\",\"society\":\"Soci\\u00e9t\\u00e9A (1)\"}'),
    (14,	14,	NULL,	'2021-04-07 19:14:07',	NULL,	1,	'{\"email\":\"mipuy2@clinfile.com\",\"firstName\":\"Martin2\",\"lastName\":\"IPUY2\",\"locale\":\"fr\",\"hasAccessEsm\":\"1\",\"hasAccessEtmf\":\"\",\"phone\":\"0102030405\",\"password_updated_at\":\"2021-04-07 19:14:07\",\"is_bot\":\"\",\"civility\":\"Mr. (2)\",\"profile\":\"Chef de Projet (3)\",\"job\":\"ARC (1)\",\"department\":\"Compta (1)\",\"society\":\"Soci\\u00e9t\\u00e9A (1)\"}'),
    (15,	8,	6,	'2021-04-07 20:06:26',	NULL,	2,	'{\"job\":[\"ARC (1)\",\"CDP (8)\"]}'),
    (16,	9,	6,	'2021-04-07 20:06:38',	NULL,	2,	'{\"job\":[\"ARC (1)\",\"CEC (5)\"]}'),
    (17,	11,	5,	'2021-04-08 09:29:15',	NULL,	2,	'{\"profile\":[\"Chef de Projet (3)\",\"Visualisation (8)\"]}'),
    (18,	14,	13,	'2021-04-13 09:21:54',	NULL,	2,	'{\"profile\":[\"Chef de Projet (3)\",\"Test MI (9)\"]}');

    DROP TABLE IF EXISTS `user_project`;
    CREATE TABLE `user_project` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    `project_id` int(11) NOT NULL,
    `enabled_at` datetime DEFAULT NULL,
    `disabled_at` datetime DEFAULT NULL,
    `source_id` int(11) DEFAULT NULL,
    `metas` longtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:json)',
    `rate` int(11) DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `IDX_77BECEE4A76ED395` (`user_id`),
    KEY `IDX_77BECEE4166D1F9C` (`project_id`),
    CONSTRAINT `FK_77BECEE4166D1F9C` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`),
    CONSTRAINT `FK_77BECEE4A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

    INSERT INTO `user_project` (`id`, `user_id`, `project_id`, `enabled_at`, `disabled_at`, `source_id`, `metas`, `rate`) VALUES
    (1,	6,	2,	'2021-04-08 10:38:04',	NULL,	NULL,	'[]',	NULL),
    (2,	6,	3,	'2021-04-08 10:38:04',	NULL,	NULL,	'[]',	NULL),
    (3,	6,	1,	'2021-04-08 10:38:04',	NULL,	NULL,	'[]',	NULL),
    (4,	12,	2,	'2021-04-08 10:38:04',	NULL,	NULL,	'[]',	NULL),
    (5,	12,	3,	'2021-04-08 10:38:04',	NULL,	NULL,	'[]',	NULL),
    (6,	12,	1,	'2021-04-08 10:38:04',	NULL,	NULL,	'[]',	NULL),
    (7,	5,	3,	'2021-04-08 15:00:07',	NULL,	NULL,	'[]',	NULL),
    (8,	11,	3,	'2021-04-08 15:00:07',	NULL,	NULL,	'[]',	NULL),
    (9,	13,	2,	'2021-04-12 09:52:31',	NULL,	NULL,	'[]',	NULL),
    (10,	13,	1,	'2021-04-12 09:52:31',	NULL,	NULL,	'[]',	NULL),
    (11,	14,	2,	'2021-04-12 09:52:31',	NULL,	NULL,	'[]',	NULL),
    (12,	14,	1,	'2021-04-12 09:52:31',	NULL,	NULL,	'[]',	NULL);

    DROP TABLE IF EXISTS `user_project_audit_trail`;
    CREATE TABLE `user_project_audit_trail` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `entity_id` int(11) NOT NULL,
    `user_id` int(11) DEFAULT NULL,
    `date` datetime NOT NULL,
    `reason` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `modif_type` smallint(6) NOT NULL COMMENT '1=insert, 2=update, 3=delete',
    `details` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:json)',
    PRIMARY KEY (`id`),
    KEY `IDX_572F79F181257D5D` (`entity_id`),
    KEY `IDX_572F79F1A76ED395` (`user_id`),
    CONSTRAINT `FK_572F79F181257D5D` FOREIGN KEY (`entity_id`) REFERENCES `user_project` (`id`),
    CONSTRAINT `FK_572F79F1A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

    INSERT INTO `user_project_audit_trail` (`id`, `entity_id`, `user_id`, `date`, `reason`, `modif_type`, `details`) VALUES
    (1,	1,	6,	'2021-04-08 10:38:04',	NULL,	1,	'{\"enabledAt\":\"2021-04-08 10:38:04\"}'),
    (2,	2,	6,	'2021-04-08 10:38:04',	NULL,	1,	'{\"enabledAt\":\"2021-04-08 10:38:04\"}'),
    (3,	3,	6,	'2021-04-08 10:38:04',	NULL,	1,	'{\"enabledAt\":\"2021-04-08 10:38:04\"}'),
    (4,	4,	6,	'2021-04-08 10:38:04',	NULL,	1,	'{\"enabledAt\":\"2021-04-08 10:38:04\"}'),
    (5,	5,	6,	'2021-04-08 10:38:04',	NULL,	1,	'{\"enabledAt\":\"2021-04-08 10:38:04\"}'),
    (6,	6,	6,	'2021-04-08 10:38:04',	NULL,	1,	'{\"enabledAt\":\"2021-04-08 10:38:04\"}'),
    (7,	7,	5,	'2021-04-08 15:00:07',	NULL,	1,	'{\"enabledAt\":\"2021-04-08 15:00:07\"}'),
    (8,	8,	5,	'2021-04-08 15:00:07',	NULL,	1,	'{\"enabledAt\":\"2021-04-08 15:00:07\"}'),
    (9,	9,	13,	'2021-04-12 09:52:31',	NULL,	1,	'{\"enabledAt\":\"2021-04-12 09:52:31\"}'),
    (10,	10,	13,	'2021-04-12 09:52:31',	NULL,	1,	'{\"enabledAt\":\"2021-04-12 09:52:31\"}'),
    (11,	11,	13,	'2021-04-12 09:52:31',	NULL,	1,	'{\"enabledAt\":\"2021-04-12 09:52:31\"}'),
    (12,	12,	13,	'2021-04-12 09:52:31',	NULL,	1,	'{\"enabledAt\":\"2021-04-12 09:52:31\"}');

    DROP TABLE IF EXISTS `variable_list`;
    CREATE TABLE `variable_list` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `label` varchar(55) COLLATE utf8mb4_unicode_ci NOT NULL,
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


    DROP TABLE IF EXISTS `variable_option`;
    CREATE TABLE `variable_option` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `list_id` int(11) DEFAULT NULL,
    `label` varchar(55) COLLATE utf8mb4_unicode_ci NOT NULL,
    `code` varchar(55) COLLATE utf8mb4_unicode_ci NOT NULL,
    PRIMARY KEY (`id`),
    KEY `IDX_D53985273DAE168B` (`list_id`),
    CONSTRAINT `FK_D53985273DAE168B` FOREIGN KEY (`list_id`) REFERENCES `variable_list` (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


    DROP TABLE IF EXISTS `variable_type`;
    CREATE TABLE `variable_type` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `label` varchar(55) COLLATE utf8mb4_unicode_ci NOT NULL,
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

    INSERT INTO `variable_type` (`id`, `label`) VALUES
    (1,	'numeric'),
    (2,	'date'),
    (3,	'string'),
    (4,	'list');

    DROP TABLE IF EXISTS `version_document_transverse`;
    CREATE TABLE `version_document_transverse` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `document_transverse_id` int(11) DEFAULT NULL,
    `version` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `filename1` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `valid_start_at` datetime DEFAULT NULL,
    `valid_end_at` datetime DEFAULT NULL,
    `is_valid` tinyint(1) NOT NULL,
    `created_at` datetime NOT NULL,
    `deleted_at` datetime DEFAULT NULL,
    `updated_at` datetime DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `IDX_A0628F92D36CBFC8` (`document_transverse_id`),
    CONSTRAINT `FK_A0628F92D36CBFC8` FOREIGN KEY (`document_transverse_id`) REFERENCES `document_transverse` (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

    INSERT INTO `version_document_transverse` (`id`, `document_transverse_id`, `version`, `filename1`, `valid_start_at`, `valid_end_at`, `is_valid`, `created_at`, `deleted_at`, `updated_at`) VALUES
    (1,	1,	'1',	'/tmp/phpHfLfJf',	'2021-04-09 00:00:00',	'2021-05-09 00:00:00',	0,	'2021-04-08 10:13:02',	NULL,	NULL),
    (2,	1,	'2',	'testenpdf-606ebd1ee11f9.pdf',	'2021-04-08 00:00:00',	'2021-05-08 00:00:00',	0,	'2021-04-08 10:21:50',	NULL,	NULL),
    (3,	1,	'3',	'testenpdf-606ebd65c524a.pdf',	'2021-04-06 00:00:00',	'2021-05-06 00:00:00',	0,	'2021-04-08 10:23:01',	NULL,	NULL),
    (4,	2,	'1',	'/tmp/phpY8ktcV',	'2021-04-08 00:00:00',	'2021-05-08 00:00:00',	0,	'2021-04-08 10:50:45',	NULL,	NULL),
    (5,	9,	'1',	'Output.pdf',	'2021-04-02 00:00:00',	'2021-05-02 00:00:00',	0,	'2021-04-08 15:54:14',	NULL,	'2021-04-08 15:54:14'),
    (6,	10,	'2',	'Output.pdf',	'2021-01-02 00:00:00',	'2021-02-01 00:00:00',	0,	'2021-04-08 15:55:08',	NULL,	'2021-04-08 15:55:08'),
    (7,	11,	'1.0',	'test.pdf',	'2021-04-07 00:00:00',	'2021-05-07 00:00:00',	0,	'2021-04-08 16:15:45',	NULL,	'2021-04-08 16:15:45'),
    (8,	11,	'2.0',	'TEST EN PDF.pdf',	'2021-04-14 00:00:00',	'2021-05-14 00:00:00',	0,	'2021-04-08 16:31:37',	NULL,	'2021-04-08 16:31:37'),
    (9,	13,	'2.0',	NULL,	'2021-01-13 00:00:00',	'2021-02-12 00:00:00',	0,	'2021-04-08 20:08:44',	NULL,	NULL),
    (10,	14,	'2.5',	NULL,	'2021-04-19 00:00:00',	'2021-05-19 00:00:00',	0,	'2021-04-08 20:09:12',	NULL,	NULL),
    (11,	15,	'2.5.3',	NULL,	'2021-04-06 00:00:00',	'2021-05-06 00:00:00',	1,	'2021-04-08 20:10:12',	NULL,	NULL),
    (12,	15,	'2.0',	NULL,	'2021-04-06 00:00:00',	'2021-05-06 00:00:00',	0,	'2021-04-08 20:10:26',	NULL,	NULL),
    (13,	16,	'1',	'TEST EN PDF.pdf',	'2021-04-08 00:00:00',	'2021-05-08 00:00:00',	1,	'2021-04-09 09:44:01',	NULL,	'2021-04-09 09:44:01'),
    (14,	17,	'1',	'TEST EN PDF.pdf',	'2021-04-13 00:00:00',	'2021-05-13 00:00:00',	0,	'2021-04-09 09:44:36',	NULL,	'2021-04-09 09:44:36'),
    (15,	16,	'2',	'TEST EN PDF.pdf',	'2021-04-20 00:00:00',	'2021-05-20 00:00:00',	0,	'2021-04-09 09:45:18',	NULL,	'2021-04-09 09:45:18'),
    (16,	11,	'test',	NULL,	'2021-04-01 00:00:00',	'2021-05-01 00:00:00',	0,	'2021-04-12 15:58:19',	NULL,	NULL),
    (17,	21,	'vegetal',	NULL,	'2021-04-01 00:00:00',	'2021-05-01 00:00:00',	1,	'2021-04-12 16:03:55',	NULL,	NULL);

    DROP TABLE IF EXISTS `version_document_transverse_audit_trail`;
    CREATE TABLE `version_document_transverse_audit_trail` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `entity_id` int(11) NOT NULL,
    `user_id` int(11) DEFAULT NULL,
    `date` datetime NOT NULL,
    `reason` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `modif_type` smallint(6) NOT NULL COMMENT '1=insert, 2=update, 3=delete',
    `details` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:json)',
    PRIMARY KEY (`id`),
    KEY `IDX_BED5D8FD81257D5D` (`entity_id`),
    KEY `IDX_BED5D8FDA76ED395` (`user_id`),
    CONSTRAINT `FK_BED5D8FD81257D5D` FOREIGN KEY (`entity_id`) REFERENCES `version_document_transverse` (`id`),
    CONSTRAINT `FK_BED5D8FDA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

    INSERT INTO `version_document_transverse_audit_trail` (`id`, `entity_id`, `user_id`, `date`, `reason`, `modif_type`, `details`) VALUES
    (1,	1,	5,	'2021-04-08 10:13:02',	NULL,	1,	'{\"version\":\"1\",\"filename1\":\"\\/tmp\\/phpHfLfJf\",\"validStartAt\":\"2021-04-09 00:00:00\",\"validEndAt\":\"2021-05-09 00:00:00\",\"isValid\":\"\",\"createdAt\":\"2021-04-08 10:13:02\",\"documentTransverse\":\"Notice ()\"}'),
    (2,	2,	5,	'2021-04-08 10:21:50',	NULL,	1,	'{\"version\":\"2\",\"filename1\":\"testenpdf-606ebd1ee11f9.pdf\",\"validStartAt\":\"2021-04-08 00:00:00\",\"validEndAt\":\"2021-05-08 00:00:00\",\"isValid\":\"\",\"createdAt\":\"2021-04-08 10:21:50\",\"documentTransverse\":\"Notice (1)\"}'),
    (3,	3,	5,	'2021-04-08 10:23:01',	NULL,	1,	'{\"version\":\"3\",\"filename1\":\"testenpdf-606ebd65c524a.pdf\",\"validStartAt\":\"2021-04-06 00:00:00\",\"validEndAt\":\"2021-05-06 00:00:00\",\"isValid\":\"\",\"createdAt\":\"2021-04-08 10:23:01\",\"documentTransverse\":\"Notice (1)\"}'),
    (4,	4,	5,	'2021-04-08 10:50:45',	NULL,	1,	'{\"version\":\"1\",\"filename1\":\"\\/tmp\\/phpY8ktcV\",\"validStartAt\":\"2021-04-08 00:00:00\",\"validEndAt\":\"2021-05-08 00:00:00\",\"isValid\":\"\",\"createdAt\":\"2021-04-08 10:50:45\",\"documentTransverse\":\"Notice bis ()\"}'),
    (5,	5,	6,	'2021-04-08 15:54:14',	NULL,	1,	'{\"version\":\"1\",\"filename1\":\"Output.pdf\",\"validStartAt\":\"2021-04-02 00:00:00\",\"validEndAt\":\"2021-05-02 00:00:00\",\"isValid\":\"\",\"createdAt\":\"2021-04-08 15:54:14\",\"documentTransverse\":\"BI Spasfon ()\"}'),
    (6,	6,	6,	'2021-04-08 15:55:08',	NULL,	1,	'{\"version\":\"2\",\"filename1\":\"Output.pdf\",\"validStartAt\":\"2021-01-02 00:00:00\",\"validEndAt\":\"2021-02-01 00:00:00\",\"isValid\":\"\",\"createdAt\":\"2021-04-08 15:55:08\",\"documentTransverse\":\"BI Spasfon invalide ()\"}'),
    (7,	7,	10,	'2021-04-08 16:15:45',	NULL,	1,	'{\"version\":\"1.0\",\"filename1\":\"test.pdf\",\"validStartAt\":\"2021-04-07 00:00:00\",\"validEndAt\":\"2021-05-07 00:00:00\",\"isValid\":\"\",\"createdAt\":\"2021-04-08 16:15:45\",\"documentTransverse\":\"Chloriquine - notice ()\"}'),
    (8,	8,	10,	'2021-04-08 16:31:37',	NULL,	1,	'{\"version\":\"2.0\",\"filename1\":\"TEST EN PDF.pdf\",\"validStartAt\":\"2021-04-14 00:00:00\",\"validEndAt\":\"2021-05-14 00:00:00\",\"isValid\":\"\",\"createdAt\":\"2021-04-08 16:31:37\",\"documentTransverse\":\"Chloriquine - notice (11)\"}'),
    (9,	9,	10,	'2021-04-08 20:08:44',	NULL,	1,	'{\"version\":\"2.0\",\"validStartAt\":\"2021-01-13 00:00:00\",\"validEndAt\":\"2021-02-12 00:00:00\",\"isValid\":\"\",\"createdAt\":\"2021-04-08 20:08:44\",\"documentTransverse\":\"notice en anglais ()\"}'),
    (10,	10,	10,	'2021-04-08 20:09:12',	NULL,	1,	'{\"version\":\"2.5\",\"validStartAt\":\"2021-04-19 00:00:00\",\"validEndAt\":\"2021-05-19 00:00:00\",\"isValid\":\"\",\"createdAt\":\"2021-04-08 20:09:12\",\"documentTransverse\":\"Notice - italien ()\"}'),
    (11,	11,	10,	'2021-04-08 20:10:12',	NULL,	1,	'{\"version\":\"2.5.3\",\"validStartAt\":\"2021-04-06 00:00:00\",\"validEndAt\":\"2021-05-06 00:00:00\",\"isValid\":\"1\",\"createdAt\":\"2021-04-08 20:10:12\",\"documentTransverse\":\"notice - russe ()\"}'),
    (12,	12,	10,	'2021-04-08 20:10:26',	NULL,	1,	'{\"version\":\"2.0\",\"validStartAt\":\"2021-04-06 00:00:00\",\"validEndAt\":\"2021-05-06 00:00:00\",\"isValid\":\"\",\"createdAt\":\"2021-04-08 20:10:26\",\"documentTransverse\":\"notice - russe (15)\"}'),
    (13,	13,	5,	'2021-04-09 09:44:01',	NULL,	1,	'{\"version\":\"1\",\"filename1\":\"TEST EN PDF.pdf\",\"validStartAt\":\"2021-04-08 00:00:00\",\"validEndAt\":\"2021-05-08 00:00:00\",\"isValid\":\"1\",\"createdAt\":\"2021-04-09 09:44:01\",\"documentTransverse\":\"Notice Doli ()\"}'),
    (14,	14,	5,	'2021-04-09 09:44:36',	NULL,	1,	'{\"version\":\"1\",\"filename1\":\"TEST EN PDF.pdf\",\"validStartAt\":\"2021-04-13 00:00:00\",\"validEndAt\":\"2021-05-13 00:00:00\",\"isValid\":\"\",\"createdAt\":\"2021-04-09 09:44:36\",\"documentTransverse\":\"Deux ()\"}'),
    (15,	15,	5,	'2021-04-09 09:45:18',	NULL,	1,	'{\"version\":\"2\",\"filename1\":\"TEST EN PDF.pdf\",\"validStartAt\":\"2021-04-20 00:00:00\",\"validEndAt\":\"2021-05-20 00:00:00\",\"isValid\":\"\",\"createdAt\":\"2021-04-09 09:45:18\",\"documentTransverse\":\"Notice Doli (16)\"}'),
    (16,	16,	13,	'2021-04-12 15:58:19',	NULL,	1,	'{\"version\":\"test\",\"validStartAt\":\"2021-04-01 00:00:00\",\"validEndAt\":\"2021-05-01 00:00:00\",\"isValid\":\"\",\"createdAt\":\"2021-04-12 15:58:19\",\"documentTransverse\":\"Chloriquine - notice (11)\"}'),
    (17,	17,	13,	'2021-04-12 16:03:55',	NULL,	1,	'{\"version\":\"vegetal\",\"validStartAt\":\"2021-04-01 00:00:00\",\"validEndAt\":\"2021-05-01 00:00:00\",\"isValid\":\"1\",\"createdAt\":\"2021-04-12 16:03:55\",\"documentTransverse\":\"notice sans pdf ()\"}');

    DROP TABLE IF EXISTS `visit`;
    CREATE TABLE `visit` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `phase_id` int(11) NOT NULL,
    `patient_variable_id` int(11) DEFAULT NULL,
    `project_id` int(11) NOT NULL,
    `short` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `label` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `ordre` smallint(6) NOT NULL,
    `position` int(11) NOT NULL,
    `delay` smallint(6) DEFAULT NULL,
    `delay_approx` smallint(6) DEFAULT NULL,
    `source_id` int(11) DEFAULT NULL,
    `price` smallint(6) NOT NULL,
    `deleted_at` datetime DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `IDX_437EE93999091188` (`phase_id`),
    KEY `IDX_437EE939C30041C1` (`patient_variable_id`),
    KEY `IDX_437EE939166D1F9C` (`project_id`),
    CONSTRAINT `FK_437EE939166D1F9C` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`),
    CONSTRAINT `FK_437EE93999091188` FOREIGN KEY (`phase_id`) REFERENCES `phase_setting` (`id`),
    CONSTRAINT `FK_437EE939C30041C1` FOREIGN KEY (`patient_variable_id`) REFERENCES `patient_variable` (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


    DROP TABLE IF EXISTS `visit_audit_trail`;
    CREATE TABLE `visit_audit_trail` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `entity_id` int(11) NOT NULL,
    `user_id` int(11) DEFAULT NULL,
    `date` datetime NOT NULL,
    `reason` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `modif_type` smallint(6) NOT NULL COMMENT '1=insert, 2=update, 3=delete',
    `details` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:json)',
    PRIMARY KEY (`id`),
    KEY `IDX_31F948D881257D5D` (`entity_id`),
    KEY `IDX_31F948D8A76ED395` (`user_id`),
    CONSTRAINT `FK_31F948D881257D5D` FOREIGN KEY (`entity_id`) REFERENCES `visit` (`id`),
    CONSTRAINT `FK_31F948D8A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


    DROP TABLE IF EXISTS `visit_patient`;
    CREATE TABLE `visit_patient` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `patient_id` int(11) NOT NULL,
    `visit_id` int(11) NOT NULL,
    `variable_id` int(11) DEFAULT NULL,
    `status_id` int(11) DEFAULT NULL,
    `iteration` int(11) NOT NULL,
    `source_id` int(11) DEFAULT NULL,
    `occured_at` datetime DEFAULT NULL,
    `monitored_at` datetime DEFAULT NULL,
    `deleted_at` datetime DEFAULT NULL,
    `disabled_at` datetime DEFAULT NULL,
    `badge` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `IDX_4C53D32C6B899279` (`patient_id`),
    KEY `IDX_4C53D32C75FA0FF2` (`visit_id`),
    KEY `IDX_4C53D32CF3037E8E` (`variable_id`),
    KEY `IDX_4C53D32C6BF700BD` (`status_id`),
    CONSTRAINT `FK_4C53D32C6B899279` FOREIGN KEY (`patient_id`) REFERENCES `patient` (`id`),
    CONSTRAINT `FK_4C53D32C6BF700BD` FOREIGN KEY (`status_id`) REFERENCES `visit_patient_status` (`id`),
    CONSTRAINT `FK_4C53D32C75FA0FF2` FOREIGN KEY (`visit_id`) REFERENCES `visit` (`id`),
    CONSTRAINT `FK_4C53D32CF3037E8E` FOREIGN KEY (`variable_id`) REFERENCES `patient_variable` (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


    DROP TABLE IF EXISTS `visit_patient_audit_trail`;
    CREATE TABLE `visit_patient_audit_trail` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `entity_id` int(11) NOT NULL,
    `user_id` int(11) DEFAULT NULL,
    `date` datetime NOT NULL,
    `reason` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `modif_type` smallint(6) NOT NULL COMMENT '1=insert, 2=update, 3=delete',
    `details` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:json)',
    PRIMARY KEY (`id`),
    KEY `IDX_A9D861D81257D5D` (`entity_id`),
    KEY `IDX_A9D861DA76ED395` (`user_id`),
    CONSTRAINT `FK_A9D861D81257D5D` FOREIGN KEY (`entity_id`) REFERENCES `visit_patient` (`id`),
    CONSTRAINT `FK_A9D861DA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


    DROP TABLE IF EXISTS `visit_patient_status`;
    CREATE TABLE `visit_patient_status` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `label` varchar(55) COLLATE utf8mb4_unicode_ci NOT NULL,
    `code` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
    `position` smallint(6) NOT NULL,
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

    INSERT INTO `visit_patient_status` (`id`, `label`, `code`, `position`) VALUES
    (1,	'à monitorer',	'S1',	10),
    (2,	'Monitoré',	'S2',	20),
    (3,	'Non faite',	'S3',	30),
    (4,	'Vide',	'S4',	40);

    DROP TABLE IF EXISTS `visit_variable`;
    CREATE TABLE `visit_variable` (
    `visit_id` int(11) NOT NULL,
    `patient_variable_id` int(11) NOT NULL,
    PRIMARY KEY (`visit_id`,`patient_variable_id`),
    KEY `IDX_C91B599A75FA0FF2` (`visit_id`),
    KEY `IDX_C91B599AC30041C1` (`patient_variable_id`),
    CONSTRAINT `FK_C91B599A75FA0FF2` FOREIGN KEY (`visit_id`) REFERENCES `visit` (`id`) ON DELETE CASCADE,
    CONSTRAINT `FK_C91B599AC30041C1` FOREIGN KEY (`patient_variable_id`) REFERENCES `patient_variable` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- 2021-04-13 15:56:52
