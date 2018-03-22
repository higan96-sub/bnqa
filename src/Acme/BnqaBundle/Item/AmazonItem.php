<?php
namespace Acme\BnqaBundle\Item;

use Symfony\Component\DependencyInjection\Container;
use Acme\BnqaBundle\Item\ItemInterface;

/**
 * AmazonItem.php .
 *
 * @author higan96.<higan.n@gmail.com>
 *
 */
class AmazonItem implements ItemInterface
{
    private $url;
    private $author;
    private $title;
    private $imgUrl;
    private $smallImgUrl;
    private $asinCode;
    private $page;
    private $productGroup;
    private $releasedDate;
    private $publisher;
    private $price;
    private $lowestPrice;
    private $dateInterval;
    private $format;
    private $isbn10;
    private $isbn13;

    /**
     * @param $parsedXml
     */
    public function __construct($parsedXml)
    {

        $this->asinCode = (string)$parsedXml->ASIN;
        $this->imgUrl = (string)$parsedXml->MediumImage->URL;
        $this->smallImgUrl = (string)$parsedXml->SmallImage->URL;
        $this->largeImgUrl = (string)$parsedXml->LargeImage->URL;

        $this->format = (string)$parsedXml->ItemAttributes->Format;

        $this->url = (string)$parsedXml->DetailPageURL;
        $this->title = (string)$parsedXml->ItemAttributes->Title;
        $this->page = (int)$parsedXml->ItemAttributes->NumberOfPages;
        $this->productGroup = (string)$parsedXml->ItemAttributes->ProductGroup;
        $this->releasedDate = (string)$parsedXml->ItemAttributes->ReleaseDate;
        $this->releaseDatePublic = (string)$parsedXml->ItemAttributes->ReleaseDate;
        $this->publisher = (string)$parsedXml->ItemAttributes->Publisher;
        $this->price = (int)$parsedXml->ItemAttributes->ListPrice->Amount;

        if ($this->productGroup === 'Book' || $this->productGroup === 'eBooks') {
            $this->isbn10 = (string)$parsedXml->ItemAttributes->ISBN;
            $this->isbn13 = $this->ISBNTran($this->isbn10);
        } else {
            $this->isbn10 = null;
            $this->isbn13 = null;
        }


        switch ($this->productGroup) {
            case "Music":
                $this->author[] = (string)$parsedXml->ItemAttributes->Artist;
                break;
            case "DVD":
                $this->author[] = (string)$parsedXml->ItemAttributes->Director;
                foreach ($parsedXml->ItemAttributes->Actor as $actor) {
                    $this->author[] = (string)$actor;
                }
                break;
            case "Book":
                foreach ($parsedXml->ItemAttributes->Author as $author) {
                    $this->author[] = (string)$author;
                }
                break;
            case "eBooks":
                foreach ($parsedXml->ItemAttributes->Author as $author) {
                    $this->author[] = (string)$author;
                }
                break;
            default:
                $this->author = null;
        }


        if ($this->getProductGroup() !== 'eBooks') {
            $this->lowestPrice = (int)$parsedXml->OfferSummary->LowestNewPrice->Amount;
        }
        if (0 === strlen($parsedXml->ItemAttributes->ReleaseDate)) {
            $this->releasedDate = (string)$parsedXml->ItemAttributes->PublicationDate;
        }


        if (null !== $this->getReleasedDate()) {
            $this->dateInterval = $this->prepareDate($this->getReleasedDate());
        } else {
            $this->dateInterval = null;
        }

    }

    private function ISBNTran($ISBN)
    {
        if (strlen($ISBN) == 10) {
            //ISBN10からISBN13への変換
            $ISBNtmp = "978" . $ISBN;
            $sum = 0;
            for ($i = 0; $i < 12; $i++) {
                $weight = ($i % 2 == 0 ? 1 : 3);
                $sum += (int)substr($ISBNtmp, $i, 1) * (int)$weight;
            }
            //チェックディジットの計算
            $checkDgt = (10 - $sum % 10) == 10 ? 0 : (10 - $sum % 10);
            return "978" . substr($ISBN, 0, 9) . $checkDgt;
        } elseif (strlen($ISBN) == 13) {
            //ISBN13からISBN10への変換
            $ISBNtmp = substr($ISBN, 3, 9);
            $weight = 10;
            $sum = 0;
            for ($i = 0; $i < 9; $i++) {
                $sum += (int)substr($ISBNtmp, $i, 1) * $weight;
                $weight--;
            }
            //チェックディジットの計算
            if ((11 - $sum % 11) == 11) {
                $checkDgt = 0;
            } elseif ((11 - $sum % 11) == 10) {
                $checkDgt = "X";
            } else {
                $checkDgt = (11 - $sum % 11);
            }
            return substr($ISBN, 3, 9) . $checkDgt;
        }
    }

