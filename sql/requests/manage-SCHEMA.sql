-- =============================================================================================
-- -------------------------------------- MANAGE Schema ----------------------------------------
-- =============================================================================================


select * from deviation;
select * from center;
select * from dl_center_status;



-- user ---------------------------------------------------------------------------------------

show create table user;

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
    `note` longtext COLLATE utf8mb4_unicode_ci,
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
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- role ----------------------------------------------------------------------------------------

show create table role;

CREATE TABLE `role` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `parent_id` int(11) DEFAULT NULL,
    `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `deleted_at` datetime DEFAULT NULL,
    `position` int(11) NOT NULL,
    PRIMARY KEY (`id`),
    KEY `IDX_57698A6A727ACA70` (`parent_id`),
    CONSTRAINT `FK_57698A6A727ACA70` FOREIGN KEY (`parent_id`) REFERENCES `role` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=92 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- deviation -----------------------------------------------------------------------------------

show create table deviation;

CREATE TABLE `deviation` (
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `type_id` int(11) DEFAULT NULL,
     `sub_type_id` int(11) DEFAULT NULL,
     `project_id` int(11) NOT NULL,
     `center_id` int(11) DEFAULT NULL,
     `institution_id` int(11) DEFAULT NULL,
     `patient_id` int(11) DEFAULT NULL,
     `declared_by_id` int(11) DEFAULT NULL,
     `code` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
     `resume` longtext COLLATE utf8mb4_unicode_ci,
     `description` longtext COLLATE utf8mb4_unicode_ci,
     `grade` int(11) DEFAULT NULL,
     `status` smallint(6) DEFAULT NULL,
     `causality` longtext COLLATE utf8mb4_unicode_ci COMMENT '(DC2Type:simple_array)',
     `causality_description` longtext COLLATE utf8mb4_unicode_ci,
     `observed_at` datetime DEFAULT NULL,
     `occurence_at` datetime DEFAULT NULL,
     `closed_at` datetime DEFAULT NULL,
     `declared_at` datetime DEFAULT NULL,
     `is_crex_submission` tinyint(1) DEFAULT NULL,
     `potential_impact` int(11) DEFAULT NULL,
     `potential_impact_description` longtext COLLATE utf8mb4_unicode_ci,
     `efficiency_measure` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
     `efficiency_justify` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
     `not_efficiency_measure_reason` longtext COLLATE utf8mb4_unicode_ci,
     PRIMARY KEY (`id`),
     UNIQUE KEY `UNIQ_90A873F477153098` (`code`),
     KEY `IDX_90A873F4C54C8C93` (`type_id`),
     KEY `IDX_90A873F4BA94D067` (`sub_type_id`),
     KEY `IDX_90A873F4166D1F9C` (`project_id`),
     KEY `IDX_90A873F45932F377` (`center_id`),
     KEY `IDX_90A873F410405986` (`institution_id`),
     KEY `IDX_90A873F46B899279` (`patient_id`),
     KEY `IDX_90A873F4C48B85B0` (`declared_by_id`),
     CONSTRAINT `FK_90A873F410405986` FOREIGN KEY (`institution_id`) REFERENCES `institution` (`id`),
     CONSTRAINT `FK_90A873F4166D1F9C` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`),
     CONSTRAINT `FK_90A873F45932F377` FOREIGN KEY (`center_id`) REFERENCES `center` (`id`),
     CONSTRAINT `FK_90A873F46B899279` FOREIGN KEY (`patient_id`) REFERENCES `patient` (`id`),
     CONSTRAINT `FK_90A873F4BA94D067` FOREIGN KEY (`sub_type_id`) REFERENCES `dl_type_deviation` (`id`),
     CONSTRAINT `FK_90A873F4C48B85B0` FOREIGN KEY (`declared_by_id`) REFERENCES `user` (`id`),
     CONSTRAINT `FK_90A873F4C54C8C93` FOREIGN KEY (`type_id`) REFERENCES `dl_type_deviation` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- deviation system -----------------------------------------------------------------------------------------------

show create table deviation_system;

