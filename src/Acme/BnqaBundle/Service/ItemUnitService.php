<?php
/**
 * Created by JetBrains PhpStorm.
 * User: nrhk
 * Date: 2013/08/26
 * Time: 21:53
 * To change this template use File | Settings | File Templates.
 */

namespace Acme\BnqaBundle\Service;


class ItemUnitService
{
    public function  buildCount($ary)
    {
        $newAry = array();
        foreach ($ary as $a) {
            $newAry[$a['asin_code']] = $a['count'];
        }

        return $newAry;
    }
}