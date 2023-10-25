select * from user;
select * from report_model;
select * from report_model_version;
select * from report_block;
select * from report_model_version_block;
select * from report_block_param;

truncate report_model;
truncate report_model_version;
truncate report_block;
truncate report_model_version_block;

INSERT INTO report_model (report_type, visit_type, name, deleted_at) VALUES
(0, 0, 'test1', null),
(0, 1, 'test2', null),
(1, 0, 'test3', null),
(1, 1, 'test4', null),
(2, 0, 'test5', null),
(2, 1, 'test6', null),
(0, 0, 'test7', null),
(1, 0, 'test8', null),
(2, 0, 'test9', null),
(0, 1, 'test10', null),
(1, 0, 'test11', null),
(2, 1, 'test12', null);

INSERT INTO report_model_version (created_by_id, published_by, obsoleted_by, report_model_id, status, created_at, published_at, obsoleted_at, number_version) VALUES
(1, 1, null, 1, 0, NOW(), null, null, 3454),
(1, 1, null, 1, 1, NOW(), null, null, 43565),
(1, 1, null, 1, 2, NOW(), null, null, 1254),
(1, 1, null, 2, 0, NOW(), null, null, 76545),
(1, 1, null, 2, 1, NOW(), null, null, 342),
(1, 1, null, 2, 2, NOW(), null, null, 5694),
(1, 1, null, 3, 0, NOW(), null, null, 1254),
(1, 1, null, 3, 1, NOW(), null, null, 3456),
(1, 1, null, 3, 2, NOW(), null, null, 23546),
(1, 1, null, 4, 1, NOW(), null, null, 3456),
(1, 1, null, 4, 2, NOW(), null, null, 235446),
(1, 1, null, 5, 1, NOW(), null, null, 235426),
(1, 1, null, 5, 2, NOW(), null, null, 235416),
(1, 1, null, 6, 1, NOW(), null, null, 235469),
(1, 1, null, 6, 2, NOW(), null, null, 2354656),
(1, 1, null, 7, 1, NOW(), null, null, 2353446),
(1, 1, null, 7, 2, NOW(), null, null, 2354656),
(1, 1, null, 8, 1, NOW(), null, null, 2354699),
(1, 1, null, 8, 2, NOW(), null, null, 2354632),
(1, 1, null, 9, 1, NOW(), null, null, 2354236),
(1, 1, null, 9, 2, NOW(), null, null, 2354236),
(1, 1, null, 10, 1, NOW(), null, null, 2354566),
(1, 1, null, 10, 2, NOW(), null, null, 2354876),
(1, 1, null, 11, 1, NOW(), null, null, 2354786),
(1, 1, null, 11, 2, NOW(), null, null, 2353446),
(1, 1, null, 12, 1, NOW(), null, null, 2354676),
(1, 1, null, 12, 2, NOW(), null, null, 2354667);

INSERT INTO report_block (name, sys) VALUES
('identification',                  1),
('participants',                    1),
('validation',                      1),
('date_of_visits',                  1),
('documents_discussed',             1),
('follow_up',                       1),
('close_out',                       1),
('table_of_patients_notes_checked', 1),
('patient_status',                  1),
('action_issues_log',               1),
('deviations_log',                  1);

INSERT INTO report_model_version_block (version_id, block_id, ordering) VALUES
(1, 1, 1),
(1, 2, 2),
(1, 3, 3),
(1, 4, 4),
(1, 5, 5),
(1, 6, 6),
(1, 7, 7),
(1, 8, 8),
(1, 9, 9),
(1, 10, 10),
(1, 11, 11);

INSERT INTO report_model_version_block (version_id, block_id, ordering) VALUES
(1, 2, 2),
(1, 3, 3),
(1, 4, 4),
(1, 5, 5),
(1, 6, 6),
(1, 7, 7),
(1, 8, 8),
(1, 9, 9),
(1, 10, 10),
(1, 11, 11);




