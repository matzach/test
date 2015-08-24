<?php

namespace Akredytacja\AttendeeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Obsługuje potierdzenia mailowe: potwierdzenie udziału w evencie i zaproszenia do OP
 * 
 * activateAppDeclareAction()
 * Obsługuje potwierdzenie lub rezygnację z eventu.
 * 
 * activateOPAction()
 * Obsługuje potwierdzenie wskazania przez Usera Attendde Opiekuna lub 
 * Podopiecznego.
 *
 * @author Mateusz Zachciał
 */
class EmailsController extends Controller
{
    
    /**
     * Obsługuje potwierdzenie lub rezygnację z eventu.
     */
    public function activateAppDeclareAction( $id_usera, $id_eventu, $token, $action )
    {
        $messages = array();
        
        $em = $this -> getDoctrine() -> getManager();
        $attendee = $em -> getRepository( "AkredytacjaAttendeeBundle:Attendee" )
                -> findAttendeeByEvent( $id_usera, $id_eventu )
                ;
        
        //$attendee = null;
        
        //Attendee nie istnieje
        if( $attendee == null ) :
            $messages[] = 'Błąd. Prawdopodobnie podałeś złe dane.';
        endif;
        
        //Attendee istnieje, potwierdził, nie zgadza się token
        if( $attendee != null && $attendee -> getCzyPotwierdzilZgloszenie() && $attendee -> getToken() != $token ) :
        
        //Attendee istnieje, potwierdził, zgadza się token
        elseif( $attendee != null && $attendee -> getCzyPotwierdzilZgloszenie() && $attendee -> getToken() == $token ) :
            
            switch( $action ) :
                case 'activate' :
                    //User zaakcetpował i powtórnie wcisnął akceptuj
                    if( !$attendee -> getRezygnacja() ) :
                        $messages[] = 'Już potwierdziłeś uczestnictwo w tym evencie';
                    else :
                        $attendee -> setRezygnacja( false );
                        $em -> persist( $attendee );
                        $em -> flush();
                        //$this-> $em -> clear();
                    endif;
                    break;
                
                case 'deactivate' :
                    //User zrezygnował i powtórnie kliknął rezygnuj
                    if( $attendee -> getRezygnacja() ) :
                        $messages[] = 'Już zrezygnowałeś z uczestnictwa w tym evencie';
                    else :
                        $attendee -> setRezygnacja( true );
                        $em -> persist( $attendee );
                        $em -> flush();
                        //$this-> $em -> clear();
                    endif;
            endswitch;
        
        //Attendee istnieje, niepotwierził, nie zgadza się token
        elseif( $attendee != null && !$attendee -> getCzyPotwierdzilZgloszenie() && $attendee -> getToken() != $token ) :
            $messages[] = 'Błąd. Prawdopodobnie podałeś złe dane.';
                
        //Attendee istnieje, niepotwierzi, zgadza się token
        elseif( $attendee != null && !$attendee -> getCzyPotwierdzilZgloszenie() && $attendee -> getToken() == $token ) :
            switch( $action ) :
                case 'activate' :
                    //User zaakcetpował 
                    $attendee -> setRezygnacja( false );
                    $attendee -> setCzyPotwierdzilZgloszenie( true );
                    $em -> persist( $attendee );
                    $em -> flush();
                    //$this-> $em -> clear();
                    break;
                
                case 'deactivate' :
                    //User zrezygnował
                    $attendee -> setRezygnacja( true );
                    $attendee -> setCzyPotwierdzilZgloszenie( true );
                    $em -> persist( $attendee );
                    $em -> flush();
                    //$this-> $em -> clear();
            endswitch;
        endif;
        
        $previusPage = $this->getRequest()->headers->get('referer');
        
        return $this -> render( 'AkredytacjaAttendeeBundle:Emails:activateAppDeclareTemplate.html.twig', array(
            'messages'  => $messages,
            'action'    => $action,
        ) );
        
    }
    
