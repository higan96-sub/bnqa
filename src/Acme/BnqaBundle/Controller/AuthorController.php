<?php
namespace Acme\BnqaBundle\Controller;

use Acme\BnqaBundle\Controller\AppController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * AuthorController.php .
 *
 * @author higan96.<higan.n@gmail.com>
 * @Route("/author",name="author")
 */
class AuthorController extends AppController
{
    /**
     * @Route("/{author_name}",name="author_show")
     * @Template("AcmeBnqaBundle:Search:item.html.twig")
     */
    public function showAction(Request $request, $author_name)
    {
        if ($this->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED') && !$request->getSession()->has('csrf_token')) {
            $token = $this->get('form.csrf_provider')->generateCsrfToken($this->container->getParameter('secret'));
            $request->getSession()->set('csrf_token', $token);
        }

        $amazonItemManager = $this->get('amazon_manager');
        $page = $request->query->get('page', 1);

        try {
            $result = $amazonItemManager->searchAuthorItems($author_name, $page);
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

        $areTheseItemsFollowing = array();
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
            'author_name' => $author_name,
            'areTheseItemsFollowing' => $areTheseItemsFollowing,
            'followersCounts' => $followersCounts,
            'reportsCounts' => $reportsCounts,
        );

    }
}
