<?php

namespace mh\BTBundle\Controller\Frontend;

class MessageController extends Base\BaseUserController
{
    public function errorAction($mode)
    {
        return $this->render("Message/Error:$mode.html.twig");
    }

	public function doneAction($mode)
    {
        return $this->render("Message/Done:$mode.html.twig");
    }
}
