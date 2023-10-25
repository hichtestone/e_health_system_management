-- ======================================================================================================
-- ------------------------------MANAGE MARIA DB system -------------------------------------------------
-- ======================================================================================================

select * from deviation;
select * from deviation_audit_trail;



-- ROLES

select * from role where code like '%NO_CONFORMITY%';
select * from role where code like '%DEVIATION%';




-- DEVIATION protocolaire

select * from deviation order by id desc;
select * from center where id=2;
select * from institution_center where center_id=2;
select * from institution where id=3;

select * from deviation where code like '%2021_1%';
select * from deviation where code like '%BOP_1_1%';
select * from deviation_action;


select * from dl_type_deviation;

-- DEVIATION SYSTEM ----------------------------------------------------------------------------------------------------

select * from deviation;
select * from deviation_system;
select * from deviation_system order by id desc;
select * from deviation_system_correction;
select * from deviation_system_review;
select * from deviation_process_system;
select * from dl_process_system;
select * from deviation_system_audit_trail;

select * from deviation_system where code like '%NC_2021_ESM_S_010%';



truncate deviation;
truncate deviation_system_audit_trail;
truncate deviation_system_action_audit_trail;
truncate deviation_system_review_audit_trail;
truncate deviation_system;
truncate deviation_system_review;
truncate deviation_system_review_action;
truncate deviation_system_correction;
truncate deviation_system_action;


delete from deviation_system where id=1;







