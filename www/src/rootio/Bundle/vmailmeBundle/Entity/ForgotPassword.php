<?php

namespace rootio\Bundle\vmailmeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ForgotPassword
 *
 * @author David Routhieau <rootio@vmail.me>
 *
 * @ORM\Table(name="forgot_password")
 * @ORM\Entity(repositoryClass="rootio\Bundle\vmailmeBundle\Repository\ForgotPasswordRepository")
 */
class ForgotPassword
{
    /**
     * @var integer
     *
     * @ORM\OneToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * @ORM\Id
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=255)
     */
    private $token;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="expire", type="datetime")
     */
    private $expire;


    /**
     * Set user
     *
     * @param integer user
     * @return ForgotPassword
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return integer
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set token
     *
     * @param string $token
     * @return ForgotPassword
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set expire
     *
     * @param \DateTime $expire
     * @return ForgotPassword
     */
    public function setExpire($expire)
    {
        $this->expire = $expire;

        return $this;
    }

    /**
     * Get expire
     *
     * @return \DateTime
     */
    public function getExpire()
    {
        return $this->expire;
    }
}
