<?php

namespace mh\BTBundle\Controller\Base;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;
use mh\BTBundle\Entity\UserAction;

class BaseController extends \Symfony\Bundle\FrameworkBundle\Controller\Controller
{
	protected function preRenderAction(){}

    private function getResponse()
    {
        $response = new Response();
        foreach ($this->get('cookie_container')->getData() as $key => $value) {
        	$response->headers->setCookie(new Cookie($key, $value));
		}
		return $response;
    }

    public function render($view, array $parameters = array(), \Symfony\Component\HttpFoundation\Response $response = null)
    {
        $this->preRenderAction();
        $parameters['env'] = $this;
        return parent::render("BTBundle:Frontend\\$view", $parameters, $this->getResponse());
    }

    public function redirect($url, $status = 302)
    {
        $response = parent::redirect($url, $status);
        foreach ($this->get('cookie_container')->getData() as $key => $value) {
        	$response->headers->setCookie(new Cookie($key, $value));
		}
		return $response;
    }

    protected function getRepository($class)
    {
        return $this->getEM()->getRepository("BTBundle:$class");
    }

    protected function getEM()
    {
        return $this->getDoctrine()->getEntityManager();
    }

    public function createRedirectException($url)
    {
        return new HttpException(307, '', null, array('Location' => $url));
    }
}
