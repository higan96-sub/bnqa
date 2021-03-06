<?php
namespace Acme\BnqaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Bookmark
 *
 * @ORM\Table(name="bookmark")
 * @ORM\Entity(repositoryClass="Acme\BnqaBundle\Entity\BookmarkRepository")
 */
class Bookmark
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
   * @var string
   *
   * @ORM\Column(name="target", type="string", length=100, nullable=false)
   */
  private $target;
  /**
   * @var string
   *
   * @ORM\Column(name="target_type", type="string", length=100, nullable=false)
   */
  private $targetType;
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
   * @var User
   *
   * @ORM\ManyToOne(targetEntity="User")
   * @ORM\JoinColumns({
   * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
   * })
   */
  private $user;

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
   * Set target
   *
   * @param string $target
   *
   * @return Bookmark
   */
  public function setTarget($target)
  {
    $this->target = $target;

    return $this;
  }

  /**
   * Get target
   *
   * @return string
   */
  public function getTarget()
  {
    return $this->target;
  }

  /**
   * Set targetType
   *
   * @param string $targetType
   *
   * @return Bookmark
   */
  public function setTargetType($targetType)
  {
    $this->targetType = $targetType;

    return $this;
  }

  /**
   * Get targetType
   *
   * @return string
   */
  public function getTargetType()
  {
    return $this->targetType;
  }

  /**
   * Set createdAt
   *
   * @param \DateTime $createdAt
   *
   * @return Bookmark
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
   * @return Bookmark
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
   * @return Bookmark
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
}