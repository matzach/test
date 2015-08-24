<?php

namespace Akredytacja\AttendeeBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class Att2GadzetType extends AbstractType
{
    private $att2gadzety;
    
    public function __construct( $att2gadzety )
    {
        $this -> att2gadzety = $att2gadzety;        
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {
            $gadzet = $event->getData();
            $form = $event->getForm();
            
            $actualGadzet = $gadzet->getGadzet();
            $opis = $actualGadzet->getNazwa() 
                    ."\n".
                    $actualGadzet->getOpis() 
                    ." \nCena: ". $actualGadzet->getCena() 
                    ."PLN";
            $form['opis']->setData($opis);
        });
        
        $builder
                -> add( 'opis', 'textarea', array(
                    'label'     => 'Opis gadżetu',
                    'required'  => false,
                    'disabled'  => true,
                    'mapped'    => false,
                    'attr'      => array('rows' => 8),
                ) )
                -> add( 'ilosc',  'integer', array(
                    'label'         => 'Ilość',
                    'required'      => false,
                ) )
                -> add( 'uwagi', 'text', array(
                    'label'     => 'Uwagi',
                    'required'  => false,
                ) )
                ;
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver -> setDefaults( array(
            'data_class'            => 'Akredytacja\AttendeeBundle\Entity\Att2Gadzet',
            'cascade_validation'    => true,
        ));
    }
    
    public function getName() 
    { 
        return 'att2gadzet';
    }
    
}



