<?php

namespace rootio\Bundle\vmailmeBundle\Manager;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\DependencyInjection\Container;

use rootio\Bundle\vmailmeBundle\Entity\ForgotPassword;

class ForgotPasswordManager
{
    /**
     * @var Registry $doctrine
     */
    protected $doctrine;

    /**
     * @var Container $container
     */
    protected $container;

    /**
     * @var \Twig_Environment $twig
     */
    protected $twig;

    /**
     * @var \Swift_Mailer $mailer
     */
    protected $mailer;

    function __construct(Registry $doctrine, Container $container, \Twig_Environment $twig, \Swift_Mailer $mailer)
    {
        $this->doctrine = $doctrine;
        $this->container = $container;
        $this->twig = $twig;
        $this->mailer = $mailer;
    }

    public function getDoctrine()
    {
        return $this->doctrine;
    }

    public function getContainer()
    {
        return $this->container;
    }

    public function getTwig()
    {
        return $this->twig;
    }

    public function getMailer()
    {
        return $this->mailer;
    }

    /**
     * Validate rescue email
     *
     * @return boolean
     */
    public function validateRescueEmail()
    {
        //FIXME
    }

    /**
     * Create token
     *
     * @return boolean
     */
    public function createToken($rescueEmail)
    {
        // Find the user who linked this rescue email
        $user = $this->getDoctrine()
            ->getRepository('rootiovmailmeBundle:User')
            ->findOneByRescueEmail($rescueEmail);

        if ($user) {

            // Find a forgot password database entry
            $forgotPassword = $this->getDoctrine()
                ->getRepository('rootiovmailmeBundle:ForgotPassword')
                ->findOneByUser($user);

            // No forgot password token or expired
            if (!$forgotPassword || $forgotPassword->getExpire() < new \DateTime('now')) {
                $em = $this->getDoctrine()->getManager();

                // Generate token
                $token = hash('sha256', uniqid('', true));

                // Update expired forgot password token or create a new one
                if ($forgotPassword) {
                    $forgotPassword->setToken($token);
                    $forgotPassword->setExpire(new \DateTime('+5 minutes'));
                } else {
                    $forgotPassword = new ForgotPassword();
                    $forgotPassword->setUser($user);
                    $forgotPassword->setToken($token);
                    $forgotPassword->setExpire(new \DateTime('+5 minutes'));
                }

                $em->persist($forgotPassword);
                $em->flush();

                // Send forgot password token to the rescue email
                $templateContent = $this->getTwig()->loadTemplate('rootiovmailmeBundle::Emailing/forgot_password.text.twig');

                $subject = $templateContent->renderBlock('subject', array());
                $body = $templateContent->renderBlock('body', array(
                  'rescueEmail' => $rescueEmail,
                  'token'       => $token,
                  'url'         => $this->getContainer()->getParameter('url')
                ));

                $message = \Swift_Message::newInstance()
                    ->setSubject($subject)
                    ->setFrom('noreply@vmail.me')
                    ->setTo($rescueEmail)
                    ->setBody($body);
                $this->getMailer()->send($message);

                return true;
            } else {
                // Return true if a token has not expired yet
                return true;
            }
        } else {
            return false;
        }
    }

    /**
     * Check token
     *
     * @return User
     */
    public function checkToken($rescueEmail, $token)
    {
        // Find token
        $forgotPassword = $this->getDoctrine()
            ->getRepository('rootiovmailmeBundle:ForgotPassword')
            ->findOneByToken($token);

        // Check if token exists and has not expired
        if ($forgotPassword && $forgotPassword->getExpire() >= new \DateTime('now')) {
            // Find user associated with the token
            $user = $this->getDoctrine()
                ->getRepository('rootiovmailmeBundle:User')
                ->findOneById($forgotPassword->getUser());

            // Check if the user and the associated rescue email exist
            if ($user && $rescueEmail == $user->getRescueEmail()) {
                return $user;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Delete token
     *
     * @return Boolean
     */
    public function deleteToken($token)
    {
        $forgotPassword = $this->getDoctrine()
            ->getRepository('rootiovmailmeBundle:ForgotPassword')
            ->findOneByToken($token);

        if ($forgotPassword) {
            $em = $this->getDoctrine()->getManager();

            $em->remove($forgotPassword);
            $em->flush();

            return true;
        } else {
          return false;
        }
    }
}
