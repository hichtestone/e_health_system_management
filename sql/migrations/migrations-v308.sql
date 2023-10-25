-- Déviation échantillons biologique -------------------------------------------------------------------------------------------

-- Contexte de détection

INSERT INTO `dl_detection_context` (`id`, `label`, `deleted_at`) VALUES
(1, 'constat terrain', NULL),
(2, 'audit interne', NULL),
(3, 'incident, accident', NULL),
(4, 'réclamation', NULL),
(5, 'autre', NULL);


-- Structure/établisement ayant détecté la déviation

INSERT INTO `dl_detection_center` (`id`, `label`, `deleted_at`) VALUES
(1, 'centre investigateur', NULL),
(2, 'CRB Unicancer', NULL),
(3, 'unicancer', NULL),
(4, 'plateforme d\'analyse', NULL),
(5, 'centralisée', NULL),
(6, 'transporteur', NULL),
(7, 'autre', NULL);

-- Processus impliqué(s)

INSERT INTO `dl_process_involved` (`id`, `label`, `deleted_at`) VALUES
(1, 'préparation de l\'expédition', NULL),
(2, 'transport', NULL),
(3, 'réception', NULL),
(4, 'conservation', NULL),
(5, 'identification, enregistrement', NULL),
(6, 'traitement, transformation', NULL),
(7, 'autre', NULL);

-- Type (nature) de déviation

INSERT INTO `dl_nature_type` (id, label, deleted_at) VALUES
(1, 'destruction/perte d\'échantillon', NULL),
(2, 'excursion de température', NULL),
(3, 'conditionnement primaire non conforme', NULL),
(4, 'colis non conforme/tubes en vrac', NULL),
(5, 'tube(s) manquant(s) ou supplémentaire(s)', NULL),
(6, 'problème identification/étiquetage', NULL),
(7, 'non-respect des BPL/Lab manuel', NULL),
(8, 'consentement non conforme', NULL),
(9, 'bordereau de transfert', NULL),
(10, 'manquant/non-conforme', NULL),
(11, 'autre', NULL);

-- Impact potentiel

INSERT INTO `dl_potential_impact_sample` (`id`, `label`, `deleted_at`) VALUES
(1, 'intégrité des échantillons', NULL),
(2, 'qualité des données', NULL),
(3, 'ethique/règlementaire', NULL),
(4, 'autre', NULL);

-- Décision prise

INSERT INTO `dl_decision_taken` (`id`, `label`, `deleted_at`) VALUES
(1, 'a placer en attente d\'action corrective', NULL),
(2, 'en accord pour conserver/enregistrer/traiter les échantillons', NULL),
(3, 'requalification des échantillons', NULL),
(4, 'destruction des échantillons', NULL),
(5, 'retour des échantillons au centre', NULL);




-- Deviations NC - Système ------------------------------------------------------------------------------------------

-- Processus

INSERT INTO `dl_process_system` (`id`, `code`, `label`) VALUES
(1, 'M1', 'mettre en oeuvre les objectifs de la R&D'),
(2, 'M2', 'piloter l\'activité de la R&D'),
(3, 'M3', 'promouvoir l\'amélioration continue'),
(4, 'M4', 'développer le capital humain'),
(5, 'R1', 'Constituer et maintenir une plateforme de données de vraie vie'),
(6, 'R2', 'Exploiter et valoriser une plateforme de données de vraie vie'),
(7, 'R3', 'Concevoir, valier et mettre en oeuvre le projet dessai clinique'),
(8, 'R4', 'Conduire et clôturer l\'EC'),
(9, 'R5', 'Surveiller et gérer la sécurité des EC'),
(10, 'S1', 'Promouvoir les activités de la R&D Unicancer'),
(11, 'S2', 'Adapter les outils SI'),
(12, 'S3', 'Piloter le suivi financier de la R&D');


-- Roles -------------------------------------------------------------------------------------------------------------
select * from role;

-- no conformity

INSERT INTO role (parent_id, code, deleted_at, position) VALUES
(null, 'ROLE_NO_CONFORMITY_SYSTEM_READ', null, 150);

INSERT INTO role (parent_id, code, deleted_at, position) VALUES
(90, 'ROLE_NO_CONFORMITY_SYSTEM_WRITE', null, 155),
(90, 'ROLE_NO_CONFORMITY_SYSTEM_QA_WRITE', null, 160),
(90, 'ROLE_NO_CONFORMITY_SYSTEM_ACTION_CREATE', null, 165),
(90, 'ROLE_NO_CONFORMITY_SYSTEM_ACTION_DELETE', null, 170),
(90, 'ROLE_NO_CONFORMITY_SYSTEM_CORRECTION_DELETE', null, 175),
(90, 'ROLE_NO_CONFORMITY_SYSTEM_CLOSE', null, 180);


INSERT INTO role (parent_id, code, deleted_at, position) VALUES
(null, 'ROLE_NO_CONFORMITY_SYSTEM_CREX_READ', null, 185);

INSERT INTO role (parent_id, code, deleted_at, position) VALUES
(97, 'ROLE_NO_CONFORMITY_SYSTEM_REVIEW_CREX', null, 190);


INSERT INTO role (parent_id, code, deleted_at, position) VALUES
(null, 'ROLE_DEVIATION_SAMPLE_READ', null, 195);

INSERT INTO role (parent_id, code, deleted_at, position) VALUES
(99, 'ROLE_DEVIATION_SAMPLE_WRITE', null, 200),
(99, 'ROLE_DEVIATION_SAMPLE_ACTION_DELETE', null, 205),
(99, 'ROLE_DEVIATION_SAMPLE_CORRECTION_DELETE', null, 210),
(99, 'ROLE_DEVIATION_SAMPLE_ASSOCIATE_DEVIATION', null, 215),
(99, 'ROLE_DEVIATION_SAMPLE_CLOSE', null, 220);