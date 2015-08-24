<?php

namespace Akredytacja\AttendeeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Akredytacja\AttendeeBundle\Form\AttendeeForm;
use Akredytacja\AttendeeBundle\Form\AttendeeFormType;

use Akredytacja\AttendeeBundle\Entity\Attendee;
use Akredytacja\AttendeeBundle\Entity\Att2Gadzet;
use Akredytacja\AttendeeBundle\Entity\Att2User;

use Symfony\Component\HttpFoundation\RedirectResponse; 


//use Akredytacja\AttendeeBundle\Resources\Classes\Skladka;

/**
 * 
 * Obsługa zgłoszenia usera na event.
 * 
 * #UC_063 Zgłoszenie uczestnictwa
 * UC_063_application_declare:
 * path: /application/declare/{id_eventu}
 * defaults: { _controller: AkredytacjaAttendeeBundle:Declare:declare, id_eventu: null }
 * 
 * #UC_063_temp Zgłoszenie uczestnictwa przez temp usera
 * UC_063_application_declare_temp:
 * path: /application/declare/temp_user/{id_eventu}/{id_tempUser}
 * defaults: { _controller: AkredytacjaAttendeeBundle:Declare:declare, id_eventu: null, id_tempUser: null, tempDeclare: true }
 * requirements: 
 * id_eventu: \d+
 * id_tempUser: \d+
 * 
 * @author Mateusz Zachciał
 */
class DeclareController extends \Akredytacja\MainBundle\Controller\MainController
{
    
