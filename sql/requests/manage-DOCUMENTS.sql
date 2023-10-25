-- ============================================================================================================
-- ------------------------------------------ MANAGE Document ETMF --------------------------------------------
-- ============================================================================================================
use esm_v3_develop;

select * from document_version;
select * from document;
select * from zone;
select * from section;
select * from artefact;
select * from mailgroup;
select * from users_mailgroups;
select * from artefact_mailGroup;
select * from user_project;
select * from user;
select * from project;



select * from document_version
left join document on document_version.document_id = document.id
left join artefact on document.artefact_id = artefact.id
left join section on artefact.section_id = section.id
left join zone on section.zone_id = zone.id
left join project on document.project_id = project.id
left join dl_sponsor on project.sponsor_id = dl_sponsor.id
left join project_country on project.id = project_country.project_id
left join dl_country on project_country.country_id = dl_country.id
left join center c on project.id = c.project_id;

-- toutes les sections d'une zone
select * from section where zone_id in (select id from zone where zone.name like '%Debitis%');

