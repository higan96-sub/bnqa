<?php
namespace Acme\BnqaBundle\Item;

use Acme\BnqaBundle\Item\ItemManager;
use Acme\BnqaBundle\Item\ItemInterface;
use Acme\BnqaBundle\Item\AmazonItem;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Acme\BnqaBundle\Item\AmazonSearchResults;
use Acme\BnqaBundle\Item\AmazonBrowseNode;
use Acme\BnqaBundle\Item\AmazonException;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * AmazonItemManager.php .
 *
 * @author higan96.<higan.n@gmail.com>
 *
 */
class AmazonItemManager extends ItemManager
{
  private $accessKeyId;
  private $secretAccessKey;
  private $affiliateId;
  private $container;
  private $params = array();
  private $accessUrl = 'http://ecs.amazonaws.jp/onca/xml';

  /**
   * @param string $accessKeyId
   * @param string $secretAccessKey
   * @param string $affiliateId
   */
  public function __construct(ContainerInterface $container)
  {
    $this->container = $container;
    $this->accessKeyId = $this->container->getParameter('amazon_access_key');
    $this->secretAccessKey = $this->container->getParameter('amazon_secret_key');
    $this->affiliateId = $this->container->getParameter('amazon_affiliate_id');

    $this->params['Service'] = 'AWSECommerceService';
    $this->params['Version'] = '2011-08-01';

    $this->params['AssociateTag'] = $this->affiliateId;


    $this->params['SignatureMethod'] = 'HmacSHA256'; // signature format name.
    $this->params['SignatureVersion'] = 2; // signature version.

    // time zone (ISO8601 format)
    $this->params['Timestamp'] = gmdate('Y-m-d\TH:i:s\Z');
  }

  /**
   * @param $asinCode
   *
   * @return AmazonItem|AmazonSearchResults
   */
  public function findAmazonItem($asinCode)
  {

    if (is_array($asinCode))
    {
      $this->params['Operation'] = 'ItemLookup';
      $this->params['ItemId'] = implode(',', $asinCode);
      $this->params['ResponseGroup'] = 'ItemAttributes,Images,OfferSummary';
      ksort($this->params);
      $response = $this->search();
      $parsedXml = $this->aksGetParsedXml($response);
      $amazonSearchResults = $this->parsedXmlToAmazonItem($parsedXml);

      return $amazonSearchResults;
    }
    $amazonItem = $this->container->get('memcache.default')->get('amazon_item_' . $asinCode);
    if (!$amazonItem)
    {
      $this->params['Operation'] = 'ItemLookup';
      $this->params['ItemId'] = $asinCode;
      $this->params['ResponseGroup'] = 'ItemAttributes,Images,OfferSummary';
      ksort($this->params);
      $response = $this->search();
      $parsedXml = $this->aksGetParsedXml($response);

      $amazonItem = new AmazonItem($parsedXml->Items->Item);
      $this->container->get('memcache.default')->set('amazon_item_' . $asinCode, $amazonItem, 60 * 60 * 24 - 1);

      return $amazonItem;
    }

    return $amazonItem;
  }

  /**
   * @param $authorName
   *
   * @return AmazonSearchResults
   */
  public function searchAuthorItems($authorName, $page)
  {
    $key = 'author_' . str_replace(array(' ','　'), '', $authorName) . '_' . $page;

    $amazonSearchResults = $this->container->get('memcache.default')->get($key);
    if (!$amazonSearchResults)
    {
      $this->params['Operation'] = 'ItemSearch';
      $this->params['Author'] = $authorName;
      $this->params['SearchIndex'] = 'Books';
//      $this->params['Power'] = 'binding:not kindle';
      $this->params['ResponseGroup'] = 'ItemAttributes,Images,OfferSummary';
      $this->params['ItemPage'] = $page;

      ksort($this->params);
      $response = $this->search();
      $parsedXml = $this->aksGetParsedXml($response);
      $amazonSearchResults = $this->parsedXmlToAmazonItem($parsedXml);

      $this->container->get('memcache.default')->set($key, $amazonSearchResults, 60 * 60 * 24 - 1);
    }

    return $amazonSearchResults;

  }

  /**
   * @param $asinCode
   *
   * @return AmazonSearchResults
   */
  public function searchSimilarities($asinCode)
  {
    $amazonSearchResults = $this->container->get('memcache.default')->get('similarities_' . $asinCode);
    if (!$amazonSearchResults)
    {
      $this->params['Operation'] = 'SimilarityLookup';
      if (is_array($asinCode))
      {
        $this->params['ItemId'] = implode(',', $asinCode);
      } else
      {
        $this->params['ItemId'] = $asinCode;
      }
      $this->params['SimilarityType'] = 'Random';
      $this->params['ResponseGroup'] = 'Medium,OfferSummary';

      ksort($this->params);
      $response = $this->search();

      $parsedXml = $this->aksGetParsedXml($response);
      $amazonSearchResults = $this->parsedXmlToAmazonItem($parsedXml);

      $this->container->get('memcache.default')->set('similarities_' . $asinCode, $amazonSearchResults, 60 * 60 * 24 - 1);
    }

    return $amazonSearchResults;
  }

