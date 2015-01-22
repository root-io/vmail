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
            "quotas",
            "security"
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
    public function createUser($username, $password, $rescueEmail = null)
    {
        $em = $this->getDoctrine()->getManager();

        $user = new User();
        $user->setUsername($username);
        $user->setEmail($username . '@vmail.me');
        $factory = $this->container->get('security.encoder_factory');
        $encoder = $factory->getEncoder($user);
        $passwordEncoded = $encoder->encodePassword($password, $user->getSalt());
        $user->setPassword($passwordEncoded);
        $user->setPasswordLegacy($password);
        $user->setRescueEmail($rescueEmail);

        $em->persist($user);
        $em->flush();

        return $user;
    }

    /**
     * Edit password
     *
     * @return User
     */
    public function editPassword($user, $newPassword)
    {
        $em = $this->getDoctrine()->getManager();

        $factory = $this->container->get('security.encoder_factory');
        $encoder = $factory->getEncoder($user);
        $password = $encoder->encodePassword($newPassword, $user->getSalt());
        $user->setPassword($password);
        $user->setPasswordLegacy($newPassword);

        $em->persist($user);
        $em->flush();

        return $user;
    }

    /**
     * Edit rescue email
     *
     * @return User
     */
    public function editRescueEmail($user, $rescueEmail)
    {
          if ($rescueEmail !== $user->getEmail()) {
              $em = $this->getDoctrine()->getManager();

              $user->setRescueEmail($rescueEmail);

              $em->persist($user);
              $em->flush();

              return $user;
          }
    }

    /**
     * Edit forwarding email
     *
     * @return User
     */
    public function editForwardingEmail($user, $forwardingEmail)
    {
        if (empty($forwardingEmail) || $forwardingEmail == $user->getEmail()) {
            $forwardingEmail = null;
        }

        // Avoid user1 forwards to user2 and user2 forwards to user1
        $loop = $this->getDoctrine()
            ->getRepository('rootiovmailmeBundle:User')
            ->findOneBy(array('email' => $forwardingEmail, 'forwardingEmail' => $user->getEmail()));

        if (!$loop) {
            $em = $this->getDoctrine()->getManager();

            $user->setForwardingEmail($forwardingEmail);

            $em->persist($user);
            $em->flush();

            return $user;
        }
    }

    /**
     * Suspend a User
     *
     * @return User
     */
    public function suspendUser($user)
    {
        $em = $this->getDoctrine()->getManager();

        $user->setIsEnabled(false);

        $em->persist($user);
        $em->flush();

        return $user;
    }
}
