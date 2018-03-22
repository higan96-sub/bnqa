<?php
namespace Acme\BnqaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * DeleteType.php .
 *
 * @author higan96.<higan.n@gmail.com>
 *
 */
class ReportDeleteType extends AbstractType
{
  /**
   * @inheritDoc
   */
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder->add('id', 'text', array(
                                              'data'     => '',
                                              'required' => true));
  }

  /**
   * @inheritDoc
   */
  public function getName()
  {
    return 'delete';
  }

}
