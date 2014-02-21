<?php
// src/UiucCms/UserBundle/Entity/User.php
namespace UiucCms\Bundle\UserBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 * @UniqueEntity(fields="email", message="Email already taken")
 */
class User
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    protected $email;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(max = 4096)
     */
    protected $plainPassword;

	protected $first_name;
	protected $last_name;
	protected $phone;
	
	public function getPhone()
	{
		return $this->phone;
	}
	
	public function setPhone($phone)
	{
		$this->phone = $phone;
	}
	
	public function getFirstName()
	{
		return $this->first_name;
	}
	
	public function setFirstName($name)
	{
		$this->first_name = $name;
	}
	
	public function getLastName()
	{
		return $this->last_name;
	}
	
	public function setLastName($name)
	{
		$this->last_name = $name;
	}
	
    public function getId()
    {
        return $this->id;
    }

	
    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    public function setPlainPassword($password)
    {
        $this->plainPassword = $password;
    }
}
?>