<?php
namespace Acme\BnqaBundle\Controller;

use Acme\BnqaBundle\Controller\AppController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Acme\BnqaBundle\Item\AmazonItemManager;
use Symfony\Component\HttpFoundation\Response;

/**
 * SearchController.php .
 *
 * @author higan96.<higan.n@gmail.com>
 * @Route("/search",name="search")
 */
class SearchController extends AppController
{
    /**
     * @Route("/item",name="search_item")
     * @Template
     */
    public function itemAction(Request $request)
    {
        if ($this->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED') && !$request->getSession()->has('csrf_token')) {
            $token = $this->get('form.csrf_provider')->generateCsrfToken($this->container->getParameter('secret'));
            $request->getSession()->set('csrf_token', $token);
        }

        $keyword = $request->query->get('keyword');
        $amazonItemManager = $this->get('amazon_manager');
        $areTheseItemsFollowing = null;


        if ($request->isXmlHttpRequest()) {
            $result = $amazonItemManager->searchItems($keyword, $request->query->get('category'));

            $response = new Response(json_encode($this->renderView("AcmeBnqaBundle:Search:ajax.html.twig", array('result' => $result))), 200);

            $response->headers->set('Content-Type', 'application/json');

            return $response;
        }


        $page = $request->query->get('page', 1);


        try {
            $result = $amazonItemManager->searchItems($keyword, $page);
        } catch (\Acme\BnqaBundle\Item\AmazonException $e) {
            return new Response($this->renderView("AcmeBnqaBundle:Search:error.html.twig", array(), 400));
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
            {
                $list = array();
            }
            foreach ($result->getAmazonItems() as $item) {
                $list[] = $item->getAsinCode();
            }
            $areTheseItemsFollowing = $this->getDoctrine()->getRepository('AcmeBnqaBundle:ItemFollowing')->AreTheseItemsFollowing($this->getUser()->getId(), $list);
        }


        return array(
            'result' => $result,
            'areTheseItemsFollowing' => $areTheseItemsFollowing,
            'followersCounts' => $followersCounts,
            'reportsCounts' => $reportsCounts,
        );

    }
}
