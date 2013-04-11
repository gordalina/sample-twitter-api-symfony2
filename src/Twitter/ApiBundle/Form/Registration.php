<?php

namespace Twitter\ApiBundle\Form;

use Twitter\DomainBundle\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation\Type;

class Registration
{
    /**
     * @Assert\NotBlank
     * @Assert\Regex("/^\w+$/")
     * @Type("string")
     * @var string
     */
    public $username;

    /**
     * @Assert\Email
     * @Assert\NotBlank
     * @Type("string")
     * @var string
     */
    public $email;

    /**
     * @Assert\NotBlank
     * @Type("string")
     * @var string
     */
    public $password;

    public function getUser()
    {
        $user = new User();
        $user->setUsername($this->username);
        $user->setEmail($this->email);
        $user->setPassword($this->password);
        $user->setEnabled(true);

        return $user;
    }
}
