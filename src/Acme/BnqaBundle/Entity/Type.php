<?php
namespace Acme\BnqaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Acme\BnqaBundle\Entity\Type
 *
 * @ORM\Table(name="type")
 * @ORM\Entity
 */
class Type
{
  public function __toString()
  {
    return (string)$this->label;
  }

  /**
   * @var integer $id
   *
   * @ORM\Column(name="id", type="integer", nullable=false)
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="IDENTITY")
   */
  private $id;

  /**
   * @var string $name
   *
   * @ORM\Column(name="name", type="string", unique=true , length=100, nullable=false)
   */
  private $name;

  /**
   * @var string $label
   *
   * @ORM\Column(name="label", type="string", unique=true , length=100, nullable=false)
   */
  private $label;


  /**
   * @var boolean $isBook
   *
   * @ORM\Column(name="is_book", type="boolean", nullable=false)
   */
  private $isBook;


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
   * Set name
   *
   * @param string $name
   *
   * @return Type
   */
  public function setName($name)
  {
    $this->name = $name;

    return $this;
  }

  /**
   * Get name
   *
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }

  /**
   * Set label
   *
   * @param string $label
   *
   * @return Type
   */
  public function setLabel($label)
  {
    $this->label = $label;

    return $this;
  }

  /**
   * Get label
   *
   * @return string
   */
  public function getLabel()
  {
    return $this->label;
  }


    /**
     * Set isBook
     *
     * @param boolean $isBook
     * @return Type
     */
    public function setIsBook($isBook)
    {
        $this->isBook = $isBook;
    
        return $this;
    }

    /**
     * Get isBook
     *
     * @return boolean 
     */
    public function isBook()
    {
        return $this->isBook;
    }
}