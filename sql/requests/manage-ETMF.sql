-- =========================================================================================================================
-- =========================================================================================================================
-- ------------------------------------------------ MANAGE ETMF ------------------------------------------------------------
-- =========================================================================================================================
-- =========================================================================================================================
use esm_v3_develop;

select * from zone;
select * from section;
select * from artefact;
select * from artefact_level;
select * from document;
select * from document_version;
select * from document_level;
select * from mailgroup;
select * from tag;
select * from users_mailgroups;

-- tous les sections d'une zone --------------------------------------------------------------------------

select * from section where zone_id in (select id from zone where zone.name like '%Ea.5%');
select * from section where zone_id in (select id from zone where zone.name like '%Zone Rem.3%');
select * from section where zone_id in (select id from zone where zone.name like '%Zone Soluta.11%');

-- search engine -----------------------------------------------------------------------------------------
select * from project_country;
select * from document;
select * from dl_sponsor;

select * from document_version doc_version
left join document doc on doc_version.document_id = doc.id
left join artefact a on doc.artefact_id = a.id
left join section on a.section_id = section.id
left join zone z on doc.zone_id = z.id
left join project p on doc.project_id = p.id
left join dl_sponsor s on p.sponsor_id = s.id
left join project_country pc on p.id = pc.project_id
left join dl_country dc on pc.country_id = dc.id
left join center c on p.id = c.project_id;

-- sponsor
select sponsor.id, sponsor.label, sponsor.code from document_version doc_version
left join document doc on doc_version.document_id = doc.id
left join artefact a on doc.artefact_id = a.id
left join section on a.section_id = section.id
left join zone z on doc.zone_id = z.id
left join project p on doc.project_id = p.id
left join dl_sponsor sponsor on p.sponsor_id = sponsor.id
left join project_country pc on p.id = pc.project_id
left join dl_country dc on pc.country_id = dc.id
left join center c on p.id = c.project_id;

select * from project p
left join dl_sponsor ds on p.sponsor_id = ds.id;



select p.acronyme, ds.label from document_version
left join document d on document_version.document_id = d.id
left join project p on d.project_id = p.id
left join dl_sponsor ds on p.sponsor_id = ds.id
where document_version.id=1;

select p.acronyme, ds.label from document_version
left join document d on document_version.document_id = d.id
left join project p on d.project_id = p.id
left join dl_sponsor ds on p.sponsor_id = ds.id
where ds.id=1;
