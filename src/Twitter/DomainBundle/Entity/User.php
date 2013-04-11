<?php

namespace Twitter\DomainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Entity\User as BaseUser;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Accessor;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;

/**
 * User
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Twitter\DomainBundle\Entity\UserRepository")
 * @ExclusionPolicy("all")
 */
class User extends BaseUser
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Tweet", mappedBy="user")
     * @ORM\OrderBy({"id" = "DESC"})
     */
    protected $tweets;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="User", inversedBy="followers")
     * @ORM\JoinTable(name="UserFollowing",
     *     joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="follower_user_id", referencedColumnName="id")}
     *     )
     * @ORM\OrderBy({"username" = "ASC"})
     */
    protected $following;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="User", mappedBy="following")
     */
    protected $followers;

    /**
     * @Accessor("getUsername")
     * @Expose
     * @SerializedName("username")
     * @Type("string")
     */
    private $__username;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    public function addTweet(Tweet $tweet)
    {
        $this->tweets->add($tweet);
    }

    public function removeTweet(Tweet $tweet)
    {
        $this->tweets->removeElement($tweet);
    }

    public function getTweets()
    {
        return $this->tweets;
    }

    public function followUser(User $user)
    {
        $this->following->add($user);
        $user->followers->add($this);
    }

    public function unfollowUser(User $user)
    {
        $this->following->removeElement($user);
        $user->followers->removeElement($this);
    }

    public function isFollowing(User $user)
    {
        return $this->following->contains($user);
    }

    public function getFollowers()
    {
        return $this->followers;
    }

    public function getFollowing()
    {
        return $this->following;
    }
}