CREATE TABLE `deviation_system` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `declared_by_id` int(11) DEFAULT NULL,
    `referent_qa_id` int(11) DEFAULT NULL,
    `official_qa_id` int(11) DEFAULT NULL,
    `code` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
    `grade` int(11) DEFAULT NULL,
    `causality` longtext COLLATE utf8mb4_unicode_ci COMMENT '(DC2Type:simple_array)',
    `causality_description` longtext COLLATE utf8mb4_unicode_ci,
    `potential_impact` int(11) DEFAULT NULL,
    `potential_impact_description` longtext COLLATE utf8mb4_unicode_ci,
    `status` smallint(6) DEFAULT NULL,
    `resume` longtext COLLATE utf8mb4_unicode_ci,
    `description` longtext COLLATE utf8mb4_unicode_ci,
    `observed_at` datetime DEFAULT NULL,
    `declared_at` datetime DEFAULT NULL,
    `closed_at` datetime DEFAULT NULL,
    `is_crex_submission` tinyint(1) DEFAULT NULL,
    `activity` longtext COLLATE utf8mb4_unicode_ci,
    `ref_iso9001` longtext COLLATE utf8mb4_unicode_ci,
    `document` longtext COLLATE utf8mb4_unicode_ci,
    `visa_pilot_process_chief_qa` longtext COLLATE utf8mb4_unicode_ci,
    `visa_at` datetime DEFAULT NULL,
    `efficiency_measure` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `efficiency_justify` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `not_efficiency_measure_reason` longtext COLLATE utf8mb4_unicode_ci,
    PRIMARY KEY (`id`),
    UNIQUE KEY `UNIQ_D0EEA25E77153098` (`code`),
    KEY `IDX_D0EEA25EC48B85B0` (`declared_by_id`),
    KEY `IDX_D0EEA25E3574BC16` (`referent_qa_id`),
    KEY `IDX_D0EEA25EF0A5D7EF` (`official_qa_id`),
    CONSTRAINT `FK_D0EEA25E3574BC16` FOREIGN KEY (`referent_qa_id`) REFERENCES `user` (`id`),
    CONSTRAINT `FK_D0EEA25EC48B85B0` FOREIGN KEY (`declared_by_id`) REFERENCES `user` (`id`),
    CONSTRAINT `FK_D0EEA25EF0A5D7EF` FOREIGN KEY (`official_qa_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- deviation audit trail --------------------------------------------------------------------------------------------------------

show create table deviation_audit_trail;

CREATE TABLE `deviation_audit_trail` (
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `entity_id` int(11) NOT NULL,
     `user_id` int(11) DEFAULT NULL,
     `date` datetime NOT NULL,
     `reason` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
     `modif_type` smallint(6) NOT NULL COMMENT '1=insert, 2=update, 3=delete',
     `details` longtext COLLATE utf8mb4_unicode_ci COMMENT '(DC2Type:json)',
     PRIMARY KEY (`id`),
     KEY `IDX_BBCFBA0481257D5D` (`entity_id`),
     KEY `IDX_BBCFBA04A76ED395` (`user_id`),
     CONSTRAINT `FK_BBCFBA0481257D5D` FOREIGN KEY (`entity_id`) REFERENCES `deviation` (`id`),
     CONSTRAINT `FK_BBCFBA04A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- deivation system audit trail ------------------------------------------------------------------------------------------------

show create table deviation_system_audit_trail;

CREATE TABLE `deviation_system_audit_trail` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `entity_id` int(11) NOT NULL,
    `user_id` int(11) DEFAULT NULL,
    `date` datetime NOT NULL,
    `reason` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `modif_type` smallint(6) NOT NULL COMMENT '1=insert, 2=update, 3=delete',
    `details` longtext COLLATE utf8mb4_unicode_ci COMMENT '(DC2Type:json)',
    PRIMARY KEY (`id`),
    KEY `IDX_AAE9A5E681257D5D` (`entity_id`),
    KEY `IDX_AAE9A5E6A76ED395` (`user_id`),
    CONSTRAINT `FK_AAE9A5E681257D5D` FOREIGN KEY (`entity_id`) REFERENCES `deviation_system` (`id`),
    CONSTRAINT `FK_AAE9A5E6A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- report visit audit trail --------------------------------------------------------------------------------------------------

show create table report_visit_audit_trail;

CREATE TABLE `report_visit_audit_trail` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `entity_id` int(11) NOT NULL,
    `user_id` int(11) DEFAULT NULL,
    `date` datetime NOT NULL,
    `reason` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `modif_type` smallint(6) NOT NULL COMMENT '1=insert, 2=update, 3=delete',
    `details` longtext COLLATE utf8mb4_unicode_ci COMMENT '(DC2Type:json)',
    PRIMARY KEY (`id`),
    KEY `IDX_8B6C8F6581257D5D` (`entity_id`),
    KEY `IDX_8B6C8F65A76ED395` (`user_id`),
    CONSTRAINT `FK_8B6C8F6581257D5D` FOREIGN KEY (`entity_id`) REFERENCES `report_visit` (`id`),
    CONSTRAINT `FK_8B6C8F65A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci

-- variable data --------------------------------------------------------------------------------------------------

show create table patient_variable;

CREATE TABLE `patient_variable` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `project_id` int(11) DEFAULT NULL,
    `variable_type_id` int(11) NOT NULL,
    `label` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `source_id` varchar(55) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `exam_id` int(11) DEFAULT NULL,
    `variable_list_id` int(11) DEFAULT NULL,
    `position` smallint(6) DEFAULT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=122 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- institution --------------------------------------------------------------------------------------------------

show create table institution;

CREATE TABLE `institution` (
   `id` int(11) NOT NULL AUTO_INCREMENT,
   `country_id` int(11) NOT NULL,
   `country_department_id` int(11) DEFAULT NULL,
   `institution_type_id` int(11) NOT NULL,
   `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
   `address1` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
   `address2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
   `city` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
   `finess` varchar(9) COLLATE utf8mb4_unicode_ci NOT NULL,
   `siret` varchar(14) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- institutionType --------------------------------------------------------------------------------------------------

show create table dl_institution_type;

CREATE TABLE `dl_institution_type` (
   `id` int(11) NOT NULL AUTO_INCREMENT,
   `label` varchar(55) COLLATE utf8mb4_unicode_ci NOT NULL,
   `code` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
   `deleted_at` datetime DEFAULT NULL,
   PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- phaseSetting ------------------------------------------------------------------------------------------------------

show create table phase_setting;

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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- exams ---------------------------------------------------------------------------------------------------------------

show create table exam;

CREATE TABLE `exam` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `type_id` int(11) NOT NULL,
    `project_id` int(11) NOT NULL,
    `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `position` smallint(6) DEFAULT NULL,
    `source_id` int(11) DEFAULT NULL,
    `price` double DEFAULT NULL,
    `type_reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `ordre` smallint(6) NOT NULL,
    `deleted_at` datetime DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `IDX_38BBA6C6C54C8C93` (`type_id`),
    KEY `IDX_38BBA6C6166D1F9C` (`project_id`),
    CONSTRAINT `FK_38BBA6C6166D1F9C` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`),
    CONSTRAINT `FK_38BBA6C6C54C8C93` FOREIGN KEY (`type_id`) REFERENCES `dl_exam_type` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- visit ------------------------------------------------------------------------------------------------------------


show create table visit;

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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci