<?php
namespace Acme\BnqaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * UserFollowing.php .
 *
 * @ORM\Table(name="user_following")
 * @ORM\Entity(repositoryClass="Acme\BnqaBundle\Entity\UserFollowingRepository")
 */
class UserFollowing
{
  /**
   * @var integer
   *
   * @ORM\Column(name="id", type="integer", nullable=false)
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="IDENTITY")
   */
  private $id;

  /**
   * @var User
   *
   * @ORM\ManyToOne(targetEntity="User")
   * @ORM\JoinColumns({
   * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
   * })
   */
  private $user;

  /**
   * @var User
   *
   * @ORM\ManyToOne(targetEntity="User")
   * @ORM\JoinColumns({
   * @ORM\JoinColumn(name="following_user_id", referencedColumnName="id")
   * })
   */
  private $followingUser;

  /**
   * @var \DateTime $createdAt
   *
   * @ORM\Column(name="created_at", type="datetime", nullable=false)
   * @Gedmo\Timestampable(on="create")
   */
  private $createdAt;

  /**
   * @var \DateTime $updatedAt
   *
   * @ORM\Column(name="updated_at", type="datetime", nullable=false)
   * @Gedmo\Timestampable(on="update")
   */
  private $updatedAt;

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
   * Set createdAt
   *
   * @param \DateTime $createdAt
   *
   * @return UserFollowing
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

  /**
   * Set updatedAt
   *
   * @param \DateTime $updatedAt
   *
   * @return UserFollowing
   */
  public function setUpdatedAt($updatedAt)
  {
    $this->updatedAt = $updatedAt;

    return $this;
  }

  /**
   * Get updatedAt
   *
   * @return \DateTime
   */
  public function getUpdatedAt()
  {
    return $this->updatedAt;
  }

  /**
   * Set user
   *
   * @param \Acme\BnqaBundle\Entity\User $user
   *
   * @return UserFollowing
   */
  public function setUser(\Acme\BnqaBundle\Entity\User $user = null)
  {
    $this->user = $user;

    return $this;
  }

  /**
   * Get user
   *
   * @return \Acme\BnqaBundle\Entity\User
   */
  public function getUser()
  {
    return $this->user;
  }

  /**
   * Set followingUser
   *
   * @param \Acme\BnqaBundle\Entity\User $followingUser
   *
   * @return UserFollowing
   */
  public function setFollowingUser(\Acme\BnqaBundle\Entity\User $followingUser = null)
  {
    $this->followingUser = $followingUser;

    return $this;
  }

  /**
   * Get followingUser
   *
   * @return \Acme\BnqaBundle\Entity\User
   */
  public function getFollowingUser()
  {
    return $this->followingUser;
  }
}