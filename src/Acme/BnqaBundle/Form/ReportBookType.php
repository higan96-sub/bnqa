<?php
namespace Acme\BnqaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Acme\BnqaBundle\Form\MisprintType;

/**
 * ReportType.
 *
 * @author higan96.<higan.n@gmail.com>
 *
 */
class ReportBookType extends AbstractType
{
    private $maxPageCount;

    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
//    $builder->add('replyTo','text',array('data' =>'false' ,'mapped' => false));
        $builder->add('replyTo', 'hidden', array('data' => 'false'));
        $builder->add('asinCode', 'hidden', array('data' => 'false'));

        $builder->add('body', 'textarea', array(
            'max_length' => 240,
            'required' => true));

        $builder->add('type', 'entity', array('class' => 'Acme\\BnqaBundle\\Entity\\Type'));

        $builder->add('misprint', new MisprintType());

        $builder->add('page', 'integer', array(
            'required' => false,
//            'max_length' => $this->maxPageCount,
            'label' => 'ページ'));
        $builder->addEventListener(\Symfony\Component\Form\FormEvents::POST_SUBMIT, function ($event) {
            $form = $event->getForm();
            $pageCount = $form->get('page')->getData();
            $type = $form->get('type')->getData();
            $misprint = $form->get('misprint')->getData();
            $asinCode = $form->get('asinCode')->getData();
            $replyTo = $form->get('replyTo')->getData();
            if ($asinCode === 'false' && $replyTo === 'false') {
                $form->addError(new \Symfony\Component\Form\FormError('投稿先アイテム、返信先レポートが指定されませんでした。もう一度やり直してください。'));
            }
            if ($pageCount > $this->maxPageCount) {
                $form->addError(new \Symfony\Component\Form\FormError('「ページ」には書籍のページ数（' . $this->maxPageCount . '）より小さな数値を指定してください。'));
            } elseif ($pageCount < 0) {
                $form->addError(new \Symfony\Component\Form\FormError('「ページ」には1より大きな数値を指定してください。'));
            }
            if ('misprint' === $type->getName()) {
                if (strlen($misprint->getWrongBody()) === 0) {
                    $form->addError(new \Symfony\Component\Form\FormError('「誤植」を投稿する場合には、正と誤の両方を指定してください'));
                }
                if (is_null($pageCount)) {
                    $form->addError(new \Symfony\Component\Form\FormError('「誤植」を投稿する場合には、ページを必ず指定してください'));
                }
            }

        });
//    $builder->addEventListener(\Symfony\Component\Form\FormEvents::POST_SET_DATA, function ($event)
//    {
//      $form = $event->getForm();
//      $type = $form->get('type')->getData();
//      $misprint = $form->get('misprint')->getData();
//      if($type !== 'misprint' && $misprint === null){
//        $form->get('misprint')->setData('test');
//      }
//    });

    }

    public function setMaxPageCount($maxPageCount)
    {
        $this->maxPageCount = $maxPageCount;
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return 'reportBook';
    }
}
