<?php

namespace rootio\Bundle\vmailmeBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author David Routhieau <rootio@vmail.me>
 */
class RescueType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('rescueEmail', 'text', array('required' => false));
    }

    public function getName()
    {
        return 'rescue';
    }
}