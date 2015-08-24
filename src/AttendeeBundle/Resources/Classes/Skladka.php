<?php

namespace Akredytacja\AttendeeBundle\Resources\Classes;
use Akredytacja\MainBundle\Service\MainService;

/**
 * Serwis służący do obliczania składek za udział w evenie.
 * 
 */
class Skladka extends MainService
{
    /**
     * Oblicza składkę dla danego uczestnika.
     * 
     * @param \Akredytacja\AttendeeBundle\Entity\Attendee $attendee
     * @param \Doctrine\Common\Collections\ArrayCollection $atts2Gadzet
     * @return type
     */
    public function obliczSkladke( \Akredytacja\AttendeeBundle\Entity\Attendee $attendee, $atts2Gadzet = null )
    {
        
        $skladka = 0;
        $event = $attendee->getEvent();
        
        //Ilość nocy
        //Sprawdzam czy user wybrał całość lub poszczególne noclegi
        
        $noce = $attendee -> getNoce();
        
        if( in_array('all', $noce) ) : //User wybrał całość
            $skladka += $event -> getSkladka();
        else: //User wybrał noce
            $skladka += ( $event -> getSkladkaDniowka() ) * count($noce);
        endif;
        
        //Zniżka za PP
        if ($event->getMoznaZglaszacPP()) {
            if ($event->getZnizkaPP() > 0) {
                //Zgłoszone
                $pps = $this->em->getRepository('AkredytacjaPPBundle:PP')
                        ->findByEventAuthor($event->getId(), $attendee->getActualUser()->getId());
                foreach ($pps as $pp) {
                    if ($pp->getStatus() == 2) {
                        $skladka -= $event->getZnizkaPP();
                    }
                }
                //Współtworzone
                $cpps = $this->em->getRepository('AkredytacjaPPBundle:PP')
                        ->findByEventCreators($event->getId(), $attendee->getActualUser()->getId());
                foreach ($cpps as $pp) {
                    
                    if ($pp->getStatus() == 2) {
                        $skladka -= $event->getZnizkaPP();
                    }
                }
            }
        }
        
        //Dodaję/odejmuję składki/zniżki dodatkowe
        //Itteruje po wybranych składkach i wybieram ich wartości wg id
        $skladkiDodatkowe = $event -> getSkladkiDodatkowe();
        $wybraneSkladkiDodatkowe = $attendee -> getWybraneSkladkiDodatkowe();
        
        if ($wybraneSkladkiDodatkowe !== null) :
        
            foreach( $wybraneSkladkiDodatkowe as $wybranaSkladkaDodatkowa ) :
                foreach( $skladkiDodatkowe as $skladkaDodatkowa ) :
                    if( $skladkaDodatkowa -> getId() == $wybranaSkladkaDodatkowa ) :
                        switch( $skladkaDodatkowa -> getTyp() ) :
                            case 'skladka' : 
                                if( !$skladkaDodatkowa -> getSkasowana() ) :
                                    $skladka = $skladka + $skladkaDodatkowa -> getWartosc();
                                endif;
                                break;
                            case 'znizka' :
                                if( !$skladkaDodatkowa -> getSkasowana() ) :
                                    $skladka -= $skladkaDodatkowa -> getWartosc();
                                endif;
                                break;
                        endswitch;
                    endif;
                endforeach;
            endforeach;
        
        endif;
        
        //Dodaję gadżety
        //Wybieram z $attendee instancje Att2Gadzet
        if( $atts2Gadzet == null ) :
            $atts2Gadzet = $attendee -> getAtt2Gadzet();
        endif;
        
        if( $atts2Gadzet != null ) :
            foreach( $atts2Gadzet as $att2Gadzet ) :
                if( $att2Gadzet -> getIlosc() > 0 ) :
                    $skladka += ($att2Gadzet -> getIlosc()) * $att2Gadzet -> getGadzet() -> getCena();
                endif;
            endforeach;
        endif;
        
        if ($skladka < 0) {
            $skladka = 0;
        }
        
        return $skladka;
        
    }
  
}
