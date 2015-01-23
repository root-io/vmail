<?php

namespace rootio\Bundle\vmailmeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use rootio\Bundle\vmailmeBundle\Entity\User;

use rootio\Bundle\vmailmeBundle\Form\Model\Registration;
use rootio\Bundle\vmailmeBundle\Form\Type\RegistrationType;
use rootio\Bundle\vmailmeBundle\Lib\RoundcubeLogin;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

use rootio\Bundle\vmailmeBundle\Entity\ForgotPassword;
use rootio\Bundle\vmailmeBundle\Form\Type\ForgotPasswordType;

use rootio\Bundle\vmailmeBundle\Form\Type\ResetPasswordType;


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

    public function forgotPasswordAction()
    {
        $form = $this->createForm(new ForgotPasswordType(), new User());

        return $this->render('rootiovmailmeBundle:Default:forgotPassword.html.twig', array('form' => $form->createView()));
    }

    public function forgotPasswordCheckAction()
    {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(new ForgotPasswordType(), new User());

        $form->bind($this->getRequest());

        if ($form->isValid()) {

            $rescueEmail = $form->getData()->getRescueEmail();

            $createToken = $this->get('rootiovmailme.forgot_password_manager')->createToken($rescueEmail);

            $t = $this->get('translator')->trans('Password reset\'s instructions sent!');
            $this->get('session')->getFlashBag()->set('success', $t);
        }

        return $this->render('rootiovmailmeBundle:Default:forgotPassword.html.twig', array('form' => $form->createView()));
    }

    public function resetPasswordAction()
    {
        $form = $this->createForm(new ResetPasswordType(), new User());

        return $this->render('rootiovmailmeBundle:Default:resetPassword.html.twig', array('form' => $form->createView()));
    }

    public function resetPasswordCheckAction($rescueEmail, $token)
    {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(new ResetPasswordType(), new User());

        $form->bind($this->getRequest());

        if ($form->isValid()) {

            $newPassword = $form->getData()->getPassword();

            $checkToken = $this->get('rootiovmailme.forgot_password_manager')->checkToken($rescueEmail, $token, $newPassword);

            return $this->render('rootiovmailmeBundle:Default:resetPassword.html.twig', array('form' => $form->createView()));
        }
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

        $errors = [];

        if ($form->isValid()) {

            $registration = $form->getData();
            $username = $registration->getUsername();
            $email = $registration->getUsername() . "@vmail.me";
            $password = $registration->getPassword();

            $isUsernameForbidden = $this->get('rootiovmailme.user_manager')->isUsernameForbidden($username);
            $isUsernameTaken = $this->get('rootiovmailme.user_manager')->isUsernameTaken($username);

            if ($isUsernameForbidden || $isUsernameTaken) {
                $errors['message'] = $this->get('translator')->trans('Email not available');
            }

            $isPasswordForbidden = $this->get('rootiovmailme.user_manager')->isPasswordForbidden($password);

            if ($isPasswordForbidden) {
                $errors['message'] = $this->get('translator')->trans('Your password is too easy to guess');
            }

            if (empty($errors)) {
                $user = $this->get('rootiovmailme.user_manager')->createUser($username, $password);

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
