<?php

namespace WebComposer\Bundle\AuthBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ODM\Document(
 *     collection="accounts",
 *     indexes={
 *         @Index(keys={"username"="desc"}, options={"unique"=true})
 *     }
 * )
 */
class Account implements AdvancedUserInterface, \Serializable
{
    /** @ODM\Id(strategy="AUTO") */
    private $id;

    /**
     * @ODM\String
     */
    private $username;

    /**
     * @ORM\String
     */
    private $password;

    /**
     * @ORM\String
     */
    private $salt;

    /**
     * @ORM\String
     */
    private $email;

    /**
     * @ORM\Boolean
     */
    private $active = true;

    /**
     * @ORM\Collection
     */
    private $roles = array();

    /**
     * @ORM\String(name="activation_token")
     */
    private $activationToken = null;

    /**
     * @ORM\String(name="reset_password_token")
     */
    private $resetPasswordToken = null;

    /**
     * @ORM\Date(name="created_at")
     */
    private $createdAt;

    /**
     * Set the user ID.
     *
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }
    /**
     * Get the user ID.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns the username, if not empty, otherwise the email address.
     *
     * Email is returned as a fallback because username is optional,
     * but the Symfony Security system depends on getUsername() returning a value.
     * Use getRealUsername() to get the actual username value.
     *
     * This method is required by the UserInterface.
     *
     * @see getRealUsername
     * @return string The username, if not empty, otherwise the email.
     */
    public function getUsername()
    {
        return $this->getRealUsername() ?: $this->getEmail();
    }

    /**
     * Get the actual username value that was set,
     * or null if no username has been set.
     * Compare to getUsername, which returns the email if username is not set.
     *
     * @see getUsername
     * @return string|null
     */
    public function getRealUsername()
    {
        return $this->username;
    }

    /**
     * Test whether username has ever been set (even if it's currently empty).
     *
     * @return bool
     */
    public function hasRealUsername()
    {
        return !is_null($this->username);
    }

    /**
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * Get the encoded password used to authenticate the user.
     *
     * On authentication, a plain-text password will be salted,
     * encoded, and then compared to this value.
     *
     * @return string The encoded password.
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set the encoded password.
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string The salt
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * Set the salt that should be used to encode the password.
     *
     * @param string $salt
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
    }

    /**
     * @return string The user's email address.
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Checks whether the user is active.
     *
     * Internally, if this method returns false, the authentication system
     * will throw a DisabledException and prevent login.
     *
     * Users are active by default.
     *
     * @return bool    true if the user is active, false otherwise
     *
     * @see DisabledException
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * Set whether the user is active.
     *
     * @param bool $isActive
     */
    public function setActive($isActive)
    {
        $this->active = (bool) $isActive;
    }

    /**
     * Returns the roles granted to the user. Note that all users have the ROLE_USER role.
     *
     * @return array A list of the user's roles.
     */
    public function getRoles()
    {
        $roles = $this->roles;
        // Every user must have at least one role, per Silex security docs.
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    /**
     * Set the user's roles to the given list.
     *
     * @param array $roles
     */
    public function setRoles(array $roles)
    {
        $this->roles = array();
        foreach ($roles as $role) {
            $this->addRole($role);
        }
    }

    /**
     * Test whether the user has the given role.
     *
     * @param string $role
     * @return bool
     */
    public function hasRole($role)
    {
        return in_array(strtoupper($role), $this->getRoles(), true);
    }

    /**
     * Add the given role to the user.
     *
     * @param string $role
     */
    public function addRole($role)
    {
        $role = strtoupper($role);
        if ($role === 'ROLE_USER') {
            return;
        }
        if (!$this->hasRole($role)) {
            $this->roles[] = $role;
        }
    }

    /**
     * Remove the given role from the user.
     *
     * @param string $role
     */
    public function removeRole($role)
    {
        if (false !== $key = array_search(strtoupper($role), $this->roles, true)) {
            unset($this->roles[$key]);
            $this->roles = array_values($this->roles);
        }
    }

    /**
     * @param string $salt
     */
    public function setActivationToken($token)
    {
        $this->activationToken = $token;
    }

    /**
     * @return string The user's email address.
     */
    public function getActivationToken()
    {
        return $this->activationToken;
    }

    /**
     * @param string $salt
     */
    public function setResetPasswordToken($token)
    {
        $this->resetPasswordToken = $token;
    }

    /**
     * @return string The user's email address.
     */
    public function getResetPasswordToken()
    {
        return $this->resetPasswordToken;
    }

    static public function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('email', new Assert\NotBlank());
        $metadata->addPropertyConstraint('email', new Assert\Email());
        $metadata->addPropertyConstraint('username', new Assert\NotBlank());
        $metadata->addPropertyConstraint('username', new Assert\Length(array('min' => 3,'max'=>60)));
        $metadata->addPropertyConstraint('password', new Assert\NotBlank());
        $metadata->addPropertyConstraint('password', new Assert\Length(array('min' => 4,'max'=>60)));
    }

    /**
     * Checks whether the user's account has expired.
     *
     * Internally, if this method returns false, the authentication system
     * will throw an AccountExpiredException and prevent login.
     *
     * @return bool    true if the user's account is non expired, false otherwise
     *
     * @see AccountExpiredException
     */
    public function isAccountNonExpired()
    {
        return true;
    }

    /**
     * Checks whether the user is locked.
     *
     * Internally, if this method returns false, the authentication system
     * will throw a LockedException and prevent login.
     *
     * @return bool    true if the user is not locked, false otherwise
     *
     * @see LockedException
     */
    public function isAccountNonLocked()
    {
        return $this->isActive();
    }

    /**
     * Checks whether the user's credentials (password) has expired.
     *
     * Internally, if this method returns false, the authentication system
     * will throw a CredentialsExpiredException and prevent login.
     *
     * @return bool    true if the user's credentials are non expired, false otherwise
     *
     * @see CredentialsExpiredException
     */
    public function isCredentialsNonExpired()
    {
        return true;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is a no-op, since we never store the plain text credentials in this object.
     * It's required by UserInterface.
     *
     * @return void
     */
    public function eraseCredentials()
    {
    }

    /**
     * The Symfony Security component stores a serialized User object in the session.
     * We only need it to store the user ID, because the user provider's refreshUser() method is called on each request
     * and reloads the user by its ID.
     *
     * @see \Serializable::serialize()
     */
    public function serialize()
    {
        return serialize(array(
            $this->id
        ));
    }

    /**
     * @see \Serializable::unserialize()
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            // see section on salt below
            // $this->salt
        ) = unserialize($serialized);
    }
}