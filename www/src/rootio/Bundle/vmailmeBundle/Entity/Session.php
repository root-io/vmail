<?php

namespace rootio\Bundle\vmailmeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Session
 *
 * @author David Routhieau <rootio@vmail.me>
 *
 * @ORM\Table(name="session")
 * @ORM\Entity
 */
class Session
{
    /**
     * @var string
     *
     * @ORM\Column(name="session_id", type="string", length=255)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $sessionId;

    /**
     * @var string
     *
     * @ORM\Column(name="session_value", type="text", nullable=false)
     */
    private $sessionValue;

    /**
     * @var integer
     *
     * @ORM\Column(name="session_time", type="integer", nullable=false)
     */
    private $sessionTime;
}
