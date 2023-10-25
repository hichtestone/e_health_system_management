show databases;
use esm_v3_develop;

select * from user;
select * from mailgroup;
select * from profile;
select * from role;
select * from profile_role;
select * from contact_interlocutor;
select * from dl_department;
select * from patient;
select * from dl_society;
select * from dl_civility;
select * from user_project;
select * from dl_user_job;
select * from project;
select * from dl_sponsor;
select * from application;
select * from interlocutor_institution;
select * from institution_center;
select * from institution;
select * from interlocutor;
select * from interlocutor_center;
select * from service;
select * from dl_cooperator;
select * from interlocutor_cooperator;
select * from dl_participant_job;
select * from dl_participant_speciality;
select * from dl_country_department;
select * from users_mailgroups;

select * from user where last_name like '%ABED%';
select * from center where name like '%ICO%';

update user set email='renot@clinfile.com', first_name='remy', last_name='enot' where user.email='mperret@clinfile.com';

insert into interlocutor_institution (interlocutor_id, institution_id) VALUE (1, 52);



-- tous les services d'un etalissement ----------------------------------------------------

select * from institution
left join institution_center ic on institution.id = ic.institution_id
left join center c on ic.center_id = c.id;

-- tous les role du profile admin
select * from profile_role
left join role r on r.id = profile_role.role_id
where profile_id=1;

select * from profile_role
left join role r on r.id = profile_role.role_id
where profile_id=1 and r.code like '%ROLE_NO_CONFORMITY_SYSTEM_ACTION_CREATE%';

select * from profile_role
left join role r on r.id = profile_role.role_id
where profile_id=1 and r.code like '%REPORT_VISIT_CREATE%';

-- tous les ROLE de non conformité
select * from role where code like '%NO_CONFORMITY%';

-- tous les users avec un role precis
select user.civility_id, user.first_name, user.last_name from user
left join profile_role pr on user.profile_id = pr.profile_id
left join role r on pr.role_id = r.id
where r.code like '%DEVIATION_ACTION_CREATE_EDIT%';

-- tous les interlocuteurs d'un projet -------------------------------------------------------------------

select * from project;
select * from interlocutor;
select * from interlocutor_center;

select * from interlocutor i
inner join interlocutor_center ic on i.id = ic.interlocutor_id
inner join center c on ic.center_id = c.id
where c.project_id = 1;




-- delete ------------------------------------------------------------------------------------------------
select * from interlocutor;
select * from interlocutor_institution;


truncate interlocutor;
truncate interlocutor_institution;