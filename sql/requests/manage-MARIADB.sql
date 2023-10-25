-- ============================================================================
-- MANAGE MARIA DB system
-- ============================================================================
show databases ;

use esm_v3_develop;
use esm_v3_prod;

SET SESSION foreign_key_checks=ON;
SET SESSION foreign_key_checks=OFF;

SET GLOBAL foreign_key_checks=ON;
SET GLOBAL foreign_key_checks=OFF;

show databases;
show tables;

create database etmf_v1_develop;
create database esm_v3_develop;

drop database esm_v3_develop;
drop database etmf_v1_develop;

SELECT * FROM mysql.user;

select * from project_country;

select * from dl_country_department;

select * from project;

select * from dl_participant_speciality;

select * from phase_setting_status;

select * from dl_institution_type;

select * from institution;

select * from service;

select * from phase_setting;

select * from user;

select * from user_project;

select * from exam_patient_variable;

select * from role;

show create table exam_patient_variable;

select * from dl_country_department;



select * from user_project
    inner join project on user_project.project_id = project.id
    inner join dl_sponsor on project.sponsor_id = dl_sponsor.id
    inner join user on project.responsible_pm_id = user.id
    where user.id = 1;

$qb = $this->createQueryBuilder('up')
            ->innerJoin('up.project', 'p')
            ->innerJoin('p.sponsor', 's')
            ->innerJoin('p.responsiblePM', 'r')
            ->where('up.user = :user')
            ->andWhere('up.disabledAt IS NULL')
            ->setParameter('user', $user)
        ;
        if (!$includeClosedProject) {
            $qb->andWhere('p.closedAt IS NULL');
}

        return $qb;













-- truncate --------------------------------------------------------------------

# truncate


-- drop table -------------------------------------------------------------------

drop table project_trail_treatment;
drop table project_trailtreatment;


-- drop database ----------------------------------------------------------------

drop database esm_v3_develop;
create database esm_v3_develop;


-- drop column ------------------------------------------------------------------

ALTER TABLE project DROP COLUMN study_population_id;


-- drop foreign key -------------------------------------------------------------

ALTER TABLE project DROP FOREIGN KEY FK_2FB3D0EE79AA6B8A;


-- drop index -------------------------------------------------------------------

ALTER TABLE project DROP INDEX IDX_2FB3D0EE79AA6B8A;


-- FIND SPECIFIC FIELD DATABASE --------------------------------------------------






-- LIST ALL FOREIGN KEYS DATABASE -----------------------------------------------

select concat(fks.constraint_schema, '.', fks.table_name) as foreign_table,
       '->' as rel,
       concat(fks.unique_constraint_schema, '.', fks.referenced_table_name)
                                                          as primary_table,
       fks.constraint_name,
       group_concat(kcu.column_name
                    order by position_in_unique_constraint separator ', ')
                                                          as fk_columns
from information_schema.referential_constraints fks
         join information_schema.key_column_usage kcu
              on fks.constraint_schema = kcu.table_schema
                  and fks.table_name = kcu.table_name
                  and fks.constraint_name = kcu.constraint_name
-- where fks.constraint_schema = 'database name'
group by fks.constraint_schema,
         fks.table_name,
         fks.unique_constraint_schema,
         fks.referenced_table_name,
         fks.constraint_name
order by fks.constraint_schema,
         fks.table_name;


-- FIND SPECIFIC CONSTRAINT -------------------------------------------------------------------


select concat(fks.constraint_schema, '.', fks.table_name) as foreign_table,
       '->' as rel,
       concat(fks.unique_constraint_schema, '.', fks.referenced_table_name) as primary_table,
       fks.constraint_name,
       group_concat(kcu.column_name
                    order by position_in_unique_constraint separator ', ')
                                                          as fk_columns
from information_schema.referential_constraints fks
join information_schema.key_column_usage kcu on fks.constraint_schema = kcu.table_schema
and fks.table_name = kcu.table_name
and fks.constraint_name = kcu.constraint_name
-- where fks.constraint_schema = 'database name'
where fks.constraint_name like '%FK_2FB3D0EE79AA6B8A%'
group by fks.constraint_schema,
         fks.table_name,
         fks.unique_constraint_schema,
         fks.referenced_table_name,
         fks.constraint_name
order by fks.constraint_schema,
         fks.table_name;


