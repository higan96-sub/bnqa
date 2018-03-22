<?php
namespace Acme\BnqaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * ReportBookmark
 *
 * @author higan96.<higan.n@gmail.com>
 *
 * @ORM\Table(name="report_bookmark")
 * @ORM\Entity(repositoryClass="Acme\BnqaBundle\Entity\ReportBookmarkRepository")
 */
class ReportBookmark
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
     * @var integer
     *
     * @ORM\Column(name="report_id", type="integer", nullable=false)
     */
  private $reportId;

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
   *
   * @return ReportBookmark
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
   * @return ReportBookmark
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
   * Set report
   *
   * @param integer $report_id
   *
   * @return ReportBookmark
   */
  public function setReportId($report_id)
  {
    $this->reportId = $report_id;

    return $this;
  }

  /**
   * Get report
   *
   * @return integer
   */
  public function getReportId()
  {
    return $this->reportId;
  }


  /**
   * Set user
   *
   * @param \Acme\BnqaBundle\Entity\User $user
   *
   * @return ReportBookmark
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