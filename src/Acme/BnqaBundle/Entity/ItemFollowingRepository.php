<?php

namespace Acme\BnqaBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * ItemFollowingRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ItemFollowingRepository extends EntityRepository
{
    /**
     * @return array
     */
    public function findNewItemList()
    {
        $sql = 'SELECT asin_code AS asinCode FROM item_following WHERE active_flag = 1  ORDER BY updated_at DESC LIMIT 10';
        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->execute();
        $result =  $stmt->fetchAll();
        $list = array();
        foreach ($result as $item) {
            $list[] = $item['asinCode'];
        }
        return $list;
    }

    public function findHotItemList()
    {
        $sql = 'SELECT asin_code AS asinCode FROM report WHERE updated_at BETWEEN (NOW() - INTERVAL 1 WEEK) AND NOW() GROUP BY asin_code ORDER BY count(*) DESC LIMIT 10';
        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->execute();
        $result =  $stmt->fetchAll();
        $list = array();
        foreach ($result as $item) {
            $list[] = $item['asinCode'];
        }
        return $list;
    }

    public function countFollowingItemByUserId($user_id)
    {
        $sql = '
    SELECT  count(id) AS count FROM item_following WHERE user_id = :user_id AND active_flag = true';

        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->bindValue("user_id", $user_id);
        $stmt->execute();
        $results = $stmt->fetchAll();

        return $results;
    }

    public function AreTheseItemsFollowing($user_id, $list)
    {
        $following_list = implode(' OR asin_code =  :', $list);
        $sql = 'SELECT asin_code,item_id,user_id FROM item_following WHERE user_id = :user_id AND active_flag = true AND (asin_code = :' . $following_list . ')';
        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->bindValue("user_id", $user_id);
        foreach ($list as $code) {
            $stmt->bindValue(":" . $code, $code);
        }
        $stmt->execute();
        $results = $stmt->fetchAll();

        return $results;
    }


    public function findHotItems()
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('
    SELECT f,i FROM AcmeBnqaBundle:ItemFollowing f
    JOIN f.item i
    GROUP BY f.item
    ORDER BY f.createdAt DESC
    ')->setMaxResults(20);

        return $query->getResult();
    }


    public function countItemFollower($itemId)
    {
        $sql = '
    SELECT count(f.id) AS followerCount
    FROM item_following f
    JOIN item i ON f.item_id = i.id
    WHERE i.asin_code = :item_id AND active_flag = true';

        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->bindValue("item_id", $itemId);
        $stmt->execute();
        $results = $stmt->fetchAll();

        return $results;
    }

    public function isNowFollowing($itemId, $userId)
    {
        $sql = '
    SELECT f.id AS isNowFollowing
    FROM item_following f
    WHERE f.asin_code = :item_id AND f.user_id = :user_id AND active_flag = true';

        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->bindValue("item_id", $itemId);
        $stmt->bindValue("user_id", $userId);
        $stmt->execute();
        $results = $stmt->fetchAll();

        return $results;
    }

    public function follwersCounts($list)
    {
        $following_list = ':' . implode(') OR ( active_flag = true AND asin_code =  :', $list);
        $sql = 'SELECT asin_code,count(*) AS count FROM item_following WHERE (active_flag = true AND asin_code = ' . $following_list . ') GROUP BY asin_code';
        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        foreach ($list as $code) {
            $stmt->bindValue(":" . $code, $code);
        }
        $stmt->execute();
        $results = $stmt->fetchAll();

        return $results;
    }
}