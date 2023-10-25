-- ============================================================================
-- MANAGE REPORT
-- ============================================================================
use esm_v3_develop;

select * from report_model;
select * from report_model order by id desc;

select * from report_model_version;
select * from report_model_version order by id desc;

select * from report_block;
select * from report_model_version_block;

select * from report_config;
select * from report_config_version;
select * from report_config_version_block;


select * from report_visit;
select * from meeting;


select * from project;



-- requests ===============================================================================================

select * from report_model where id=12;

select * from report_config where id=1;
select * from report_model_version where id=2;
select * from report_model_version_block where version_id=2;

-- word generator ==========================================================================================

select * from report_visit where id=4;
select * from report_config_version where id=23;
select * from report_config_version_block where config_version_id=23;


-- drugs name =================================================================================
select * from project;
select * from project_drug;
select * from drug;
select * from dl_trail_treatment;