    public function declareAction(Request $request, $id_eventu, $id_tempUser = null, $tempDeclare = false, $id_attendee = null)
    {
        
        $messages = array();
        //Zmienna, która określa czy formularz ma być zapisany do bazy danych.
        //Przyjmuje ona false gdy w zmiennej $messages pojawią się odpowiednie wiadomości
        $additionalValid = true; 
        
        //Sprawdzam czy event istnieje i się już odbył
        //Jeżeli nie istnieje to przekierowanie do UC_012
        //Jeżeli się odbył to odpowiednie info o tym i przekierowanie do UC_063
        
        $em = $this->getDoctrine();

        $event = $this->get('event.event')->getEvent($id_eventu);
        
        if( $event == null ) :
            $messages[] = 'Nie ma takiego eventu';
            return $this->redirect063A($id_eventu, $messages);
        endif;
        
        if( $event->czyOdbylSie() ) :
            $messages[] = 'Event '.$event -> getNazwa().' już się odbył';
            return $this->redirect063A($id_eventu, $messages);
        endif;
        
        if ($id_attendee !== null) {
            if ( !$this->checkDeclareSecutiry($id_attendee)) {
                return $this->returnPanel();
            }
            $attendee = $em->getRepository( 'AkredytacjaAttendeeBundle:Attendee' )
                ->find($id_attendee);
            $user = $attendee->getActualUser();
            //$czySaGadzety = true;
        } elseif (!$tempDeclare) {
            // Sprawdzam czy user jest orgiem lub współorgiem
            // W przypadku współorga sprawdzam czy jest aktywny:
            // Jesli nie jest aktywny to informacja o tym + link aktywacyjny
            $securityContext = $this -> container -> get( 'security.context' );
            $user = $securityContext -> getToken() -> getUser();

            $org2Event = $em -> getRepository( 'AkredytacjaEventBundle:Org2Event' )
                    -> findOrgByEvent( $user -> getId(), $id_eventu);
            
//            if( $org2Event !== null ) :
//                if( $org2Event -> getPotwierdzil() ) :
//                    $messages[] = 'Jesteś organizatorem lub współorganizatorem tego eventu, nie możesz się zgłosić.';
//                else:
//                    $messages[] = 'Jesteś współorganizatorem tego eventu, jednak nie potwierdziłeś tego faktu. [TU DAĆ LINK] [LINK ODRZUĆ ZAPROSZENIE]';
//                endif;
//                return $this->redirect063A($id_eventu, $messages);
//            endif;
        } else { //Pobieram tempUsera
            $user = $em -> getRepository( 'AkredytacjaTempUserBundle:TempUser' ) -> find( $id_tempUser );
        }
        
        //Sprawdzam czy user uczestniczy w evencie
        if( $user -> czyUczestniczy( $event ) && !$tempDeclare && $id_attendee === null ) :
            $messages[] = 'Zgłosiłeś już swój udział w tym evencie.';
            return $this->redirect063A($id_eventu, $messages);
        elseif( $user -> czyUczestniczy( $event ) && $tempDeclare && $id_attendee === null ) :
            $messages[] = 'Użytkownik jest już zgłoszony na ten event.';
            return $this -> redirect( $this -> generateUrl('UC_088_attendee_list',array(
                'id_eventu'     => $event -> getId(),
                'page'          => 1,
            ) ) );
        endif;
        
        //Generuję formularz Zgłoszenia na event
        $attendeeForm = new AttendeeForm();
        
        if ($id_attendee !== null) {
            $attendeeForm->populate($attendee);
            if ($attendee->getAtt2Gadzet() != null) {
                $czySaGadzety = true;
            } else {
                $czySaGadzety = false;
            }
        } else {
            //Z góry dodaję formularze dostępnych gadzetów (class Att2Gadzet)
            $gadzety = $event -> getGadzets();
            $czySaGadzety = false;
            
            if (count($gadzety) != 0) {
                $czySaGadzety = true;
                foreach ($gadzety as $gadzet) {
                    //Ustawiam relację odwrotną - Att2Gadzet do Gadzetu
                    $attendeeForm -> getAtt2Gadzety() -> add( Att2Gadzet::create() -> setGadzet( $gadzet ) ) ;
                }
            }
        }
        
        $edit = false;
        if ($id_attendee !== null) {
            $form = $this->createForm( new AttendeeFormType(), $attendeeForm, array(
                'event'         => $event,
                'user'          => $user,
                'attendee'      => $attendee,
                'czySaGadzety'  => $czySaGadzety,
                'edit'          => true,
            ) );
            $edit = true;
        } else {
            $form = $this -> createForm( new AttendeeFormType(), $attendeeForm, array(
                    'event'         => $event,
                    'user'          => $user,
                    'czySaGadzety'  => $czySaGadzety,
            ) );
        }
        
        
        
        $form -> handleRequest( $request );
        
        if( $form -> isValid() ) :
            //Przygotowuję tablice na wiadomości
            $messages = array();
            
            if ($id_attendee === null) {
                //Tworzę instancję Attendee
                $attendee = new Attendee();

                //Ustawiam w niej relację do usera
                if( !$tempDeclare ) :
                    $attendee -> setUser($user);
                else:
                    $attendee -> setTempUser($user);
                    $attendee->setArrived(true);
                endif;

                //Ustawiam w niej relacje do kowentu
                $attendee -> setEvent($event);
            }
            
// -----------------------------------------------------------------------------
            //Noce
            //----------------------------------
            //Sprawdzam czy event jest jednodniowy, czy oplata za całość
            
            if( $event -> obliczIloscDni() == 1 ) : //Event jednodniowy
                $noce[] = 'all';
            elseif( !$event -> getJednaSkladka() ) : //Event bez dniówek
                $noce[] = 'all';
            elseif( $attendeeForm -> getCzyCalosc() ) : //$user (Att) zaznaczył udział w całości
                $noce[] = 'all';
            else :
                $noce = $attendeeForm -> getNoce();
            endif;
            
            $attendee -> setNoce( $noce );
            
// -----------------------------------------------------------------------------            
            //Mail Opiekuna/Podpiecznego
            //----------------------------------
            //Sprawdzam czy opcja $zgloszePozniej jest zaznaczona
            //Ustawiam typ $userOP (opiekun/podopieczny)
            //Jeśli jest zaznaczona opcja ZgloszePozniej to pomijam sprawdzanie maila
            //Jeśli nie jest zaznaczona to kontunuuję sprawdzanie miala
            $userOP = null;
            $czyUserAttOP = false;
            $czyUserOP = false;
            
            if ($id_attendee !== null) {
                //Sprawdzam, czy user zrezygnował/zmienił usera.
                $emailOPForm = trim($attendeeForm->getEmailAtt2User());
                $emailOPActual = trim($attendeeForm->getEmailUserOP($attendee));
                if (($emailOPActual !=  $emailOPForm) || ($emailOPActual == '') || ($attendeeForm->getZgloszePozniej())) {
                    $userOPService = $this->get('attendee.user_op');
                    $userOPService -> resignation($attendee);
                } else {
                    $userOP = $attendee->getUserOP();
                }
            }
            
            if( !$attendeeForm -> getZgloszePozniej() ) : //User (Attendee) podaje maila
                
                //Tworzę instancję Att2User 
                $att2User = new Att2User();
                $att2User -> setRezygnacja( false );
                $att2User -> setEmail( $attendeeForm -> getEmailAtt2User() );
                
                $attendee -> setZgloszePozniej( false );
            
                //Sprawdzam, czy $user (Attendee) nie jest już opiekunem lub podopiecznym 
                //w aktualnym Evencie. Jeżeli jest to nie możesz zostać kolejnym OP.
                $czyUserAttOP = $this->isUserOP($user, $event);
                
                $userOP = $this->getUserOP($attendeeForm->getEmailAtt2User(), $event->getId());
                
                // Sprawdzam, czy $userOP nie ma już przypisanego OP dla tego Eventu
                // Itteruję po wszystkich Att2User dla danego usera i szukam takiego,
                // gdzie Attendee ma przypisany event z $id_eventu
                $czyUserOP = $this -> isUserOP( $userOP, $event ); 
                
            else :
                $attendee -> setZgloszePozniej( true );
                $att2User = null;
            endif;
                
            if( (!$attendeeForm -> getZgloszePozniej()) && ($userOP == null) && ($user -> czyPelnoletni()) && (!$czyUserAttOP) ) : 
            //UserOP nie istnieje w bazie
            //User (Att) jest pełnoletni => UserOP - Podopieczny

                $att2User -> setTyp('podopieczny');
                $token = $this -> get( 'token' );
                $att2User -> setToken( $token -> generuj($user -> getEmail() ) );
                $this->sendMail($attendeeForm, $user, $event, $att2User, 'pI');

            elseif( (!$attendeeForm -> getZgloszePozniej()) && ($userOP == null) && (!$user -> czyPelnoletni()) && (!$czyUserAttOP) ) :
            //UserOP nie istnieje w bazie
            //User (Att) jest niepełnoletni => UserOP - Opiekun

                $att2User -> setTyp('opiekun');
                $token = $this -> get( 'token' );
                $att2User -> setToken( $token -> generuj($user -> getEmail() ) );
                $this->sendMail($attendeeForm, $user, $event, $att2User, 'oI');

            endif;

            //Zapisuję dane do bazy danych
            if( (!$attendeeForm -> getZgloszePozniej()) &&  $userOP == null ) :
                $messages[] = 'Użytkownik o podanym mailu nie istnieje. Wysyłam miala z prośbą o dołączenie do systemu potwierdzenie twojego wskazania.';
                $att2User -> setCzyUserOPIstnieje( false );
                $att2User -> setEmail( $attendeeForm -> getEmailAtt2User() );
                if($tempDeclare) :
                    $att2User -> setCzyPotwierdzil( true );
                else :
                    $att2User -> setCzyPotwierdzil( false );
                endif;
                $attendee -> setAtt2User( $att2User );
            endif;
            // Koniec "User nie istnieje w bazie"
            
            //Warunek sprawdzający:
            $userOPinDB = 
                    !$userOP == null // $userOP jest w bazie
                    && !$czyUserOP  //$userOP nie ma żadnych OP w aktualnym evencie
                    && ( $user -> getEmail() != $attendeeForm -> getEmailAtt2User() ) //$user (Att) nie zgłasza sam siebie
                    && (!$czyUserAttOP) //$user (Att) nie ma żadnych OP w aktualnym evencie
                    && (!$attendeeForm -> getZgloszePozniej()) //$user wybrał, że zgłosi teraz email OP
                    ;
            
            //Sprawdzam czy Attendee jest pelnoletni, czy niepelnoletni
            //UserOP jest w bazie
            if( $userOPinDB && ($user -> czyPelnoletni()) ) :
                
                //Attendee jest pełnoletni, wiec $userOP jest podopieczny
                //Sprawdzam, czy $userOP jest niepełnoletni
                //Jeśli jest pełnoletni to tylko wyrzuca informację o tym.
                if( $userOP -> czyPelnoletni() ) :
                    $messages[] = 'Podopieczny musi być niepełnoletni';
                    $additionalValid = false;
                else :
                    //Ustawiam typ podopieczny
                    $att2User -> setTyp('podopieczny');
                    $att2User -> setCzyUserOPIstnieje( true );
                    $att2User -> setEmail( $attendeeForm -> getEmailAtt2User() );
                    if($tempDeclare) :
                        $att2User -> setCzyPotwierdzil( true );
                    else :
                        $att2User -> setCzyPotwierdzil( false );
                    endif;

                    //Wysyłam maila z informacją i linkami do potwierdzenia lub rezygnacji
                    $token = $this -> get( 'token' );
                    $att2User -> setToken( $token -> generuj($user -> getEmail() ) );
                    $this->sendMail($attendeeForm, $user, $event, $att2User, 'pA');
                    
                    //Zapisuję w Att2User relację do User i do Attendee
                    $att2User -> setUserOP( $userOP );
                    $attendee -> setAtt2User( $att2User );
                endif; //Koniec sprawdzania czy pełnoletni $userOP
            endif;
            
            //Attendee jest niepełnoletni, więc $userOP musi być pełnoletni
            if( $userOPinDB && (!$user -> czyPelnoletni()) ) : 

                if( $userOP -> czyPelnoletni() ) :
                    //$userOP musi być pełnoletni być Opiekun 
                    //Ustawiam typ podopieczny
                    $att2User -> setTyp('opiekun');
                    $att2User -> setCzyUserOPIstnieje( true );
                    $att2User -> setEmail( $attendeeForm -> getEmailAtt2User() );
                    if($tempDeclare) :
                        $att2User -> setCzyPotwierdzil( true );
                    else :
                        $att2User -> setCzyPotwierdzil( false );
                    endif;

                    //Wysyłam maila z informacją i linkami do potwierdzenia lub rezygnacji
                    $token = $this -> get( 'token' );
                    $att2User -> setToken( $token -> generuj($user -> getEmail() ) );
                    $this->sendMail($attendeeForm, $user, $event, $att2User, 'oA');
                    //Zapisuję w Att2User relację do User i do Attendee
                    $att2User -> setUserOP( $userOP );
                    $attendee -> setAtt2User( $att2User );
                else :
                    $messages[] = 'Opiekun musi być pełnoletni';
                    $additionalValid = false;
                endif; //Koniec sprawdzania czy pełnoletni $userOP

            endif; //Koniec sprawdzania Attendee czyPelnoletni

            if( $czyUserOP ) : //UserOP ma już przypisanego OP
                $messages[] = 'Wskazany przez ciebie user ma już przypisanego Opiekna lub Podopiecznego';
                $additionalValid = false;
            endif;
            
            if( $user -> getEmail() == $attendeeForm -> getEmailAtt2User() ) :
                $messages[] = 'Nie możesz zgłosić sam siebie na swojego Opiekuna lub Podopiecznego';
                $additionalValid = false;
            endif;
            
            if( $czyUserAttOP ) :
                $messages[] = 'Nie możesz mieć pod opieką lub być podopiecznym więcej niż raz';
                $additionalValid = false;
            endif;
            
// -----------------------------------------------------------------------------
            //Entity manager dla zapisu
            //----------------------------------        
            $manager = $em -> getManager();
            
// -----------------------------------------------------------------------------
            //Gadżety
            //----------------------------------
            //Ustawiam relację każdego gadżetu do usera
            //Relacja zawiera takie informacje jak ilość i uwagi dla orgów
            foreach( $attendeeForm -> getAtt2Gadzety() as $att2Gadzet):
                $att2Gadzet -> setAttendee( $attendee );
                $manager -> persist($att2Gadzet);
            endforeach;
            
// -----------------------------------------------------------------------------
            //Dodaję składki dodatkowe
            //----------------------------------
            $attendee -> setWybraneSkladkiDodatkowe( $attendeeForm -> getSkladkiDodatkowe() );
            
// -----------------------------------------------------------------------------
            //Populuję $attendee
            //----------------------------------
            $attendee -> populate($attendeeForm, $edit);
            $attendeeToken = $this -> get( 'token' );
            $attendee -> setToken( $attendeeToken -> generuj( $attendee -> getId() ) );
            $attendee->setName($user->getName());
            $attendee->setSurname($user->getSurname());
            $attendee->setRokUrodzenia($user->getRokUrodzenia());
            
// -----------------------------------------------------------------------------
            //Zapisuję do bazy danych
            //----------------------------------        
            //Jeżeli są jakieś wiadomości to nie zapisuję do bazy danych
            
            if( $additionalValid ): 
            
                $manager -> persist( $attendee );

                if( !$attendeeForm -> getZgloszePozniej() ) :
                    $manager -> persist( $att2User );
                    if( $userOP instanceof \Akredytacja\TempUserBundle\Entity\TempUser ):
                        $manager -> persist( $userOP );
                    endif;
                endif;
                $manager -> flush();
                
                //Wysyłam maila do usera z prośbą o potwierdzenie udziału
                $message = \Swift_Message::newInstance()
                    ->setSubject('SNAILBook - potwierdzenie udziału w '.$event -> getNazwa())
                    ->setFrom(array('admin@snailbook.eu' => 'SNAILbook akredytacja'))
                    ->setTo( $user -> getEmail() )
                    ->setContentType("text/html")
                    ->setBody( 
                        $this->renderView(
                            'AkredytacjaAttendeeBundle:Emails:applicationDeclareActivate.html.twig',
                            array( 
                                'user'      => $user,
                                'event'     => $event,
                                'att2User'  => $att2User,
                                'attendee'  => $attendee,
                                )
                        )
                    );
                $this->get('mailer')->send($message);

                //Szacuję składkę do opłacenia
                $skladka = $this -> get('skladka');
                $oszacowanaSkladka = $skladka->obliczSkladke( $attendee, $attendeeForm -> getAtt2Gadzety() );
                
                //Mail do orgów o nowym uczestniku
                $mailer = $this->get('mymailer');
                $subject = 'SNAILBook - zgłoszenie uczestnika na twój konwent';
                $template = 'AkredytacjaMainBundle:Emails:attAdded.html.twig';
                $options = array( 
                                    'event'     => $event,
                                    'user'      => $user,
                                );
                $mailer->mailToOrgs($subject, $template, $this, $options);
                

                return $this->render('AkredytacjaAttendeeBundle:Declare:declareInfoTemplate.html.twig', array(
                    'event'                 => $event,
                    'attendee'              => $attendee,
                    'oszacowanaSkladka'     => $oszacowanaSkladka,
                    'messages'              => $messages,
                    'att2Gadzets'           => $attendeeForm -> getAtt2Gadzety(),
                    'tempDeclare'           => $tempDeclare,
                ));
            endif;
            
        endif;
        
        
        return $this->render('AkredytacjaAttendeeBundle:Declare:declareTemplate.html.twig', array(
            'form'                  => $form -> createView(),
            'event'                 => $event,
            'messages'              => $messages,
            'tempDeclare'           => $tempDeclare,
            'user'                  => $user,
        ));
    }
    
