<?php

namespace rootio\Bundle\vmailmeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use rootio\Bundle\vmailmeBundle\Form\Type\RescueType;

use rootio\Bundle\vmailmeBundle\Form\Model\Password;
use rootio\Bundle\vmailmeBundle\Form\Type\PasswordType;

use rootio\Bundle\vmailmeBundle\Form\Type\ForwardingType;

use rootio\Bundle\vmailmeBundle\Form\Model\Suspend;
use rootio\Bundle\vmailmeBundle\Form\Type\SuspendType;

use rootio\Bundle\vmailmeBundle\Entity\User;

/**
 * @author David Routhieau <rootio@vmail.me>
 */
class UserController extends Controller
{
    public function webmailAction()
    {
        return $this->render('rootiovmailmeBundle:User:webmail/index.html.twig');
    }

    public function webmailAddressBookAction()
    {
        return $this->render('rootiovmailmeBundle:User:webmail/addressBook.html.twig');
    }

    public function webmailSettingsAction()
    {
        return $this->render('rootiovmailmeBundle:User:webmail/settings.html.twig');
    }

    public function settingsAction()
    {
        return $this->render('rootiovmailmeBundle:User:settings.html.twig');
    }

    public function rescueAction()
    {
        $form = $this->createForm(new RescueType(), new User());

        return $this->render('rootiovmailmeBundle:User:rescue.html.twig', array('form' => $form->createView()));
    }

    public function rescueEditAction()
    {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(new RescueType(), new User());

        $form->bind($this->getRequest());

        $user = $this->get('security.context')->getToken()->getUser();

        if ($form->isValid()) {

            $rescueEmail = $form->getData()->getRescueEmail();
            if (empty($rescueEmail) || $rescueEmail == $user->getEmail()) {
                $rescueEmail = null;
            }
            $user->setRescueEmail($rescueEmail);

            $em->persist($user);
            $em->flush();

            $t = $this->get('translator')->trans('Rescue email updated!');
            $this->get('session')->getFlashBag()->set('success', $t);
        }

        return $this->render('rootiovmailmeBundle:User:rescue.html.twig', array('form' => $form->createView()));
    }

    public function passwordAction()
    {
        $form = $this->createForm(new PasswordType(), new Password());

        return $this->render('rootiovmailmeBundle:User:password.html.twig', array('form' => $form->createView()));
    }

    public function passwordEditAction()
    {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(new PasswordType(), new Password());

        $form->bind($this->getRequest());

        $user = $this->get('security.context')->getToken()->getUser();

        if ($form->isValid()) {

            $newPassword = $form->getData()->getNewPassword();

            $factory = $this->get('security.encoder_factory');
            $encoder = $factory->getEncoder($user);
            $password = $encoder->encodePassword($newPassword, $user->getSalt());
            $user->setPassword($password);
            $user->setPasswordLegacy($newPassword);

            $em->persist($user);
            $em->flush();

            // Update session password
            $token = $this->get( 'security.context' )->getToken();
            $token->setAuthenticated(false);

            $t = $this->get('translator')->trans('Password updated!');
            $this->get('session')->getFlashBag()->set('success', $t);
        }

        return $this->render('rootiovmailmeBundle:User:password.html.twig', array('form' => $form->createView()));
    }

    public function forwardingAction()
    {
        $form = $this->createForm(new ForwardingType(), new User());

        return $this->render('rootiovmailmeBundle:User:forwarding.html.twig', array('form' => $form->createView()));
    }

    public function forwardingEditAction()
    {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(new ForwardingType(), new User());

        $form->bind($this->getRequest());

        $user = $this->get('security.context')->getToken()->getUser();

        if ($form->isValid()) {

            $forwardingEmail = $form->getData()->getForwardingEmail();
            if (empty($forwardingEmail) || $forwardingEmail == $user->getEmail()) {
                $forwardingEmail = null;
            }

            $loop = $this->getDoctrine()
                ->getRepository('rootiovmailmeBundle:User')
                ->findOneBy(array('email' => $forwardingEmail, 'forwardingEmail' => $user->getEmail()));

            if (!$loop) {

                $user->setForwardingEmail($forwardingEmail);

                $em->persist($user);
                $em->flush();

                $t = $this->get('translator')->trans('Forwarding email updated!');
                $this->get('session')->getFlashBag()->set('success', $t);
            }
        }

        return $this->render('rootiovmailmeBundle:User:forwarding.html.twig', array('form' => $form->createView()));
    }

    public function accountAction()
    {
        $form = $this->createForm(new SuspendType(), new Suspend());

        return $this->render('rootiovmailmeBundle:User:suspend.html.twig', array('form' => $form->createView()));
    }

    public function accountSuspendAction()
    {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(new SuspendType(), new Suspend());

        $form->bind($this->getRequest());

        $user = $this->get('security.context')->getToken()->getUser();

        if ($form->isValid()) {

            $user->setIsEnabled(false);

            $em->persist($user);
            $em->flush();

            $t = $this->get('translator')->trans('Account now suspended!');
            $this->get('session')->getFlashBag()->set('success', $t . ' =\'(');
        }

        return $this->render('rootiovmailmeBundle:User:suspend.html.twig', array('form' => $form->createView()));
    }
}
