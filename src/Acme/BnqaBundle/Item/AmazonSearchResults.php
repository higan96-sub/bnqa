<?php
namespace Acme\BnqaBundle\Item;

/**
 * AmazonSearchResults.php .
 *
 * @author higan96.<higan.n@gmail.com>
 *
 */
use Acme\BnqaBundle\Item\AmazonItem;


class AmazonSearchResults
{
  private $totalResults;
  private $totalPages;
  private $amazonItems;

  public function __construct($totalPages, $totalResults, $amazonItems)
  {
    $this->totalPages = $totalPages;
    $this->totalResults = $totalResults;
    $this->amazonItems = $amazonItems;
  }

  public function getTotalResults()
  {
    if ($this->totalResults > 51)
    {
      return 50;
    }

    return $this->totalResults;
  }

  public function getTotalPages()
  {
    return $this->totalPages;
  }

  public function getAmazonItems()
  {
    return $this->amazonItems;
  }


}
