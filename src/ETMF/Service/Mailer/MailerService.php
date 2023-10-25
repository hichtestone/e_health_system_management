<?php

declare(strict_types=1);

namespace App\ETMF\Service\Mailer;

use Swift_Mailer;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

/**
 * Class MailerService.
 */
class MailerService
{
    /** @var Swift_Mailer */
    private $mailer;
    /** @var Environment */
    private $renderer;
    /** @var string */
    private $env;
    /** @var TranslatorInterface */
    private $translator;
    /** @var string */
    public static $EMAIL_DEV;
    /** @var string */
    public static $EMAIL_ADMIN;
    /** @var string */
    public static $EMAIL_FROM;
    /** @var string */
    public static $FROM_NAME;
    /** @var string */
    private $lastLocale;
    /**
     * @var string
     */
    private $appName;

    /**
     * Swift constructor.
     */
    public function __construct(string $APP_NAME, string $APP_ENV, string $EMAIL_FROM, string $EMAIL_DEV, string $EMAIL_ADMIN, string $FROM_NAME, Swift_Mailer $mailer, Environment $renderer, TranslatorInterface $translator)
    {
        $this->mailer = $mailer;
        $this->renderer = $renderer;
        $this->translator = $translator;
        $this->env = $APP_ENV;
        $this->appName = $APP_NAME;
        self::$EMAIL_DEV = $EMAIL_DEV;
        self::$EMAIL_FROM = $EMAIL_FROM;
        self::$EMAIL_ADMIN = $EMAIL_ADMIN;
        self::$FROM_NAME = $FROM_NAME;

        //dump(self::$EMAIL_DEV,self::$EMAIL_DEV,self::$EMAIL_DEV,self::$EMAIL_DEV);exit();
    }

    /**
     * @param string $template        twig template (without email/ suffix and extension, will search for .txt.twig and .html.twig in email directory)
     * @param array  $template_params associative array to be passed to twig
     * @param string $subject         will be translated
     *
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function sendEmail(string $template, array $template_params, string $subject, array $subject_params, array $to, array $cc = [], array $bcc = []): void
    {
        // remove recipients if not prod
        $subjectPrefix = '';
        if ('prod' !== $this->env) {
            $subjectPrefix = '[CTMS ';
            $to = [];
            $cc = [];
            $bcc = [];
        }
        $subjectPrefix .= $this->appName.'] ';

        // add dev bcc
        $bcc[] = self::$EMAIL_DEV;
        $bcc[] = self::$EMAIL_ADMIN;

        // configure mail
        $mail = new \Swift_Message();
        $mail->setFrom(self::$EMAIL_FROM, self::$FROM_NAME)
            ->setReplyTo(self::$EMAIL_FROM)
            ->setReturnPath(self::$EMAIL_FROM)
            ->setSubject($subjectPrefix.$this->translator->trans($subject, $subject_params))
            ->setBody($this->renderer->render('email/'.$template.'.html.twig', $template_params), 'text/html')
            ->addPart($this->renderer->render('email/'.$template.'.txt.twig', $template_params), 'text/plain')
        ;
        $mail->setTo($to);
        $mail->setCc($cc);
        $mail->setBcc($bcc);

        // send
        $this->mailer->send($mail);
    }

    public function setLocale(string $locale): void
    {
        $this->lastLocale = $this->translator->getLocale();
        $this->translator->setLocale($locale);
    }

    public function resetLocale(): void
    {
        $this->translator->setLocale($this->lastLocale);
    }
}
