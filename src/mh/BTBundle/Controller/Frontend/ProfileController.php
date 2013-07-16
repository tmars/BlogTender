<?php

namespace mh\BTBundle\Controller\Frontend;
use mh\BTBundle\Form\Frontend\EnterScreenNameType;
use mh\BTBundle\Form\Frontend\EnterEmailType;
use mh\BTBundle\Form\Frontend\RegistrationType;
use mh\BTBundle\Form\Frontend\LoginType;
use mh\BTBundle\Entity\User;
use mh\BTBundle\Entity\UserSession;
use Symfony\Component\Validator\Constraints as Assert;


class ProfileController extends Base\SocnetLoginController
{
    public function confirmAction($code)
    {
		$confirm = $this->getRepository('UserEmailConfirm')->findOneByCode($code);
        if ($confirm) {
            $user = $confirm->getUser();
			$user->setEmailConfirmed(true);

            $em = $this->getEM();
            $em->remove($confirm);
            $em->flush();

			$this->createNewUserSession($user);
			return $this->redirect($this->generateUrl('homepage'));
        } else {
            return $this->errorMessage('confirm_code_failed');
        }
    }

    public function socnetLoginAction($mode)
    {
        $this->createExceptionIfAuthorized();

        $userData = $this->getUserDataFrom($mode);

        $user = $this->getRepository('User')->findOneBy(array(
            'source' => User::getSourceByMode($mode),
            'idInSource' => $mode.'.'.$userData['id'],
        ));

		if (!$user) {

            $user = new User();
            $user->setSource(User::getSourceByMode($mode));
            $user->setIdInSource($mode.'.'.$userData['id']);
			$user->setSocnetInfo($userData['socnet_info']);
			$user->setName($userData['name']);

			if (@$userData['email']) {
                $user->setEmail($userData['email']);
                $user->setEmailConfirmed(true);
            }

			$cookie = $this->get('cookie_container');
			if (	($inviteCode = $cookie->get('invite')) &&
					($invitingUser = $this->getRepository('User')->findOneByInviteCode($inviteCode))) {
				$user->setInvitingUser($invitingUser);
			}

			if (@$userData['photo'] &&
				($content = @file_get_contents($userData['photo']))) {
				$filename = $this->get('random')->generate(array('length' => 8));

				file_put_contents($this->container->getParameter("kernel.cache_dir").'/'.$filename, $content);

				$user->setFoto(new \mh\BTBundle\Entity\UserFoto(
					new \Symfony\Component\HttpFoundation\File\UploadedFile(
						$this->container->getParameter("kernel.cache_dir").'/'.$filename,
						$filename
					)
				));
			}

			$user->setScreenName($this->getRepository('User')->getUniqueScreenName($userData['screen_name']));

			$em = $this->getEM();
            $em->persist($user);
            $em->flush();
        }

        $this->createNewUserSession($user);
        return $this->redirect($this->generateURL('homepage'));
    }

    public function loginAction()
    {
        $this->createExceptionIfAuthorized();

        $form = $this->createForm(new LoginType());
        $request = $this->getRequest();

        while(1) {

            if ($request->getMethod() != 'POST') {
                break;
            }

            $form->bindRequest($request);

            if ( ! $form->isValid()) {
                break;
            }

            $data = $form->getData();


			$emailConstraint = new Assert\Email(array('checkMX' => false));

			$loginConstraint = array(
				new Assert\MaxLength(array('limit' => 30)),
				new Assert\MinLength(array('limit' => 6)),
				new Assert\Regex(array('pattern' => '/^[a-z0-9][a-z0-9_]+[a-z0-9]$/i'))
			);

			$validator = $this->get('validator');

			$errorListEmail = count($validator->validateValue($data['email'], $emailConstraint));

			$errorListLogin = 0;
			$errorListLogin += count($validator->validateValue($data['email'], $loginConstraint[0]));
			$errorListLogin += count($validator->validateValue($data['email'], $loginConstraint[1]));
			$errorListLogin += count($validator->validateValue($data['email'], $loginConstraint[2]));

			if ($errorListEmail == 0) {
				$user = $this->getRepository('User')->findOneBy(array(
					'email' => $data['email'],
					'source' => User::SOURCE_INTERNAL,
				));
			} else if ($errorListLogin == 0){
				$user = $this->getRepository('User')->findOneByScreenName($data['email']);
			} else {
				$form->get('email')->addError(new
                    \Symfony\Component\Form\FormError('нет такого логина и email.'));
				break;
			}

			if ($user->getPassword() != $user->passwordEncode($data['password'])) {
                $form->addError(new
                    \Symfony\Component\Form\FormError('Неверный пароль. '.$user->getPassword().'.'.$user->passwordEncode($data['password'])));
                break;
            }

            if (!$user->getEmailConfirmed()) {
                $form->addError(new
                    \Symfony\Component\Form\FormError('Пользователь не подтвердил почту.'));
                break;
            }

            $this->createNewUserSession($user);

            return $this->redirect($this->generateURL('homepage'));
        }

        return $this->render('Profile:login.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function logoutAction()
    {
        $user = $this->getUser();
        if ($user) {
        	$this->get('cookie_container')->set('auth_hash', '');

            $session = $user->getCurrentSession();
            $session->close();
        	$user->setCurrentSession(null);

        	$this->getEM()->flush();
        }

        return $this->redirect($this->generateURL('homepage'));
    }

	public function twitterOAuthAction()
	{
		session_start();
		// The TwitterOAuth instance
		$twitteroauth = $this->get('twitter_oauth');
		// Requesting authentication tokens, the parameter is the URL we will be redirected to
		$request_token = $twitteroauth->getRequestToken($this->generateUrl('profile_socnet_login', array('mode' => 'tw'), true));

		// Saving them into the session
		$_SESSION['oauth_token'] = $request_token['oauth_token'];
		$_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];

		// If everything goes well..
		if($twitteroauth->http_code == 200){
			// Let's generate the URL and redirect
			$url = $twitteroauth->getAuthorizeURL($request_token['oauth_token']);
			return $this->redirect($url);
		} else {
			// It's a bad idea to kill the script, but we've got to know when there's an error.
			return $this->errorMessage('twitter_error');
		}

	}

    private function createNewUserSession(User $user)
    {
        // Генерируем хэш авторизации
        $request = $this->getRequest();
        $random = $this->get('random');
        $hash = $random->generate(array('length' => 32));

        // Создаём сессию
        $session = new UserSession();
        $session->setHash($hash);
        $session->setIp($request->getClientIp());
        $session->setUser($user);
		$session->setUserAgent($_SERVER['HTTP_USER_AGENT']);
        $this->getEM()->persist($session);

        // Сохраняем в бд
        $user->setCurrentSession($session);
        $this->getEM()->flush();

        // Сохраняем хеш в печеньках
        $cookie = $this->get('cookie_container');
        $cookie->set('auth_hash', $session->getHash());
    }
}
