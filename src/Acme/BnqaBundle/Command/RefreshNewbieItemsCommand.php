<?php
/**
 * Created by JetBrains PhpStorm.
 * User: nrhk
 * Date: 2013/08/23
 * Time: 22:00
 * To change this template use File | Settings | File Templates.
 */

namespace Acme\BnqaBundle\Command;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Acme\BnqaBundle\Item\AmazonItemManager;

class RefreshNewbieItemsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('bnqa:refresh:items')
            ->setDescription('新着アイテムの情報を更新します')
            ->addArgument(
                'asinCode',
                InputArgument::OPTIONAL,
                'Amazon ASIN-code'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $em = $this->getContainer()->get('doctrine')->getManager();
        //$inputを使うのかな？
        $asinCode = $input->getArgument('asinCode');
        if ($asinCode === null) {
            $query = $em->createQuery(
                'SELECT i
                FROM AcmeBnqaBundle:Item i
                WHERE (i.imgUrl IS NULL OR i.page = 999) AND i.releasedDate > :date
                ORDER BY i.releasedDate ASC'
            )->setParameter('date', new \DateTime('-7 day'))->setMaxResults(10);
            $items = $query->getResult();
            if ($items) {
                $amazonItemManager = new AmazonItemManager($this->getContainer());
                $list = array();
                foreach ($items as $item) {
                    $list[] = $item->getAsinCode();
                }
                $amazonItems = $amazonItemManager->findAmazonItem($list)->getAmazonItems();
                $i = 0;
                foreach ($items as $item) {
                    $item->setImgUrl($amazonItems[$i]->getImgUrl(true));
                    $item->setPage($amazonItems[$i]->getPage());
                    $item->setReleasedDate($amazonItems[$i]->getReleasedDate());
                    $i += 1;
                }
                $em->flush();

                return $output->writeln('<info>done</info>');
            }
            return $output->writeln('no item');
        }
        $item = $em->getRepository('AcmeBnqaBundle:Item')->findOneBy(array('asinCode' => $asinCode));
        if (!$item) {
            return $output->writeln(sprintf('<error>Asincode:"%s"は見つかりませんでした</error>',$asinCode));
        }
        $amazonItemManager = new AmazonItemManager($this->getContainer());
        try{
            $amazonItem = $amazonItemManager->findAmazonItem($asinCode);
        }catch (\Exception $e){
            return $output->writeln(sprintf('<error>%s</error>',$e->getMessage()));
        }
        $item->setPage($amazonItem->getPage());
        $item->setImgUrl($amazonItem->getImgUrl());
        $item->setReleasedDate($amazonItem->getReleasedDate());
        try {
            $em->flush();
        } catch (\Exception $e) {
            return $output->writeln(sprintf('<error>%s</error>',$e->getMessage()));
        }
        return $output->writeln(sprintf('<info>AsinCode"%s"を更新しました</info>',$asinCode));
    }
}