<?php

namespace mh\BTBundle\Controller\Frontend;

use Symfony\Component\Validator\Constraints as Assert;
use mh\BTBundle\Entity as Entity;
class AjaxController extends Base\BaseUserController
{
	public function fastRegAction()
	{
		$form = $this->createForm(new \mh\BTBundle\Form\Frontend\FastRegType());
		$request = $this->getRequest();
		
		while (1) {
			if ($request->getMethod() != 'POST') {
                break;
            }

            $form->bindRequest($request);

			if ( ! $form->isValid()) {
                $status = 'error';
				$message = 'Неверный адрес электронной почты.';
				break;
            }

			$data = $form->getData();
			$user = $this->getRepository('User')->findOneBy(array(
				'email' => $data['email'],
				'source' => Entity\User::SOURCE_INTERNAL,
			));

			if ($user) {
				$status = 'error';
				$message = 'Пользователь с такой почтой уже зарегистрирован.';
				break;
			}

			$em = $this->getEM();
			$random = $this->get('random');

			$login = substr($data['email'], 0, strpos($data['email'], '@'));
			$login = $this->getRepository('User')->getUniqueLogin($login);
            $password = $random->generate(array('length' => 10));

			$user = new Entity\User();
			$user->setSource(Entity\User::SOURCE_INTERNAL);
			$user->setEmail($data['email']);
			$user->setName($login);
			$user->setLogin($login);
			$user->setPassword($password);

			// удаляем предыдущий код
			$beforeCode = $this->getRepository("UserEmailConfirm")->findOneByUser($user);
			if ($beforeCode) {
				$em->remove($beforeCode);
				$em->flush();
			}

			$code = $random->generate(array('length' => 32));

			$confirm = new Entity\UserEmailConfirm();
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

			$message = 'Проверьте ващу почту';
			$status = 'success';
			
			break;
		}

		return $this->JSONMessage(array('status' => $status, 'message' => $message));
	
	}
	
	public function postSearchPromtAction($tag)
	{
		$tag .= '%';

		$tags = $this->getRepository('Tag')->createQueryBuilder('t')
			->select('t')
            ->where("t.label LIKE :tag")
            ->setParameter(':tag', $tag)
			->getQuery()->getResult()
		;

		return $this->render('Ajax:tags.json.twig', array(
            'tags' => $tags,
        ));
	}
    public function newCommentAction()
	{
		$user = $this->getUser();
        if (!$user) {
            return $this->errorMessage('чтобы оставлять комент нужно авторизироваться');
        }

		$request = $this->getRequest();
		$form = $this->createForm(new \mh\BTBundle\Form\Frontend\CommentType());

		$form->bindRequest($request);

		if (!$form->isValid()) {
			return $this->render('Post:comment_form.html.twig', array(
				'form' => $form->createView(),
			));
		}

		$data = $form->getData();

		$post = $this->getRepository("Post")->find($data['post_id']);

		if (!$post) {
            return $this->errorMessage('пост не найден');
        }

		if (false == $post->getIsPublished()) {
            return $this->errorMessage('объект не опубликован');
		}

		$comment = new Entity\PostComment();
		$comment->setUser($user);
		$comment->setPost($post);
		$comment->setContent($data['content']);

		$em = $this->getEM();
		$em->persist($comment);
		$em->flush();

		return $this->render('Post:comment.html.twig', array(
			'comment' => $comment,
		));
	}

