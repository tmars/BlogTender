<?php

namespace mh\BTBundle\Controller\Frontend;

use Symfony\Component\HttpFoundation\Response;
use Knp\Menu\MenuFactory;
use Knp\Menu\Renderer\ListRenderer;

class WidgetController extends Base\BaseUserController
{
	public function eventsListAction($flag = '', $id = '')
	{
		$eventsList = $this->get('events_list');

		$count = $this->container->getParameter('count_events_per_page');
		if ($flag == 'next') {
			$events = $eventsList->getNext($id, $count);
		} else if ($flag == 'prev') {
			$events = $eventsList->getPrev($id, $count);
		} else {
            $events = $eventsList->getLast($count);
        }


		return $this->render('EventsList:block.html.twig', array(
            'events' => $events,
        ));
	}
	
	public function randomPostsAction()
	{
		return $this->render('Widget:post_block.html.twig', array(
            'posts' => $this->getRepository('Post')->getRandom(5),
			'title' => 'случайные',
        ));
	}
	
	public function lastPostsAction()
	{
		return $this->render('Widget:post_block.html.twig', array(
            'posts' => $this->getRepository('Post')->getLast(5),
			'title' => 'последние',
        ));
	}
	
	public function postSelectorAction()
	{
		return $this->render('Widget:post_selector.html.twig', array(
            'posts' => $this->getRepository('Post')->getRandomMarked(3),
		));
	}
	
	public function postSelectorMiniAction()
	{
		return $this->render('Widget:post_selector_mini.html.twig', array(
            'posts' => $this->getRepository('Post')->getRandomMarked(3),
		));
	}
	
	public function topMenuAction($child = '')
	{
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
					array('slug' => $category->getSlug())),
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
					array('slug' => $category->getSlug())),
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
		
		if ('' != $child) {
			$topMenu = $topMenu->getChild($child);
		}
		
		$renderer = new ListRenderer();
		$raw = $renderer->render($topMenu);
	
		return new Response($raw);
	}
	
	public function bottomMenuAction()
	{
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
		
		$renderer = new ListRenderer();
		$raw = $renderer->render($menu);
		
		return new Response($raw);
	}
	
	public function userMenuAction()
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
		$raw = $renderer->render($menu);
		
		return new Response($raw);
	}
}
