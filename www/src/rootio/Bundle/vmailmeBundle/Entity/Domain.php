<?php

namespace rootio\Bundle\vmailmeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Domain
 *
 * @author David Routhieau <rootio@vmail.me>
 *
 * @ORM\Table(name="domain")
 * @ORM\Entity
 */
class Domain
{
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $name;
}
