use esm_v3_develop;

-- 22/04
  -- Creation
CREATE TABLE dl_type_deviation (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, type VARCHAR(255) NOT NULL, code VARCHAR(10) NOT NULL, position INT NOT NULL, delete_at DATETIME DEFAULT NULL, INDEX IDX_78A5B3BF727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;
ALTER TABLE dl_type_deviation ADD CONSTRAINT FK_78A5B3BF727ACA70 FOREIGN KEY (parent_id) REFERENCES dl_type_deviation (id);

  -- Insertion
INSERT INTO `dl_type_deviation` (`id`, `parent_id`, `type`, `code`, `position`, `delete_at`) VALUES
(1, NULL, 'IMP', '01', 10, NULL),
(2, 1, 'Supply/storage/retrieval/destruction', '01.01', 15, NULL),
(3, 1, 'Prescription/administration/compliance', '01.02', 20, NULL),
(4, 1, 'IMP accountability', '01.03', 25, NULL),
(5, 1, 'Manufacturing/packaging/labelling', '01.04', 30, NULL),
(6, NULL, 'IC', '02', 35, NULL),
(7, 6, 'Lack of IC in the site', '02.01', 40, NULL),
(8, 6, 'IC process', '02.02', 45, NULL),
(9, 6, 'IC form', '02.03', 50, NULL),
(10, NULL, 'IEC/IRB', '03', 55, NULL),
(11, 10, 'Lack of IEC/IRB favourable opinion in the site', '03.01', 60, NULL),
(12, 10, 'Opinion/amendments/notifications to the IEC/IRB', '03.02', 65, NULL),
(13, 10, 'Composition, functions and operation', '03.03', 70, NULL),
(14, NULL, 'Subject protection', '04', 75, NULL),
(15, 14, 'Design of the trial', '04.01', 80, NULL),
(16, 14, 'Personal data protection', '04.02', 85, NULL),
(17, 14, 'Safeguard of the safety and well-being of subject', '04.03', 90, NULL),
(18, 14, 'Insurance/indemnity/compensation to subjects', '04.04', 95, NULL),
(19, 14, 'Payment to trial subjects', '04.05', 100, NULL),
(20, NULL, 'Regulatory issues', '05', 105, NULL),
(21, 20, 'Lack of regulatory authorities (RA) approval at the site', '05.01', 110, NULL),
(22, 20, 'Approval/amendments/notifications to the RA', '05.02', 115, NULL),
(23, 20, 'Manufacturing/importing authorisation', '05.03', 120, NULL),
(24, NULL, 'Trial management (sponsor)', '06', 125, NULL),
(25, 24, 'Protocol/CRF/diary/questionnaires design', '06.01', 130, NULL),
(26, 24, 'Data management', '06.02', 135, NULL),
(27, 24, 'Monitoring', '06.03', 140, NULL),
(28, 24, 'Audit', '06.04', 145, NULL),
(29, 24, 'Document control', '06.05', 150, NULL),
(30, 24, 'Statistical analysis', '06.06', 155, NULL),
(31, 24, 'CSR', '06.07', 160, NULL),
(32, NULL, 'Computer system', '07', 165, NULL),
(33, 32, 'Computer validation', '07.01', 170, NULL),
(34, 32, 'Audit trail and authorised access', '07.02', 175, NULL),
(35, 32, 'Physical security system and backup', '07.03', 180, NULL),
(36, NULL, 'Investigational site', '08', 185, NULL),
(37, 36, 'Protocol compliance (selection criteria)', '08.01', 190, NULL),
(38, 36, 'Protocol compliance (assessment of efficacy)', '08.02', 195, NULL),
(39, 36, 'Protocol compliance (safety reporting)', '08.03', 200, NULL),
(40, 36, 'Protocol compliance (others)', '08.04', 205, NULL),
(41, 36, 'Reporting in CRF/diary', '08.05', 210, NULL),
(42, NULL, 'Laboratory/technical facilities', '09', 215, NULL),
(43, 42, 'Certification/accreditation', '09.01', 220, NULL),
(44, 42, 'Assay validation', '09.02', 225, NULL),
(45, 42, 'Normal values/ranges/updates', '09.03', 230, NULL),
(46, 42, 'Shipment/storage/labelling/kit samples', '09.04', 235, NULL),
(47, 42, 'Accountability/traceability of samples', '09.05', 240, NULL),
(48, 42, 'Analysis/reporting (laboratory)', '09.06', 245, NULL),
(49, 42, 'Technical validation', '09.07', 250, NULL),
(50, NULL, 'General', '10', 255, NULL),
(51, 50, 'Organisation and personnel', '10.01', 260, NULL),
(52, 50, 'Facilities and equipment', '10.02', 265, NULL),
(53, 50, 'Qualification/training', '10.03', 270, NULL),
(54, 50, 'SOPs', '10.04', 275, NULL),
(55, 50, 'Randomisation/blinding/codes IMP', '10.05', 280, NULL),
(56, 50, 'Source documentation', '10.06', 285, NULL),
(57, 50, 'Essential documents', '10.07', 290, NULL),
(58, 50, 'Direct access to data', '10.08', 295, NULL),
(59, 50, 'Contracts/agreements', '10.09', 300, NULL),
(60, NULL, 'Others', '11', 305, NULL),
(61, 60, 'other', '11', 310, NULL);


-- 23/04

INSERT INTO `role` (`id`, `parent_id`, `code`, `deleted_at`, `position`) VALUES
(61, NULL, 'ROLE_DEVIATION_READ', NULL, 95),
(62, 61, 'ROLE_DEVIATION_WRITE', NULL, 100),
(63, 61, 'ROLE_DEVIATION_ACTION_DELETE', NULL, 105),
(64, 61, 'ROLE_DEVIATION_CORRECTION_DELETE', NULL, 110),
(65, 61, 'ROLE_DEVIATION_REVIEW', NULL, 115),
(66, 61, 'ROLE_DEVIATION_CLOSE', NULL, 120),
(67, 61, 'ROLE_DEVIATION_ASSOCIATE_SAMPLE', NULL, 125),
(68, NULL, 'ROLE_DEVIATION_REVIEW_CREX_READ', NULL, 130),
(69, 67, 'ROLE_DEVIATION_REVIEW_CREX', NULL, 135),
(70, NULL, 'ROLE_NON_CONFORMITY_READ', NULL, 140);
