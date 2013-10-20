<?php

namespace mh\BTBundle\Controller\Frontend;

class DefaultController extends Base\BaseUserController
{
    public function indexAction()
    {
		$form = $this->createForm(new \mh\BTBundle\Form\Frontend\FastRegType());
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

		return $this->redirect($this->generateUrl('profile_reg'));
	}
}
