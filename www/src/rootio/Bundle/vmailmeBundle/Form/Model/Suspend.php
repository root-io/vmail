<?php

namespace rootio\Bundle\vmailmeBundle\Form\Model;

use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author David Routhieau <rootio@vmail.me>
 */
class Suspend
{
    /**
     * @SecurityAssert\UserPassword(
     *     message = "Incorrect password"
     * )
     */
    protected $password;

    /**
     * @Assert\NotBlank()
     */
    protected $agreement;

    /**
     * Set password
     *
     * @param string $password
     * @return Suspend
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set agreement
     *
     * @param boolean $agreement
     * @return Suspend
     */
    public function setAgreement($agreement)
    {
        $this->agreement = (Boolean)$agreement;

        return $this;
    }

    /**
     * Get agreement
     *
     * @return boolean
     */
    public function getAgreement()
    {
        return $this->agreement;
    }
}
