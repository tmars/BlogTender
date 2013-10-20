<?php
/**
 * Created by JetBrains PhpStorm.
 * User: marcky
 * Date: 20.10.13
 * Time: 13:57
 * To change this template use File | Settings | File Templates.
 */
namespace mh\BTBundle;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

class BTBundleExceptionListener
{

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception =  $event->getException();
        
    }
}