<?php

namespace Cps\chequeBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class comprobanteType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('bte')
                ->add('docres')
                ->add('fecha','genemu_jquerydate', array(
                      'widget' => 'single_text',
                      'html5' => 'true',
                ))
                ->add('glosa')
                ->add('Usuario');
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Cps\chequeBundle\Entity\comprobante'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'cps_chequebundle_comprobante';
    }


}
