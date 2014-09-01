<?php

namespace rootio\Bundle\vmailmeBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author David Routhieau <rootio@vmail.me>
 */
class SuspendType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('password', 'password');
        $builder->add('agreement', 'checkbox');
    }

    public function getName()
    {
        return 'suspend';
    }
}
