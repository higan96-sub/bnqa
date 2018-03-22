<?php
namespace Acme\BnqaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * MisprintType.php .
 *
 * @author higan96.<higan.n@gmail.com>
 *
 */
class MisprintType extends AbstractType
{
  /**
   * @inheritDoc
   */
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder->add('wrongBody', 'textarea', array(
                                                  'max_length' => 1400,
                                                  'required'   => false));
  }
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Acme\BnqaBundle\Entity\Misprint',
        ));
    }


  /**
   * @inheritDoc
   */
  public function getName()
  {
    return 'misprint';
  }
}