    /**
     * #UC_099 Pokaż dane uczestnika eventu
     * UC_099_show_attendee
     * Pokazuje dane zgłoszenia uczestnika.
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param int $id_attendee
     */
    public function showAction( Request $request, $id_attendee, $tempDeclare = false )
    {
        $messages = array();
        if ( !$this->checkDeclareSecutiry($id_attendee)) {
            return $this->returnPanel();
        }
        $em = $this -> getDoctrine();
        $attendee = $em->getRepository( 'AkredytacjaAttendeeBundle:Attendee' )
                ->find($id_attendee);
        $event = $attendee->getEvent();
        
        $skladka = $this->get( 'skladka' );
        $oszacowanaSkladka = $skladka->obliczSkladke( $attendee, $attendee->getAtt2Gadzet() );
        
        return $this->render('AkredytacjaAttendeeBundle:Declare:declareInfoTemplate.html.twig', array(
                    'event'                 => $event,
                    'attendee'              => $attendee,
                    'oszacowanaSkladka'     => $oszacowanaSkladka,
                    'messages'              => $messages,
                    'att2Gadzets'           => $attendee->getAtt2Gadzet(),
                    'tempDeclare'           => $tempDeclare,
                ));
    }
   
    /**
     * #UC_063A Zgłoszenie informacje, również może pracować jako show application
     * UC_063A_application_declare_info:
     * path: /application/declare/info/{id_eventu}
     * defaults: { _conrtoller: AkredytacjaAttendeeBundle:Declare:info, id_eventu: null }
     * 
     * @param type $id_eventu
     * @param type $messages
     */
    public function infoAction( $id_eventu, $messages ) 
    {
        return $this->render('AkredytacjaAttendeeBundle:Declare:declateMessagesTemplate.html.twig', array(
             'messages'      => unserialize( $messages ),
         ));
    }
    
