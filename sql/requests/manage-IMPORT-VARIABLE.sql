-- =========================================================================================================================
-- =========================================================================================================================
-- ------------------------------------------------ MANAGE Import Variable -------------------------------------------------
-- =========================================================================================================================
-- =========================================================================================================================

use esm_v3_develop;

select * from project;
select * from center;
select * from center where id in (7, 14);

select * from patient;
select * from patient where id=49;
select * from patient where number='100-004';
select * from patient where number='01-002';

select * from variable_type;
select * from variable_type where id=2;

select * from patient_variable;
select * from patient_variable where source_id in (825052, 34012, 3012, 1062) and project_id=17;
select * from patient_variable where id in (73, 74, 94, 95);

select * from patient_data;
select * from patient_data where patient_id=55;
select * from patient_data where patient_id=55 and variable_id=97;
select * from patient_data where variable_value='07/06/2021';
select * from patient_data where variable_value='2019-02-29';

select * from patient_data where variable_value <> '';

select * from variable_list;

select * from phase_setting;


update patient_data set variable_value='';
delete from patient_data_audit_trail where entity_id in (405, 407, 408);
delete from patient_data where patient_id=52 and variable_id in (73, 74, 94, 95);


select * from visit_patient;
select * from visit;

select * from patient_data pd
inner join patient p on pd.patient_id = p.id
where p.project_id=17
and pd.variable_value='07/06/2021' or pd.variable_value='06/07/2021' or pd.variable_value='2021/07/06' or pd.variable_value='2021/06/07';


update patient_data set variable_value='08/08/2021' where patient_id=49 and variable_id in (75,77);

-- change date format

update patient_data set variable_value='21/12/2021' where id in (614, 653, );
update patient_data set variable_value='21/12/2021' where patient_id=52 and variable_id=78;
update patient_data set variable_value='21/12/2021' where patient_id=52 and variable_id=94;
update patient_data set variable_value='21/12/2021' where patient_id=52 and variable_id=73;



-- exemple date invalid affichage
select * from project where id in (17, 18);
select * from center where id in (7, 14);
select * from patient where number='01-001' and project_id=17 and center_id=7;
select * from patient where number='01-002' and project_id=17 and center_id=7;
select * from patient_variable where project_id=17 and label='Date de signature du consentement';
select * from patient_data where patient_id in (select id from patient where number='01-002' and project_id=17 and center_id=7) and variable_id in (select id from patient_variable where project_id=17 and label='Date de signature du consentement');
select * from patient_variable where project_id=17 and label='Date de signature du consentement';
select * from patient_variable where id in (73, 75);


-- patient data page suivi patients
SELECT patient_data.ordre as ordre, patient_data.id as id, patient_data.variable_id as idVariable, patient_data.deleted_at as archived, patient.id as idPatient, center.number as center, patient.number as patient, patient_variable.label as variable, patient_data.variable_value as value, patient_variable.variable_type_id as type, patient_variable.id as idVariable
FROM patient_data
     INNER JOIN patient ON patient_data.patient_id = patient.id
     INNER JOIN center ON patient.center_id = center.id
     INNER JOIN patient_variable ON patient_data.variable_id = patient_variable.id
     LEFT JOIN visit_variable ON patient_variable.id = visit_variable.patient_variable_id
     LEFT JOIN visit ON visit.id = visit_variable.visit_id
     LEFT JOIN exam_variable ON patient_variable.id = exam_variable.patient_variable_id
     LEFT JOIN exam ON exam.id = exam_variable.exam_id
WHERE patient.project_id = 17 and patient_variable.deleted_at is null and patient_data.disabled_at is null and patient_variable.has_patient = 1
ORDER BY patient_data.ordre ASC, visit.position ASC, exam.position ASC, center.number ASC, patient.number ASC, patient_variable.id ASC;

SELECT patient_data.ordre as ordre, patient_data.id as id, patient_data.variable_id as idVariable, patient_data.deleted_at as archived, patient.id as idPatient, center.number as center, patient.number as patient, patient_variable.label as variable, patient_data.variable_value as value, patient_variable.variable_type_id as type, patient_variable.id as idVariable
FROM patient_data
     INNER JOIN patient ON patient_data.patient_id = patient.id
     INNER JOIN center ON patient.center_id = center.id
     INNER JOIN patient_variable ON patient_data.variable_id = patient_variable.id
     LEFT JOIN visit_variable ON patient_variable.id = visit_variable.patient_variable_id
     LEFT JOIN visit ON visit.id = visit_variable.visit_id
     LEFT JOIN exam_variable ON patient_variable.id = exam_variable.patient_variable_id
     LEFT JOIN exam ON exam.id = exam_variable.exam_id
WHERE patient.project_id = 17 and patient_variable.deleted_at is null and patient_data.disabled_at is null and patient_variable.has_patient = 1 and patient.number='01-002' and patient_variable.project_id=17
ORDER BY patient_data.ordre ASC, visit.position ASC, exam.position ASC, center.number ASC, patient.number ASC, patient_variable.id ASC;

select * from patient_data where id=403;