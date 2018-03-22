<?php
namespace Acme\BnqaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Acme\BnqaBundle\Item\AmazonItem;
use Acme\BnqaBundle\Item\ItemInterface;

/**
 * Acme\BnqaBundle\Entity\Item
 *
 * @ORM\Table(name="item")
 * @ORM\Entity(repositoryClass="Acme\BnqaBundle\Entity\ItemRepository")
 */
class Item implements ItemInterface
{
    /**
     * Constructor,
     *
     * @param \Acme\BnqaBundle\Item\AmazonItem $amazonItem
     */
    public function __construct(AmazonItem $amazonItem = null)
    {
        if ($amazonItem !== null) {
            $this->setTitle($amazonItem->getTitle());
            $this->setAsinCode($amazonItem->getAsinCode());
            $this->setPage($amazonItem->getPage());
            $this->setAdult($amazonItem->isAdult());
            $this->setIsbn10($amazonItem->getIsbn10());
            $this->setIsbn13($amazonItem->getIsbn13());
            $this->setProductGroup($amazonItem->getProductGroup());
            if (is_null($amazonItem->getReleasedDate())) {
                $this->setReleasedDate(new \DateTime());
            } else {
                $this->setReleasedDate($amazonItem->getReleasedDate());
            }
            if ($amazonItem->isImgUrlNull()) {
                $this->setImgUrl(null);
            } else {
                $this->setImgUrl($amazonItem->getSizedImgUrl(80, 'AA', false));
            }
        }
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
     * @var string $asinCode
     *
     * @ORM\Column(name="asin_code", type="string", unique=false, length=100, nullable=false)
     */
    private $asinCode;


    /**
     * @var string $isbn10
     *
     * @ORM\Column(name="isbn10", type="string", unique=false, length=100, nullable=true)
     */
    private $isbn10;


    /**
     * @var string $isbn13
     *
     * @ORM\Column(name="isbn13", type="string", unique=false, length=100, nullable=true)
     */
    private $isbn13;


    /**
     * @var string $productGroup
     *
     * @ORM\Column(name="product_group", type="string", unique=false, length=100, nullable=true)
     */
    private $productGroup;


    /**
     * @var string $imgUrl
     *
     * @ORM\Column(name="img_url", type="string", unique=false , length=100, nullable=true)
     */
    private $imgUrl;

    /**
     * @var string $title
     *
     * @ORM\Column(name="title", type="string", length=100, nullable=false)
     */
    private $title;

    /**
     * @var integer $page
     *
     * @ORM\Column(name="page", type="integer", nullable=true)
     */
    private $page;

    /**
     * @var boolean $adult
     *
     * @ORM\Column(name="adult", type="boolean", nullable=false)
     */
    private $adult;


    /**
     * @var \DateTime $releasedDate
     *
     * @ORM\Column(name="released_date", type="datetime", nullable=false)
     */
    private $releasedDate;

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
     * Set asinCode
     *
     * @param string $asinCode
     *
     * @return Item
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
     * @return string
     */
    public function getIsbn10()
    {
        return $this->isbn10;
    }

    /**
     * @return string
     */
    public function getIsbn13()
    {
        return $this->isbn13;
    }

    /**
     * @return string
     */
    public function getProductGroup()
    {
        return $this->productGroup;
    }

    /**
     * @param $isbn10
     * @return $this
     */
    public function setIsbn10($isbn10)
    {
        $this->isbn10 = $isbn10;
        return $this;
    }

    /**
     * @param $isbn13
     * @return $this
     */
    public function setIsbn13($isbn13)
    {
        $this->isbn13 = $isbn13;
        return $this;
    }

    /**
     * @param $productGroup
     * @return $this
     */
    public function setProductGroup($productGroup)
    {
        $this->productGroup = $productGroup;
        return $this;
    }

    /**
     * Set imgUrl
     *
     * @param string $imgUrl
     *
     * @return Item
     */
    public function setImgUrl($imgUrl)
    {
        $this->imgUrl = $imgUrl;

        return $this;
    }

    /**
     * Get imgUrl
     *
     * @return string
     */
    public function getImgUrl()
    {
        return $this->imgUrl;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Item
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $associateId
     *
     * @return string
     */
    public function getUrl($associateId = null)
    {
        if (is_null($associateId)) {
            $associateId = 'bnqa0c-22';
        }
        $baseUrl = 'http://www.amazon.co.jp/dp/';

        $url = $baseUrl . $this->asinCode . '/' . $associateId;


        return $url;
    }

    /**
     * @return bool
     */
    public function isAdultItem()
    {
        return (boolean)$this->adult;
    }


    /**
     * Get sized imgUrl
     *
     * @return string
     */

    public function getSizedImgUrl($imgSize = 90, $sizeCode = 'SL', $blindAdult = true)
    {
        if ($this->imgUrl === null) {
            if ($imgSize <= 90) {
                return 'bundles/acmebnqa/images/no-image-80.jpg';
            }

            return 'bundles/acmebnqa/images/no-image.gif';
        }

        $imgUrl = str_replace('._AA90_.', '._' . $sizeCode . $imgSize . '_.', $this->imgUrl);

        return $imgUrl;
    }

    public function getFormattedTitle()
    {
        preg_match('/^.*\b./', $this->title, $matches);

        return $matches[0];
    }

    private function getRawItemImgUrl()
    {
        if ($this->imgUrl == null) {
            return 'bundles/acmebnqa/images/no-image-80.jpg';
        }

        return $this->imgUrl;
    }


    /**
     * This item is book.
     *
     * @return boolean
     */
    public function isBook()
    {
        if ($this->page == 0) {
            return false;
        }

        return true;
    }

    /**
     * Set adult
     *
     * @param boolean $adult
     *
     * @return Item
     */
    public function setAdult($adult)
    {
        $this->adult = $adult;

        return $this;
    }

    /**
     * Get adult
     *
     * @return boolean
     */
    public function isAdult()
    {
        return (boolean)$this->adult;
    }

    /**
     * Set page
     *
     * @param integer $page
     *
     * @return Item
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
     * Get adult
     *
     * @return boolean
     */
    public function getAdult()
    {
        return $this->adult;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Item
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
     * @return Item
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
     * Set releasedDate
     *
     * @param \DateTime $releasedDate
     *
     * @return Item
     */
    public function setReleasedDate($releasedDate)
    {
        $this->releasedDate = $releasedDate;

        return $this;
    }

    /**
     * Get releasedDate
     *
     * @return \DateTime
     */
    public function getReleasedDate()
    {
        return $this->releasedDate;
    }


    public function getDateInterval()
    {
        if (null !== $this->getReleasedDate()) {
            $now = new \DateTime();

            $diff = $now->diff($this->getReleasedDate());
            if ($diff->days === 0) {
                $dateInterval = '本日発売';
            } elseif (!$diff->invert) {
                $dateInterval = $diff->days . '日後';
            } else {
                $dateInterval = $diff->days . '日前';
            }

        } else {
            $dateInterval = null;
        }

        return (string)$dateInterval;
    }
}