    /**
     * #UC_104 AJAX - uczestnik rezygnuje
     * UC_104_att_resignate:
     *      path: /attendee/ajax/resignate
     *      defaults: { _controller: AkredytacjaAttendeeBundle:Declate:ajaxResignate }
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Akredytacja\AttendeeBundle\Controller\Response
     */
    public function ajaxResignateAction( Request $request )
    {
        $response = new Response();
        $translator = $this->get('translator');
            
        $em = $this->getDoctrine()->getManager();
        $content = $this->get("request")->getContent();
        
        if (!empty($content))
        {
            $params = json_decode($content, true); 
        }
        
        $idAttendee = $params['attendee'];
        
        $attendee = $em->getRepository( 'AkredytacjaAttendeeBundle:Attendee' )
                ->find($idAttendee);
        if ($attendee === null) {
            $response -> setContent( json_encode(array(
                'result' => $translator->trans('error.UC_104_empty_att'),
            )) );
            $response -> headers -> set( 'Content-Type', 'application/json' );
            return $response;
        }
        
        $event = $attendee->getEvent();
        
        if ( !$this->checkSecurity($event->getId())) {
            if (!$this -> ifUserAttendee($attendee)) {
                $response -> setContent( json_encode(array(
                'result' => $translator->trans('error.UC_104_access_denied'),
            )) );
            $response -> headers -> set( 'Content-Type', 'application/json' );
            return $response;
            }
        }
        
        $attendee->setStatus(6);
        $em->persist($attendee);
        $em->flush();
        
        $response -> setContent( json_encode(array(
                'result' => $translator->trans('success.UC_104')
            )) );
        $response -> headers -> set( 'Content-Type', 'application/json' );
        return $response;
    }
   
