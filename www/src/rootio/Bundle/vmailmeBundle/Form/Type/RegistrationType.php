<?php

namespace rootio\Bundle\vmailmeBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author David Routhieau <rootio@vmail.me>
 */
class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('username', 'text');
        $builder->add('password', 'repeated', array(
            'first_name'      => 'password',
            'second_name'     => 'confirm',
            'invalid_message' => 'You have written two different passwords',
            'type'            => 'password'
        ));
        $builder->add('rescueEmail', 'text', array('required' => false));
        $builder->add('captcha', 'captcha', array(
            'disabled'        => false,
            'width'           => 220,
            'height'          => 50,
            'length'          => 8,
            'invalid_message' => 'Bad security code'
        ));
        $builder->add('termsOfService', 'checkbox');
    }

    public function getName()
    {
        return 'registration';
    }
}
