<?php

namespace rootio\Bundle\vmailmeBundle\Entity;

use Symfony\Component\Security\Core\User\AdvancedUserInterface;

use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Doctrine\Common\Collections\ArrayCollection;

use rootio\Bundle\vmailmeBundle\Entity\Ban;

/**
 * User
 *
 * @author David Routhieau <rootio@vmail.me>
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="rootio\Bundle\vmailmeBundle\Repository\UserRepository")
 * @UniqueEntity(fields={"username", "email"}, message="Email not available")
 */
class User implements AdvancedUserInterface
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
     *
     * @ORM\Column(name="username", type="string", length=255, unique=true)
     */
    private $username;

    /**
     * @var string
     *
     * @Assert\Email(
     *     message = "Email {{ value }} is incorrect (Only letters (a-z), numbers (0-9), dashes (-) and dots (.) are allowed)"
     * )
     *
     * @ORM\Column(name="email", type="string", length=255, unique=true)
     */
    private $email;

    /**
     * @var string
     *
     * @Assert\Length(
     *      min = "6",
     *      minMessage = "Password must have at least {{ limit }} characters"
     * )
     *
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="passwordLegacy", type="string", length=255)
     */
    private $passwordLegacy;

    /**
     * @var string
     *
     * @Assert\Email(
     *     message = "Email {{ value }} is incorrect",
     *     checkMX = true
     * )
     *
     * @ORM\Column(name="rescueEmail", type="string", length=255, nullable=true)
     */
    private $rescueEmail;

    /**
     * @var string
     *
     * @Assert\Email(
     *     message = "Email {{ value }} is incorrect",
     *     checkMX = true
     * )
     *
     * @ORM\Column(name="forwardingEmail", type="string", length=255, nullable=true)
     */
    private $forwardingEmail;

    const PLAN_BASIC = 'basic';
    const PLAN_PREMIUM = 'premium';

    /**
     * @var string
     *
     * @ORM\Column(name="plan", type="string", length=255, nullable=true)
     */
    private $plan;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Ban", mappedBy="user", fetch="LAZY")
     */
    private $bans;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="lastActivity", type="datetime")
     */
    private $lastActivity;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isEnabled", type="boolean")
     */
    private $isEnabled;


    public function __construct()
    {

        $this->rescueEmail = null;
        $this->forwardingEmail = null;
        $this->plan = 'basic';
        $this->isEnabled = true;
        $this->lastActivity = new \DateTime('now');
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
     * Set username
     *
     * @param string $username
     * @return User
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
     * Set email
     *
     * @param string $email
     * @return User
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
     * Set password
     *
     * @param string $password
     * @return User
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

    private function hexToAscii($hex)
    {
        $strLength = strlen($hex);
        $returnVal = '';

        for ($i = 0; $i < $strLength; $i += 2) {
            $dec_val = hexdec(substr($hex, $i, 2));
            $returnVal .= chr($dec_val);
        }

        return $returnVal;
    }

    /**
     * Set passwordLegacy
     *
     * @param string $password
     * @return User
     */
    public function setPasswordLegacy($password)
    {
        $salt = substr(sha1(uniqid('', true)), 18, 8);
        $salt_ascii = $this->hexToAscii($salt);

        $this->passwordLegacy = "{SSHA512.hex}" . hash('sha512', $password . $salt_ascii) . $salt;

        return $this;
    }

    /**
     * Get passwordLegacy
     *
     * @return string
     */
    public function getPasswordLegacy()
    {
        return $this->passwordLegacy;
    }

    /**
     * Set rescueEmail
     *
     * @param string $rescueEmail
     * @return User
     */
    public function setRescueEmail($rescueEmail)
    {
        $this->rescueEmail = $rescueEmail;

        return $this;
    }

    /**
     * Get rescueEmail
     *
     * @return string
     */
    public function getRescueEmail()
    {
        return $this->rescueEmail;
    }

    /**
     * Set forwardingEmail
     *
     * @param string $forwardingEmail
     * @return User
     */
    public function setForwardingEmail($forwardingEmail)
    {
        $this->forwardingEmail = $forwardingEmail;

        return $this;
    }

    /**
     * Get forwardingEmail
     *
     * @return string
     */
    public function getForwardingEmail()
    {
        return $this->forwardingEmail;
    }

    /**
     * Set plan
     *
     * @param string $plan
     * @return User
     */
    public function setPlan($plan)
    {
        if (!in_array($plan, array(self::PLAN_BASIC, self::PLAN_PREMIUM))) {
            throw new \InvalidArgumentException("Invalid plan");
        }
        $this->plan = $plan;

        return $this;
    }

    /**
     * Get plan
     *
     * @return string
     */
    public function getPlan()
    {
        return $this->plan;
    }

    /**
     * Add bans
     *
     * @param Ban $bans
     * @return ArrayCollection
     */
    public function addBans($bans)
    {
        $this->bans[] = $bans;

        return $this;
    }

    /**
     * Get bans
     *
     * @return ArrayCollection
     */
    public function getBans()
    {
        return $this->bans;
    }

    /**
     * Set lastActivity
     *
     * @return User
     */
    public function setLastActivity()
    {
        $this->lastActivity = new \DateTime('now');

        return $this;
    }

    /**
     * Get lastActivity
     *
     * @return \DateTime
     */
    public function getLastActivity()
    {
        return $this->lastActivity;
    }

    /**
     * @inheritDoc
     */
    public function getSalt()
    {
    }

    /**
     * @inheritDoc
     */
    public function getRoles()
    {
        return array('ROLE_USER');
    }

    /**
     * @inheritDoc
     */
    public function eraseCredentials()
    {
    }

    /**
     * @inheritDoc
     */
    public function isAccountNonExpired()
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function isAccountNonLocked()
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function isCredentialsNonExpired()
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function isEnabled()
    {
        return $this->isEnabled;
    }

    /**
     * Set isEnabled
     *
     * @param string $isEnabled
     * @return User
     */
    public function setIsEnabled($isEnabled)
    {
        $this->isEnabled = $isEnabled;

        return $this;
    }
}