    /**
     * #UC_105 AJAX - uczestnik ponowne zgłoszenie
     * UC_105_att_redeclare:
     *      path: /attendee/ajax/redeclare
     *      defaults: { _controller: AkredytacjaAttendeeBundle:Declare:ajaxRedeclare }
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function ajaxRedeclareAction(Request $request)
    {
        $response = new Response();
        $translator = $this->get('translator');
            
        $em = $this->getDoctrine()->getManager();
        $content = $this->get("request")->getContent();
        
        if (!empty($content))
        {
            $params = json_decode($content, true); 
        }
        
        $idAttendee = $params['attendee'];
        
        $attendee = $em->getRepository( 'AkredytacjaAttendeeBundle:Attendee' )
                ->find($idAttendee);
        if ($attendee === null) {
            $response -> setContent( json_encode(array(
                'result' => $translator->trans('error.UC_105_empty_att'),
            )) );
            $response -> headers -> set( 'Content-Type', 'application/json' );
            return $response;
        }
        
        $event = $attendee->getEvent();
        
        if ( !$this->checkSecurity($event->getId())) {
            if (!$this -> ifUserAttendee($attendee)) {
                $response -> setContent( json_encode(array(
                'result' => $translator->trans('error.UC_105_access_denied'),
            )) );
            $response -> headers -> set( 'Content-Type', 'application/json' );
            return $response;
            }
        }
        
        $attendee->setRezygnacja(false);
        $em->persist($attendee);
        $em->flush();
        
        $response -> setContent( json_encode(array(
                'result' => $translator->trans('success.UC_105')
            )) );
        $response -> headers -> set( 'Content-Type', 'application/json' );
        return $response;
    }
    
    /**
     * Sprawdza, czy user jest uczestnikiem eventu.
     * 
     * @param Attendee $attendee
     */
    protected function ifUserAttendee($attendee)
    {
        $user = $this->getLoggedUser();
        if ($attendee->getUser()->getId() != $user->getId()) {
            return false;
        }
        return true;

    }
    
