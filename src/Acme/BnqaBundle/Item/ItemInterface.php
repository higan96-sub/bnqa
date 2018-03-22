<?php
namespace Acme\BnqaBundle\Item;

/**
 * ItemInterface.php .
 *
 * @author higan96.<higan.n@gmail.com>
 *
 */
interface ItemInterface
{

  public function getId();

  public function getAsinCode();

  public function getImgUrl();

  public function getTitle();

  public function getUrl($associateId = null);

  public function getSizedImgUrl($imgSize = 90, $sizeCode = 'SL', $blindAdult = true);

  public function getFormattedTitle();

  public function isBook();

  public function isAdult();

  public function getPage();
//    public function getFormat();
//
//    public function getIsbn10();
//    public function getIsbn13();

  public function getReleasedDate();

  public function getDateInterval();
}
