<?php

namespace mh\Common;

class UserMailer
{
	private $mailer;

	private $templating;

	private $templatesDir;

	private $email;

    public function __construct(\Swift_Mailer $mailer, \Symfony\Bundle\TwigBundle\TwigEngine $templating, $templatesDir)
	{
		$this->mailer = $mailer;
		$this->templating = $templating;
		$this->templatesDir = $templatesDir;
	}

    public function setEmail($email)
	{
		$this->email = $email;
	}

	public function send($templateName, $subject, $parameters)
	{
		if (! $this->email) {
			return;
		}

		$body = $this->templating->render($this->templatesDir . $templateName, $parameters);

		$message = \Swift_Message::newInstance()
			->setFrom('mail@tochetta.ru')
			->setTo($this->email)
			->setSubject($subject)
			->addPart($body, 'text/html')
		;

		$this->mailer->send($message);
	}
}