	public function likeAction()
	{
		$message = '';
		$data = $this->checkForm($message);
		if (!$data) {
			return $this->errorJSONMessage($message);
		}

		if ($this->getRepository('ContentObjectLike')->findOneBy(array(
				'target' => $data['object'],
				'user' => $data['user']))) {
			return $this->errorJSONMessage('уже лайкнул');
		}

		/*// Увеличиваем счетчик в кеше
		switch ($data['class']) {
		case 'Post': $cacheName = 'post_show_'.$data['object']->getUser()->getLogin().'_'.$data['object']->getSlug();break;
		}

		$object = $this->getCached($cacheName);
		if ($object) {
			$object->incLikesCount();
			$this->setCached($cacheName, $object);
		}*/

		$data['object']->setLikesCount($data['object']->getLikesCount() + 1);

		$like = new Entity\ContentObjectLike();
		$like->setUser($data['user']);
		$like->setTarget($data['object']);

		$em = $this->getEM();
		$em->persist($like);
		$em->flush();

        $eventsList = $this->get('events_list');
        switch($data['object']->getContentType()) {
            case 'post':
                $eventsList->happened($eventsList::LIKE_POST, $like);
                break;
            case 'question':
                $eventsList->happened($eventsList::LIKE_QUESTION, $like);
                break;
            case 'answer':
                $eventsList->happened($eventsList::LIKE_ANSWER, $like);
                break;
        }

		return $this->doneJSONMessage();
	}

	public function complaintAction()
	{
		$message = '';
		$data = $this->checkForm($message);
		if (!$data) {
			return $this->errorJSONMessage($message);
		}

		if ($this->getRepository('ContentObjectComplaint')->findOneBy(array(
				'target' => $data['object'],
				'user' => $data['user']))) {
			return $this->errorJSONMessage('уже пожаловался');
		}

		$data['object']->setComplaintsCount($data['object']->getComplaintsCount() + 1);

		$complaint = new Entity\ContentObjectComplaint();
		$complaint->setUser($data['user']);
		$complaint->setTarget($data['object']);

		$em = $this->getEM();
		$em->persist($complaint);
		$em->flush();

		return $this->doneJSONMessage();
	}

	public function bestAnswerAction()
	{
		$message = '';
		$data = $this->checkForm($message);
		if (!$data) {
			return $this->errorJSONMessage($message);
		}

		$answer = $this->getRepository('Answer')->findOneByContentObject($data['object']);
		var_dump($answer->getIsBest());
		if (!$answer) {
            return $this->errorMessage('ответ не найден');
        }

		if (false == $answer->getIsPublished()) {
            return $this->errorMessage('объект не опубликован');
		}

		$bestAnswer = $this->getRepository("Answer")->findOneBy(array(
			'question' => $answer->getQuestion(),
			'isBest' => true,
		));

		if ($bestAnswer) {
			return $this->errorMessage('на вопрос уже есть лучший ответ');
		}

		$answer->setIsBest(true);
		$answer->setContent('asdasd');
		var_dump($answer->getIsBest());
		//$allocator = $this->get('scores_allocator');
		//$allocator->allocateScoresForBestAnswer($answer);
		$this->getEM()>flush();
		$answer = $this->getRepository('Answer')->findOneByContentObject($data['object']);
		var_dump($answer->getIsBest());

		return $this->doneJSONMessage('пост лучший!');
	}

	public function moreCommentsAction($id)
	{
		$lastComment = $this->getRepository("PostComment")->find($id);
		$comments = array();

		if ($lastComment) {

			$query = $this->getEM()->createQuery('SELECT c FROM BTBundle:PostComment c WHERE c.post = :post AND c.id < :comment_id ORDER BY c.createdDate DESC')
				->setParameter('post', $lastComment->getPost())
				->setParameter('comment_id', $id)
				->setMaxResults(5);
			$comments = $query->getResult();

		}

		return $this->render('Post:comment_list.html.twig', array(
			'comments' => $comments,
		));
	}

	public function otherThemesForPostAction()
	{
		$themes = $this->getRepository('ThemeForPost')->getRandom(5);

		return $this->render('ProfileAdminPost:post_themes.html.twig', array(
            'themes_for_post' => $themes,
        ));
	}

	public function nextEventsAction($id)
	{
        $eventsList = $this->get('events_list');
		$events = $eventsList->getNext($id, $this->container->getParameter('count_events_per_page'));

		if ($events) {
			return $this->JSONMessage(array(
				'status' => 'done',
				'content' => $this->render('EventsList:list.html.twig', array('events' => $events))->getContent(),
				'id' => $events[count($events) - 1]['id'],
			));
		} else {
			return $this->errorJSONMessage('нет истории');
		}
	}

