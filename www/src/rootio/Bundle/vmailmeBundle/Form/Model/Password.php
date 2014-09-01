<?php

namespace rootio\Bundle\vmailmeBundle\Form\Model;

use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author David Routhieau <rootio@vmail.me>
 */
class Password
{
    /**
     * @var string
     *
     * @SecurityAssert\UserPassword(
     *     message = "Incorrect current password"
     * )
     */
    protected $currentPassword;

    /**
     * @var string
     *
     * @Assert\Length(
     *      min = "6",
     *      minMessage = "The new password must have at least {{ limit }} characters"
     * )
     */
    protected $newPassword;

    /**
     * Set currentPassword
     *
     * @param string $currentPassword
     * @return Password
     */
    public function setCurrentPassword($currentPassword)
    {
        $this->currentPassword = $currentPassword;

        return $this;
    }

    /**
     * Get currentPassword
     *
     * @return string
     */
    public function getCurrentPassword()
    {
        return $this->currentPassword;
    }

    /**
     * Set newPassword
     *
     * @param string $newPassword
     * @return Password
     */
    public function setNewPassword($newPassword)
    {
        $this->newPassword = $newPassword;

        return $this;
    }

    /**
     * Get newPassword
     *
     * @return string
     */
    public function getNewPassword()
    {
        return $this->newPassword;
    }
}
