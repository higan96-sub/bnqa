<?php

namespace Acme\BnqaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Acme\BnqaBundle\Entity\Misprint
 *
 * @ORM\Table(name="misprint")
 * @ORM\Entity(repositoryClass="Acme\BnqaBundle\Entity\MisprintRepository")
 *
 *
 */
class Misprint
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
   * @var string $wrongBody
   *
   * @ORM\Column(name="wrong_body", type="string", nullable=true)
   * @Assert\NotBlank()
   * @Assert\Length(max="240")
   */
  private $wrongBody;


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
     * @return Misprint
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
     * @return Misprint
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
     * Set wrongBody
     *
     * @param string $wrongBody
     * @return Misprint
     */
    public function setWrongBody($wrongBody)
    {
        $this->wrongBody = $wrongBody;
    
        return $this;
    }

    /**
     * Get wrongBody
     *
     * @return string 
     */
    public function getWrongBody()
    {
        return $this->wrongBody;
    }
}