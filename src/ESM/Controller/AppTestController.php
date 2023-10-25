<?php

namespace App\ESM\Controller;

use App\ESM\Entity\Center;
use App\ESM\Entity\ReportModelVersion;
use App\ESM\Service\Mailer\MailerService;
use App\ESM\Service\Office\WordGenerator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Swift_Mailer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/test")
 */
class AppTestController extends AbstractController
{
	/**
	 * @Route("/tsmail", name="app.test.swiftmail")
	 */
	public function testswiftmail(Swift_Mailer $mailer)
	{
		$subject = 'Unicancer - email envoyé de recette';
		$message_html = '<html><head><title>Message</title></head><body><p>Bonjour,<br /> ceci est le message en HTML<br />Merci</body></html>';
		$message_txt = "Bonjour,\n ceci est le message en txt\nMerci";
		$EMAIL_FROM = 'dev@clinfile.com';
		$to = ['rabehasy@gmail.com', 'mrabehasy@clinfile.com'];

		// configure mail
		$mail = new \Swift_Message();
		$mail->setFrom($EMAIL_FROM)
			->setSubject($subject)
			->setBody($message_txt)
			->addPart($message_html, 'text/html')
		;
		$mail->setTo($to);

		// send
		$mailer->send($mail);

		return new Response('OK testswiftmail envoye');
	}

	/**
	 * @Route("/tmail", name="app.test.mailerService")
	 * @Security("is_granted('ROLE_SHOW_AUDIT_TRAIL')")
	 */
	public function testmailerService(MailerService $mailerService)
	{
		$to = ['rabehasy@gmail.com', 'mrabehasy@clinfile.com'];

		// send mail
		$mailerService->sendEmail(
			'testemail',
			[],
			'Unicancer - email de test',
			[],
			$to
		);

		return new Response('OK testmailerService envoye');
	}

	/**
	 * @Route("/phpoffice/word", name="app.test.word")
	 */
	public function testPhpOfficeWord(): Response
	{
		return $this->render('test/phpoffice-word.html.twig', []);
	}

	/**
	 * @Route("/phpoffice/word/generate", name="app.test.word.generate")
	 */
	public function generateWord(WordGenerator $wordGenerator)
	{
		$em = $this->getDoctrine()->getManager();
		$reportModelVersion = $em->getRepository(ReportModelVersion::class)->find(1);

		$fileName = $wordGenerator->generateVersion($reportModelVersion);

		return $this->file($fileName);
	}

	/**
	 * @Route("/doctrine", name="app.test.doctrine")
	 */
	public function testDoctrine(): Response
	{

		$center1 = $this->getDoctrine()->getRepository(Center::class)->find(1);
		$center2 = $this->getDoctrine()->getRepository(Center::class)->find(2);

		$institutionsCenter1 = $center1->getInstitutions();
		$institutionsCenter2 = $center2->getInstitutions();

//		$deviationsCenter1 = $center1->getDeviations();
//		$deviationsCenter2 = $center2->getDeviations();

		$toto = 'toto';

		return $this->render('test/test-doctrine.html.twig', [
			'institutions' => $institutionsCenter1
		]);
	}
}