	public function prevEventsAction($id)
	{
		/*if (@$_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest'){
			echo 'yes';
		} */
        $eventsList = $this->get('events_list');
        $events = $eventsList->getPrev($id, $this->container->getParameter('count_events_per_page'));

		if ($events) {
			return $this->JSONMessage(array(
				'status' => 'done',
				'content' => $this->render('EventsList:list.html.twig', array('events' => $events))->getContent(),
				'id' => $events[0]['id'],
			));
		} else {
			return $this->errorJSONMessage('нет истории');
		}
	}
	
	public function userUploadAction($mode)
	{
		$em = $this->getEM();
		$request = $this->getRequest();
		$url = '';
		$status = 400;
		$message = 'function() {[return false;]}';
		
		while (1) {
			if ( ! $this->getUser()) {
				break;
			}
			
			if (count($request->files) != 1) {
				break;
			}
		
			$uploadFile = $request->files->get('upload');
				
			if ($mode == 'image') {
				$imageConstraint = new Assert\Image();
				$imageConstraint->maxSize = '1M';
				$errorList = $this->get('validator')->validateValue($uploadFile, $imageConstraint);

				if (count($errorList) != 0) {
					break;
				}
				
				// @fix add post relation!
				$attachment = new Entity\PostAttachmentImage($uploadFile);
				$em->persist($attachment);
				$em->flush();
				
				$url = $attachment->getBrowserPath();
				$avalancheService = $this->get('imagine.cache.path.resolver');
				$url = $avalancheService->getBrowserPath($url, 'image_in_post');
				
				$message = '"New image uploaded"';
				$status = 200;
			}
			
			if ($mode == 'file') {
				$fileConstraint = new Assert\File();
				$fileConstraint->maxSize = '1M';
				
				$errorList = $this->get('validator')->validateValue($uploadFile, $fileConstraint);

				if (count($errorList) != 0) {
					break;
				}
				
				$attachment = new Entity\PostAttachmentFile($uploadFile);
				$em->persist($attachment);
				$em->flush();
				
				$url = $attachment->getBrowserPath();
				
				$message = '"New file uploaded"';
				$status = 200;
			}
		
			break;
		}
		

		$response = $this->render('Ajax:user_uploade.html.twig', array(
			'func_num' => $request->get('CKEditorFuncNum'),
			'url' => $url,
			'message' => $message,
		));
		$response->setStatusCode($status);
		return $response;
		
	}

	private function checkForm(&$message) {
		$user = $this->getUser();
		if ( ! $user) {
			$message = 'авторизироваться';
			return false;
		}

		$request = $this->getRequest();
		$form = $this->createForm(new \mh\BTBundle\Form\Frontend\ContentObjectType());

		$form->bindRequest($request);

		if (!$form->isValid()) {
			$message = 'ошибка параметров123';
			return false;
		}

		$data = $form->getData();

		$object = $this->getRepository('ContentObject')->find($data['content_id']);
		if ( ! $object ) {
			$message = 'объект не найден';
			return false;
		}

		/*if (false == $object->getIsPublished()) {
			$message = 'объект не опубликован';
			return false;
		}*/

		return array(
			'object' => $object,
			'user' => $user,
		);
	}

	protected function errorJSONMessage($message)
	{
		return $this->JSONMessage(array(
			'status' => 'error',
			'message' => $message,
		));
	}

	protected function doneJSONMessage($message = '')
	{
		return $this->JSONMessage(array(
			'status' => 'done',
			'message' => $message,
		));
	}

	protected function errorMessage($message, $data = array())
	{
		return $this->render('Ajax:error.html.twig', array('message' => $message));
	}

	protected function doneMessage($message, $data = array())
	{
		return $this->render('Ajax:done.html.twig', array('message' => $message));
	}

	protected function JSONMessage($data)
	{
		return $this->render('Ajax:result.json.twig', array('result' => json_encode($data)));
	}
}
