<?php
namespace Acme\BnqaBundle\Controller;

use Acme\BnqaBundle\Controller\AppController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Acme\BnqaBundle\Entity\Item;

use Acme\BnqaBundle\Entity\ItemFollowing;
use Acme\BnqaBundle\Entity\UserFollowing;

/**
 * FollowingController.php .
 *
 * @author higan96.<higan.n@gmail.com>
 * @Route("/following",name="following")
 */
class FollowingController extends AppController
{
  /**
   * @Route("/create/item",name="following_item")
   */
  public function followItemAction(Request $request)
  {
    if ($this->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED') && $request->getMethod() === "POST")
    {
      $em = $this->getDoctrine()->getManager();
      $user = $this->getUser();
      $asinCode = $request->request->get('asinCode');
      $itemFollowing = $em->getRepository('AcmeBnqaBundle:ItemFollowing')->findOneBy(array(
                                                                                          'user'     => $user,
                                                                                          'asinCode' => $asinCode));
      if (!$itemFollowing)
      {
        $targetItem = $em->getRepository('AcmeBnqaBundle:Item')->findOneBy(array('asinCode' => $asinCode));
        if (!$targetItem)
        {
          try
          {
            $amazonItem = $this->get('amazon_manager')->findAmazonItem($asinCode);
          } catch (\Exception $e)
          {
            throw $this->createNotFoundException('不正なリクエスト');
          }
          $targetItem = new Item($amazonItem);
        }
        $ItemFollowing = new ItemFollowing();
        $ItemFollowing->setItem($targetItem);
        $ItemFollowing->setUser($user);
        $ItemFollowing->setAsinCode($asinCode);
        $ItemFollowing->setActiveFlag(true);

        try
        {
          //          $em->persist($targetItem);
          $em->persist($ItemFollowing);
          $em->flush();
        } catch (\Exception $e)
        {
          throw new \Exception($e->getMessage());
        }

        return new Response('success item_id = ' . $targetItem->getId(), 200);

      } elseif ($itemFollowing->getActiveFlag() === false)
      {
        $itemFollowing->setActiveFlag(true);
        $em->persist($itemFollowing);
        $em->flush();

        return new Response('success', 200);
      }

      return new Response('既にフォロー済みです', 400);
    }
  }

  /**
   * @Route("/remove/item",name="remove_item")
   */
  public function removeItemAction(Request $request)
  {
    if ($this->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED') && $request->getMethod() === "POST")
    {
      $asinCode = $request->request->get('asinCode');
      $em = $this->getDoctrine()->getManager();
      $user = $this->getUser();

      $itemFollowing = $em->getRepository('AcmeBnqaBundle:ItemFollowing')->findOneBy(array(
                                                                                          'user'     => $user,
                                                                                          'asinCode' => $asinCode));
      if ($itemFollowing)
      {
        $itemFollowing->setActiveFlag(false);
        try
        {

          $em->persist($itemFollowing);
          $em->flush();
        } catch (\Exception $e)
        {
          return new Response('fail', 400);
        }

        return new Response('success', 200);
      }

      return new Response('フォローしてません', 404);
    }
    throw $this->createNotFoundException();
  }
}
