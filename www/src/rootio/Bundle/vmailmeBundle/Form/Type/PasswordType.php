<?php

namespace rootio\Bundle\vmailmeBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author David Routhieau <rootio@vmail.me>
 */
class PasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('currentPassword', 'password');
        $builder->add('newPassword', 'repeated', array(
           'first_name' => 'password',
           'second_name' => 'confirm',
           'type' => 'password'
        ));
    }

    public function getName()
    {
        return 'password';
    }
}