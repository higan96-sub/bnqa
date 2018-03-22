<?php
namespace Acme\BnqaBundle\Controller;

use Acme\BnqaBundle\Controller\AppController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Acme\BnqaBundle\Entity\User;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\KernelEvent;


/**
 * UserController.php .
 *
 * @author higan96.<higan.n@gmail.com>
 * @Route("/user",name="user")
 */
class UserController extends AppController
{
    /**
     * @Route("/{username}",name="user_show")
     * @Template
     */
    public function showAction(Request $request, $username)
    {
        if ($this->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED') && !$request->getSession()->has('csrf_token')) {
            $token = $this->get('form.csrf_provider')->generateCsrfToken($this->container->getParameter('secret'));
            $request->getSession()->set('csrf_token', $token);
        }

        $userRepository = $this->getDoctrine()->getRepository('AcmeBnqaBundle:User');
        $user = $userRepository->findOneByUsernameCanonical($username);
        if (!$user) {
            throw $this->createNotFoundException('存在しないユーザーです');
        }

        return array(
            'user' => $user,
            'pillMenu' => 'reports');
    }


    /**
     * @Route("/homeSidebar/following/",name="user_home_sidebar")
     */
    public function homeSidebarAction(Request $request)
    {
        $page = $request->query->get('page');
        $followingItems = $this->getDoctrine()->getRepository('AcmeBnqaBundle:Item')->findItemByFollowing($this->getUser()->getId(), 10, $page);
        if ($followingItems) {
            foreach ($followingItems as $item) {
                $list[] = $item['asin_code'];
            }
            $followersCounts = $this->getDoctrine()->getRepository('AcmeBnqaBundle:ItemFollowing')->follwersCounts($list);
            $reportsCounts = $this->getDoctrine()->getRepository('AcmeBnqaBundle:Report')->reportsCounts($list);
        }else{
            $followersCounts = null;
            $reportsCounts = null;
        }
        $params = array(
            'followingItems' => $followingItems,
            'followersCounts' => $followersCounts,
            'reportsCounts' => $reportsCounts,
            'affiliateId' => $this->container->getParameter('amazon_affiliate_id'));
        if ($request->isXmlHttpRequest()) {
            if (count($followingItems)) {
                $response = new JsonResponse($this->renderView("AcmeBnqaBundle:Sidebar:_home_following_inner.html.twig", $params));
            } else {
                $response = new Response('empty', 204);
            }
        } else {
            $response = new Response($this->renderView("AcmeBnqaBundle:Sidebar:_home_following.html.twig", $params));
        }
        $response->setPrivate();
        $response->setMaxAge(20);

        return $response;
    }

    /**
     * @Route("/inner/sidebar/{user_id}/page/{page}",name="user_sidebar")
     */
    public function sidebarAction(Request $request, $user_id, $page = 1)
    {
        $user = $this->getDoctrine()->getRepository('AcmeBnqaBundle:User')->find($user_id);
        if ($user) {
            $followingItems = $this->getDoctrine()->getRepository('AcmeBnqaBundle:Item')->findItemByFollowing($user_id, 10, $page);
            $areTheseItemsFollowing = array();
            $followersCounts = array();
            $reportsCounts = array();
            if ($followingItems) {
                foreach ($followingItems as $item) {
                    $list[] = $item['asin_code'];
                }
                if ($this->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
                    $areTheseItemsFollowing = $this->getDoctrine()->getRepository('AcmeBnqaBundle:ItemFollowing')->AreTheseItemsFollowing($this->getUser()->getId(), $list);
                }
                $followersCounts = $this->getDoctrine()->getRepository('AcmeBnqaBundle:ItemFollowing')->follwersCounts($list);
                $reportsCounts = $this->getDoctrine()->getRepository('AcmeBnqaBundle:Report')->reportsCounts($list);
            }
            $params = array(
                'followingItems' => $followingItems,
                'followersCounts' => $followersCounts,
                'reportsCounts' => $reportsCounts,
                'areTheseItemsFollowing' => $areTheseItemsFollowing,
                'affiliateId' => $this->container->getParameter('amazon_affiliate_id'),
            );

            if ($request->isXmlHttpRequest()) {
                $response = new Response($this->renderView("AcmeBnqaBundle:Sidebar:_following.html.twig", $params));
            } else {
                $response = new Response($this->renderView("AcmeBnqaBundle:Sidebar:_following.html.twig", $params));
            }
            return $response;
        }

        return $this->createNotFoundException();
    }

    /**
     * @Route("/{username}/following",name="user_show_following")
     * @Template
     */
    public function showFollowingAction(Request $request, $username)
    {
        if ($this->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED') && !$request->getSession()->has('csrf_token')) {
            $token = $this->get('form.csrf_provider')->generateCsrfToken($this->container->getParameter('secret'));
            $request->getSession()->set('csrf_token', $token);
        }


        $user = $this->getDoctrine()->getRepository('AcmeBnqaBundle:User')->findOneByUsernameCanonical($username);
        if (!$user) {
            throw $this->createNotFoundException('存在しないユーザーです');
        }
        $followingItems = $this->getDoctrine()->getRepository('AcmeBnqaBundle:ItemFollowing')->findBy(array('user' => $user), array('createdAt' => 'DESC'), 15);

        $areTheseItemsFollowing = null;

        if ($this->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED') && $this->getUser()->getUserName() !== $username) {
            $list = array();
            foreach ($followingItems as $item) {
                $list[] = $item->getAsinCode();
            }
            $areTheseItemsFollowing = $this->getDoctrine()->getRepository('AcmeBnqaBundle:ItemFollowing')->AreTheseItemsFollowing($this->getUser()->getId(), $list);
        }

        return array(
            'followingItems' => $followingItems,
            'user' => $user,
            'areTheseItemsFollowing' => $areTheseItemsFollowing,);
    }
}

