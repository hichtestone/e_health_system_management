<?php

namespace App\ESM\DataFixtures;

use App\ESM\Entity\Role;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Class RoleFixtures.
 */
class RoleFixtures extends Fixture implements FixtureGroupInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager): void
    {
        $rolesList = [

            'ROLE_PROJECT_READ' => [
                'ROLE_PROJECT_WRITE',
                'ROLE_PROJECT_CLOSE_DEMAND',
                'ROLE_PROJECT_CLOSE',
                'ROLE_PROJECT_READ_CLOSED',
            ],

            'ROLE_USER_READ' => [
                'ROLE_USER_WRITE',
                'ROLE_USER_ACCESS',
                'ROLE_USER_ARCHIVE',
            ],

            'ROLE_PROFILE_READ' => [
                'ROLE_PROFILE_WRITE',
            ],

            'ROLE_INSTITUTION_READ' => [
                'ROLE_INSTITUTION_WRITE',
                'ROLE_INSTITUTION_ARCHIVE',
                'ROLE_INSTITUTION_READ_WRITE_ARCHIVE_DOCUMENTTRANSVERSE',
            ],

            'ROLE_INTERLOCUTOR_READ' => [
                'ROLE_INTERLOCUTOR_WRITE',
                'ROLE_INTERLOCUTOR_ARCHIVE',
                'ROLE_INTERLOCUTOR_READ_WRITE_ARCHIVE_DOCUMENTTRANSVERSE',
            ],

            'ROLE_SHOW_AUDIT_TRAIL' => [
            ],

            'ROLE_PROJECT_SETTINGS_READ' => [
                'ROLE_PROJECT_SETTINGS_WRITE',
                'ROLE_DIAGRAMVISIT_READ' => [
                    'ROLE_DIAGRAMVISIT_WRITE',
                    'ROLE_ECRF_READ',
                ],
                'ROLE_CONFIGURATION_MONITORING_MODEL',
            ],

            'ROLE_PROJECT_INTERVENANT_READ' => [],

            'ROLE_COMMUNICATION_READ' => [
                'ROLE_COMMUNICATION_WRITE',
                'ROLE_COMMUNICATION_DELETE',
            ],

            'ROLE_PUBLICATION_READ' => [
                'ROLE_PUBLICATION_WRITE',
                'ROLE_PUBLICATION_ARCHIVE',
            ],

            'ROLE_FUNDING_READ' => [
                'ROLE_FUNDING_WRITE',
                'ROLE_FUNDING_ARCHIVE',
            ],

            'ROLE_DATE_READ' => [
                'ROLE_DATE_WRITE',
            ],

            'ROLE_SUBMISSION_READ' => [
                'ROLE_SUBMISSION_WRITE',
                'ROLE_SUBMISSION_ARCHIVE',
            ],

            'ROLE_PROJECT_RULE_READ' => [
                'ROLE_PROJECT_RULE_WRITE',
            ],

            'ROLE_CENTER_READ' => [
                'ROLE_CENTER_WRITE',
                'ROLE_CENTER_ARCHIVE',
                'ROLE_DOCUMENTTRACKING_READ' => [
                    'ROLE_DOCUMENTTRACKING_WRITE',
                    'ROLE_DOCUMENTTRACKING_ARCHIVE',
                ],
                'ROLE_PATIENTTRACKING_READ' => [
                    'ROLE_PATIENTTRACKING_WRITE',
                    'ROLE_PATIENT_ARCHIVE',
                ],
                'ROLE_PROJECT_INTERLOCUTOR_READ' => [
                    'ROLE_PROJECT_INTERLOCUTOR_WRITE',
                ],
                'ROLE_MONITORING_REPORT_READ' => [
                    'ROLE_MONITORING_REPORT_LIST',
                    'ROLE_MONITORING_REPORT_WRITE',
                    'ROLE_MONITORING_REPORT_UPLOAD',
                ],
            ],

            'ROLE_DOCUMENTTRANSVERSE_READ' => [],
            'ROLE_DRUG_READ' => [
                'ROLE_DRUG_WRITE',
                'ROLE_DRUG_ARCHIVE',
                'ROLE_DRUG_READ_WRITE_ARCHIVE_DOCUMENTTRANSVERSE',
            ],

            'ROLE_DEVIATION_READ' => [
                'ROLE_DEVIATION_WRITE',
                'ROLE_DEVIATION_ACTION_DELETE',
                'ROLE_DEVIATION_CORRECTION_DELETE',
                'ROLE_DEVIATION_REVIEW',
                'ROLE_DEVIATION_CLOSE',
                'ROLE_DEVIATION_ASSOCIATE_SAMPLE',
            ],

            'ROLE_NO_CONFORMITY_SYSTEM_READ' => [
            	'ROLE_NO_CONFORMITY_SYSTEM_WRITE',
				'ROLE_NO_CONFORMITY_SYSTEM_QA_WRITE',
				'ROLE_NO_CONFORMITY_SYSTEM_ACTION_DELETE',
				'ROLE_NO_CONFORMITY_SYSTEM_CORRECTION_DELETE',
				'ROLE_NO_CONFORMITY_SYSTEM_CLOSE',
            ],

			'ROLE_DEVIATION_REVIEW_CREX_READ' => [
				'ROLE_DEVIATION_REVIEW_CREX',
			],

			'ROLE_NO_CONFORMITY_SYSTEM_CREX_READ' => [
				'ROLE_NO_CONFORMITY_SYSTEM_REVIEW_CREX',
			],

            'ROLE_MONITORING_MODEL_READ' => [
                'ROLE_MONITORING_MODEL_WRITE',
            ],

			'ROLE_DEVIATION_SAMPLE_READ' => [
				'ROLE_DEVIATION_SAMPLE_WRITE',
				'ROLE_DEVIATION_SAMPLE_ACTION_DELETE',
				'ROLE_DEVIATION_SAMPLE_CORRECTION_DELETE',
				'ROLE_DEVIATION_SAMPLE_ASSOCIATE_DEVIATION',
				'ROLE_DEVIATION_SAMPLE_CLOSE',
			],

			'ROLE_ETMF_FULL_ACCESS' => [
				'ROLE_ETMF_READ',
				'ROLE_ETMF_WRITE'
			]
        ];

        $i = 10;

        // Roles parents
        foreach ($rolesList as $code => $children_roles) {
            $role = new Role();
            $role->setCode($code);
            $role->setPosition($i);

            $i += 5;

            $manager->persist($role);

            // Roles Enfants
            if (is_array($children_roles)) {
                $this->createRecursiveRole($children_roles, $role, $manager, $i);
            }
        }
        $manager->flush();
    }

	public static function getGroups(): array
	{
		return ['profile', 'recette-light', 'prod'];
	}

    /**
     * @param mixed $roles
     */
    private function createRecursiveRole($roles, Role $parent_role, ObjectManager $manager, int $position): void
    {
        // vide ou string
        if (empty($roles) || !is_array($roles)) {
            return;
        }

        // Roles parents
        foreach ($roles as $code => $children_roles) {
            $role = new Role();
            $role->setCode(is_array($children_roles) ? $code : $children_roles);
            $role->setPosition($position);

            $position += 5;

            // parent
            $role->setParent($parent_role);

            $manager->persist($role);

            // Roles Enfants
            $this->createRecursiveRole($children_roles, $role, $manager, $position);
        }
    }
}
