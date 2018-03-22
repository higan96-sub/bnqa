<?php
namespace Acme\BnqaBundle\Controller;

use Acme\BnqaBundle\Controller\AppController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Acme\BnqaBundle\Item\AmazonItemManager;
use Symfony\Component\HttpFoundation\Response;
use Acme\BnqaBundle\Entity\Report;
use Acme\BnqaBundle\Form\ReportType;
use Acme\BnqaBundle\Form\ReportBookType;
use Acme\BnqaBundle\Form\ReportDeleteType;
use Acme\BnqaBundle\Entity\Item;
use \Acme\BnqaBundle\Entity\ReplyMapping;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * ReportController.php .
 *
 * @author higan96.<higan.n@gmail.com>
 * @Route("/")
 */
class ReportController extends AppController
{
    /**
     * @Route(name="homepage")
     *
     * @param Request $request
     * @return Response
     */
    public function homeAction(Request $request)
    {
        if ($this->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {

            $response = new Response($this->renderView("AcmeBnqaBundle:Report:index_for_members.html.twig", array()));

            $response->setPrivate();
            $response->setMaxAge(10);

            return $response;
        }

        $resultSet = $this->container->get('memcache.default')->get('home_result_set');
        if (!$resultSet) {
            $itemFollowingRepository = $this->getDoctrine()->getRepository('AcmeBnqaBundle:ItemFollowing');
            $reportRepository = $this->getDoctrine()->getRepository('AcmeBnqaBundle:Report');

            $amazonItemManager = $this->get('amazon_manager');
            $newList = $itemFollowingRepository->findNewItemList();

            $newItems = $amazonItemManager->findAmazonItem($newList);
            $newFollowersCounts = $itemFollowingRepository->follwersCounts($newList);
            $newReportsCounts = $reportRepository->reportsCounts($newList);

            $hotList = $itemFollowingRepository->findHotItemList();
            $hotItems = $amazonItemManager->findAmazonItem($hotList);
            $hotFollowersCounts = $itemFollowingRepository->follwersCounts($hotList);
            $hotReportsCounts = $reportRepository->reportsCounts($hotList);


            $resultSet = array(
                'newItems' => $newItems->getAmazonItems(),
                'newFollowersCounts' => $newFollowersCounts,
                'newReportsCounts' => $newReportsCounts,
                'hotItems' => $hotItems->getAmazonItems(),
                'hotFollowersCounts' => $hotFollowersCounts,
                'hotReportsCounts' => $hotReportsCounts,
            );
            $this->container->get('memcache.default')->set('home_result_set', $resultSet, 60 * 60 );
        }

        $response = new Response($this->renderView("AcmeBnqaBundle:Report:index.html.twig", $resultSet));
        $response->setPublic();
        //$response->setSharedMaxAge(60);

        return $response;

    }

    /**
     * @Route("inner/tab/menu",name="report_tab_menu")
     *
     * @param Request $request
     * @param null $controller
     * @param null $isBook
     * @param null $userId
     * @param null $asinCode
     * @param null $bookPage
     * @return Response
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function tabMenuAction(Request $request, $controller = null, $isBook = null, $userId = null, $asinCode = null, $bookPage = null)
    {
        $reportRepository = $this->getDoctrine()->getRepository('AcmeBnqaBundle:Report');
        switch ($controller) {
            case 'item':
                $typeIdCount = $reportRepository->countReportType($asinCode, $bookPage);
                break;
            case 'user':
                $typeIdCount = $reportRepository->countReportTypeByUserId($userId);
                $isBook = true;
                break;
            case'home':
                $user = $this->getUser();
                $typeIdCount = $reportRepository->countTypeIdByUserId($user->getId());
                $isBook = true;
                break;
            case 'bookmark':
                $typeIdCount = $reportRepository->countBookmarkedReportByUserId($userId);
                $isBook = true;
                break;
            default:
                throw $this->createNotFoundException();
                break;
        }
        $response = new Response($this->renderView("AcmeBnqaBundle::_tabmenu.html.twig", array(
            'typeIdCount' => $typeIdCount,
            'showBook' => $isBook)));

        return $response;
    }


    /**
     * @Route("editReport",name="report_edit_report")
     *
     */

    public function editReportAction(Request $request)
    {
        if ($this->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $repository = $this->getDoctrine()->getRepository('AcmeBnqaBundle:Item');
            $reportFormType = new ReportType();
            $form = $this->createForm($reportFormType, new Report());
            if ('POST' === $request->getMethod()) {

                $form->bind($request);
                if ($form->isValid()) {
                    $report = $form->getData();
                    $em = $this->getDoctrine()->getManager();

                    $asinCode = $report->getAsinCode();
                    $replyToId = $report->getReplyTo();
                    if ($asinCode !== 'false') {
                        $item = $repository->findOneBy(array('asinCode' => $asinCode));
                        if (!$item) {
                            try {
                                $amazonItem = $this->get('amazon_manager')->findAmazonItem($asinCode);
                            } catch (\Exception $e) {
                                return new Response('不正なリクエストです' . $asinCode, 400);
                            }
                            $item = new Item($amazonItem);
                        }
                    }

                    if ($replyToId) {
                        $replyTo = $this->getDoctrine()->getRepository('AcmeBnqaBundle:Report')->find((int)$replyToId);
                        if ($replyTo) {
                            $replyMapping = new ReplyMapping();
                            $replyMapping->setReplyTo($replyTo);
                            $replyMapping->setReply($report);
                            $replyMapping->setUser($this->getUser());

                            if ($asinCode === 'false') {
                                $item = $replyTo->getItem();
                            }

                            $em->persist($replyMapping);
                        }
                    }


                    if ($report->getPage() > $item->getPage()) {
                        return new Response('不正なリクエスト', 400);
                    }

                    $report->setUser($this->getUser());
                    $report->setItem($item);
                    $report->setAsinCode($item->getAsinCode());

                    $em->persist($report);

                    $em->flush();


                    $this->get('session')->getFlashBag()->set('message', '投稿に成功しました');

                    return $this->redirect($this->generateUrl('item_show', array('asin_code' => $item->getAsinCode())));
                }
                $errors = $form->getErrors();
                foreach ($errors as $error) {
                    $this->get('session')->getFlashBag()->add('error', $error->getMessage());
                }

                return $this->redirect($this->generateUrl('homepage'));

            }

            $response = new Response($this->renderView("AcmeBnqaBundle:Report:edit.html.twig", array(
                'form' => $form->createView(),
            )));

            return $response;
        }

        return $this->createNotFoundException('ログインしてください');
    }


    /**
     * @Route("editReportBook",name="report_edit_report_book")
     */
    public function editReportBookAction(Request $request)
    {
        if($this->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED')){
        $repository = $this->getDoctrine()->getRepository('AcmeBnqaBundle:Item');

        $reportBookFormType = new ReportBookType();
        $reportBookFormType->setMaxPageCount(2000);

        $form = $this->createForm($reportBookFormType, new Report());

        if ('POST' === $request->getMethod()) {
            $form->bind($request);
            if ($form->isValid()) {
                $report = $form->getData();
                $em = $this->getDoctrine()->getManager();

                $asinCode = $report->getAsinCode();
                $replyToId = $report->getReplyTo();
                if ($asinCode !== 'false') {
                    $item = $repository->findOneBy(array('asinCode' => $asinCode));
                    if (!$item) {
                        try {
                            $amazonItem = $this->get('amazon_manager')->findAmazonItem($asinCode);
                        } catch (\Exception $e) {
                            return new Response('不正なリクエストです' . $asinCode, 400);
                        }
                        $item = new Item($amazonItem);
                    }
                }

                if ($replyToId) {
                    $replyTo = $this->getDoctrine()->getRepository('AcmeBnqaBundle:Report')->find((int)$replyToId);
                    if ($replyTo) {
                        $replyMapping = new ReplyMapping();
                        $replyMapping->setReplyTo($replyTo);
                        $replyMapping->setReply($report);
                        $replyMapping->setUser($this->getUser());

                        if ($asinCode === 'false') {
                            $item = $replyTo->getItem();
                        }

                        $em->persist($replyMapping);
                    }
                }


                if ($report->getPage() > $item->getPage()) {
                    $this->get('session')->getFlashBag()->set('error', '設定可能なページ数（' . $item->getPage() . '）を超えたページ数を指定しています。');
                    return $this->redirect($this->generateUrl('homepage'));
                }

                $report->setUser($this->getUser());
                $report->setItem($item);
                $report->setAsinCode($item->getAsinCode());

                $em->persist($report);

                $em->flush();


                $this->get('session')->getFlashBag()->set('message', '投稿に成功しました');

                return $this->redirect($this->generateUrl('item_show', array('asin_code' => $item->getAsinCode())));
            }
            $errors = $form->getErrors();
            foreach ($errors as $error) {
                $this->get('session')->getFlashBag()->add('error', $error->getMessage());
            }

            return $this->redirect($this->generateUrl('homepage'));

        }

//        $item = new Item();
//        $item->setPage($page);
//        $item->setAsinCode($asin_code);

        $response = new Response($this->renderView("AcmeBnqaBundle:Report:edit_book.html.twig", array(
            'form' => $form->createView(),
        )));


        return $response;
        }
        return $this->createNotFoundException('ログインしてください');
    }

//
//    /**
//     * @Route("home/{target_type}",name="home")
//     */
//    public function homeItemAction(Request $request, $target_type)
//    {
//        $response = new Response($this->renderView("AcmeBnqaBundle:Report:index.html.twig", array('targetType' => $target_type,)));
//        $response->setPublic();
//        $response->setSharedMaxAge(0);
//
//        return $response;
//    }

    /**
     * @Route("inner/reports/line",name="reports_line")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param null $controller
     * @param null $asinCode
     * @param null $targetUserId
     * @param int $bookPage
     * @param string $type
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function reportsLineAction(Request $request, $controller = null, $asinCode = null, $targetUserId = null, $bookPage = 0, $type = 'main', $page = 0)
    {

        if ($this->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED') && !$request->getSession()->has('csrf_token')) {
            $token = $this->get('form.csrf_provider')->generateCsrfToken($this->container->getParameter('secret'));
            $request->getSession()->set('csrf_token', $token);
        }

        if ($request->isXmlHttpRequest()) {
            //ページ指定がないなら、１ページ目として処理
            $page = $request->query->get('page', 0) - 1;
            if ($page < 0) {
                $page = 0;
            }
            //レポートタイプの処理
            $type = $request->query->get('type', 'main');
            $asinCode = $request->query->get('asinCode', null);
            $bookPage = $request->query->get('bookPage', 0);
            $controller = $request->query->get('controller');
            $targetUserId = $request->query->get('userId', null);
        }


        $reportRepository = $this->getDoctrine()->getRepository('AcmeBnqaBundle:Report');
        if ($this->getUser()) {
            $accountUserId = $this->getUser()->getId();
        } else {
            $accountUserId = null;
        }

        if ('bookmark' === $type) {
            $reports = $this->getDoctrine()->getRepository('AcmeBnqaBundle:Bookmark')->findForLine($asinCode, $bookPage, $controller, $page, $this->getUser()->getId(), $targetUserId);
        } else {
            switch ($controller) {
                case 'item':
                    $reports = $reportRepository->findByAsinCodeAndType($asinCode, $type, $page, $bookPage, $accountUserId);
                    break;
                case'home':
                    $user = $this->getUser();
                    if (!$user) {
                        throw $this->createNotFoundException();
                    }
                    $reports = $reportRepository->findAllPersonalArchivesByFollowing($user->getId(), 'item', $type, $page);
                    break;
                case 'user':
                    $reports = $reportRepository->findByUserIdAndType($targetUserId, $type, $page, $accountUserId);
                    break;
                case 'public':
                    $reports = $reportRepository->findPublicTimeLine($type);
                    break;
                default:
                    throw $this->createNotFoundException();
                    break;
            }
        }


        if ($request->isXmlHttpRequest()) {
            if (count($reports) === 0) {
                return new Response('No Content', 204);
            }
            $response = new JsonResponse($this->renderView("AcmeBnqaBundle:Line:line_" . $controller . ".html.twig", array('reports' => $reports)));

            if ($this->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
                $response->setPrivate();
                $response->setMaxAge(20);

                return $response;
            }
            $response->setPublic();
            if ($controller === 'public') {
                $response->setSharedMaxAge(60);
            } else {
                $response->setSharedMaxAge(20);
            }

            return $response;
        }

        $response = $this->render("AcmeBnqaBundle:Line:line_" . $controller . ".html.twig", array('reports' => $reports));
        if ($this->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $response->setPrivate();
            $response->setMaxAge(20);

            return $response;
        }
        $response->setPublic();
        if ($controller === 'public') {
            $response->setSharedMaxAge(60);
        } else {
            $response->setSharedMaxAge(20);
        }

        return $response;

    }

    /**
     * @Route("user/{username}/report/{id}",name="report_show", requirements={"id" = "\d+"})
     * @Template
     */
    public function showAction(Request $request, $id, $username)
    {
        if ($this->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $token = $this->get('form.csrf_provider')->generateCsrfToken($this->container->getParameter('secret'));
            $request->getSession()->set('csrf_token', $token);
        }

        $reportRepository = $this->getDoctrine()->getRepository('AcmeBnqaBundle:Report');
        $userRepository = $this->getDoctrine()->getRepository('AcmeBnqaBundle:User');
        $user = $userRepository->findOneBy(array('usernameCanonical' => $username));
        $report = $reportRepository->findForShow($id);
        if (!$user || !$report) {
            throw $this->createNotFoundException();
        }
        $amazonItem = $this->get('amazon_manager')->findAmazonItem($report['asin_code']);
        $userRelatedItems = $reportRepository->fetchUserRelatedItems($username);

        return array(
            'report' => $report,
            'user' => $user,
            'item' => $amazonItem,
            'userRelatedItems' => $userRelatedItems);
    }

    /**
     * @Route("delete",name="report_delete")
     */
    public function deleteAction(Request $request)
    {
        if($this->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED')){
        $token = $request->headers->get('X-CSRF-Token');
        if (false === $this->get('form.csrf_provider')->isCsrfTokenValid($this->container->getParameter('secret'), $token)) {
            return new Response('csrf token has been invalid', 400);
        }
        if ($this->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            if ($request->getMethod() === 'POST' && $request->isXmlHttpRequest()) {
                $reportId = $request->request->get('reportId');

                $em = $this->getDoctrine()->getManager();
                $reportRepository = $em->getRepository('AcmeBnqaBundle:Report');
                $report = $reportRepository->findOneBy(array(
                    'user' => $this->getUser(),
                    'id' => $reportId));
                if ($report) {
                    $em->remove($report);
                    $em->flush();

                    return new Response('Success! your post has deleted', 200);
                }
                return new Response('your requested report has not found', 400);

            }
        }
        return new Response('failed', 400);
    }
        return $this->createNotFoundException('ログインしてください');
    }

}