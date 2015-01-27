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

    public function webmailContactsAction()
    {
        return $this->render('rootiovmailmeBundle:User:webmail/contacts.html.twig');
    }

    public function webmailSettingsAction()
    {
        return $this->render('rootiovmailmeBundle:User:webmail/settings.html.twig');
    }

    public function expertAction()
    {
        return $this->render('rootiovmailmeBundle:User:expert.html.twig');
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

            $editPassword = $this->get('rootiovmailme.user_manager')->editPassword($user, $newPassword);

            // Update session password
            $token = $this->get('security.context')->getToken();
            $token->setAuthenticated(false);

            $t = $this->get('translator')->trans('Password updated!');
            $this->get('session')->getFlashBag()->set('success', $t);
        }

        return $this->render('rootiovmailmeBundle:User:password.html.twig', array('form' => $form->createView()));
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

            $editRescueEmail = $this->get('rootiovmailme.user_manager')->editRescueEmail($user, $rescueEmail);

            $t = $this->get('translator')->trans('Rescue email updated!');
            $this->get('session')->getFlashBag()->set('success', $t);
        }

        return $this->render('rootiovmailmeBundle:User:rescue.html.twig', array('form' => $form->createView()));
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

            $editForwardingEmail = $this->get('rootiovmailme.user_manager')->editForwardingEmail($user, $forwardingEmail);

            $t = $this->get('translator')->trans('Forwarding email updated!');
            $this->get('session')->getFlashBag()->set('success', $t);
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

            $suspendUser = $this->get('rootiovmailme.user_manager')->suspendUser($user);

            $t = $this->get('translator')->trans('Account now suspended!');
            $this->get('session')->getFlashBag()->set('success', $t . ' =\'(');
        }

        return $this->render('rootiovmailmeBundle:User:suspend.html.twig', array('form' => $form->createView()));
    }
}
