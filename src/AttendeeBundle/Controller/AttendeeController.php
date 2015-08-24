<?php

namespace Akredytacja\AttendeeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * 
 * Obsługa uczestników eventu.
 * 
 * @author Mateusz Zachciał
 */
class AttendeeController extends Controller
{
    /**
     * #UC_088 Lista uczestników eventu
     * UC_088_attendee_list:
     *   path: /attendee/show/{id_eventu}
     *   defaults: { _controller: Akredytacja\AttendeeBundle:Attendee:show,. id_eventu: null }
     *   requirements:
     *       id_eventu: \d+ 
     */
    public function showAction(Request $request, $id_eventu, $page, $sorting_col='surname', $order='ASC', $is_default)
    {
        /**
         * @todo Sortowanie przygotować.
         * @todo Przygotować wyświetlanie wiadomości - zastosować już Flash Messages
         */
        
        $search = $request->query->get('s');
        
        //Sprawdzam, czy $id_eventu == null
        //Jeśli tak to przekierowuję do UC_012
        if( $id_eventu == null ) :
            return $this -> redirect( $this -> generateUrl('UC_012_eventpanel'), 301 );
        endif;
        
        //Sprawdzam czy event istnieje.
        //Jeżeli nie istnieje to przekierowuje do panelu usera.
        $em = $this -> getDoctrine();
        $event = $this->get('event.event')->getEvent($id_eventu);
        
        if( $event == null ) :
            return $this -> redirect( $this -> generateUrl('UC_012_eventpanel'), 301 );
        endif;
        
        //Jeżeli user nie jest admninem lub orgiem, współorgiem (potwierdzonym)
        //to przekierowuję go do panelu usera.
        $securityContext = $this -> container -> get( 'security.context' );
        $user = $securityContext -> getToken() -> getUser();
        
        if( !$securityContext -> isGranted( 'ROLE_ADMIN' ) ) :
            $org2Event = $em -> getRepository( 'AkredytacjaEventBundle:Org2Event' )
                -> findOrgByEvent( $user -> getId(), $id_eventu);
            if( $org2Event == null || !$org2Event -> getPotwierdzil() || $org2Event->getRezygnacja() ) {
                return $this -> redirect( $this -> generateUrl('UC_012_eventpanel'), 301 );
            }
        endif;
        
        //Pobieram wszystkich attendee dla danego eventu
        if ($search == null ) {
            $attendees = $em -> getRepository( 'AkredytacjaAttendeeBundle:Attendee' )
                    -> findAllAttendeesByEvent( $id_eventu, $page, $order, $sorting_col );
        } else {
            $attendees = $em -> getRepository( 'AkredytacjaAttendeeBundle:Attendee' )
                    -> searchAllAttendeesByEvent( $id_eventu, $page, $search );
        }
        
        $totalRowCount = $em->getRepository('AkredytacjaAttendeeBundle:Attendee')->countAtts($id_eventu);
        
        return $this -> render( 'AkredytacjaAttendeeBundle:Show:showTemplate.html.twig',
                    array(
                        'attendees'     => $attendees,
                        'sorting_col'   => $sorting_col,
                        'order'         => $order,
                        'event'         => $event,
                        'actualPage'    => $page,
                        'pageCount'     => ceil( $totalRowCount/20 ), //Ilość stron paginacji
                        'is_default'    => $is_default,
                    )
                );
    }
    
