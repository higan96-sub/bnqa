<?php
namespace Acme\BnqaBundle\Controller;

use Acme\BnqaBundle\Controller\AppController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

use Acme\BnqaBundle\Entity\ReportBookmarkRepository;
use Acme\BnqaBundle\Entity\ReportBookmark;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;


/**
 * ReportBookmarkController.php .
 *
 * @author higan96.<higan.n@gmail.com>
 * @Route("/reportbookmark",name="reportbookmark")
 */
class ReportBookmarkController extends AppController
{

  /**
   * @Route("/create",name="reportbookmark_create")
   *
   */
  public function createAction(Request $request)
  {
    $token = $request->headers->get('X-CSRF-Token');
    if (false === $this->get('form.csrf_provider')->isCsrfTokenValid($this->container->getParameter('secret'), $token))
    {
      return new Response('creation is failed', 400);
    }
    if ('POST' === $request->getMethod() && $this->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED'))
    {
      $report = $request->request->get('report');
      $bookmarkRepository = $this->getDoctrine()->getManager()->getRepository('AcmeBnqaBundle:ReportBookmark');
      try
      {
        $bookmarkRepository->createBookmark($this->getUser(), $report);
      } catch (\Exception $e)
      {
        throw $this->createNotFoundException();
      }
        return new Response('success',200);
    }
    throw $this->createNotFoundException();
  }

  /**
   * @Route("/delete",name="reportbookmark_delete")
   */
  public function deleteAction(Request $request)
  {
    $token = $request->headers->get('X-CSRF-Token');
    if (false === $this->get('form.csrf_provider')->isCsrfTokenValid($this->container->getParameter('secret'), $token))
    {
      return new Response('delete is failed', 400);
    }
    if ('POST' === $request->getMethod() && $this->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED'))
    {
      $em = $this->getDoctrine()->getManager();
      $bookmarkRepository = $em->getRepository('AcmeBnqaBundle:ReportBookmark');
      $report_id = $request->request->get('report');
      $reportRepository = $this->getDoctrine()->getManager()->getRepository('AcmeBnqaBundle:Report');
      $report = $reportRepository->find($report_id);
        if(!$report){
            return $this->createNotFoundException('存在しないレポートへのリクエストです');
        }
      $reportBookmark = $bookmarkRepository->findOneBy(array('reportId' => $report_id));
      if ($report)
      {
        try
        {
          $em->remove($reportBookmark);
          $em->flush();
        } catch (\Exception $e)
        {
          return $this->createNotFoundException();
        }

        return new Response('success', 200);
      }
    }
    throw $this->createNotFoundException();
  }
}
