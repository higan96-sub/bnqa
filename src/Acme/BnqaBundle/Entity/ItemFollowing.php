<?php
namespace Acme\BnqaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * ItemFollowing.php .
 *
 * @ORM\Table(name="item_following")
 * @ORM\Entity(repositoryClass="Acme\BnqaBundle\Entity\ItemFollowingRepository")
 */
class ItemFollowing
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
   * @var Item
   *
   * @ORM\ManyToOne(targetEntity="Item",cascade={"persist","remove"})
   * @ORM\JoinColumns({
   * @ORM\JoinColumn(name="item_id", referencedColumnName="id")
   * })
   */
  private $item;

  /**
   * @var string $asinCode
   *
   * @ORM\Column(name="asin_code", type="string" , length=100, nullable=false)
   */
  private $asinCode;


  /**
   * @var boolean
   *
   * @ORM\Column(name="active_flag", type="boolean" , nullable=false)
   */
  private $activeFlag;

  /**
   * @var \DateTime $createdAt
   *
   * @ORM\Column(name="created_at", type="datetime", unique=true,nullable=false)
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
   * @return ItemFollowing
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
   * @return ItemFollowing
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
   * @return ItemFollowing
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
   * Set item
   *
   * @param \Acme\BnqaBundle\Entity\Item $followingItem
   *
   * @return ItemFollowing
   */
  public function setItem(\Acme\BnqaBundle\Entity\Item $followingItem = null)
  {
    $this->item = $followingItem;

    return $this;
  }

  /**
   * Get item
   *
   * @return \Acme\BnqaBundle\Entity\Item
   */
  public function getItem()
  {
    return $this->item;
  }

  /**
   * Set asinCode
   *
   * @param string $asinCode
   *
   * @return ItemFollowing
   */
  public function setAsinCode($asinCode)
  {
    $this->asinCode = $asinCode;

    return $this;
  }

  /**
   * Get asinCode
   *
   * @return string
   */
  public function getAsinCode()
  {
    return $this->asinCode;
  }


  /**
   * Get ActiveFlag
   *
   * @return bool
   */
  public function getActiveFlag()
  {
    return $this->activeFlag;
  }

  /**
   * Set ActiveFlag
   *
   * @param $activeFlag
   *
   * @return ItemFollowing
   */
  public function setActiveFlag($activeFlag)
  {
    $this->activeFlag = $activeFlag;

    return $this;
  }
}