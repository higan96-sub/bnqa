<?php

namespace Acme\BnqaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Acme\BnqaBundle\Entity\Report
 *
 * @ORM\Table(name="report")
 * @ORM\Entity(repositoryClass="Acme\BnqaBundle\Entity\ReportRepository")
 *
 *
 */
class Report
{
  /**
   * @var integer $id
   *
   * @ORM\Column(name="id", type="integer", nullable=false)
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="IDENTITY")
   */
  private $id;

  /**
   * @var Item
   *
   * @ORM\ManyToOne(targetEntity="Item",cascade={"persist"})
   * @ORM\JoinColumns({
   * @ORM\JoinColumn(name="item_id", referencedColumnName="id")
   * })
   */
  private $item;

  /**
   * @var string $asinCode
   *
   * @ORM\Column(name="asin_code", type="string", length=100, nullable=false)
   */
  private $asinCode;


  /**
   * @var Type
   *
   * @ORM\ManyToOne(targetEntity="Type")
   * @ORM\JoinColumns({
   * @ORM\JoinColumn(name="type_id", referencedColumnName="id")
   * })
   */
  private $type;


  /**
   * @var string $body
   *
   * @ORM\Column(name="body", type="string", nullable=false)
   * @Assert\NotBlank()
   * @Assert\Length(max="240")
   */
  private $body;

  /**
   * @var integer $page
   *
   * @ORM\Column(name="page", type="integer", nullable=true)
   * @Assert\Type(type="integer")
   */
  private $page;

  /**
   * @var Misprint
   *
   *
   * @ORM\OneToOne(targetEntity="Misprint",cascade={"persist","remove"})
   * @ORM\JoinColumns({
   * @ORM\JoinColumn(name="misprint_id", referencedColumnName="id",nullable=true)
   * })
   */
  private $misprint;


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
   * @var String
   */
  private $replyTo;

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
   * Set id
   *
   * @param $id
   *
   * @return Report
   */
  public function setId($id)
  {
    $this->id = $id;

    return $this;
  }

  /**
   * Set body
   *
   * @param string $body
   *
   * @return Report
   */
  public function setBody($body)
  {
    $this->body = $body;

    return $this;
  }

  /**
   * Get body
   *
   * @return string
   */
  public function getBody()
  {
    return $this->body;
  }

  /**
   * Set page
   *
   * @param integer $page
   *
   * @return Report
   */
  public function setPage($page)
  {
    $this->page = $page;

    return $this;
  }

  /**
   * Get page
   *
   * @return integer
   */
  public function getPage()
  {
    return $this->page;
  }

  /**
   * Set createdAt
   *
   * @param \DateTime $createdAt
   *
   * @return Report
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
   * @return Report
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
   * @param Acme\BnqaBundle\Entity\User $user
   *
   * @return Report
   */
  public function setUser(\Acme\BnqaBundle\Entity\User $user = null)
  {
    $this->user = $user;

    return $this;
  }

  /**
   * Get user
   *
   * @return Acme\BnqaBundle\Entity\User
   */
  public function getUser()
  {
    return $this->user;
  }

  /**
   * Set Item
   *
   * @param Acme\BnqaBundle\Entity\Item $item
   *
   * @return Report
   */
  public function setItem(\Acme\BnqaBundle\Entity\Item $item = null)
  {
    $this->item = $item;

    return $this;
  }

  /**
   * Get item
   *
   * @return Acme\BnqaBundle\Entity\Item
   */
  public function getItem()
  {
    return $this->item;
  }


  /**
   * Set asinCode
   *
   * @param string $rawItemId
   *
   * @return Report
   */
  public function setAsinCode($rawItemId)
  {
    $this->asinCode = $rawItemId;

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
   * Set type
   *
   * @param \Acme\BnqaBundle\Entity\Type $type
   *
   * @return Report
   */
  public function setType(\Acme\BnqaBundle\Entity\Type $type = null)
  {
    $this->type = $type;

    return $this;
  }

  /**
   * Get type
   *
   * @return \Acme\BnqaBundle\Entity\Type
   */
  public function getType()
  {
    return $this->type;
  }


  /**
   * Set misprint
   *
   * @param \Acme\BnqaBundle\Entity\Misprint $misprint
   *
   * @return Report
   */
  public function setMisprint(\Acme\BnqaBundle\Entity\Misprint $misprint = null)
  {
    $this->misprint = $misprint;

    return $this;
  }

  /**
   * Get misprint
   *
   * @return \Acme\BnqaBundle\Entity\Misprint
   */
  public function getMisprint()
  {
    return $this->misprint;
  }

  /**
   * @return String
   */
  public function getReplyTo()
  {
    return $this->replyTo;
  }

  /**
   * @param $replyTo
   *
   * @return Report
   */
  public function setReplyTo($replyTo)
  {
    $this->replyTo = $replyTo;

    return $this;
  }
}