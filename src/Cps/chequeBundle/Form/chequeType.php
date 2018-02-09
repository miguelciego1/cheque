<?php

namespace Cps\chequeBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;



class chequeType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('cheque')
                ->add('monto');
                //->add('comprobante')
                //->add('benficiario');
                //->add('estado');
                // ->add('benficiario','genemu_jqueryautocomplete_entity', array(
                //         'route_name' => 'ajax_member',
                //         'class' => 'Cps\chequeBundle\Entity\benficiario',
                //     ));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Cps\chequeBundle\Entity\cheque'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'cps_chequebundle_cheque';
    }


}
