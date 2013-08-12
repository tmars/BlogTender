<?php

namespace mh\BTBundle\Controller\Frontend;

use Symfony\Component\Validator\Constraints as Assert;

class DefaultController extends Base\BaseUserController
{
    public function indexAction()
    {
		$form = $this->createFormBuilder()
			->add('email', 'email', array(
				'required' => true
			))
			->getForm();
		$request = $this->getRequest();

		while (1) {
			if ($request->getMethod() != 'POST') {
                break;
            }

            $form->bindRequest($request);

			if ( ! $form->isValid()) {
                break;
            }

			$data = $form->getData();
			$user = $this->getRepository('User')->findOneBy(array(
				'email' => $data['email'],
				'source' => \mh\BTBundle\Entity\User::SOURCE_INTERNAL,
			));

			if ($user) {
				$form->addError(new \Symfony\Component\Form\FormError('Пользователь с такой почтой уже зарегистрирован.'));
				break;
			}

			$em = $this->getEM();
			$random = $this->get('random');

			$login = substr($data['email'], 0, strpos($data['email'], '@'));
			$screenName = $this->getRepository('User')->getUniqueScreenName($login);
            $password = $random->generate(array('length' => 10));

			$user = new \mh\BTBundle\Entity\User();
			$user->setSource(\mh\BTBundle\Entity\User::SOURCE_INTERNAL);
			$user->setEmail($data['email']);
			$user->setName($login);
			$user->setScreenName($screenName);
			$user->setPassword($password);

			// удаляем предыдущий код
			$beforeCode = $this->getRepository("UserEmailConfirm")->findOneByUser($user);
			if ($beforeCode) {
				$em->remove($beforeCode);
				$em->flush();
			}

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

			$em->persist($user);
			$em->flush();

			$mailer = $this->get('user_mailer');
			$mailer->setEmail($user->getEmail());
			$mailer->send('fast_reg_email.html.twig', 'Быстрая регистрация.', array('url' => $url, 'user' => $user, 'password' => $password));

			$form->addError(new \Symfony\Component\Form\FormError('Проверьте ващу почту', array('info' => 1)));
			break;
		}

		$posts = $this->getRepository('Post')
			->getList($this->container->getParameter('count_post_per_page'), $this->getRequest()->get('page', 1));

        return $this->render('Default:index.html.twig', array(
            'posts' => $posts,
			'fast_reg_form' => $form->createView()
        ));
    }

	public function feedbackAction()
	{
		$form = $this->createFormBuilder()
			->add('subject', 'text', array(
				'required' => true,
				'label' => 'Тема',
			))
			->add('email', 'email', array(
				'required' => true,
				'label' => 'Email',
			))
			->add('content', 'textarea', array(
				'required' => true,
				'label' => 'Содержание',
			))
			->getForm();
		$request = $this->getRequest();

		while (1) {
			if ($request->getMethod() != 'POST') {
                break;
            }

            $form->bindRequest($request);

			if ( ! $form->isValid()) {
                break;
            }

			$data = $form->getData();

			$mailer = $this->get('user_mailer');
			$mailer->setEmail($this->container->getParameter('feedback_receivers'));
			$mailer->send('feedback_to.html.twig', 'Обратная связь.', array(
				'email' => $data['email'],
				'content' => $data['content'],
				'subject' => $data['subject'],
			));

			$mailer->setEmail($data['email']);
			$mailer->send('feedback_from.html.twig', 'Обратная связь.', array(
				'email' => $data['email'],
				'content' => $data['content'],
				'subject' => $data['subject'],
			));

			//return $this->doneMessage('feedback_sended', array('email' => $data['email']));
			$form->addError(new \Symfony\Component\Form\FormError(sprintf('Ваше сообщение отправлено, ждите ответа на %s.', $data['email']), array('info' => 1)));
			break;
		}

        return $this->render('Default:feedback.html.twig', array(
         	'form' => $form->createView()
        ));
	}

    public function ratingAction()
    {
        $query = $this->getRepository('User')->createQueryBuilder('u')
			->select('u')
            ->orderBy('u.scores', 'DESC')
        ;

		$users = $this->getPaginated($query, 10);

		return $this->render('Default:users_rating.html.twig', array(
            'users' => $users,
        ));
    }

	public function staticPageAction($page, $subpage)
    {
		$pages = array('conditions', 'how_it_work', 'prizes');

		if ( ! in_array($page, $pages)) {
			throw $this->createNotFoundException();
		}

		if ($subpage) {
			return $this->render('Default:'.$page.'\\'.$subpage.'.html.twig', array());
		} else {
			return $this->render('Default:'.$page.'.html.twig', array());
		}
    }

	public function regFromInviteAction($code)
	{
		$cookie = $this->get('cookie_container');
		$cookie->set('invite', $code);

		return $this->redirect($this->generateUrl('homepage'));
	}

	public function userUploadAction($mode)
	{
		$request = $this->getRequest();
		$url = '';
		$status = 400;
		
		if ( ! $this->getUser()) {
			$message = '"Unauthorized."';
		}
		
		switch($mode) {
			default:
				$message = '"Error mode."';
				break;

			case 'image':
				if (count($request->files) == 1) {
					$uploadFile = $request->files->get('upload');
					$imageConstraint = new Assert\Image();
					$errorList = $this->get('validator')->validateValue($uploadFile, $imageConstraint);

					if (count($errorList) == 0) {
						$name = pathinfo($uploadFile->getClientOriginalName(), PATHINFO_FILENAME);
						$ext = pathinfo($uploadFile->getClientOriginalName(), PATHINFO_EXTENSION);
						$name = $this->get('slug')->getSlug($name);
						$name = sprintf("%s_%s.%s", uniqid(), $name, $ext);
						
						$path = sprintf("%s/../web%s",
							$this->get('kernel')->getRootDir(), $this->container->getParameter('user_upload_image_dir'));
						
						$file = $uploadFile->move($path, $name);
				
						//$image = new \Imagick($path.$name);
						//$image->cropImage(600, 0);
						//$image->writeImage($path.$name);
						
						$url = $this->container->getParameter('user_upload_image_dir') . $name;
						$message = 'New file uploaded';
						$status = 200;
					} else {
						$message = 'function() {[return false;]}';
					}
				} else {
					$message = '"No file has been sent"';
				}
				break;
		}

		return $this->render('Default:uploaded.html.twig', array(
			'func_num' => $request->get('CKEditorFuncNum'),
			'url' => $url,
			'message' => $message,
		));

	}
}
