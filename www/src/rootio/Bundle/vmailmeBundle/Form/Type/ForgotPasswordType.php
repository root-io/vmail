<?php

namespace rootio\Bundle\vmailmeBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author David Routhieau <rootio@vmail.me>
 */
class ForgotPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('rescueEmail', 'text');
    }

    public function getName()
    {
        return 'forgotPassword';
    }
}
