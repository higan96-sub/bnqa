<?php

namespace Acme\BnqaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Acme\BnqaBundle\Entity\Report;
use Acme\BnqaBundle\Entity\User;

/**
 * Acme\BnqaBundle\Entity\Reply
 *
 * @ORM\Table(name="reply_mapping")
 * @ORM\Entity(repositoryClass="Acme\BnqaBundle\Entity\ReplyMappingRepository")
 */
class ReplyMapping
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
   * @var Report
   *
   * @ORM\ManyToOne(targetEntity="Report")
   * @ORM\JoinColumns({
   * @ORM\JoinColumn(name="reply_id", referencedColumnName="id", onDelete="CASCADE")
   * })
   */
  private $reply;

  /**
   * @var Report
   *
   * @ORM\ManyToOne(targetEntity="Report")
   * @ORM\JoinColumns({
   * @ORM\JoinColumn(name="reply_to_id", referencedColumnName="id", onDelete="CASCADE")
   * })
   */
  private $replyTo;

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
     * @return ReplyMapping
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
     * @return ReplyMapping
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
     * Set reply
     *
     * @param \Acme\BnqaBundle\Entity\Report $reply
     * @return ReplyMapping
     */
    public function setReply(\Acme\BnqaBundle\Entity\Report $reply = null)
    {
        $this->reply = $reply;
    
        return $this;
    }

    /**
     * Get reply
     *
     * @return \Acme\BnqaBundle\Entity\Report 
     */
    public function getReply()
    {
        return $this->reply;
    }

    /**
     * Set replyTo
     *
     * @param \Acme\BnqaBundle\Entity\Report $replyTo
     * @return ReplyMapping
     */
    public function setReplyTo(\Acme\BnqaBundle\Entity\Report $replyTo = null)
    {
        $this->replyTo = $replyTo;
    
        return $this;
    }

    /**
     * Get replyTo
     *
     * @return \Acme\BnqaBundle\Entity\Report 
     */
    public function getReplyTo()
    {
        return $this->replyTo;
    }

    /**
     * Set user
     *
     * @param \Acme\BnqaBundle\Entity\User $user
     * @return ReplyMapping
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