-- LIST ALL INDEX ------------------------------------------------------------------------------------

select index_schema,
       index_name,
       group_concat(column_name order by seq_in_index) as index_columns,
       index_type,
       case non_unique
           when 1 then 'Not Unique'
           else 'Unique'
           end as is_unique,
       table_name
from information_schema.statistics
where table_schema not in ('information_schema', 'mysql', 'performance_schema', 'sys')
group by index_schema,
         index_name,
         index_type,
         non_unique,
         table_name
order by index_schema,
         index_name;

-- FIND SPECIFIC INDEX -------------------------------------------------------

select index_schema,
       index_name,
       group_concat(column_name order by seq_in_index) as index_columns,
       index_type,
       case non_unique
           when 1 then 'Not Unique'
           else 'Unique'
           end as is_unique,
       table_name
from information_schema.statistics
where table_schema not in ('information_schema', 'mysql', 'performance_schema', 'sys')
and index_name like '%IDX_2FB3D0EE79AA6B8A%'
group by index_schema,
         index_name,
         index_type,
         non_unique,
         table_name
order by index_schema,
         index_name;



-- dl participant speciality manage ------------------------------------------------------
select * from dl_participant_speciality;

update dl_participant_speciality SET code='ASS'             WHERE label='Assurance';
update dl_participant_speciality SET code='AC'              WHERE label='Autorité Compétente';
update dl_participant_speciality SET code='AUTR'            WHERE label='Autre';
update dl_participant_speciality SET code='CCF'             WHERE label='Cancérologie Cervico-faciale';
update dl_participant_speciality SET code='CARD'            WHERE label='Cardiologie';
update dl_participant_speciality SET code='CHIRG'           WHERE label='Chirurgie';
update dl_participant_speciality SET code='CE'              WHERE label='Comité Ethique';
update dl_participant_speciality SET code='CONDIT'          WHERE label='Conditionnement';
update dl_participant_speciality SET code='CYTO'            WHERE label='Cytologie';
update dl_participant_speciality SET code='DM'              WHERE label='Data management';
update dl_participant_speciality SET code='DERMA'           WHERE label='Dermatologie';
update dl_participant_speciality SET code='DISTRI'          WHERE label='Distribution';
update dl_participant_speciality SET code='ENDO'            WHERE label='Endocrinologie';
update dl_participant_speciality SET code='GASTRO'          WHERE label='Gastro-entérologie';
update dl_participant_speciality SET code='GENE'            WHERE label='Génétique';
update dl_participant_speciality SET code='GENO'            WHERE label='Génomique';
update dl_participant_speciality SET code='GERIA'           WHERE label='Gériatrie';
update dl_participant_speciality SET code='GCOOP'           WHERE label='Groupe Coopérateur';
update dl_participant_speciality SET code='GYNE-MED'        WHERE label='Gynécologie Médicale';
update dl_participant_speciality SET code='GYNE-OBS'        WHERE label='Gynécologie Obstétrique';
update dl_participant_speciality SET code='HEMA'            WHERE label='Hématologie';
update dl_participant_speciality SET code='HEPA'            WHERE label='Hépatologie';
update dl_participant_speciality SET code='HISTO'           WHERE label='Histologie';
update dl_participant_speciality SET code='IMMU'            WHERE label='Immunologie';
update dl_participant_speciality SET code='IMP'             WHERE label='Imprimeur';
update dl_participant_speciality SET code='INDUS'           WHERE label='Industriel';
update dl_participant_speciality SET code='INSTI'           WHERE label='Institutionnel';
update dl_participant_speciality SET code='MED-INT'         WHERE label='Médecine interne';
update dl_participant_speciality SET code='MED-NUC'         WHERE label='Médecine Nucléaire';
update dl_participant_speciality SET code='METABO'          WHERE label='Métabolisme';
update dl_participant_speciality SET code='NEURO'           WHERE label='Neurologie';
update dl_participant_speciality SET code='NUTRI'           WHERE label='Nutrition';
update dl_participant_speciality SET code='ONCO'            WHERE label='Oncologie';
update dl_participant_speciality SET code='ONCO-MED'        WHERE label='Oncologie Médicale';
update dl_participant_speciality SET code='ORL'             WHERE label='ORL';
update dl_participant_speciality SET code='PEDIA'           WHERE label='Pédiatrie';
update dl_participant_speciality SET code='PHARMA-CINE'     WHERE label='PharmacoCinétique';
update dl_participant_speciality SET code='PHARMA-GENE'     WHERE label='Pharmacogénétique';
update dl_participant_speciality SET code='PHARMACO'        WHERE label='Pharmacologie';
update dl_participant_speciality SET code='PNEU'            WHERE label='Pneumologie';
update dl_participant_speciality SET code='PROD'            WHERE label='Production';
update dl_participant_speciality SET code='PROTEO'          WHERE label='Protéomique';
update dl_participant_speciality SET code='RADIOPHARMA'     WHERE label='Radiopharmacien';
update dl_participant_speciality SET code='RADIOTHERA'      WHERE label='Radiothérapie';
update dl_participant_speciality SET code='RANDO'           WHERE label='Randomisation';
update dl_participant_speciality SET code='RHUMA'           WHERE label='Rhumatologie';
update dl_participant_speciality SET code='STE-PUB-MED-SOC' WHERE label='Santé publique et Médecine Soc';
update dl_participant_speciality SET code='SENO'            WHERE label='Sénologie';
update dl_participant_speciality SET code='STATI'           WHERE label='Statistiques';
update dl_participant_speciality SET code='STOMA'           WHERE label='Stomatologie';
update dl_participant_speciality SET code='TRANSCRI'        WHERE label='Transcriptomique';
update dl_participant_speciality SET code='TRSP'            WHERE label='Transport';
update dl_participant_speciality SET code='URO'             WHERE label='Urologie';
update dl_participant_speciality SET code='MED-GEN'         WHERE label='Médecine générale';
update dl_participant_speciality SET code='CIC'             WHERE label='CIC';
update dl_participant_speciality SET code='URC'             WHERE label='URC';
update dl_participant_speciality SET code='RCHR-CLI'        WHERE label='Recherche clinique';
update dl_participant_speciality SET code='NA'              WHERE label='NA';
update dl_participant_speciality SET code='NEPHRA'          WHERE label='Néphrologie';
update dl_participant_speciality SET code='ORTHO'           WHERE label='Orthopédie';
update dl_participant_speciality SET code='VIRO'            WHERE label='Virologie';


