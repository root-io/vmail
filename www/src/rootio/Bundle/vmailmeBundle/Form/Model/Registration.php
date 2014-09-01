<?php

namespace rootio\Bundle\vmailmeBundle\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author David Routhieau <rootio@vmail.me>
 */
class Registration
{
    /**
     * @Assert\Regex(
     *     pattern="/^[a-z0-9]+(?:[\.-][a-z0-9]+)*$/",
     *     message="Email {{ value }}@vmail.me is incorrect (Only letters (a-z), numbers (0-9), dashes (-) and dots (.) are allowed)"
     * )
     *
     * @Assert\Length(
     *      min = "3",
     *      max = "30",
     *      minMessage = "Email must have at least {{ limit }} characters",
     *      maxMessage = "Email must have less than {{ limit }} characters"
     * )
     */
    protected $username;

    /**
     * @Assert\Length(
     *      min = "6",
     *      minMessage = "Password must have at least {{ limit }} characters"
     * )
     */
    protected $password;

    /**
     * @Assert\NotBlank()
     */
    protected $termsOfService;

    /**
     * Set username
     *
     * @param string $username
     * @return Registration
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return Registration
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
     * Set termsOfService
     *
     * @param boolean $termsOfService
     * @return Registration
     */
    public function setTermsOfService($termsOfService)
    {
        $this->termsOfService = (Boolean) $termsOfService;

        return $this;
    }

    /**
     * Get termsOfService
     *
     * @return boolean
     */
    public function getTermsOfService()
    {
        return $this->termsOfService;
    }
}
