-- =============================================================================================
-- -------------------------------------- MANAGE Schema ETUDE ----------------------------------
-- =============================================================================================
use esm_v3_develop;

select * from phase_setting;
select * from schema_condition;
select * from visit_patient;
select * from visit;
select * from project where name like '%Rego%';


-- toutes les visites d'un projet
select * from visit_patient
left join visit on visit_patient.visit_id = visit.id
where visit.project_id in (select id from project where name like '%Rego%');