    /**
     * #UC_100 AJAX - zmiana statusu att
     * Zmiana statusu usera
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function ajaxStatusAction( Request $request )
    {
        $response = new Response();
        
        $content = $this->get("request")->getContent();
        
        if (!empty($content))
        {
            $params = json_decode($content, true); 
        }
        $newStatus = $params['status'];
        $idEventu = $params['event'];
        $idAttendee = $params['attendee'];
       
        if( !$this -> checkSecurity( $idEventu ) ) :
            $response -> setContent( json_encode(array(
                'result' => 'error',
            )) );
            $response -> headers -> set( 'Content-Type', 'application/json' );
            return $response;
        endif;
        
        $em = $this -> getDoctrine() -> getManager();
        $attendee = $em -> getRepository( 'AkredytacjaAttendeeBundle:Attendee' )
                -> find($idAttendee);
        
        $attendee -> setStatus( $newStatus );
        $em -> persist($attendee);
        $em -> flush();
        
        $response -> setContent( json_encode(array('result' => 'success')) );
        $response -> headers -> set( 'Content-Type', 'application/json' );
        return $response;
    }
    
    /**
     * #UC_101 AJAX - wpisanie składki
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function ajaxDuesAction( Request $request )
    {
        $response = new Response();
        
        $content = $this->get("request")->getContent();
        
        if (!empty($content))
        {
            $params = json_decode($content, true); 
        }
        
        $idEventu = $params['event'];
        $idAttendee = $params['attendee'];
        $skladka = $params['skladka'];
        $comment = $params['comment'];
        
        if( !$this -> checkSecurity( $idEventu ) ) :
            $response -> setContent( json_encode(array(
                'result' => 'error',
            )) );
            $response -> headers -> set( 'Content-Type', 'application/json' );
            return $response;
        endif;
        
        if( !is_numeric( $skladka ) ) :
            $response -> setContent( json_encode(array(
                'result' => 'Składka musi być dodatnią liczbą rzeczywistą.',
            )) );
            $response -> headers -> set( 'Content-Type', 'application/json' );
            return $response;
        endif;
        
        if( $skladka < 0 ) :
            $response -> setContent( json_encode(array(
                'result' => 'Składka musi być dodatnią liczbą rzeczywistą.',
            )) );
            $response -> headers -> set( 'Content-Type', 'application/json' );
            return $response;
        endif;
        
        $em = $this -> getDoctrine() -> getManager();
        $attendee = $em -> getRepository( 'AkredytacjaAttendeeBundle:Attendee' )
                -> find($idAttendee);
        
        $attendee -> setStatus( 7 );
        $attendee -> setCzyOplacilSkladke(true);
        $attendee -> setWysokoscOplaconejSkladki($skladka);
        $attendee -> setSkladkaComment($comment);
        $em -> persist($attendee);
        $em -> flush();
        
        $response -> setContent( json_encode(array('result' => 'success')) );
        $response -> headers -> set( 'Content-Type', 'application/json' );
        return $response;
    }
    
    public function ajaxOrgCommentAction(Request $request)
    {
        $response = new Response();
        
        $content = $this->get("request")->getContent();
        
        if (!empty($content))
        {
            $params = json_decode($content, true); 
        }
        
        $idEventu = $params['event'];
        $idAttendee = $params['attendee'];
        $comment = $params['comment'];
        
        if( !$this -> checkSecurity( $idEventu ) ) :
            $response -> setContent( json_encode(array(
                'result' => 'error',
            )) );
            $response -> headers -> set( 'Content-Type', 'application/json' );
            return $response;
        endif;
        
        $em = $this -> getDoctrine() -> getManager();
        $attendee = $em -> getRepository( 'AkredytacjaAttendeeBundle:Attendee' )
                -> find($idAttendee);
        
        $attendee -> setOrgComment( $comment );
        $em -> persist($attendee);
        $em -> flush();
        
        $response -> setContent( json_encode(array('result' => 'success')) );
        $response -> headers -> set( 'Content-Type', 'application/json' );
        return $response;
    }
    
    public function ajaxArrivedAction(Request $request)
    {
        $response = new Response();
        
        $content = $this->get("request")->getContent();
        
        if (!empty($content))
        {
            $params = json_decode($content, true); 
        }
        
        $idEventu = $params['event'];
        $idAttendee = $params['attendee'];
        $arrived = $params['arrived'];
        
        if( !$this -> checkSecurity( $idEventu ) ) :
            $response -> setContent( json_encode(array(
                'result' => 'error',
            )) );
            $response -> headers -> set( 'Content-Type', 'application/json' );
            return $response;
        endif;
        
        $em = $this -> getDoctrine() -> getManager();
        $attendee = $em -> getRepository( 'AkredytacjaAttendeeBundle:Attendee' )
                -> find($idAttendee);
        
        $attendee -> setArrived( $arrived );
        $em -> persist($attendee);
        $em -> flush();
        
        $response -> setContent( json_encode(array('result' => 'success')) );
        $response -> headers -> set( 'Content-Type', 'application/json' );
        return $response;
    }
    
    /**
     * Funkcja sprawdza czy user ma dostęp do danego eventu.
     * 
     * @param type $id_eventu
     * @return boolean
     */
    protected function checkSecurity( $id_eventu )
    {
        //Sprawdzam, czy $id_eventu == null
        //Jeśli tak to przekierowuję do UC_012
        if( $id_eventu == null ) :
            //return $this -> redirect( $this -> generateUrl('UC_012_eventpanel'), 301 );
            return false;
        endif;
        
        //Sprawdzam czy event istnieje.
        //Jeżeli nie istnieje to przekierowuje do panelu usera.
        $em = $this -> getDoctrine();
        $event = $em -> getRepository( 'AkredytacjaEventBundle:Event' )
                -> find( $id_eventu );
        
        if( $event == null ) :
            //return $this -> redirect( $this -> generateUrl('UC_012_eventpanel'), 301 );
            return false;
        endif;
        
        //Jeżeli user nie jest admninem lub orgiem, współorgiem (potwierdzonym)
        //to przekierowuję go do panelu usera.
        $securityContext = $this -> container -> get( 'security.context' );
        $user = $securityContext -> getToken() -> getUser();
        
        if( !$securityContext -> isGranted( 'ROLE_ADMIN' ) ) :
            $org2Event = $em -> getRepository( 'AkredytacjaEventBundle:Org2Event' )
                -> findOrgByEvent( $user -> getId(), $id_eventu);
            if( $org2Event == null || !$org2Event -> getPotwierdzil() ) {
                //return $this -> redirect( $this -> generateUrl('UC_012_eventpanel'), 301 );
                return false;
            }
        endif;
        
        return true;
    }
    