    /**
     * Sprawdza czy user ma dostęp do zgłoszenia. Pilnuje również czy odpowiednie
     * dane sa w bazie.<br>
     * Zwraca tablicę z informacjami lub wyrzuca do panelu z listą eventów.
     * 
     * @param type $id_attendee
     * @return array
     */
    protected function checkDeclareSecutiry($id_attendee)
    {
        $em = $this -> getDoctrine();
        $attendee = $em->getRepository( 'AkredytacjaAttendeeBundle:Attendee' )
                ->find($id_attendee);
        if($attendee === null) :
            return false;
        endif;
        
        $event = $attendee->getEvent();
        
        if ( !$this->checkSecurity($event->getId())) {
            if (!$this -> ifUserAttendee($attendee)) {
                return false;
            }
        }
        return true;
    }
    
   
// =============================================================================
// 
// Funkcja waliduje wpisany email OP podczas wysyłania zgłoszenia na event
// 
// =============================================================================
   
   public function isEmailOk() 
   {
       
   }
   
// =============================================================================
// 
// Sprawdzam, czy $user nie jest już opiekunem lub podopiecznym
// w aktualnym Evencie
// 
// =============================================================================
   
   public function isUserOP( $user, $event )
   {
        $id_eventu = $event -> getId();
        
        //Sprawdzam, czy user został wybrany.
        if( $user !== null ) :
            $atts2UserAtt = $user -> getAtts2User();
            foreach( $atts2UserAtt as $att2UserAtt ) :
                if( $att2UserAtt != null ) :
                    $idEventu2Att2UserAtt = $att2UserAtt -> getAttendee() -> getEvent() -> getID();
                    if( $idEventu2Att2UserAtt == $id_eventu ) :
                        return true;
                    endif;
                endif;
            endforeach;
        endif; 
        
        //Sprawdzam, czy wskazany user OP nie wybrał już OP.
        if($user !== null) :
            $att2User = $this->getDoctrine()->getManager()->getRepository( 'AkredytacjaAttendeeBundle:Attendee' )
                    -> findAtt2UserByUserEvent( $user, $event );
                    
            echo 'dumpuje'; 
            \Doctrine\Common\Util\Debug::dump($att2User);
        
            if($att2User === null) :
                echo 'false';
                return false;
            else :
                echo 'true';
                return true;
            endif;
        endif;
        
        return false;
   }
   
