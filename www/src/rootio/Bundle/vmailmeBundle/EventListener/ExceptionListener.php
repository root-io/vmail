<?php

namespace rootio\Bundle\vmailmeBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ExceptionListener
{
    /**
     * @var \Twig_Environment $twig
     */
    protected $twig;

    function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    public function getTwig()
    {
        return $this->twig;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        $status_code = $exception->getStatusCode();

        $response = new Response($this->getTwig()->render('rootiovmailmeBundle::error.html.twig', array('status_code' => $status_code)));

        $event->setResponse($response);
    }
}
