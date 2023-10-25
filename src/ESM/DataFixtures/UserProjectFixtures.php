<?php

namespace App\ESM\DataFixtures;

use App\ESM\Entity\UserProject;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Class UserProjectFixtures
 * @package App\ESM\DataFixtures
 */
class UserProjectFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
	public function load(ObjectManager $manager): void
	{
		$userProject1 = new UserProject();
		$userProject1->setProject($this->getReference('project-1'));
        $userProject1->setUser($this->getReference('user-admin'));
        $userProject1->setEnabledAt(new \DateTime());
        $userProject1->setDisabledAt(null);
        $this->setReference('userProject-1', $userProject1);
        $manager->persist($userProject1);

        $userProject2 = new UserProject();
        $userProject2->setProject($this->getReference('project-1'));
        $userProject2->setUser($this->getReference('user-cdp'));
        $userProject2->setEnabledAt(new \DateTime());
        $userProject2->setDisabledAt(null);
        $this->setReference('userProject-2', $userProject2);
        $manager->persist($userProject2);


        $userProject3 = new UserProject();
        $userProject3->setProject($this->getReference('project-1'));
        $userProject3->setUser($this->getReference('user-6'));
        $userProject3->setEnabledAt(new \DateTime());
        $userProject3->setDisabledAt(null);
        $this->setReference('userProject-3', $userProject3);
        $manager->persist($userProject3);


        $userProject4 = new UserProject();
        $userProject4->setProject($this->getReference('project-2'));
        $userProject4->setUser($this->getReference('user-admin'));
        $userProject4->setEnabledAt(new \DateTime());
        $userProject4->setDisabledAt(null);
        $this->setReference('userProject-4', $userProject4);
        $manager->persist($userProject4);

        $userProject5 = new UserProject();
        $userProject5->setProject($this->getReference('project-2'));
        $userProject5->setUser($this->getReference('user-1'));
        $userProject5->setEnabledAt(new \DateTime());
        $userProject5->setDisabledAt(null);
        $this->setReference('userProject-5', $userProject5);
        $manager->persist($userProject5);


        $userProject6 = new UserProject();
        $userProject6->setProject($this->getReference('project-2'));
        $userProject6->setUser($this->getReference('user-3'));
        $userProject6->setEnabledAt(new \DateTime());
        $userProject6->setDisabledAt(null);
        $this->setReference('userProject-6', $userProject6);
        $manager->persist($userProject6);

        $userProject7 = new UserProject();
        $userProject7->setProject($this->getReference('project-2'));
        $userProject7->setUser($this->getReference('user-6'));
        $userProject7->setEnabledAt(new \DateTime());
        $userProject7->setDisabledAt(null);
        $this->setReference('userProject-7', $userProject7);
        $manager->persist($userProject7);


        $userProject8 = new UserProject();
        $userProject8->setProject($this->getReference('project-2'));
        $userProject8->setUser($this->getReference('user-7'));
        $userProject8->setEnabledAt(new \DateTime());
        $userProject8->setDisabledAt(null);
        $this->setReference('userProject-8', $userProject8);
        $manager->persist($userProject8);

		$manager->flush();
	}

	public static function getGroups(): array
	{
		return ['userProject', 'user', 'project'];
	}

	public function getDependencies(): array
	{
		return [
			UserFixtures::class,
			ProjectFixtures::class
		];
	}
}
