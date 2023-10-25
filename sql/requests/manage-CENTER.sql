-- ======================================================================================================
-- ---------------------------------- MANAGE Center -----------------------------------------------------
-- ======================================================================================================
use esm_v3_develop;


select * from project;
select * from institution;
select * from dl_institution_type;
select * from center;
select * from dl_center_status;
select * from institution_center;
select * from dl_crf_type;
select * from dl_trail_type;
select * from dl_trail_phase;
select * from dl_country_department;
select * from dl_country;
select * from dl_submission_type_authority;
select * from dl_submission_name;
select * from dl_institution_type;
select * from dl_territory;
select * from interlocutor;
select * from interlocutor_center;
select * from service;
select * from interlocutor_institution;


select * from interlocutor where first_name like '%brad%' or last_name like '%Pitt%';

insert into interlocutor_institution (interlocutor_id, institution_id) VALUES (1, 1);


-- tous les centres d'un etablissement -------------------------------------------------

select c.name, c.number from institution
left join institution_center ic on institution.id = ic.institution_id
left join center c on ic.center_id = c.id
left join project p on c.project_id = p.id
where p.acronyme='BOP'
group by c.id;

select c.name, c.number from institution
left join institution_center ic on institution.id = ic.institution_id
left join center c on ic.center_id = c.id
left join project p on c.project_id = p.id
where p.acronyme='TFD'
group by c.id;

-- tous les services d'un centre --------------------------------------------------------

select * from service;
select * from interlocutor_center;








