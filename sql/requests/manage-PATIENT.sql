select * from project;
select * from patient;
select * from phase_setting;
select * from phase_setting_status;
select * from patient_data;
select * from patient_variable;
select * from visit_variable;
select * from visit;
select * from exam_variable;
select * from exam;
select * from dl_exam_type;
select * from visit_patient;


select count(patient_data.id), patient.project_id from patient_data
left join patient on patient_data.patient_id = patient.id
group by patient.project_id;

select * from patient_data where variable_id in (select id from patient_variable where source_id=17010 and project_id=30);
select * from patient_data where variable_id in (select id from patient_variable where source_id=236020 and project_id=30);

select * from patient_variable where id in (64, 80);

select * from patient_data where variable_value like '%2021-10-10%';
select * from patient_data where variable_value like '%2021-10-01%';

select * from patient_variable where source_id=236020 and project_id=30;

insert into patient_data (patient_id, variable_id, variable_value, iteration, importing, ordre, deleted_at, disabled_at)
VALUES (10, 64, '2021-10-01', 1, 0, 3, null, null);

insert into patient_data (patient_id, variable_id, variable_value, iteration, importing, ordre, deleted_at, disabled_at)
VALUES (10, 80, '2021-10-10', 1, 0, 3, null, null);

delete from patient_data where variable_id in (select id from patient_variable where source_id not in (17010, 236020));
truncate patient_data_audit_trail;









-- -------------------------------------------------------------------------------------------------------------

SELECT patient_data.ordre as ordre, patient_data.id as id, patient_variable.visit_id as visit, patient_variable.exam_id as exam, patient_data.variable_id as idVariable, patient_data.deleted_at as archived, patient.id as idPatient, center.number as center, patient.number as patient, patient_variable.label as variable, patient_data.variable_value as value, patient_variable.variable_type_id as type, patient_variable.id as idVariable, phase_setting_status.label as phase_label
FROM patient_data
         left JOIN patient ON patient_data.patient_id = patient.id
         left JOIN center ON patient.center_id = center.id
         left JOIN patient_variable ON patient_data.variable_id = patient_variable.id
         LEFT JOIN visit_variable ON patient_variable.id = visit_variable.patient_variable_id
         LEFT JOIN visit_patient ON patient.id = visit_patient.patient_id
         LEFT JOIN visit ON visit.id = visit_patient.visit_id
         LEFT JOIN phase_setting ON phase_setting.id = visit.phase_id
         LEFT JOIN phase_setting_status ON phase_setting.phase_setting_status_id = phase_setting_status.id
         LEFT JOIN exam_variable  ON patient_variable.id = exam_variable .patient_variable_id
         LEFT JOIN exam ON exam.id = exam_variable .exam_id
WHERE patient.project_id = 30 and patient_variable.deleted_at is null and patient_data.disabled_at is null and patient_variable.has_patient = 1
ORDER BY patient_data.ordre ASC, visit.position ASC, exam.position ASC, center.number ASC, patient.number ASC, patient_variable.id ASC;








SELECT patient_data.ordre as ordre, patient_data.id as id, patient_variable.visit_id as visit, patient_variable.exam_id as exam, patient_data.variable_id as idVariable, patient_data.deleted_at as archived, patient.id as idPatient, center.number as center, patient.number as patient, patient_variable.label as variable, patient_data.variable_value as value, patient_variable.variable_type_id as type, patient_variable.id as idVariable
FROM patient_data
         left JOIN patient ON patient_data.patient_id = patient.id
         left JOIN center ON patient.center_id = center.id
         left JOIN patient_variable ON patient_data.variable_id = patient_variable.id
         LEFT JOIN visit_variable ON patient_variable.id = visit_variable.patient_variable_id
WHERE patient.project_id = 30 and patient_variable.deleted_at is null and patient_data.disabled_at is null and patient_variable.has_patient = 1;




