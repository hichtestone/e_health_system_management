<?php

namespace App\ETMF\Command;

use App\ETMF\Entity\DocumentVersion;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Doctrine\ORM\EntityManagerInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use App\ESM\Service\Mailer\MailerService;

/**
 * Class NotificationDocumentCommand
 * @package App\ETMF\Command
 */
class NotificationDocumentCommand extends Command
{
	public const EXPIRATION_EMAIL 		= 1;
	public const IMPLEMENTATION_EMAIL 	= 2;
	public const TYPE_EXPIRATION		= 'expiration';
	public const TYPE_IMPLEMENTATION 	= 'implementation';

	private $em;

	private $logger;

	private $mailer;

	private $notificationEmail;

	private $errors = [];

	private $usersEmailNotificationsExpiration = [];

	private $usersEmailNotificationsImplementation = [];

	private $documentsVersionExpiration = [];

	private $documentsVersionImplementation = [];

	protected function configure(): void
	{
		$this
			->setName('notification:document')
			->setDescription('CRON de controle des dates des documents')
		;
	}

	/**
	 * ImportRepriseCommand constructor.
	 * @param EntityManagerInterface $em
	 * @param ParameterBagInterface $parameters
	 * @param LoggerInterface $importLogger
	 * @param MailerService $mailer
	 */
	public function __construct(EntityManagerInterface $em, ParameterBagInterface $parameters, LoggerInterface $importLogger, MailerService $mailer)
	{
		parent::__construct();

		$this->em 							= $em;
		$this->logger						= $importLogger;
		$this->mailer						= $mailer;
		$this->notificationEmail			= $parameters->get('email.notification.adress');
	}

	/**
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 * @return int
	 * @throws LoaderError
	 * @throws RuntimeError
	 * @throws SyntaxError
	 * @throws Exception
	 */
	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$ioFiles = new SymfonyStyle($input, $output);
		$ioFiles->title(' ****** Start process DOCUMENTS ETMF ****** ');
		
		$documentsVersion = $this->em->getRepository(DocumentVersion::class)->findAll();
		$ioFiles->progressStart(count($documentsVersion));

		foreach ($documentsVersion as $documentVersion) {

			$this->processExpiration($documentVersion);

			if (array_key_exists($documentVersion->getId(), $this->documentsVersionExpiration) && array_key_exists($documentVersion->getId(), $this->usersEmailNotificationsExpiration)) {
				$this->sendNotifications(self::EXPIRATION_EMAIL, $documentVersion);
			}

			$this->processImplementation($documentVersion);

			if (array_key_exists($documentVersion->getId(), $this->documentsVersionImplementation) && array_key_exists($documentVersion->getId(), $this->usersEmailNotificationsImplementation)) {
				$this->sendNotifications(self::IMPLEMENTATION_EMAIL, $documentVersion);
			}

			$ioFiles->progressAdvance();
		}

		$ioFiles->progressFinish();
		$ioFiles->success(' ****** End process DOCUMENTS ETMF ****** ');

		return 0;
	}

	/**
	 * @param DocumentVersion $documentVersion
	 * @throws Exception
	 */
	private function processExpiration(DocumentVersion $documentVersion): void
	{
		$expiredAt 		= $documentVersion->getExpiredAt();
		$delayExpired 	= $documentVersion->getDocument()->getArtefact()->getDelayExpired();
//		$now 			= new \DateTime();
		$now = new \DateTime('2021-03-28');

		$beforeExpiredAt = clone $expiredAt;
		$beforeExpiredAt->sub(new \DateInterval($interval = "P" . $delayExpired . "D"));

		if (($now >= $beforeExpiredAt) && ($now <= $expiredAt)) {

			$this->setEmailUsersNotifications(self::TYPE_EXPIRATION, $documentVersion);
			$this->documentsVersionExpiration[$documentVersion->getId()] = $documentVersion;
		}
	}

	/**
	 * @param DocumentVersion $documentVersion
	 */
	private function processImplementation(DocumentVersion $documentVersion): void
	{
		$applicationDate = $documentVersion->getApplicationAt();
//		$now 			 = new \DateTime();
		$now = new \DateTime('2021-01-03');

		if ($now->format('Y-m-d') === $applicationDate->format('Y-m-d')) {

			$this->setEmailUsersNotifications(self::TYPE_IMPLEMENTATION, $documentVersion);
			$this->documentsVersionImplementation[$documentVersion->getId()] = $documentVersion;
		}
	}

	/**
	 * @param $type
	 * @param DocumentVersion $documentVersion
	 * @throws LoaderError
	 * @throws RuntimeError
	 * @throws SyntaxError
	 */
	private function sendNotifications($type, DocumentVersion $documentVersion): void
	{
		switch ($type) {

			case self::EXPIRATION_EMAIL:

				$emails 	= $this->usersEmailNotificationsExpiration[$documentVersion->getId()];
				$subject 	= 'ETMF Unicancer - notification d\'expiration d\'un document';
				$template 	= 'notificationExpiredDocument';

				break;

			case self::IMPLEMENTATION_EMAIL:

				$emails 	= $this->usersEmailNotificationsImplementation[$documentVersion->getId()];
				$subject 	= 'ETMF Unicancer - notification de mise en application d\'un document';
				$template 	= 'notificationImplementationDocument';

				break;
		}

		$this->mailer->setLocale('fr');

		$this->mailer->sendEmailETMF($template, [
			'documentVersion'  => $documentVersion,
			'dateNow' 			=> new \DateTime('2021-03-28')
		], $subject, [], $emails);

		$this->mailer->resetLocale();
	}

	/**
	 * @param $type
	 * @param DocumentVersion $documentVersion
	 */
	private function setEmailUsersNotifications($type, DocumentVersion $documentVersion): void
	{
		$project = $documentVersion->getDocument()->getProject();

		foreach ($documentVersion->getDocument()->getArtefact()->getMailgroups() as $mailgroup) {

			foreach ($mailgroup->getUsers() as $user) {

				foreach ($user->getUserProjects() as $userProject) {

					if ($userProject->getProject()->getId() === $project->getId()) {

						if ($type === self::TYPE_EXPIRATION) {

							$this->usersEmailNotificationsExpiration[$documentVersion->getId()][] = $user->getEmail();

						} elseif ($type === self::TYPE_IMPLEMENTATION) {

							$this->usersEmailNotificationsImplementation[$documentVersion->getId()][] = $user->getEmail();

						}
					}
				}
			}
		}
	}
}
