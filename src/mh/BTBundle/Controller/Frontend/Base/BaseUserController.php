<?php

namespace mh\BTBundle\Controller\Frontend\Base;

use mh\BTBundle\Controller\Base\BaseController;
use mh\BTBundle\Entity\UserAction;
use mh\BTBundle\Form\Frontend\SearchFormType;

use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Exception\NotValidCurrentPageException;

use Knp\Menu\MenuFactory;
use Knp\Menu\Renderer\ListRenderer;

class BaseUserController extends BaseController
{
    private $_container;

	public function getLastPosts($count = 1)
	{
		return $this->getRepository('Post')->getLast($count);
	}

	public function getRandomPosts($count = 1)
	{
        return $this->getRepository('Post')->getRandom($count);
	}

	public function getRandomMarkedPosts($count = 1)
	{
		return $this->getRepository('Post')->getRandomMarked($count);
	}

	public function getTags()
	{
		return $this->getRepository('Tag')->findAll();
	}

	public function getSocnetOAuthLink($mode)
	{
		switch ($mode) {
		case 'fb':
			return sprintf("https://www.facebook.com/dialog/oauth/?client_id=%s&redirect_uri=%s&scope=email",
				$this->container->getParameter('fb_app_id'),
				$this->generateUrl('profile_socnet_login', array('mode' => 'fb'), true)
			);
		case 'vk':
			return sprintf("http://oauth.vk.com/authorize?client_id=%s&redirect_uri=%s&response_type=code",
				$this->container->getParameter('vk_app_id'),
				$this->generateUrl('profile_socnet_login', array('mode' => 'vk'), true)
			);
		case 'tw':
			return sprintf("%s",
				$this->generateUrl('profile_twitter_oauth', array(), true)
			);
		case 'od':
			return sprintf("http://www.odnoklassniki.ru/oauth/authorize?client_id=%s&response_type=code&redirect_uri=%s",
				$this->container->getParameter('od_app_id'),
				$this->generateUrl('profile_socnet_login', array('mode' => 'od'), true)
			);
		case 'gl':
			return sprintf("https://accounts.google.com/o/oauth2/auth?response_type=code&client_id=%s&redirect_uri=%s&scope=https%%3A%%2F%%2Fwww.googleapis.com%%2Fauth%%2Fuserinfo.email+https%%3A%%2F%%2Fwww.googleapis.com%%2Fauth%%2Fuserinfo.profile",
				$this->container->getParameter('gl_app_id'),
				$this->generateUrl('profile_socnet_login', array('mode' => 'gl'), true)
			);
		case 'ml':
			return sprintf("https://connect.mail.ru/oauth/authorize?client_id=%s&response_type=code&redirect_uri=%s",
				$this->container->getParameter('ml_app_id'),
				$this->generateUrl('profile_socnet_login', array('mode' => 'ml'), true)
			);
		}
	}

	public function getHistoryOnline($flag = '', $hash = '')
	{
		$sql = "SELECT * FROM (SELECT
            CONCAT(l.created_date, 'pl', l.id) hash,
			'post_like' type,
			l.created_date created_date,
			u.login login,
			u.name user_name,
			p.title p1,
			p.slug p2,
			NULL p3,
			NULL p4
			FROM content_object__like l
		INNER JOIN post__post p ON p.content_object_id = l.content_object_id
		INNER JOIN user__user u ON u.id = l.user_id
		WHERE p.is_published = 1

		UNION

		SELECT
            CONCAT(l.created_date, 'ql', l.id),
			'question_like',
			l.created_date,
			u.login,
			u.name user_name,
			q.id,
			q.title,
			NULL,
			NULL
			FROM content_object__like l
		INNER JOIN question__question q ON q.content_object_id = l.content_object_id
		INNER JOIN user__user u ON u.id = l.user_id
		WHERE q.is_published = 1

		UNION

		SELECT
            CONCAT(l.created_date, 'al', l.id),
			'answer_like',
			l.created_date,
			u.login,
			u.name user_name,
			q.id,
			q.title,
			a.id,
			NULL
			FROM content_object__like l
		INNER JOIN answer__answer a ON a.content_object_id = l.content_object_id
		INNER JOIN question__question q ON q.id = a.question_id
		INNER JOIN user__user u ON u.id = l.user_id
		WHERE a.is_published = 1

		UNION

		SELECT
            CONCAT(p.created_date, 'np', p.id),
			'new_post',
			p.created_date,
			u.login,
			u.name user_name,
			p.title,
			p.slug,
			p.scores,
			NULL
			FROM post__post p
		INNER JOIN user__user u ON u.id = p.user_id
		WHERE p.is_published = 1

		UNION

		SELECT
            CONCAT(q.created_date, 'nq', q.id),
			'new_question',
			q.created_date,
			u.login,
			u.name user_name,
			q.id,
			q.title,
			q.scores,
			NULL
			FROM question__question q
		INNER JOIN user__user u ON u.id = q.user_id
		WHERE q.is_published = 1

		UNION

		SELECT
            CONCAT(a.created_date, 'na', a.id),
			'new_answer',
			a.created_date,
			u.login,
			u.name user_name,
			q.id,
			q.title,
			a.id,
			a.scores
			FROM answer__answer a
		INNER JOIN user__user u ON u.id = a.user_id
		INNER JOIN question__question q ON q.id = a.question_id
		WHERE a.is_published = 1

		UNION

		SELECT
            CONCAT(l.created_date, 'fl', l.id),
			'foreign_link',
			l.created_date,
			u.login,
			u.name user_name,
			p.title,
			p.slug,
			l.scores,
			NULL
			FROM post__foreign_link l
		INNER JOIN post__post p ON p.id = l.post_id
		INNER JOIN user__user u ON u.id = p.user_id
		WHERE p.is_published = 1) t";


