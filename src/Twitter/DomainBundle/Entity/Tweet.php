<?php

namespace Twitter\DomainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Accessor;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;

/**
 * Tweet
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Twitter\DomainBundle\Entity\TweetRepository")
 * @ExclusionPolicy("all")
 */
class Tweet
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
     * @ORM\Column(name="content", type="string", length=140)
     * @Assert\NotBlank()
     * @Expose
     * @Type("string")
     */
    private $content;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     * @Expose
     * @Type("DateTime")
     */
    private $createdAt;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="tweets")
     */
    private $user;

    /**
     * @Accessor("getUsername")
     * @Expose
     * @SerializedName("username")
     * @Type("string")
     */
    private $__username;

    public function __construct()
    {
        $this->__wakeup();
    }

    public function __wakeup()
    {
        $this->createdAt = new \DateTime();
    }

    public function getUsername()
    {
        return $this->user->getUsername();
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
     * Set content
     *
     * @param string $content
     * @return Tweet
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Tweet
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function merge(Tweet $tweet)
    {
        $this->content = $tweet->content;
    }
    public function setUser(User $user)
    {
        $this->user = $user;
        $this->user->addTweet($this);
    }
}
