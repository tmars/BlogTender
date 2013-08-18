<?php

namespace mh\BTBundle\Controller\Frontend\Base;

use mh\BTBundle\Controller\Base\BaseController;
use mh\BTBundle\Entity\UserAction;
use mh\BTBundle\Form\Frontend\SearchFormType;

use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Exception\NotValidCurrentPageException;

class BaseUserController extends BaseController
{
    private $_container;

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
	
	public function genUrl($mode, $obj, $flag = false)
	{
		$url = '';
		if($mode == 'show_post') {
			$url = $this->generateUrl('show_post', array('id' => $obj->getId()), $flag);
		}
		
		return $url;
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
