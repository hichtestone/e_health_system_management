-- ======================================================================================================
-- ---------------------------------- MANAGE Audit Trail ------------------------------------------------
-- ======================================================================================================


select * from deviation;
select * from deviation_audit_trail;
select * from deviation_system_audit_trail;

select * from connection_audit_trail;

select * from interlocutor_cooperator;
select * from interlocutor_center;
select * from interlocutor_center_audit_trail;

select * from project_audit_trail;

select * from esm_v3_develop.project_audit_trail;

select * from patient_data_audit_trail;
select * from patient_audit_trail;
select * from patient_variable_audit_trail;

select * from mailgroup;
select * from mailgroup_audit_trail;

select * from deviation;
select * from deviation_audit_trail;



show create table mailgroup_audit_trail;



truncate deviation_audit_trail;
truncate deviation_system_audit_trail;
truncate project_audit_trail;
truncate patient_data_audit_trail;
truncate patient_audit_trail;
truncate patient_variable_audit_trail;


CREATE TABLE `mailgroup_audit_trail` (
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `entity_id` int(11) NOT NULL,
     `user_id` int(11) DEFAULT NULL,
     `date` datetime NOT NULL,
     `reason` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
     `modif_type` smallint(6) NOT NULL COMMENT '1=insert, 2=update, 3=delete',
     `details` longtext COLLATE utf8mb4_unicode_ci COMMENT '(DC2Type:json)',
     PRIMARY KEY (`id`),
     KEY `IDX_D716511581257D5D` (`entity_id`),
     KEY `IDX_D7165115A76ED395` (`user_id`),
     CONSTRAINT `FK_D716511581257D5D` FOREIGN KEY (`entity_id`) REFERENCES `mailgroup` (`id`),
     CONSTRAINT `FK_D7165115A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

