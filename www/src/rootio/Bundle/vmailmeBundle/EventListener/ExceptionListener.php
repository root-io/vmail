<?php

namespace rootio\Bundle\vmailmeBundle\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Twig_Environment;

class ExceptionListener
{
    /**
     * @var Twig_Environment
     */
    protected $twig;

    /**
     * @var KernelInterface
     */
    protected $kernel;

    function __construct(Twig_Environment $twig, KernelInterface $kernel)
    {
        $this->twig = $twig;
        $this->kernel = $kernel;
    }

    public function getTwig()
    {
        return $this->twig;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        if ($this->kernel->getEnvironment() === 'prod') {
            $exception = $event->getException();

            if ($exception instanceof HttpExceptionInterface) {
                $status_code = $exception->getStatusCode();
            } else {
                $status_code = 500;
            }

            $response = new Response($this->getTwig()->render('rootiovmailmeBundle::error.html.twig', array('status_code' => $status_code)));

            $event->setResponse($response);
        }
    }
}