-- manage study population -------------------------------------------------------------------------------------

select * from dl_study_population;
select study_population from project;

update project set study_population='[]';



-- manage ROLE -------------------------------------------------------------------------------------------------
select * from role;

INSERT INTO role (parent_id, code, deleted_at, position) VALUES (null,'ROLE_ETMF_FULL_ACCESS',null,230);
INSERT INTO role (parent_id, code, deleted_at, position) VALUES (105,'ROLE_ETMF_READ',null,235);
INSERT INTO role (parent_id, code, deleted_at, position) VALUES (105,'ROLE_ETMF_WRITE',null,240);





-- USER departement --------------------------------------------------------------------------------------------

select * from user where department_id is null;


-- USER job ----------------------------------------------------------------------------------------------------

select * from user where job_id is null;

-- manage PHASE SETTING et PHASE STATUS --------------------------------------------------------------------------

select * from phase_setting;
select * from phase_setting_status;

INSERT INTO phase_setting_status (label, code) VALUES ('entity.PhaseSettingStatus.planned','PLD');
INSERT INTO phase_setting_status (label, code) VALUES ('entity.PhaseSettingStatus.screened','SCR');
INSERT INTO phase_setting_status (label, code) VALUES ('entity.PhaseSettingStatus.screeningFailure','SCRF');
INSERT INTO phase_setting_status (label, code) VALUES ('entity.PhaseSettingStatus.signedICF','SICF');
INSERT INTO phase_setting_status (label, code) VALUES ('entity.PhaseSettingStatus.ongoing','ONG');
INSERT INTO phase_setting_status (label, code) VALUES ('entity.PhaseSettingStatus.followUp','FLU');
INSERT INTO phase_setting_status (label, code) VALUES ('entity.PhaseSettingStatus.completed','CPL');
INSERT INTO phase_setting_status (label, code) VALUES ('entity.PhaseSettingStatus.withdrawals','WDW');
INSERT INTO phase_setting_status (label, code) VALUES ('entity.PhaseSettingStatus.lostFollowUp','LFLU');
INSERT INTO phase_setting_status (label, code) VALUES ('entity.PhaseSettingStatus.eos','EOS');
















