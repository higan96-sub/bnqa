<?php
namespace Acme\BnqaBundle\Controller;

use Acme\BnqaBundle\Controller\AppController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Acme\BnqaBundle\Entity\Report;
use Acme\BnqaBundle\Entity\Item;
use Acme\BnqaBundle\Item\AmazonItemManager;
use Acme\BnqaBundle\Form\ReportType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * ItemController.php .
 *
 * @author higan96.<higan.n@gmail.com>
 * @Route("/item",name="item")
 */
class ItemController extends AppController
{


    /**
     * @Route("/similarities",name="item_similarities")
     */
    public function similaritiesAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $amazonItemManager = $this->get('amazon_manager');
            $asinCode = $request->query->get('asin_code');
            $followersCounts = null;
            $reportsCounts = null;
            $areTheseItemsFollowing = null;

            try {
                $result = $amazonItemManager->searchSimilarities($asinCode);
            } catch (\Exception $e) {
                return new JsonResponse('商品情報の取得に失敗しました', 400);
            }

            $list = array();
            foreach ($result->getAmazonItems() as $item) {
                $list[] = $item->getAsinCode();
            }
            if (count($list)) {
                $followersCounts = $this->getDoctrine()->getRepository('AcmeBnqaBundle:ItemFollowing')->follwersCounts($list);
                $reportsCounts = $this->getDoctrine()->getRepository('AcmeBnqaBundle:Report')->reportsCounts($list);
            }
            if ($this->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
                $areTheseItemsFollowing = $this->getDoctrine()->getRepository('AcmeBnqaBundle:ItemFollowing')->AreTheseItemsFollowing($this->getUser()->getId(), $list);
            }


            $response = new JsonResponse($this->renderView("AcmeBnqaBundle:Sidebar:_similarities.html.twig", array(
                'amazonItems' => $result->getAmazonItems(),
                'areTheseItemsFollowing' => $areTheseItemsFollowing,
                'affiliateId' => $this->container->getParameter('amazon_affiliate_id'),
                'followersCounts' => $followersCounts,
                'reportsCounts' => $reportsCounts,
            )));
            if ($this->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
                $response->setPrivate();
                $response->setMaxAge(10);
            } else {
                $response->setPublic();
                $response->setSharedMaxAge(20);
            }

