select * from project;

INSERT INTO center (center_status_id, project_id, number, name, deleted_at, source_id) VALUES
(1, 2, 'FcdvTR', 'CHU Paris12', null, null),
(1, 2, '43FdfrT', 'CLINIQUE de ma mère', null, null),
(1, 2, '3243tgHYu', 'DRX labas', null, null),
(1, 2, '34DEDR444', 'CHU jupiter', null, null),

(1, 3, 'FcdvTR', 'CHU Bordeaux', null, null),
(1, 3, '43FdfrT', 'CLINIQUE Saint-Etienne', null, null),
(1, 3, '3243tgHYu', 'DRX dreux', null, null),
(1, 3, '34DEDR444', 'CHU space', null, null);


INSERT INTO institution_center (center_id, institution_id) VALUES
(17, 1),
(18, 1);