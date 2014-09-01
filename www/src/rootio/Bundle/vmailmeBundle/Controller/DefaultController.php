<?php

namespace rootio\Bundle\vmailmeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use rootio\Bundle\vmailmeBundle\Entity\User;

use rootio\Bundle\vmailmeBundle\Form\Model\Registration;
use rootio\Bundle\vmailmeBundle\Form\Type\RegistrationType;
use rootio\Bundle\vmailmeBundle\Lib\RoundcubeLogin;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

use rootio\Bundle\vmailmeBundle\Entity\Forgot;
use rootio\Bundle\vmailmeBundle\Form\Type\ForgotType;

use rootio\Bundle\vmailmeBundle\Form\Type\ResetType;


use Symfony\Component\Security\Core\SecurityContext;

/**
 * @author David Routhieau <rootio@vmail.me>
 */
class DefaultController extends Controller
{
    public function indexAction()
    {
        if ($this->get('security.context')->isGranted('ROLE_USER')) {
            return $this->redirect($this->generateUrl('user_webmail'));
        }

        return $this->render('rootiovmailmeBundle:Default:homepage.html.twig');
    }

    public function legalDocumentsAction()
    {
        return $this->render('rootiovmailmeBundle:Default:legalDocuments.html.twig');
    }

    public function termsOfServiceAction()
    {
        return $this->render('rootiovmailmeBundle:Default:termsOfService.html.twig');
    }

    public function privacyPolicyAction()
    {
        return $this->render('rootiovmailmeBundle:Default:privacyPolicy.html.twig');
    }

    public function plansAction()
    {
        return $this->render('rootiovmailmeBundle:Default:plans.html.twig');
    }

    public function featuresAction()
    {
        return $this->render('rootiovmailmeBundle:Default:features.html.twig');
    }

    public function loginAction()
    {
        $request = $this->getRequest();
        $session = $request->getSession();

        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }

        return $this->render('rootiovmailmeBundle:Default:login.html.twig', array(
            'last_username' => $session->get(SecurityContext::LAST_USERNAME),
            'error'         => $error,
        ));
    }

    public function forgotAction()
    {
        $form = $this->createForm(new ForgotType(), new User());

        return $this->render('rootiovmailmeBundle:Default:forgot.html.twig', array('form' => $form->createView()));
    }

    public function forgotCheckAction()
    {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(new ForgotType(), new User());

        $form->bind($this->getRequest());

        if ($form->isValid()) {

            $rescueEmail = $form->getData()->getRescueEmail();

            $users = $this->getDoctrine()
                ->getRepository('rootiovmailmeBundle:User')
                ->findByRescueEmail($rescueEmail);

            if ($users) {

                foreach ($users as $user) {

                    // Save forgot demand
                    $forgot = $em->getRepository('rootiovmailmeBundle:Forgot')->findOneByUser($user);

                    if (!$forgot) {

                        $forgot = new Forgot();
                    }

                    if ($forgot->getExpire() < new \DateTime('now')) { // Wait token expiration to send another

                        // Generate token
                        $token = hash('sha256', uniqid('', true));

                        $forgot->setUser($user);
                        $forgot->setToken($token);
                        $forgot->setExpire(new \DateTime('+5 minutes'));

                        $em->persist($forgot);
                        $em->flush();

                        // Send forgot token to rescue email
                        $templateContent = $this->get('twig')->loadTemplate('rootiovmailmeBundle::Emailing/forgot.text.twig');

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

                    $t = $this->get('translator')->trans('Password reset\'s instructions sent!');
                    $this->get('session')->getFlashBag()->set('success', $t);
                }
            }
        }

        return $this->render('rootiovmailmeBundle:Default:forgot.html.twig', array('form' => $form->createView()));
    }

    public function resetAction()
    {
        $form = $this->createForm(new ResetType(), new User());

        return $this->render('rootiovmailmeBundle:Default:reset.html.twig', array('form' => $form->createView()));
    }

    public function resetCheckAction($rescueEmail, $token)
    {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(new ResetType(), new User());

        $form->bind($this->getRequest());

        if ($form->isValid()) {

            $newPassword = $form->getData()->getPassword();

            $user = $this->getDoctrine()
                ->getRepository('rootiovmailmeBundle:User')
                ->findOneByRescueEmail($rescueEmail);

            if ($user) {

                $forgot = $this->getDoctrine()
                    ->getRepository('rootiovmailmeBundle:Forgot')
                    ->findOneBy(array('user_id' => $user->getId(), 'token' => $token));

                if ($forgot) {

                    $factory = $this->get('security.encoder_factory');
                    $encoder = $factory->getEncoder($user);
                    $password = $encoder->encodePassword($newPassword, $user->getSalt());
                    $user->setPassword($password);
                    $user->setPasswordLegacy($newPassword);

                    $em->remove($forgot);
                    $em->flush();

                    $t = $this->get('translator')->trans('Password updated!');
                    $this->get('session')->getFlashBag()->set('success', $t);
                }
            }
        }

        return $this->render('rootiovmailmeBundle:Default:reset.html.twig', array('form' => $form->createView()));
    }

    public function registrationAction()
    {
        if ($this->get('security.context')->isGranted('ROLE_USER')) {
            return $this->redirect($this->generateUrl('user_webmail'));
        }

        $form = $this->createForm(new RegistrationType(), new Registration());

        $registration = $this->container->getParameter('registration');

        return $this->render('rootiovmailmeBundle:Default:registration.html.twig', array(
            'form'              => $form->createView(),
            'registration' => $registration
            ));
    }

    public function registrationNewAction(Request $request)
    {
        $form = $this->createForm(new RegistrationType(), new Registration());

        $form->bind($this->getRequest());

        $error = false;
        $errors = array();

        if ($form->isValid()) {

            $registration = $form->getData();
            $username = $registration->getUsername();
            $email = $registration->getUsername() . '@vmail.me';
            $password = $registration->getPassword();

            $isUserForbidden = $this->get('rootiovmailme.user_manager')->isUserForbidden($username);
            $isUsernameOrEmailExist = $this->get('rootiovmailme.user_manager')->isUsernameOrEmailExist($username);

            if ($isUserForbidden || $isUsernameOrEmailExist) {
                $errors['message'] = $this->get('translator')->trans('Email not available');
                $error = true;
            }

            if (!$error) {
                $user = $this->get('rootiovmailme.user_manager')->createUser($username, $email, $password, null, null, 'basic', true);

                $this->get('rootiovmailme.login_manager')->createLogin($email, $this->get('request')->getClientIp(), 'web');

                $token = new UsernamePasswordToken($user, null, 'main', array('ROLE_USER'));
                $this->get('security.context')->setToken($token);

                $rcl = new RoundcubeLogin($request, '/webmail/');

                try {
                   $rcl->login($email, $password);
                } catch (RoundcubeLoginException $ex) {}

                return $this->redirect(
                    $this->generateUrl('user_webmail')
                );
            }
        }

        $registration = $this->container->getParameter('registration');

        return $this->render('rootiovmailmeBundle:Default:registration.html.twig', array(
            'form' => $form->createView(),
            'registration' => $registration,
            'errors' => $errors
        ));
    }
}