   /**
    * Pobiera emaila UseraOp jeśli istnieje (User lub TempUser).
    * Jeżeli nie znajdzie usera to zwraca null;
    * 
    * @param type $email
    */
   protected function getUserOp( $email, $idEventu ) 
   {
        $em = $this -> getDoctrine() -> getManager();
        
        $userOP = $em -> getRepository( 'AkredytacjaUsersBundle:User' )
                    -> findOneBy( array( 'email' => $email ) );
        
        if( $userOP == null ) :
            //Szukam TempUsera po mailu i po evencie            
            $userOP = $em -> getRepository( 'AkredytacjaTempUserBundle:TempUser' )
                    -> findByEvent( $email, $idEventu );
        endif;
        
        return $userOP;
   }
   
   /**
    * Wysłanie maila do useraOP, z prośbą o dołączenie do systemu i podanie powodu dlaczego.
    */
   protected function sendMail($attendeeForm, $user, $event, $att2User, $mailType)
   {
       
        switch ($mailType) {
            case('pI'): //podopieczny, zaproszenie
                $template = 'AkredytacjaAttendeeBundle:Emails:opPodopiecznyActivate.html.twig';
                break;
            case('oI'): //opiekun, zaporszenie
                $template = 'AkredytacjaAttendeeBundle:Emails:opOpiekunActivate.html.twig';
                break;
            case('pA'):
                $template = 'AkredytacjaAttendeeBundle:Emails:opPodopiecznyInDBActivate.html.twig';
                break;
            case('oA'):
                $template = 'AkredytacjaAttendeeBundle:Emails:opOpiekunInDBActivate.html.twig';
                break;
        }
       
        $message = \Swift_Message::newInstance()
            ->setSubject('SNAILBook - prośba użytkownika '.$user -> getName().' '.$user -> getSurname())
            ->setFrom(array('admin@snailbook.eu' => 'SNAILbook akredytacja'))
            ->setTo( $attendeeForm -> getEmailAtt2User() )
            ->setContentType("text/html")
            ->setBody( 
                $this->renderView(
                    $template,
                    array( 
                        'user'      => $user,
                        'event'     => $event,
                        'att2User'  => $att2User,
                        )
                )
            );
        $this->get('mailer')->send($message);
   }

   protected function redirect063A($id_eventu, $messages)
   {
       return $this->redirect( $this -> generateUrl('UC_063A_application_declare_info', array(
                'id_eventu' => $id_eventu,
                'messages'  => serialize($messages),
            ) ) );
   }
   
}

