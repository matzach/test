<?php

namespace Akredytacja\AttendeeBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
//use Symfony\Component\Form\FormEvent;
//use Symfony\Component\Form\FormEvents;


//use Akredytacja\AttendeeBundle\Entity\Att2Gadzet;

class AttendeeFormType extends AbstractType
{
    public function buildForm( FormBuilderInterface $builder, array $options )
    {
        $attendeeForm = $builder -> getData();
        //echo get_class($builder -> getData() -> getAtt2Gadzety());
        $att2Gadzety = $attendeeForm -> getAtt2Gadzety();
        foreach( $att2Gadzety as $att2Gadzet ) :
//            echo get_class($att2Gadzet -> getGadzet());
        $att2Gadzet -> setNazwa( $att2Gadzet -> getGadzet() -> getNazwa() );
        endforeach;
        
        if( $options['event'] == null ) :
            return null;
        endif;
        
        $event = $options['event'];
        $user = $options['user'];
        $czySaGadzety = $options['czySaGadzety'];
        $edit = $options['edit'];
        $attendee = $options['attendee'];
        
        //Tworzę listę dostępnych nocy
        $iloscNocy = $event -> obliczIloscNocy();
        $iloscDni = $event -> obliczIloscDni();
        
        $jednaSkladka = $event -> getJednaSkladka();
        
        $choices = array();
        
        $dataCzyCalosc = false;
        $czyDisabled = false;
        
        $labelNoce = 'Przyjeżdżam na część konwentu';
        
        if( $iloscNocy <= 1 ) :
            //brak wyboru nocy
            $dataCzyCalosc = true;
            $labelNoce = 'Organizatorzy nie przewidują dniówek';
//            $czyDisabled = true;
        elseif ( $iloscNocy > 1 && !$jednaSkladka ):
            //brak wyboru nocy
            $dataCzyCalosc = true;
            $labelNoce = 'Organizatorzy nie przewidują dniówek';
//            $czyDisabled = true;
        elseif ( $iloscNocy > 1 && $jednaSkladka ):
            //Przygotowuję listę nocy
            $date = clone( $event -> getStartEventu() );
            
            for( $day = 1; $day <= $iloscNocy; $day++ ) :
                $key = $day;
                $value = $date->format('Y-m-d').' - ';
                $interval = new \DateInterval( 'P1D' );
                $date -> add( $interval );
                $value .= $date->format('Y-m-d');
                $choices[ $key ] = $value;
            endfor;
            
        endif;
        
        //Tworzę listę dostępnych składek dodatkowych
        //W tablicy system zwróci id wybranych składek
        /**
         * @todo Wybrać tylko składki nieskasowane
         */
        $skladkiDodatkowe = array();
        foreach( $event -> getSkladkiDodatkowe() as $skladkaDodatkowa  ) :
            $skladkiDodatkowe[ $skladkaDodatkowa -> getId() ] = $skladkaDodatkowa -> getNazwa().' - '.$skladkaDodatkowa ->getWartosc();
        endforeach;
        
        //Sprawdzam czy user jest pełnoletni
        //## Trzeba sprawdzić czy to już nie jest zrealizowane
        if( $user -> czyPelnoletni() ) :
            $labelOP = 'Wybierzpodopiecznego';
            $labelCheckbox = 'Wybiorę później lub nie chce go wybierać';
        else: 
            $labelOP = 'Wybierz opiekuna';
            $labelCheckbox = 'Wybiorę później';
        endif;
        
        if ($edit) {
            $builder
                -> add ( 'czyCalosc', 'checkbox', array(
                    'label'     => 'Przyjeżdzam na cały event',
                    'required'  => false,
                    'disabled'  => $czyDisabled,
                ) );
        } else {
            $builder
                -> add ( 'czyCalosc', 'checkbox', array(
                    'label'     => 'Przyjeżdzam na cały event',
                    'required'  => false,
                    'data'      => $dataCzyCalosc,
                    'disabled'  => $czyDisabled,
                ) );
        }
        
        $builder
                -> add( 'noce', 'choice', array(
                    'label'     => $labelNoce,
//                    'label'     => 'Przyjeżdżam na część konwentu',
                    'required'  => true,
                    'choices'   => $choices,
                    'multiple'  => true,
                    'expanded'  => true,
                ) )
                -> add( 'zgloszePozniej', 'checkbox', array(
                    'label'     => $labelCheckbox,
                    'required'  => false,
                ) ) 
                -> add( 'emailAtt2User', 'email', array(
                    'label'     => 'Podaj jego email',
                    'required'  => false,
                ) )
                -> add( 'skadWieszOEvencie', 'textarea', array(
                    'label'     => 'Skąd dowiedziałeś/dowiedziałaś się o evencie?',
                    'required'  => false,
                    'attr' => array('rows' => '6'),
                ) )
                -> add( 'uwagiDoOrgow', 'textarea', array(
                    'label'     => 'Uwagi dla Organizatorów',
                    'required'  => false,
                    'attr' => array('rows' => '6'),
                ) )
                ;
        if ($attendee !== null) {
            switch ($attendee->statusUseraOP()) {
                case(4):
                case(5):
                case(10):
                case(11):
//                    $builder->add(
//                            'Zmień', 'button', array(
//                                'label'     => 'Zmień',
//                            )
//                        );
                    break;
            }
        }
        
        if( $czySaGadzety ) :
            $builder
                -> add( 'att2gadzety', 'collection', array(
                    'label'     => 'Gadżety',
                    'type'      => new Att2GadzetType($att2Gadzety),
                    'allow_add' => false,
                    'required'   =>false,
                    'attr'      => array('class' => 'collection-container')
                ) );
        endif;
        
        if( !empty($skladkiDodatkowe) ) :
            $builder
                -> add( 'skladkiDodatkowe', 'choice', array(
                    'label'     => 'Wybierz dodatkowe składki i zniżki',
                    'required'  => false,
                    'choices'   => $skladkiDodatkowe,
                    'multiple'  => true,
                    'expanded'  => true,
                ) );
        endif;
        
        $builder
                -> add('submit', 'submit', array(
                    'label'     => 'Zgłaszam się',
                    'attr'      => array('class' => 'button large success')
                ))
                ;
        
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver -> setDefaults( array(
            'event'                 => null,
            'user'                  => null,
            'attendee'              => null,
            'czySaGadzety'          => false,
            'cascade_validation'    => true,
            'data_type'             => 'Akredytacja\AttendeeBundle\Form\AttendeeForm',
            'edit'                  => false,
        ));
    }
    
    public function getName()
    { 
        return 'attendee'; 
    }
    
}