    public function getId()
    {
        return null;
    }


    public function getUrl($associateId = null)
    {
        return $this->url;
    }

    /**
     * @return bool
     */
    public function isAdult()
    {
        if (preg_match('/アダルト/u', $this->format)) {
            return true;
        }

        return false;
    }

    /**
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * @return string
     */
    public function getImgUrl($raw = false)
    {
        if ($raw) {
            if ($this->smallImgUrl == null) {
                return null;
            }
            $imgUrl = (string)$this->smallImgUrl;
            return $imgUrl = str_replace('._SL75_.', '._' . 'AA' . '80' . '_.', $imgUrl);
        }
        if ($this->imgUrl == null) {
            if ($this->isAdult()) {
                return 'bundles/acmebnqa/images/adult-item.gif';
            }

            return 'bundles/acmebnqa/images/no-image.gif';
        } elseif ($this->isAdult()) {
            return 'bundles/acmebnqa/images/adult-item.gif';
        }


        return $this->imgUrl;
    }

    /**
     * @return bool
     */
    public function isImgUrlNull()
    {
        if ($this->imgUrl == null) {
            return true;
        }

        return false;
    }

    /**
     * @return string
     */
    public function getFormattedTitle()
    {
        //    if(preg_match('/^.*\b./',$this->title,$matches)){
        //      return $matches[0];
        //    }
        return $this->title;
    }


    /**
     * @return null
     */
    public function getAuthor()
    {
        if (count($this->author) === 0) {
            return null;
        }

        return $this->author;
    }


    /**
     * Amazon item image resize to imgSize
     *
     * @param int $imgSize
     * @param string $sizeCode
     * @param bool $blindAdult
     *
     * @return mixed|string
     */
    public function getSizedImgUrl($imgSize = 90, $sizeCode = 'SL', $blindAdult = true)
    {
        if ($this->isAdult() && $blindAdult) {
            if ($imgSize <= 90) {
                return 'bundles/acmebnqa/images/adult-item-small.gif';
            }

            return 'bundles/acmebnqa/images/adult-img.gif';

        } elseif ($this->smallImgUrl == null) {

            if ($imgSize <= 90) {
                return 'bundles/acmebnqa/images/no-image-80.jpg';
            }
            return 'bundles/acmebnqa/images/no-image.gif';
        }

        $imgUrl = (string)$this->smallImgUrl;
        $imgUrl = str_replace('._SL75_.', '._' . $sizeCode . $imgSize . '_.', $imgUrl);

        return $imgUrl;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        if ($this->getProductGroup() === 'eBooks') {
            return $this->title . '（kindle版）';
        }

        return $this->title;
    }


    /**
     * @return int
     */
    public function getPage()
    {
        if ($this->isBook() && (int)$this->page === 0) {
            return 999;
        }

        return $this->page;
    }

    /**
     * @return string
     */
    public function getProductGroup()
    {
        return $this->productGroup;
    }

    /**
     * @return \DateTime|null
     */
    public function getReleasedDate()
    {
        if (0 === strlen((string)$this->releasedDate)) {
            return null;
        }

        return new \DateTime($this->releasedDate);
    }

    /**
     * @return string
     */
    public function getAsinCode()
    {
        return $this->asinCode;
    }

    public function getIsbn10()
    {
        return $this->isbn10;
    }

    public function getIsbn13()
    {
        return $this->isbn13;
    }

    /**
     * @return string
     */
    public function getPublisher()
    {
        return $this->publisher;
    }

    /**
     * @return int
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @return int
     */
    public function getLowestPrice()
    {
        return $this->lowestPrice;
    }

    /**
     * @return string
     */
    public function getDateInterval()
    {
        return $this->dateInterval;
    }

    /**
     * @return bool
     */
    public function isBook()
    {
        if ($this->getProductGroup() === 'eBooks' || $this->getProductGroup() === 'Book') {
            return true;
        }

        return false;
    }


    /**
     * @return string
     */
    private function generateUrl()
    {
        $url = "http://www.amazon.co.jp/dp/";

        return $url . $this->asinCode;
    }

    /**
     * @param \DateTime $targetDate
     *
     * @return string
     */
    private function prepareDate(\DateTime $targetDate)
    {
        $now = new \DateTime();

        $diff = $now->diff($targetDate);
        if ($diff->days === 0) {
            return '本日発売';
        } elseif (!$diff->invert) {
            return $diff->days . '日後';
        } else {
            return $diff->days . '日前';
        }
    }
}
