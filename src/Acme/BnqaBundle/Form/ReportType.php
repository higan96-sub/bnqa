<?php
namespace Acme\BnqaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * ReportType.
 *
 * @author higan96.<higan.n@gmail.com>
 *
 */
class ReportType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('replyTo', 'hidden', array('data' => 'false'));
        $builder->add('asinCode', 'hidden', array('data' => 'false'));

        $builder->add('body', 'textarea', array(
            'max_length' => 240,
            'required' => true));

        $builder->add('type', 'entity', array('class' => 'Acme\\BnqaBundle\\Entity\\Type', 'query_builder' => function (\Doctrine\ORM\EntityRepository $er) {
            return $er->createQueryBuilder('t')->where('t.isBook = false');
        }));

        $builder->addEventListener(\Symfony\Component\Form\FormEvents::POST_SUBMIT, function ($event) {
            $form = $event->getForm();
            $asinCode = $form->get('asinCode')->getData();
            $replyTo = $form->get('replyTo')->getData();
            if ($asinCode === 'false' && $replyTo === 'false') {
                $form->addError(new \Symfony\Component\Form\FormError('投稿先アイテム、返信先レポートが指定されませんでした。もう一度やり直してください。'));
            }
        });

    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return 'report';
    }
}