     /**
     * Obsługuje potwierdzenie wskazania przez Usera Attendde Opiekuna lub 
     * Podopiecznego.
     * 
     * @param type $id_useraOP
     * @param type $id_attendee
     * @param type $token
     * @param type $action
     */
    public function activateOPAction( $email_att2user, $token, $action  )
    {
        $messages = array();
        
        $em = $this -> getDoctrine() -> getManager();
        $att2User = $em -> getRepository( 'AkredytacjaAttendeeBundle:Att2User' )
                -> findOneBy( array( 'email' => $email_att2user, 'token' => $token ) )
                ;
        
        $userOP = null;
        if( $att2User !== null ) : // $att2User nie istnieje
            //Sprawdzam w DB czy userOP na pewno nie istnieje (ładuje go z bazy danych)
            $userOP = $em -> getRepository( 'AkredytacjaUsersBundle:User' )
                -> findOneBy( array( 'email' => $att2User -> getEmail() ) )
                ;
        endif;
        
        if( $att2User == null ) : // $att2User nie istnieje
            $messages[] = 'Błąd. Prawdopodobnie podałeś błędne dane.';
        
        //userOP nie istnieje w DB i wg att2User
        elseif( !$att2User -> getCzyUserOPIstnieje() && $userOP == null ) : 
            $messages[] = 'Błąd. By móc potwierdzić otrzymaną prośbę, musisz zarejestrować się do systemu SNAIL.';
        
        //userOP istnieje w DB, wg att2User nie istnieje, nie zgadza się token
        elseif( !$att2User -> getCzyUserOPIstnieje() && $userOP !== null && $att2User -> getToken() != $token ) :
            $messages[] = 'Błąd! Prawdopodbnie podałeś błędne dane.';
        
        //userOP istnieje w DB, wg att2User nie istnieje, zgadza się token
        elseif( !$att2User -> getCzyUserOPIstnieje() && $userOP !== null && $att2User -> getToken() == $token ) :    
            switch( $action ) :
                case 'activate' :
                    $messages[] = 'Twoje potwierdzenie zostało aktywowane.';
                    $att2User -> setRezygnacja( false );
                    $att2User -> setCzyPotwierdzil( true );
                    $att2User -> setCzyUserOPIstnieje( true );
                        
                    //Przypisuję useraOP do att2User
                    $att2User -> setUserOP( $userOP );
                    
                    $em -> persist( $att2User );
                    $em -> flush();
                    break;
                case 'deactivate' :
                    $messages[] = 'Twoja rezygnacja została aktywowana.';
                    //$att2User -> setRezygnacja( true );
                    $this->get('attendee.user_op')->resignation($att2User->getAttendee());
            endswitch;
            
            /**
             * @todo Wysłanie maila do $usera z akcją podjętą przez $userOP
             */
        
        //UserOP istnieje, potwierdzil, 
        elseif( $att2User -> getCzyUserOPIstnieje() && $att2User -> getCzyPotwierdzil() ) :
            
            /**
             * @todo Sprawdzić czy zmienił zdanie, czy się rozmyslił
             * @todo Trzeba sprawdzić token
             */
            
            switch( $action ) :
                case 'activate' :
                    //User jest potwierdzony i nie zmienił zdania
                    if( $att2User -> getRezygnacja() == false ) :
                        $messages[] = 'Już zaakceptowałeś prośbę.';
                    //User jest potwierdzony i zmienił zdanie
                    else :
                        $messages[] = 'Użytkownik zostanie poinformowany o twojej akceptacji.';
                        $att2User -> setRezygnacja( false );
                        
                        /**
                         * @todo Wysłać maila do user att
                         */
                        
                        $em -> persist( $att2User );
                        $em -> flush();
                        
                    endif;
                    break;
                    
                case 'deactivate' :
                    //User jest potwierdzony i zmienił zdanie
                    $messages[] = 'Użytkownik zostanie poinformowany o twojej rezygnacji.';
                    $this->get('attendee.user_op')->resignation($att2User->getAttendee());
            endswitch;
            
        //UserOP istnieje, nie potwierdził, nie zgadza się token
        elseif( $att2User -> getCzyUserOPIstnieje() && !$att2User -> getCzyPotwierdzil() && $att2User -> getToken() != $token ) :
            $messages[] = 'Błąd! Prawdopodbnie podałeś błędne dane.';
        
        //UserOP istnieje, nie potwierdził, zgadza się token
        elseif( $att2User -> getCzyUserOPIstnieje() && !$att2User -> getCzyPotwierdzil() && $att2User -> getToken() == $token ) :
            
            $messages[] = '';
                
            $att2User -> setCzyPotwierdzil( true );
            
            switch( $action ) :
                case 'activate' :
                    $att2User -> setRezygnacja( false );
                    $messages[] = 'Potwierdzone zaproszenie.';
                    break;
                    
                case 'deactivate' :
                    $att2User -> setRezygnacja( false );
                    $messages[] = 'Odrzucenie zaproszenia.';
            endswitch;
            
            $em -> persist( $att2User );
            $em -> flush();
            
        endif;
        
        return $this -> render( 'AkredytacjaAttendeeBundle:Emails:activateOPTemplate.html.twig', array(
            'messages'  => $messages,
        ) );
    }
    
}