    /**
     * @param $parsedXml
     * @return AmazonSearchResults
     */
    private function parsedXmlToAmazonItem($parsedXml)
  {
    $totalPages = (int)$parsedXml->Items->TotalPages;
    $totalResults = (int)$parsedXml->Items->TotalResults;
    $amazonItems = array();
    foreach ($parsedXml->Items->Item as $current)
    {
        $amazonItem = $this->container->get('memcache.default')->get('amazon_item_' . $current->ASIN);
        if($amazonItem){
            $amazonItems[] = $amazonItem;
        }else{
            $amazonItem = new AmazonItem($current);
            $this->container->get('memcache.default')->set('amazon_item_' . $amazonItem->getAsinCode(), $amazonItem, 60 * 60 * 24 - 1);
            $amazonItems[] = $amazonItem;
        }
    }

    return new AmazonSearchResults($totalPages, $totalResults, $amazonItems);
  }

  /**
   * @param $keyword
   * @param int $page
   * @param null $category
   *
   * @return AmazonSearchResults
   */
  public function searchItems($keyword, $page = 1, $category = null)
  {
    $key = 'search_item_' . str_replace(array(' ','　'), '', $keyword). '_' . $page;

    $amazonSearchResults = $this->container->get('memcache.default')->get($key);
    if (!$amazonSearchResults)
    {
      $this->params['Operation'] = 'ItemSearch';
      $this->params['Keywords'] = $keyword;
      if ('Books' === $category)
      {
          $this->params['SearchIndex'] = $category;
          $this->params['Power'] = 'binding:not kindle';
      } else{
          $this->params['SearchIndex'] = 'All';

      }

      $this->params['ResponseGroup'] = 'ItemAttributes,Images,OfferSummary';
      $this->params['ItemPage'] = $page;

      ksort($this->params);

      $response = $this->search();

      $parsedXml = $this->aksGetParsedXml($response);

      $amazonSearchResults = $this->parsedXmlToAmazonItem($parsedXml);

      $this->container->get('memcache.default')->set($key, $amazonSearchResults, 60 * 60 * 24 * 1);
    }

    return $amazonSearchResults;
  }

  /**
   * @return string
   * @throws \Exception
   */
  private function search()
  {
    $base_param = 'AWSAccessKeyId=' . $this->accessKeyId;

    // create canonical string.
    $canonical_string = $base_param;
    foreach ($this->params as $k => $v)
    {
      $canonical_string .= '&' . $this->urlEncodeRFC3986($k) . '=' . $this->urlEncodeRFC3986($v);
    }

    // create signature strings.( HMAC-SHA256 & BASE64 )
    $parsed_url = parse_url($this->accessUrl);
    $string_to_sign = "GET\n{$parsed_url['host']}\n{$parsed_url['path']}\n{$canonical_string}";

    $signature = base64_encode(hash_hmac('sha256', $string_to_sign, $this->secretAccessKey, true));

    // create URL strings.
    $url = $this->accessUrl . '?' . $canonical_string . '&Signature=' . $this->urlEncodeRFC3986($signature);

    $context = stream_context_create(array('http' => array('ignore_errors' => true)));

    $response = file_get_contents($url, false, $context);

    list($version, $status_code, $msg) = explode(' ', $http_response_header[0], 3);

    if ($status_code >= 400)
    {
      if ((int)$status_code === 503)
      {
        throw new \Exception('APIリクエスト数の上限に達しました。時間をあけて再度アクセスしてください。');
      }
      throw new \Exception($status_code . ' ' . $msg);
    }

    return $response;
  }

  /**
   * @param $response
   *
   * @return null|object
   * @throws AmazonException
   */
  private function aksGetParsedXml($response)
  {
    // response to strings.
    $parsed_xml = null;
    if (isset($response))
    {
      $parsed_xml = simplexml_load_string($response);
    }

    if (count($parsed_xml->Items->Request->Errors))
    {
      throw new AmazonException($parsed_xml->Items->Request->Errors->Error->Message, 400);
    }

    return $parsed_xml;
  }

  private function urlEncodeRFC3986($string)
  {
    return str_replace('%7E', '~', rawurlencode($string));
  }


  public function setAffiliateId($affiliateId)
  {
    $this->affiliateId = $affiliateId;
  }

  /**
   * @return AmazonSearchResults
   */
  public function getTopSellers()
  {
    $this->params['Operation'] = 'BrowseNodeLookup';
    $this->params['BrowseNodeId'] = '465610';
    $this->params['ResponseGroup'] = 'TopSellers';

    ksort($this->params);

    $response = $this->search();
    $parsedXml = simplexml_load_string($response);

    return new AmazonBrowseNode($parsedXml);
  }

    /**
     * @return AmazonSearchResults
     */
    public function searchTopSellers($browseNode = null)
  {
    $this->params['Operation'] = 'ItemSearch';
      if($browseNode !== null){
          $this->params['BrowseNode'] = (string)$browseNode;
      }else{
          $this->params['BrowseNode'] = '465392';
      }
    $this->params['SearchIndex'] = 'Books';
    $this->params['Sort'] = 'salesrank';
    $this->params['ResponseGroup'] = 'Medium';

    ksort($this->params);

    $response = $this->search();

    $parsedXml = $this->aksGetParsedXml($response);

    $amazonSearchResults = $this->parsedXmlToAmazonItem($parsedXml);

    return $amazonSearchResults;
  }

  public function searchNewItems()
  {
    $this->params['Operation'] = 'ItemSearch';
    $this->params['BrowseNode'] = '465610';
    $this->params['SearchIndex'] = 'Books';
    $this->params['Sort'] = 'daterank';
    $this->params['Keywords'] = 'ドラゴンクエスト';

    $this->params['ResponseGroup'] = 'Medium';

    ksort($this->params);

    $response = $this->search();

    $parsedXml = $this->aksGetParsedXml($response);

    $amazonSearchResults = $this->parsedXmlToAmazonItem($parsedXml);

    return $amazonSearchResults;
  }
}
