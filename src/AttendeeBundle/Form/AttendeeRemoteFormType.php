<?php

namespace Akredytacja\AttendeeBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Mateusz Zachciał
 */
class AttendeeRemoteFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                -> add( 'loginEmail', 'text', array(
                    'label'     => 'Podaj swój login lub email',
                    'required'  => false,
                ) )
                ;
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        
    }
    
    public function getName() 
    { 
        return 'attremote';
    }
}