<?php

namespace rootio\Bundle\vmailmeBundle\Manager;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\DependencyInjection\Container;

use rootio\Bundle\vmailmeBundle\Entity\Login;

class LoginManager {

    /**
     * @var Registry $doctrine
     */
    protected $doctrine;

    /**
     * @var Container $container
     */
    protected $container;

    function __construct(Registry $doctrine, Container $container)
    {
        $this->doctrine = $doctrine;
        $this->container = $container;
    }

    public function getDoctrine()
    {
        return $this->doctrine;
    }

    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Create login
     *
     * @return Login
     */
    public function createLogin($email, $ip, $protocol)
    {
        $em = $this->getDoctrine()->getManager();

        $login= new Login();
        $login->setEmail($email);
        $login->setIp($ip);
        $login->setProtocol($protocol);

        $em->persist($login);
        $em->flush();

        return $login;
    }
}