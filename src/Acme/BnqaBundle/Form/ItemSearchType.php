<?php
namespace Acme\BnqaBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;

/**
 * ItemSearchType.php .
 *
 * @author higan96.<higan.n@gmail.com>
 *
 */
class ItemSearchType extends AbstractType
{
  /*
   * @inheritDoc
   */
  public function buildForm(FormBuilderInterface $builder,array $options)
  {
    $builder->add('keyword','search');
  }

  /*
   * @inheritDoc
   */
  public function getName(){
    return 'itemSearch';
  }
}
