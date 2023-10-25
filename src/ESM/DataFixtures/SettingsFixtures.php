<?php

namespace App\ESM\DataFixtures;

use App\ESM\Entity\Settings;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * Class SettingsFixtures
 * @package App\ESM\DataFixtures
 */
class SettingsFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $params = [
            'variable_date_id' => '2', // Id de la variable date dans variable_type
            'job_cdp_id' => '8', // Id du JOB CDP dans la table dl_user_job
            'job_cec_id' => '5', // Id du JOB CEC dans la table dl_user_job
        ];

        foreach ($params as $key => $value) {
            $entity = new Settings();
            $entity->setName($key);
            $entity->setValue($value);
            $manager->persist($entity);
        }

        $manager->flush();
    }
}