    public function ajaxStatsAction(Request $request)
    {
        $response = new Response();
        
        $content = $this->get("request")->getContent();
        
        if (!empty($content)) {
            $params = json_decode($content, true); 
        }
        
        $id_eventu = $params['event'];
       
        if(!$this->checkSecurity($id_eventu)) :
            $response -> setContent( json_encode(array(
                'result' => 'error',
            )) );
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        endif;
        
        $em = $this -> getDoctrine() -> getManager();
        $totalRowCount = $em->getRepository('AkredytacjaAttendeeBundle:Attendee')->countAtts($id_eventu);
        $newAttsNo = $em->getRepository( 'AkredytacjaAttendeeBundle:Attendee')->countNewAtts($id_eventu);
        $arrivedAtts = $em->getRepository( 'AkredytacjaAttendeeBundle:Attendee')->countArrivedAtts($id_eventu);
        $declaredUsers = $em->getRepository( 'AkredytacjaAttendeeBundle:Attendee')->countDeclaredUsers($id_eventu);
        $declaredTempUsers = $totalRowCount - $declaredUsers;
        $arrivedUserAtts = $em->getRepository( 'AkredytacjaAttendeeBundle:Attendee')->countArrivedUserAtts($id_eventu);
        $arrivedTempUserAtts = $arrivedAtts - $arrivedUserAtts;
        
        $stats = array();
        $stats['attsNo'] = $totalRowCount;
        $stats['newAttsNo'] = $newAttsNo;
        $stats['arrivedAtts'] = $arrivedAtts;
        $stats['declaredUsers'] = $declaredUsers;
        $stats['declaredTempUsers'] = $declaredTempUsers;
        $stats['arrivedUserAtts'] = $arrivedUserAtts;
        $stats['arrivedTempUserAtts'] = $arrivedTempUserAtts;
        
        $stats['attsNoP'] = round(($totalRowCount/$totalRowCount)*100);
        $stats['newAttsNoP'] = round(($newAttsNo/$totalRowCount)*100);
        $stats['arrivedAttsP'] = round(($arrivedAtts/$totalRowCount)*100);
        $stats['declaredUsersP'] = round(($declaredUsers/$totalRowCount)*100);
        $stats['declaredTempUsersP'] = round(($declaredTempUsers/$totalRowCount)*100);
        $stats['arrivedUserAttsP'] = round(($arrivedUserAtts/$totalRowCount)*100);
        $stats['arrivedTempUserAttsP'] = round(($arrivedTempUserAtts/$totalRowCount)*100);
        
        $response->setContent(json_encode($stats));
        $response->headers->set( 'Content-Type', 'application/json' );
        return $response;
    }
    
    public function eventStatsAction(Request $request, $id_eventu = null)
    {
        if (!$this->checkSecurity($id_eventu)) {
            return $this->returnPanel();            
        }
        
        $event = $this->getDoctrine()->getManager()->getRepository('AkredytacjaEventBundle:Event')->find($id_eventu);
        
        return $this -> render( 'AkredytacjaAttendeeBundle:Show:showStatsTemplate.html.twig', array(
            'event'     => $event,
        ));
    }
    
}