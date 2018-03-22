<?php
namespace Acme\BnqaBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="Acme\BnqaBundle\Entity\UserRepository")
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    public function __construct()
    {
        parent::__construct();
        // your own logic
    }


    /**
     * @var string $associateId
     *
     * @ORM\Column(name="associate_id", type="string", length=100, nullable=true)
     */
    protected $associateId;

    /**
     * @var string $profile
     * @ORM\Column(name="profile", type="string", length=100, nullable=true)
     * @Assert\Length(max="100")
     */
    protected $profile;

    /**
     * @var string $profileImgUrl
     * @ORM\Column(name="profile_img_url", type="string", length=100, nullable=true)
     * @Assert\Length(max="100")
     */
    protected $profileImgUrl;


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
     * Set associateId
     *
     * @param string $associateId
     *
     * @return User
     */
    public function setAssociateId($associateId)
    {
        $this->associateId = $associateId;

        return $this;
    }

    /**
     * Get associateId
     *
     * @return string
     */
    public function getAssociateId()
    {
        if ($this->associateId === null) {
            return 'higan96-22';
        }

        return $this->associateId;
    }

    /**
     * Set profile
     *
     * @param string $profile
     *
     * @return User
     */
    public function setProfile($profile)
    {
        $this->profile = $profile;

        return $this;
    }

    /**
     * Get profile
     *
     * @return string
     */
    public function getProfile()
    {
        return $this->profile;
    }

    /**
     * Set profileImgUrl
     *
     * @param string $profileImgUrl
     *
     * @return User
     */
    public function setProfileImgUrl($profileImgUrl)
    {
        $this->profileImgUrl = $profileImgUrl;

        return $this;
    }

    /**
     * Get profileImgUrl
     *
     * @return string
     */
    public function getProfileImgUrl($isFullPath = false)
    {
        if (null === $this->profileImgUrl) {
            if($isFullPath){
                return 'http://bnqa.jp/bundles/acmebnqa/images/profile-img-default.png';
            }
            return 'bundles/acmebnqa/images/profile-img-default.png';
        }

        return $this->profileImgUrl;
    }
}