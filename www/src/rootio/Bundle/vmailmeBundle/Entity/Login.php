<?php

namespace rootio\Bundle\vmailmeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Login
 *
 * @author David Routhieau <rootio@vmail.me>
 *
 * @ORM\Table(name="login")
 * @ORM\Entity(repositoryClass="rootio\Bundle\vmailmeBundle\Repository\LoginRepository")
 */
class Login
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="ip", type="string", length=255)
     */
    private $ip;

    const PROTOCOL_WEB = 'web';
    const PROTOCOL_POP = 'pop';
    const PROTOCOL_IMAP = 'imap';

    /**
     * @var string
     *
     * @ORM\Column(name="protocol", type="string", length=255)
     */
    private $protocol;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    public function __construct() {

        $this->date = new \DateTime('now');
    }


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Login
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set ip
     *
     * @param string $ip
     * @return Login
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Get ip
     *
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Set protocol
     *
     * @param string $protocol
     * @return Login
     */
    public function setProtocol($protocol)
    {
        if (!in_array($protocol, array(self::PROTOCOL_WEB, self::PROTOCOL_POP, self::PROTOCOL_IMAP))) {
            throw new \InvalidArgumentException("Invalid protocol");
        }
        $this->protocol = $protocol;

        return $this;
    }

    /**
     * Get protocol
     *
     * @return string
     */
    public function getProtocol()
    {
        return $this->protocol;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Login
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }
}
