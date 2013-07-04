<?php

namespace mh\BTBundle\Controller\Frontend;

class AjaxController extends Base\BaseUserController
{
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

		$comment = new \mh\BTBundle\Entity\PostComment();
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
		case 'Post': $cacheName = 'post_show_'.$data['object']->getUser()->getScreenName().'_'.$data['object']->getSlug();break;
		}

		$object = $this->getCached($cacheName);
		if ($object) {
			$object->incLikesCount();
			$this->setCached($cacheName, $object);
		}*/

		$data['object']->setLikesCount($data['object']->getLikesCount() + 1);

		$like = new \mh\BTBundle\Entity\ContentObjectLike();
		$like->setUser($data['user']);
		$like->setTarget($data['object']);

		$em = $this->getEM();
		$em->persist($like);
		$em->flush();

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

		$complaint = new \mh\BTBundle\Entity\ContentObjectComplaint();
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

	public function nextHistoryAction($hash)
	{
		$history = $this->getHistoryOnline('next', $hash);

		if ($history) {
			return $this->JSONMessage(array(
				'status' => 'done',
				'content' => $this->render('Base:history_online_list.html.twig', array('history_online' => $history))->getContent(),
				'hash' => $history[count($history) - 1]['hash'],
			));
		} else {
			return $this->errorJSONMessage('нет истории');
		}
	}

	public function prevHistoryAction($hash)
	{
		/*if (@$_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest'){
			echo 'yes';
		} */
		$history = $this->getHistoryOnline('prev', $hash);

		if ($history) {
			return $this->JSONMessage(array(
				'status' => 'done',
				'content' => $this->render('Base:history_online_list.html.twig', array('history_online' => $history))->getContent(),
				'hash' => $history[0]['hash'],
			));
		} else {
			return $this->errorJSONMessage('нет истории');
		}
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
