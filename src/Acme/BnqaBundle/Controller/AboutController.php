<?php
/**
 * Created by JetBrains PhpStorm.
 * User: nrhk
 * Date: 2013/07/31
 * Time: 22:16
 * To change this template use File | Settings | File Templates.
 */

namespace Acme\BnqaBundle\Controller;

use Acme\BnqaBundle\Controller\AppController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * AboutController.php .
 *
 * @author higan96.<higan.n@gmail.com>
 * @Route("/about",name="about")
 */
class AboutController extends AppController
{
    /**
     * @Route("/me",name="about_me")
     * @Template
     */
    public function meAction()
    {
        $items = $this->container->get('memcache.default')->get('my_favorite_items');
        if (!$items) {
            $result = $this->get('amazon_manager')->findAmazonItem(
                array('4774150827', '4774144371', '4091853293')
            );
            $items = $result->getAmazonItems();
            $this->container->get('memcache.default')->set('my_favorite_items', $items, 60 * 60 * 24 - 60);
        }

        return array('items' => $items);
    }

    /**
     * @Route("/contact",name="about_contact")
     * @Template
     */
    public function contactAction()
    {
        return  array();
    }

    /**
     * @Route("/",name="about_bnqa")
     * @Template
     */
    public function bnqaAction()
    {
        return array();
    }

    /**
     * @Route("/terms",name="about_terms")
     * @Template
     */
    public function termsAction()
    {
        return array();
    }

}