            return $response;
        }

        throw $this->createNotFoundException();
    }

    /**
     * img_url がNullのアイテム情報を更新する。
     * 10件ずつ、発売時期順に更新する。古いアイテムも検索結果に入るが、これについては今後どうするか考える。
     * 発売時期順なので、たぶんうまく回る。
     *
     */
    public function refreshAction($asinCode = null)
    {
        $em = $this->getDoctrine()->getManager();
        if ($asinCode === null) {
            $query = $em->createQuery(
                'SELECT i
                FROM AcmeBnqaBundle:Item i
                WHERE (i.imgUrl IS NULL OR i.page = 999) AND i.releasedDate > :date
                ORDER BY i.releasedDate ASC'
            )->setParameter('date', new \DateTime('-7 day'))->setMaxResults(10);
            $items = $query->getResult();
            if ($items) {
                $amazonItemManager = $this->get('amazon_manager');
                $list = array();
                foreach ($items as $item) {
                    $list[] = $item->getAsinCode();
                }
                $amazonItems = $amazonItemManager->findAmazonItem($list)->getAmazonItems();
                $i = 0;
                foreach ($items as $item) {
                    $item->setImgUrl($amazonItems[$i]->getImgUrl(true));
                    $item->setPage($amazonItems[$i]->getPage());
                    $i += 1;
                }
                $em->flush();

                return array('items' => $items);
            }
            return new Response('no img null items', 204);
        }
        $item = $em->getRepository('AcmeBnqaBundle:Item')->findOneBy(array('asinCode' => $asinCode));
        if (!$item) {
            throw $this->createNotFoundException($asinCode . 'は見つかりませんでした');
        }
        $amazonItemManager = $this->get('amazon_manager');
        $amazonItem = $amazonItemManager->findAmazonItem($asinCode);
        $item->setPage($amazonItem->getPage());
        $item->setImgUrl($amazonItem->getImgUrl());
        $em->flush();

        return array('items' => $item);
    }


    /**
     * @Route("/{asinCode}/page/{bookPage}",name="item_page")
     */
    public function pageAction(Request $request, $asinCode, $bookPage)
    {

        if ($this->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED') && !$request->getSession()->has('csrf_token')) {
            $token = $this->get('form.csrf_provider')->generateCsrfToken($this->container->getParameter('secret'));
            $request->getSession()->set('csrf_token', $token);
        }

        try {
            $item = $this->get('amazon_manager')->findAmazonItem((string)$asinCode);
        } catch (\Exception $e) {
            throw $this->createNotFoundException($e->getMessage());
        }
        if ($item->isBook()) {
            if ($bookPage > (int)$item->getPage() || $bookPage < 0) {
                throw $this->createNotFoundException('ページ数が正しくありません');
            }
        } else {
            if ($bookPage > 0) {
                throw $this->createNotFoundException('メディアのミスマッチ');
            }
        }

//      var_dump($item);

        $followerCount = $this->getDoctrine()->getRepository('AcmeBnqaBundle:ItemFollowing')->countItemFollower($asinCode);

        $params = array(
            'item' => $item,
            'bookPage' => $bookPage,
            'followerCount' => $followerCount[0]['followerCount'],);

        if ($this->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $isNowFollowing = $this->getDoctrine()->getRepository('AcmeBnqaBundle:ItemFollowing')->findOneBy(array(
                'asinCode' => $asinCode,
                'user' => $this->getUser()->getId()));
            if ($isNowFollowing) {
                $params['isNowFollowing'] = $isNowFollowing->getActiveFlag();
            } else {
                $params['isNowFollowing'] = false;
            }
        } else {
            $params['isNowFollowing'] = false;
        }
        $response = $this->render("AcmeBnqaBundle:Item:show.html.twig", $params);
        if ($this->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $response->setPrivate();
            $response->setMaxAge(10);

            return $response;
        }
        $response->setPublic();
        $response->setSharedMaxAge(20);

        return $response;
    }

    /**
     * @Route("/{asin_code}",name="item_show")
     */
    public function showAction(Request $request, $asin_code)
    {
        return $this->forward('AcmeBnqaBundle:Item:page', array(
            'asinCode' => $asin_code,
            'bookPage' => 0));
    }

    /**
     * @Route("/sidebar/topSellers",name="item_top_sellers")
     */
    public function topSellersAction()
    {
        $items = $this->container->get('memcache.default')->get('top_sellers_items');
        if (!$items) {
            $items = $this->get('amazon_manager')->searchTopSellers();
            $this->container->get('memcache.default')->set('top_sellers_items', $items, 60 * 60);
        }

        $list = array();
        foreach ($items->getAmazonItems() as $item) {
            $list[] = $item->getAsinCode();
        }
        if (count($list)) {
            $followersCounts = $this->getDoctrine()->getRepository('AcmeBnqaBundle:ItemFollowing')->follwersCounts($list);
            $reportsCounts = $this->getDoctrine()->getRepository('AcmeBnqaBundle:Report')->reportsCounts($list);
        }
        if ($this->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $areTheseItemsFollowing = $this->getDoctrine()->getRepository('AcmeBnqaBundle:ItemFollowing')->AreTheseItemsFollowing($this->getUser()->getId(), $list);
        } else {
            $areTheseItemsFollowing = null;
        }


        return new Response($this->renderView("AcmeBnqaBundle:Sidebar:_top_sellers.html.twig", array('items' => $items->getAmazonItems(), 'followersCounts' => $followersCounts, 'reportsCounts' => $reportsCounts, 'areTheseItemsFollowing' => $areTheseItemsFollowing)));

    }
}
