<?php

namespace rootio\Bundle\vmailmeBundle\Manager;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\DependencyInjection\Container;

use rootio\Bundle\vmailmeBundle\Entity\ForgotPassword;

class ForgotPasswordManager {

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
     * Create token
     *
     * @return boolean
     */
    public function createToken($rescueEmail)
    {
        // Find all users who linked this rescue email
        $users = $this->getDoctrine()
            ->getRepository('rootiovmailmeBundle:User')
            ->findByRescueEmail($rescueEmail);

        if ($users) {

            foreach ($users as $user) {

                // For each user find a forgot password database entry
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
                    $templateContent = $this->get('twig')->loadTemplate('rootiovmailmeBundle::Emailing/forgot_password.text.twig');

                    $subject = $templateContent->renderBlock('subject', array());
                    $body = $templateContent->renderBlock('body', array('rescueEmail' => $rescueEmail, 'token' => $token));

                    $message = \Swift_Message::newInstance()
                        ->setSubject($subject)
                        ->setFrom('noreply@vmail.me')
                        ->setTo($rescueEmail)
                        ->setBody($body)
                    ;
                    $this->get('mailer')->send($message);
                }
            }
        }
    }

    /**
     * Check token
     *
     * @return boolean
     */
    public function checkToken($rescueEmail, $token, $newPassword)
    {
        // Find all users who linked this rescue email
        $users = $this->getDoctrine()
            ->getRepository('rootiovmailmeBundle:User')
            ->findByRescueEmail($rescueEmail);

        if ($users) {

            foreach ($users as $user) {

                // For each user find the token
                $forgotPassword = $this->getDoctrine()
                    ->getRepository('rootiovmailmeBundle:ForgotPassword')
                    ->findOneBy(array('user' => $user->getId(), 'token' => $token));

                if ($forgotPassword) {

                    $editPassword = $this->getContainer()->get('rootiovmailme.user_manager')->editPassword($user, $newPassword);
                }
            }
        }
    }
}