		if ($flag == 'next') {
			$sql .= sprintf(' WHERE t.hash < "%s"', $hash);
		} else if ($flag == 'prev') {
			$sql .= sprintf(' WHERE t.hash > "%s"', $hash);
		}
		$sql .= " ORDER BY t.hash DESC LIMIT 0, 10";

		$stmt = $this->getEM()->getConnection()->prepare($sql);
		$stmt->execute();
		$result = $stmt->fetchAll();

		foreach ($result as $k => $r) {

			$h['hash'] = array_shift($r);
			$h['type'] = array_shift($r);
			$h['created_date'] = array_shift($r);
			$h['login'] = array_shift($r);
			$h['user_name'] = array_shift($r);

			switch($h['type']) {
				case 'post_like':
					$h['post_title'] = array_shift($r);
					$h['post_slug'] = array_shift($r);
					break;

				case 'question_like':
					$h['question_id'] = array_shift($r);
					$h['question_title'] = array_shift($r);
					break;

				case 'answer_like':
					$h['question_id'] = array_shift($r);
					$h['question_title'] = array_shift($r);
					$h['answer_id'] = array_shift($r);
					break;

				case 'new_post':
					$h['post_title'] = array_shift($r);
					$h['post_slug'] = array_shift($r);
					$h['post_scores'] = array_shift($r);
					break;

				case 'new_question':
					$h['question_id'] = array_shift($r);
					$h['question_title'] = array_shift($r);
					$h['question_scores'] = array_shift($r);
					break;

				case 'new_answer':
					$h['question_id'] = array_shift($r);
					$h['question_title'] = array_shift($r);
					$h['answer_id'] = array_shift($r);
					$h['answer_scores'] = array_shift($r);
					break;

				case 'foreign_link':
					$h['post_title'] = array_shift($r);
					$h['post_slug'] = array_shift($r);
					$h['link_scores'] = array_shift($r);
					break;
			}
			$result[$k] = $h;
		}

