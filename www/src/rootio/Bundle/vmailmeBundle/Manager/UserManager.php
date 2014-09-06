<?php

namespace rootio\Bundle\vmailmeBundle\Manager;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\DependencyInjection\Container;

use rootio\Bundle\vmailmeBundle\Entity\User;

class UserManager {

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
     * Is username forbidden ?
     *
     * @return boolean
     */
    public function isUsernameForbidden($username) {

        $forbiddenUsers = [
            "admin",
            "administrator",
            "hostmaster",
            "webmaster",
            "postmaster",
            "root",
            "ssladmin",
            "ssl-admin",
            "sysadmin",
            "info",
            "it",
            "abuse",
            "noc",
            "support",
            "www",
            "test",

            "noreply",
            "no-reply",
            "no.reply",
            "vmail",
            "vmailme",
            "vmail-me",
            "vmail.me",
            "help",
            "helps",
            "infos",
            "nobody",
            "anyone",
            "somebody",
            "supports",
            "www-data",
            "www.data",
            "webmasters",
            "contact",
            "contacts",
            "bank",
            "banks",
            "local",
            "new",
            "news",
            "update",
            "bug",
            "bugs",
            "unix",
            "bsd",
            "linux",
            "foo",
            "samples",
            "spam",
            "spams",
            "example",
            "examples",
            "mydomain",
            "mydomains",
            "nodomain",
            "nodomains",
            "gov",
            "gouv",
            "sendmail",
            "sendmails",
            "secure",
            "privacy",
            "service",
            "services",
            "submit",
            "plan",
            "plans",
            "quota",
            "quotas"
        ];

        if (in_array($username, $forbiddenUsers)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Is username taken ?
     *
     * @return boolean
     */
    public function isUsernameTaken($username)
    {
        return $this
            ->getDoctrine()
            ->getRepository('rootiovmailmeBundle:User')
            ->isUsernameTaken($username);
    }

    /**
     * Create user
     *
     * @return User
     */
    public function createUser($username, $email, $password, $rescueEmail = null, $forwardingEmail = null, $plan = null, $isEnabled = false)
    {
        $em = $this->getDoctrine()->getManager();

        $user = new User();
        $user->setUsername($username);
        $user->setEmail($email);
        $factory = $this->container->get('security.encoder_factory');
        $encoder = $factory->getEncoder($user);
        $passwordEncoded = $encoder->encodePassword($password, $user->getSalt());
        $user->setPassword($passwordEncoded);
        $user->setPasswordLegacy($password);
        $user->setRescueEmail($rescueEmail);
        $user->setForwardingEmail($forwardingEmail);
        $user->setPlan($plan);
        $user->setIsEnabled($isEnabled);

        $em->persist($user);
        $em->flush();

        return $user;
    }
}
