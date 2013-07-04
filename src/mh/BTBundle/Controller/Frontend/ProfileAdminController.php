<?php

namespace mh\BTBundle\Controller\Frontend;
use mh\BTBundle\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

class ProfileAdminController extends Base\BaseUserController
{
	public function editAction()
	{
		$user = $this->getUserOrException();
		$request = $this->getRequest();
		$form = $this->createForm(new \mh\BTBundle\Form\Frontend\ProfileType());

		$emailMes = 'для использования всех возможностей подтвердите email адрес';
		while (1) {
			if ($request->getMethod() != 'POST') {
				$form->setData(array(
					'screen_name' => $user->getScreenName(),
					'about' => $user->getAbout(),
					'email' => $user->getEmail(),
					'name' => $user->getName(),
				));

				break;
			}
			$form->bindRequest($request);

			if (!$form->isValid()) {
				break;
			}

			$data = $form->getData();

			// Проверяем уникальность screen_name
			if ($data['screen_name'] != $user->getScreenName()) {
				if ($this->getRepository("User")->findOneByScreenName($data['screen_name'])) {
					$form->get('screen_name')->addError(new \Symfony\Component\Form\FormError('уже занят.'));
					break;
				}
			}

			// Меняем пароль
			if ($data['password']) {
				if ($data['password'] != $data['password_repeat']) {
					$form->get('password_repeat')->addError(new \Symfony\Component\Form\FormError('пароли не совпадают.'));
				} else {
					$user->setPassword($user->passwordEncode($data['password']));
					$form->get('password')->addError(new \Symfony\Component\Form\FormError('изменен.', array('info' => 1)));
				}
			}

			$user->setScreenName($data['screen_name']);
			$user->setAbout($data['about']);
			$user->setName($data['name']);

			if ($data['email'] != $user->getEmail()) {
				$user->setEmail($data['email']);
				$user->setEmailConfirmed(false);
				$this->setEmailConfirmCodeForUser($user);
				$emailMes = 'адрес изменен. Проверте почту, чтобы подтвердить его.';
			}

			if ($data['foto']) {
				$user->setFoto(new \mh\BTBundle\Entity\UserFoto($data['foto']));
			}

			$this->getEM()->flush();

			break;
		}

		if ($user->getEmailConfirmed() == false) {
			$form->get('email')->addError(new \Symfony\Component\Form\FormError($emailMes, array('info' => 1)));
		}

		$form->addError(new \Symfony\Component\Form\FormError(sprintf("Вы зарегистрировались через %s", $user->getSourceText()), array('info' => 1)));


		return $this->render("ProfileAdmin:edit.html.twig", array(
			'user' => $user,
			'form' => $form->createView(),
		));
	}

	public function scoresAction($date)
	{
		$user = $this->getActiveUserOrException();

		$dateObj = new \DateTime($date);
		$date = $dateObj->format('Y-m-d');

		$next = new \DateTime($date);
		$next->modify('+1 day');
		$next = $next->format('Y-m-d');

		$prev = new \DateTime($date);
		$prev->modify('-1 day');
		$prev = $prev->format('Y-m-d');

		$scoreGroups = array();

		$allocator = $this->get('scores_allocator');
		$types = $allocator->getTypes($user);

		foreach ($types as $type => $query) {
			$stmt = $this->getEM()->getConnection()->prepare($query. " AND t.created_date >= '{$date}' AND t.created_date < DATE_ADD('{$date}', INTERVAL 1 DAY)");
			$stmt->execute();
			$rows = $stmt->fetchAll();
			$scoreGroups[$type]['scores'] = ($rows[0]['scores'] == null) ? '0' : $rows[0]['scores'];
			$scoreGroups[$type]['count'] = ($rows[0]['count'] == null) ? '0' : $rows[0]['count'];
		}

		return $this->render("ProfileAdmin:scores.html.twig", array(
			"types" => $scoreGroups,
			'next' => $next,
			'prev' => $prev,
			'date' => $dateObj,
		));
	}

	public function inviteFriendsAction()
	{
		$user = $this->getActiveUserOrException();

		$form = $this->createFormBuilder()
			->add('emails', 'collection', array(
				'allow_add' => true,
				'allow_delete' => true,
				'type' => 'email',
			))
			->add('message', 'textarea', array(
				'required' => true,
				'label' => 'Текст сообщения:',
			))
			->getForm();
		$request = $this->getRequest();
		$inviteUrl = $this->generateUrl('reg_from_invite', array('code' => $user->getInviteCode()), true);

		while (1) {

			if ($request->getMethod() != 'POST') {
				break;
			}

			$form->bindRequest($request);

			if (!$form->isValid()) {
				break;
			}

			$data = $form->getData();

			$emailConstraint = new Assert\Email();
			$emailConstraint->message = 'Некорректный адрес';
			$emailConstraint->checkMX = true;

			$flag = false;

			foreach ($data['emails'] as $key => $email) {
				$errorList = $this->get('validator')->validateValue($email, $emailConstraint);
				if (count($errorList) != 0) {
					$form->get('emails')->get($key)->addError(new \Symfony\Component\Form\FormError($errorList[0]->getMessage()));
					$flag = true;
				}
			}

			if ($flag == true) {
				break;
			}

			foreach ($data['emails'] as $email) {
				$mailer = $this->get('user_mailer');
				$mailer->setEmail($email);
				$mailer->send('invite.html.twig', 'Приглашение.', array(
					'screen_name' => $user->getScreenName(),
					'invite_url' => $inviteUrl,
					'message' => $data['message'],
				));
			}

			//return $this->doneMessage('invites_sended', array('emails' => $data['emails']));
			break;
		}

		return $this->render("ProfileAdmin:invite_friends.html.twig", array(
			'form' => $form->createView(),
			'invite_url' => $inviteUrl,
		));
	}

	private function setEmailConfirmCodeForUser($user)
    {
		$em = $this->getEM();

		// удаляем предыдущий код
		$beforeCode = $this->getRepository("UserEmailConfirm")->findOneByUser($user);
		if ($beforeCode) {
			$em->remove($beforeCode);
			$em->flush();
		}

		$random = $this->get('random');
        $code = $random->generate(array('length' => 32));

        $confirm = new \mh\BTBundle\Entity\UserEmailConfirm();
        $confirm->setUser($user);
        $confirm->setCode($code);

        $em->persist($confirm);

        // Посылаем письмо
        $url = $this->generateUrl('profile_confirm_email', array(
			'code' => $code,
			'from' => $this->getRequest()->get('from', ''),
		), true);

		$mailer = $this->get('user_mailer');
        $mailer->setEmail($user->getEmail());
        $mailer->send('confirm_email.html.twig', 'Завершение регистрации.', array('url' => $url, 'user' => $user));
    }
}