		return $result;
	}

	public function getTopMenu()
	{
		//$topMenu = $this->getCached('top_menu');
		//if (!$topMenu) {

			$categories = $this->getRepository('Category')->findAll();
			$factory = new MenuFactory();

			$topMenu = $factory->createItem('top_menu');
			$topMenu->setCurrentUri($this->get('request')->getRequestUri());

			$topMenu->addChild('new_post', array(
				'label' => "Написать пост",
				'uri' => $this->generateUrl('profile_admin_new_post')
			));
			$topMenu->addChild('how_it_work', array(
				'label' => "Как это работает?",
				'uri' => $this->generateUrl('static_page', array('page' => 'how_it_work'))
			));

			$subMenu = $factory->createItem('how_it_work', array('label' => 'Как это работает?'));
			$subMenu->setChildrenAttributes(array('class' => 'blue'));
			$subMenu->addChild('how_it_work_1',
				array('label' => 'Статьи', 'uri' => $this->generateUrl('static_page', array('page' => 'how_it_work', 'subpage' => 'posts'))));
			$subMenu->addChild('how_it_work_2',
				array('label' => 'Картинки', 'uri' => $this->generateUrl('static_page', array('page' => 'how_it_work', 'subpage' => 'images'))));
			$subMenu->addChild('how_it_work_3',
				array('label' => 'Лайки', 'uri' => $this->generateUrl('static_page', array('page' => 'how_it_work', 'subpage' => 'likes'))));
			$subMenu->addChild('how_it_work_4',
				array('label' => 'Вопросы и ответы', 'uri' => $this->generateUrl('static_page', array('page' => 'how_it_work', 'subpage' => 'questions'))));
			$subMenu->addChild('how_it_work_5',
				array('label' => 'Ссылки', 'uri' => $this->generateUrl('static_page', array('page' => 'how_it_work', 'subpage' => 'links'))));
			$subMenu->addChild('how_it_work_6',
				array('label' => 'Комментарии', 'uri' => $this->generateUrl('static_page', array('page' => 'how_it_work', 'subpage' => 'comments'))));
			$subMenu->addChild('how_it_work_7',
				array('label' => 'Модерация', 'uri' => $this->generateUrl('static_page', array('page' => 'how_it_work', 'subpage' => 'moderation'))));
			$subMenu->addChild('how_it_work_8',
				array('label' => 'Таблица баллов', 'uri' => $this->generateUrl('static_page', array('page' => 'how_it_work', 'subpage' => 'scores'))));
			$subMenu->addChild('how_it_work_9',
				array('label' => 'Условия', 'uri' => $this->generateUrl('static_page', array('page' => 'how_it_work', 'subpage' => 'conditions'))));
			$topMenu->addChild($subMenu);

			$postMenu = $factory->createItem('post_menu', array('label' => 'Посты', 'uri' => $this->generateUrl('list_post')));
			$postMenu->setChildrenAttributes(array('class' => 'blue'));
			foreach ($categories as $category) {
				$postMenu->addChild('post_category_'.$category->getId(), array(
					'label' => $category->getLabel(),
					'uri' => $this->generateUrl(
						'list_post_by_category',
						array('category_id' => $category->getId())),
				));
			}
			$topMenu->addChild($postMenu);

			$questionMenu = $factory->createItem('question_menu', array('label' => 'Вопросы и ответы', 'uri' => $this->generateUrl('list_question')));
			$questionMenu->setChildrenAttributes(array('class' => 'blue'));
			foreach ($categories as $category) {
				$questionMenu->addChild('post_question_'.$category->getId(), array(
					'label' => $category->getLabel(),
					'uri' => $this->generateUrl(
						'list_question_by_category',
						array('category_id' => $category->getId())),
				));
			}
			$topMenu->addChild($questionMenu);


			$topMenu->addChild('users_rating', array(
				'label' => "Рейтинг",
				'uri' => $this->generateUrl('users_rating')
			));
			$topMenu->addChild('prizes', array(
				'label' => "Призы",
				'uri' => $this->generateUrl('static_page', array('page' => 'prizes'))
			));
			$topMenu->addChild('feedback', array(
				'label' => "Обратная связь",
				'uri' => $this->generateUrl('feedback')
			));




			//$this->setCached('top_menu', $topMenu);
		//}

		return $topMenu;
	}

	public function getRenderedMenu($menu)
	{
		$renderer = new ListRenderer();
		return $renderer->render($menu);
	}

	public function getBottomMenu()
	{
		//$menu = $this->getCached('bottom_menu');
		//if (!$menu) {

			$factory = new MenuFactory();

			$menu = $factory->createItem('bottom_menu');
			$menu->setCurrentUri($this->get('request')->getRequestUri());

			$menu->addChild('condition', array(
				'label' => "Условия",
				'uri' => $this->generateUrl('static_page', array('page' => 'conditions'))
			));
			$menu->addChild('how_it_work', array(
				'label' => "Как это работает?",
				'uri' => $this->generateUrl('static_page', array('page' => 'how_it_work'))
			));
			$menu->addChild('prizes', array(
				'label' => "Призы",
				'uri' => $this->generateUrl('static_page', array('page' => 'prizes'))
			));
			$menu->addChild('post_menu', array(
				'label' => 'Посты',
				'uri' => $this->generateUrl('list_post')
			));
			$menu->addChild('question_menu', array(
				'label' => 'Вопросы и ответы',
				'uri' => $this->generateUrl('list_question')
			));
			$menu->addChild('users_rating', array(
				'label' => "Рейтинг",
				'uri' => $this->generateUrl('users_rating')
			));

			//$this->setCached('bottom_menu', $menu);
		//}

		return $menu;
	}

	public function getUserMenu()
	{
		$user = $this->getUser();
		$factory = new MenuFactory();

		$menu = $factory->createItem('user_menu');
		$menu->setCurrentUri($this->get('request')->getRequestUri());

		$menu->addChild('profile_edit', array('label' => 'Редактировать профиль',	'uri' => $this->generateUrl('profile_admin_edit')));
		$menu->addChild('list_post', array('label' => 'Мои посты',	'uri' => $this->generateUrl('profile_admin_list_post')));
		$menu->addChild('list_question', array('label' => 'Мои вопросы',	'uri' => $this->generateUrl('profile_admin_list_question')));
		$menu->addChild('new_post', array('label' => 'Добавить пост', 'uri' => $this->generateUrl('profile_admin_new_post')));
		$menu->addChild('new_question', array('label' => 'Добавить вопрос', 'uri' => $this->generateUrl('profile_admin_new_question')));
		$menu->addChild('list_foreign_link', array('label' => 'Добавить ссылку', 'uri' => $this->generateUrl('profile_admin_list_foreign_link')));
		$menu->addChild('scores', array('label' => 'Мои баллы', 'uri' => $this->generateUrl('profile_admin_scores')));
		$menu->addChild('invite_friends', array('label' => "Пригласить друзей", 'uri' => $this->generateUrl('profile_admin_invite_friends')));
		$menu->addChild('logout', array('label' => 'Выход', 'uri' => $this->generateUrl('profile_logout')));
		$renderer = new ListRenderer();
		return $renderer->render($menu);
	}

    public function getUser()
    {
    	if (!$this->_container['user']) {
            $cookie = $this->get('cookie_container');

            if ($cookie->get('auth_hash')) {
				//$user = $this->getCached('user_'.$cookie->get('auth_hash'));
				$user = null;
				//if (!$user) {
				    $session = $this->getRepository('UserSession')->findOneBy(array(
						'hash' => $cookie->get('auth_hash'),
						'userAgent' => $_SERVER['HTTP_USER_AGENT'],
					));
					if ($session) {
						$user = $this->getRepository('User')->findOneByCurrentSession($session);
						//$this->setCached('user_'.$cookie->get('auth_hash'), $user);
					}
				//}

				$this->_container['user'] = $user;
			}
        }
        return $this->_container['user'];
    }

	public function getPaginated($query, $max_per_page = 3)
	{
		$pagerfanta = new Pagerfanta(new DoctrineORMAdapter($query));
		$pagerfanta->setMaxPerPage($max_per_page);

		try {
			$pagerfanta->setCurrentPage($this->getRequest()->get('page', 1));
		} catch(Exception $e) {
			throw new NotFoundHttpException();
		}

		return $pagerfanta;
	}

	public function getContentObject($id)
	{
		$object = $this->getCached('content_object_'.$id);
		if (!$object) {
			$object = $this->getRepository("ContentObject")->find($id);
			$this->setCached('content_object_'.$id, $object);
		}
		return $object;
	}

	public function getContentObjectForm($object)
	{
		$form = null;
		if ($this->getUser()) {
			$form = $this->createForm(new \mh\BTBundle\Form\Frontend\ContentObjectType());
			$form->setData(array(
				'content_id' => $object->getContentObject()->getId(),
			));
			$form = $form->createView();
		}
		return $form;
	}

	protected function preRenderAction()
    {
        /*$user = $this->getUser();
        if ($user && $user->getCurrentSession()) {
            $action = new UserAction();
            $action->setFromUrl($this->getRequest()->headers->get('referer'));
            $action->setToUrl($this->generateUrl('homepage', array(), true).substr($this->getRequest()->server->get('PATH_INFO'), 1));
            $action->setUserSession($user->getCurrentSession());

            $em = $this->getEM();
            $em->persist($action);
            $em->flush();
        }*/
    }

    protected function getUserOrException()
    {
        $user = $this->getUser();
        if (!$user) {
            throw $this->createRedirectException($this->generateUrl('profile_login'));
        }
        return $user;
    }

	protected function getActiveUserOrException()
	{
		$user = $this->getUserOrException();
		if (!$user->getEmailConfirmed()) {
			throw $this->createRedirectException($this->generateUrl('profile_admin_edit'));
		}

		return $user;
	}

    protected function errorMessage($message, $data = array())
    {
        return $this->redirect($this->generateUrl('error_message', array('mode' => $message)));
    }

    protected function doneMessage($message, $data = array())
    {
        return $this->redirect($this->generateUrl('done_message', array('mode' => $message)));
    }

    protected function createExceptionIfAuthorized()
    {
        $user = $this->getUser();
        if ($user) {
            throw $this->createRedirectException(
                $this->generateUrl('error_message', array('mode' => 'already_registered'))
            );
        }
    }

	protected function getClassByObject($object)
	{
		$class = explode('\\', get_class($object));
		return array_pop($class);
	}

	protected function getCached($key)
	{
		if ($string = $this->get('cache')->fetch($key)) {
            return unserialize($string);
        }
		return false;
	}

	protected function setCached($key, $value, $time = 0)
	{
		$this->get('cache')->save($key, serialize($value), $time);
	}

	protected function deleteCached($key)
	{
		$this->get('cache')->delete($key);
    }
}
