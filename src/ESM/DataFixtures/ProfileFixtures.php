<?php

namespace App\ESM\DataFixtures;

use App\ESM\Entity\Profile;
use App\ESM\Entity\Role;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Class ProfileFixtures.
 */
class ProfileFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
	/**
	 * {@inheritdoc}
	 * @throws \Exception
	 */
    public function load(ObjectManager $manager): void
    {
        $profileRoles = [
            [
                'name' => 'Admin',
                'acronyme' => 'ADM',
				'type' => 'ESM',
                'roles' => 'all',
            ],
            [
                'name' => 'Attaché de Recherche Clinique',
                'acronyme' => 'ARC',
				'type' => 'ESM',
				'roles' => ['ROLE_PROJECT_READ', 'ROLE_PROJECT_WRITE'],
            ],
            [
                'name' => 'Chef de Projet',
                'acronyme' => 'CDP',
				'type' => 'ESM',
				'roles' => ['ROLE_PROJECT_READ', 'ROLE_PROJECT_WRITE', 'ROLE_PROJECT_CLOSE_DEMAND', 'ROLE_PROJECT_READ_CLOSED'],
            ],
            [
                'name' => 'Assurance Qualité',
                'acronyme' => 'QA',
				'type' => 'ESM',
				'roles' => ['ROLE_PROJECT_READ', 'ROLE_PROJECT_WRITE'],
            ],
            [
                'name' => 'Data Manager',
                'acronyme' => 'DM',
				'type' => 'ESM',
				'roles' => ['ROLE_PROJECT_READ', 'ROLE_PROJECT_WRITE'],
            ],
            [
                'name' => 'Directeur des Opérations',
                'acronyme' => 'Dir OP',
				'type' => 'ESM',
				'roles' => ['ROLE_PROJECT_READ', 'ROLE_PROJECT_WRITE'],
            ],
            [
                'name' => 'Investigateur',
                'acronyme' => 'INV',
				'type' => 'ESM',
				'roles' => ['ROLE_PROJECT_READ', 'ROLE_PROJECT_WRITE'],
            ],
			[
				'name' => 'Assurance Qualité',
				'acronyme' => 'AQ',
				'type' => 'ESM',
				'roles' => ['ROLE_PROJECT_READ', 'ROLE_PROJECT_WRITE'],
			],
			[
				'name' => 'Arc/Externe',
				'acronyme' => 'ARC-EXT',
				'type' => 'ESM',
				'roles' => ['ROLE_PROJECT_READ', 'ROLE_PROJECT_WRITE'],
			],
			[
				'name' => 'Assistance',
				'acronyme' => 'ASST',
				'type' => 'ESM',
				'roles' => ['ROLE_PROJECT_READ', 'ROLE_PROJECT_WRITE'],
			],
			[
				'name' => 'Chef de Projet Externe',
				'acronyme' => 'CDP-EXT',
				'type' => 'ESM',
				'roles' => ['ROLE_PROJECT_READ', 'ROLE_PROJECT_WRITE'],
			],
			[
				'name' => 'Coordinateur Echantillon',
				'acronyme' => 'COORD-ECH',
				'type' => 'ESM',
				'roles' => ['ROLE_PROJECT_READ', 'ROLE_PROJECT_WRITE'],
			],
			[
				'name' => 'Redacteur Médical',
				'acronyme' => 'RM',
				'type' => 'ESM',
				'roles' => ['ROLE_PROJECT_READ', 'ROLE_PROJECT_WRITE'],
			],
			[
				'name' => 'Réglementaire',
				'acronyme' => 'RGL',
				'type' => 'ESM',
				'roles' => ['ROLE_PROJECT_READ', 'ROLE_PROJECT_WRITE'],
			],
			[
				'name' => 'Super Admin',
				'acronyme' => 'SUP-ADM',
				'type' => 'ESM',
				'roles' => ['ROLE_PROJECT_READ', 'ROLE_PROJECT_WRITE'],
			],
			[
				'name' => 'Admin',
				'acronyme' => 'ADM',
				'type' => 'ETMF',
				'roles' => [],
			],
			[
				'name' => 'viewer',
				'acronyme' => 'ETMF-VIEWER',
				'type' => 'ETMF',
				'roles' => [],
			],
			[
				'name' => 'Writer',
				'acronyme' => 'ETMF-WRITER',
				'type' => 'ETMF',
				'roles' => [],
			],
        ];

        foreach ($profileRoles as $profileRole) {

            $entity = new Profile();
            $entity->setName($profileRole['name']);
            $entity->setAcronyme($profileRole['acronyme']);
            $entity->setType($profileRole['type']);

            switch (true) {

                case 'all' === $profileRole['roles']:
                    $roles = $manager->getRepository(Role::class)->findAll();
                    break;

                case is_array($profileRole['roles']):
                    $roles = [];
                    foreach ($profileRole['roles'] as $sRole) {
                        $role = $manager->getRepository(Role::class)->findOneBy(['code' => $sRole]);
                        if (null === $role) {
                            echo 'Role '.$sRole.'unfound';
                            exit();
                        }
                        $roles[] = $role;
                    }
                    break;

                default:
                    $roles = [];
            }

            foreach ($roles as $role) {
                $entity->addRole($role);
            }

            $manager->persist($entity);
        }

        $manager->flush();
    }

	public static function getGroups(): array
	{
		return ['profile', 'recette-light', 'prod'];
	}

	public function getDependencies(): array
	{
		return [
			RoleFixtures::class,
		];
	}
}
