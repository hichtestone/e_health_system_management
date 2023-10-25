<?php

namespace App\ESM\DataFixtures;

use App\ESM\Entity\DropdownList\Civility;
use App\ESM\Entity\DropdownList\Department;
use App\ESM\Entity\Profile;
use App\ESM\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

/**
 * Class UserFixtures
 * @package App\ESM\DataFixtures
 */
class UserFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
	/**
	 * @var UserPasswordEncoderInterface
	 */
	private $encoder;

	public function __construct(UserPasswordEncoderInterface $encoder)
	{
		$this->encoder = $encoder;
	}

	public function load(ObjectManager $manager): void
	{
		$profile    = $manager->getRepository(Profile::class)->findOneBy(['acronyme' => 'ADM']);
		$civility   = $manager->getRepository(Civility::class)->find(1);
		$department = $manager->getRepository(Department::class)->find(1);

		$profile2  = $manager->getRepository(Profile::class)->findOneBy(['acronyme' => 'CDP']);
		$civility2 = $manager->getRepository(Civility::class)->find(2);

		$job 		= $this->getReference('job-1');
		$job_cpd 	= $this->getReference('job-9');
		$job_cec 	= $this->getReference('job-6');

		$user            = new User();
		$encodedPassword = $this->encoder->encodePassword($user, 'Test2000!');
		$user->setEmail('dev@clinfile.com');
		$user->setFirstName('Dev');
		$user->setLastName('Cinfile');
		$user->setPassword($encodedPassword);
		$user->setPasswordUpdatedAt(new \DateTime());
		$user->setProfile($profile);
		$user->setLocale('fr');
		$user->setIsSuperadmin(true);
		$user->setDepartment($department);
		$user->setCivility($civility2);
		$user->setHasAccessEsm(true);
		$user->setHasAccessEtmf(true);
		$user->setPhone('0102030405');
		$user->setNote('remarque...');
		$user->setJob($job_cec);
		$user->setSociety($this->getReference('society-3'));

		$this->addReference('user-admin', $user);

		$this->addReference(AppFixtures::FIRST_USER, $user);

		$manager->persist($user);
		$manager->flush();

		if ($_ENV['APP_ENV'] === 'dev') {

			$user2            = new User();
			$encodedPassword2 = $this->encoder->encodePassword($user2, 'Test2000!');
			$user2->setEmail('dev2@clinfile.com');
			$user2->setFirstName('Dev2');
			$user2->setLastName('Cinfile2');
			$user2->setPassword($encodedPassword2);
			$user2->setPasswordUpdatedAt(new \DateTime());
			$user2->setProfile($profile2);
			$user2->setLocale('fr');
			$user2->setDepartment($department);
			$user2->setCivility($civility);
			$user2->setHasAccessEsm(true);
			$user2->setHasAccessEtmf(false);
			$user2->setPhone('0102030405');
			$user2->setNote('remarque...');
			$user2->setJob($job_cpd);
			$user2->setSociety($this->getReference('society-3'));

			$this->addReference('user-cdp', $user2);

			$user3            = new User();
			$encodedPassword3 = $this->encoder->encodePassword($user3, 'Test2000!');
			$user3->setEmail('cdp@clinfile.com');
			$user3->setFirstName('CDP');
			$user3->setLastName('Cinfile CDP');
			$user3->setPassword($encodedPassword3);
			$user3->setPasswordUpdatedAt(new \DateTime());
			$user3->setProfile($profile2);
			$user3->setLocale('fr');
			$user3->setDepartment($department);
			$user3->setCivility($civility);
			$user3->setHasAccessEsm(true);
			$user3->setHasAccessEtmf(false);
			$user3->setPhone('0102030405');
			$user3->setNote('remarque...');
			$user3->setJob($job_cpd);
			$user3->setSociety($this->getReference('society-3'));

			$user4            = new User();
			$encodedPassword4 = $this->encoder->encodePassword($user4, 'Test2000!');
			$user4->setEmail('cec@clinfile.com');
			$user4->setFirstName('CEC');
			$user4->setLastName('Cinfile CEC');
			$user4->setPassword($encodedPassword4);
			$user4->setPasswordUpdatedAt(new \DateTime());
			$user4->setProfile($profile2);
			$user4->setLocale('fr');
			$user4->setDepartment($department);
			$user4->setCivility($civility);
			$user4->setHasAccessEsm(true);
			$user4->setHasAccessEtmf(false);
			$user4->setPhone('0102030405');
			$user4->setNote('remarque...');
			$user4->setJob($job_cec);
			$user4->setSociety($this->getReference('society-3'));

			$manager->persist($user2);
			$manager->persist($user3);
			$manager->persist($user4);

			$manager->flush();

			$users = [
				[$civility,  'Neïla', 		'Sebaïs', 	 'clinfile-stagiaire3@clinfile.com', $profile],
				[$civility2, 'Raphaël', 	'Durville',  'rdurville@clinfile.com', 			 $profile],
				[$civility2, 'Jordan',  	'Gonçalves', 'jgoncalves@clinfile.com', 		 $profile],
				[$civility2, 'Matthias', 	'Perret', 	 'mperret@clinfile.com', 			 $profile],
				[$civility2, 'Miary', 		'Rabehasy',  'mrabehasy@clinfile.com', 			 $profile],
				[$civility2, 'Khadar', 		'Yonis', 	 'kyonis@clinfile.com', 			 $profile],
				[$civility,  'Neïla2', 		'Sebaïs2', 	 'nsebais@clinfile.com', 			 $profile2],
				[$civility2, 'Raphaël2', 	'Durville2', 'rdurville2@clinfile.com', 		 $profile2],
				[$civility,  'Martin', 		'IPUY', 	 'mipuy@clinfile.com', 				 $profile],
				[$civility2, 'Martin2', 	'IPUY2', 	 'mipuy2@clinfile.com', 			 $profile2],
			];

			$countReference = 1;
			foreach ($users as $row) {

				$user = new User();
				$user->setCivility($row[0]);
				$user->setFirstName($row[1]);
				$user->setLastName($row[2]);
				$user->setEmail($row[3]);
				$user->setProfile($row[4]);
				$user->setHasAccessEsm(true);
				$user->setHasAccessEtmf(false);
				$user->setJob($job);
				$user->setSociety($this->getReference('society-3'));
				$user->setDepartment($department);
				$user->setLocale('fr');
				$user->setPassword($encodedPassword2);
				$user->setPasswordUpdatedAt(new \DateTime());
				$user->setPhone('0102030405');

				$this->addReference('user-'.$countReference, $user);
				$countReference++;

				$manager->persist($user);
			}

			$manager->flush();
		}
	}

	public static function getGroups(): array
	{
		return ['user', 'userCreate', 'recette-light', 'prod'];
	}

	public function getDependencies(): array
	{
		return [
			JobFixtures::class,
			SocietyFixtures::class,
			ProfileFixtures::class
		];
	}
}
