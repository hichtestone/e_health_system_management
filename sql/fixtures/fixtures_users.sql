select * from user;
select * from profile;

INSERT INTO `user` (`id`, `civility_id`, `profile_id`, `job_id`, `department_id`, `society_id`, `email`, `roles`, `password`, `first_name`, `last_name`, `locale`, `is_super_admin`, `deleted_at`, `has_access_esm`, `has_access_etmf`, `phone`, `note`, `password_updated_at`, `reset_password_token`, `reset_password_at`, `is_bot`, `two_factor_secret`) VALUES
(15,	1,	1,	1,	1,	4,	'tata1@unicancer.com',	    '[]',	'$argon2id$v=19$m=65536,t=4,p=1$CAnJbiJWdouz+nIlpNADRA$ibvbA3znJ0eifClU80HRv0BVXD3Sp5DAB5wQsnCMzp8',	'tata',	    'cyroma',	    'fr',	0,	NULL,	1,	1,	'0102030405',	'remarque...',	'2021-04-07 19:14:07',	NULL,	NULL,	0,	NULL),
(16,	2,	2,	2,	2,	4,	'toto2@unicancer.com',	    '[]',	'$argon2id$v=19$m=65536,t=4,p=1$CAnJbiJWdouz+nIlpNADRA$ibvbA3znJ0eifClU80HRv0BVXD3Sp5DAB5wQsnCMzp8',	'toto',	    'Callo',	    'fr',	0,	NULL,	1,	1,	'0102030405',	'remarque...',	'2021-04-07 19:14:07',	NULL,	NULL,	0,	NULL),
(17,	1,	3,	3,	3,	4,	'tyty3@unicancer.com',	    '[]',	'$argon2id$v=19$m=65536,t=4,p=1$CAnJbiJWdouz+nIlpNADRA$ibvbA3znJ0eifClU80HRv0BVXD3Sp5DAB5wQsnCMzp8',	'tyty',	    'fromis',	    'en',	0,	NULL,	1,	1,	'0102030405',	'remarque...',	'2021-04-07 19:14:07',	NULL,	NULL,	0,	NULL),
(18,	2,	4,	4,	1,	4,	'florence@unicancer.com',	'[]',	'$argon2id$v=19$m=65536,t=4,p=1$CAnJbiJWdouz+nIlpNADRA$ibvbA3znJ0eifClU80HRv0BVXD3Sp5DAB5wQsnCMzp8',	'Florence',	'Indofile',	    'fr',	0,	NULL,	1,	1,	'0102030405',	'remarque...',	'2021-04-07 19:14:07',	NULL,	NULL,	0,	NULL),
(19,	1,	5,	5,	2,	4,	'phanie@unicancer.com',	    '[]',	'$argon2id$v=19$m=65536,t=4,p=1$CAnJbiJWdouz+nIlpNADRA$ibvbA3znJ0eifClU80HRv0BVXD3Sp5DAB5wQsnCMzp8',	'phani',	'brotille',	    'fr',	0,	NULL,	1,	1,	'0102030405',	'remarque...',	'2021-04-07 19:14:07',	NULL,	NULL,	0,	NULL),
(20,	2,	6,	6,	3,	4,	'sylvaine@unicancer.com',	'[]',	'$argon2id$v=19$m=65536,t=4,p=1$CAnJbiJWdouz+nIlpNADRA$ibvbA3znJ0eifClU80HRv0BVXD3Sp5DAB5wQsnCMzp8',	'Sylvaine',	'fripouille',	'fr',	0,	NULL,	1,	1,	'0102030405',	'remarque...',	'2021-04-07 19:14:07',	NULL,	NULL,	0,	NULL),
(21,	1,	7,	7,	1,	4,	'gertrude@unicancer.com',	'[]',	'$argon2id$v=19$m=65536,t=4,p=1$CAnJbiJWdouz+nIlpNADRA$ibvbA3znJ0eifClU80HRv0BVXD3Sp5DAB5wQsnCMzp8',	'gertrude',	'christo',	    'fr',	0,	NULL,	1,	1,	'0102030405',	'remarque...',	'2021-04-07 19:14:07',	NULL,	NULL,	0,	NULL),
(22,	1,	8,	8,	2,	4,	'marie@unicancer.com',	    '[]',	'$argon2id$v=19$m=65536,t=4,p=1$CAnJbiJWdouz+nIlpNADRA$ibvbA3znJ0eifClU80HRv0BVXD3Sp5DAB5wQsnCMzp8',	'marie',	'Crynatille',	'fr',	0,	NULL,	1,	1,	'0102030405',	'remarque...',	'2021-04-07 19:14:07',	NULL,	NULL,	0,	NULL),
(23,	1,	9,	9,	3,	4,	'julie@unicancer.com',	    '[]',	'$argon2id$v=19$m=65536,t=4,p=1$CAnJbiJWdouz+nIlpNADRA$ibvbA3znJ0eifClU80HRv0BVXD3Sp5DAB5wQsnCMzp8',	'julie',	'klotiche',	    'en',	0,	NULL,	1,	1,	'0102030405',	'remarque...',	'2021-04-07 19:14:07',	NULL,	NULL,	0,	NULL),
(24,	2,	9,	10,	1,	4,	'bertrand@unicancer.com',	'[]',	'$argon2id$v=19$m=65536,t=4,p=1$CAnJbiJWdouz+nIlpNADRA$ibvbA3znJ0eifClU80HRv0BVXD3Sp5DAB5wQsnCMzp8',	'bertrand',	'virule',	    'fr',	0,	NULL,	1,	1,	'0102030405',	'remarque...',	'2021-04-07 19:14:07',	NULL,	NULL,	0,	NULL),
(25,	1,	8,	11,	2,	4,	'dyne@unicancer.com',	    '[]',	'$argon2id$v=19$m=65536,t=4,p=1$CAnJbiJWdouz+nIlpNADRA$ibvbA3znJ0eifClU80HRv0BVXD3Sp5DAB5wQsnCMzp8',	'Dyne',	    'audio',	    'en',	0,	NULL,	1,	1,	'0102030405',	'remarque...',	'2021-04-07 19:14:07',	NULL,	NULL,	0,	NULL),
(26,	2,	7,	12,	3,	4,	'hybro@unicancer.com',	    '[]',	'$argon2id$v=19$m=65536,t=4,p=1$CAnJbiJWdouz+nIlpNADRA$ibvbA3znJ0eifClU80HRv0BVXD3Sp5DAB5wQsnCMzp8',	'hybro',	'Cinfilo',	    'fr',	0,	NULL,	1,	1,	'0102030405',	'remarque...',	'2021-04-07 19:14:07',	NULL,	NULL,	0,	NULL),
(27,	1,	6,	13,	1,	4,	'janis@unicancer.com',	    '[]',	'$argon2id$v=19$m=65536,t=4,p=1$CAnJbiJWdouz+nIlpNADRA$ibvbA3znJ0eifClU80HRv0BVXD3Sp5DAB5wQsnCMzp8',	'janis',	'fillo',	    'fr',	0,	NULL,	1,	1,	'0102030405',	'remarque...',	'2021-04-07 19:14:07',	NULL,	NULL,	0,	NULL),
(28,	2,	5,	14,	2,	4,	'leopold@unicancer.com',	'[]',	'$argon2id$v=19$m=65536,t=4,p=1$CAnJbiJWdouz+nIlpNADRA$ibvbA3znJ0eifClU80HRv0BVXD3Sp5DAB5wQsnCMzp8',	'leopold',	'dryto',	    'en',	0,	NULL,	1,	1,	'0102030405',	'remarque...',	'2021-04-07 19:14:07',	NULL,	NULL,	0,	NULL),
(29,	2,	4,	15,	3,	4,	'katy@unicancer.com',	    '[]',	'$argon2id$v=19$m=65536,t=4,p=1$CAnJbiJWdouz+nIlpNADRA$ibvbA3znJ0eifClU80HRv0BVXD3Sp5DAB5wQsnCMzp8',	'katy',	    'bunisse',	    'fr',	0,	NULL,	1,	1,	'0102030405',	'remarque...',	'2021-04-07 19:14:07',	NULL,	NULL,	0,	NULL),
(30,	1,	3,	16,	1,	4,	'rymona@unicancer.com',	    '[]',	'$argon2id$v=19$m=65536,t=4,p=1$CAnJbiJWdouz+nIlpNADRA$ibvbA3znJ0eifClU80HRv0BVXD3Sp5DAB5wQsnCMzp8',	'rymona',	'chevalier',	'fr',	0,	NULL,	1,	1,	'0102030405',	'remarque...',	'2021-04-07 19:14:07',	NULL,	NULL,	0,	NULL);


-- add deviations roles to admin dev account
select * from role;

INSERT INTO profile_role (profile_id, role_id) VALUES
(1, 61),
(1, 62),
(1, 63),
(1, 64),
(1, 65),
(1, 66),
(1, 67),
(1, 68),
(1, 69),
(1, 70);

-- add role create deviation for user unicancer
insert into profile_role (profile_id, role_id) VALUES
(7, 62),
(8, 62),
(9, 62);

-- add project into user
select * from user_project;

insert into user_project (user_id, project_id, enabled_at, disabled_at, source_id, metas, rate) VALUES
(15, 1, NOW(), null, null, '[]', null),
(15, 2, NOW(), null, null, '[]', null),
(15, 3, NOW(), null, null, '[]', null);

-- add roles creation / Edition NC systeme to users

select * from role order by id desc ;

select * from user;
select * from profile;
select * from profile_role;
select * from profile_role where role_id in (78, 79, 80, 81, 82);