<?php

namespace Akredytacja\AttendeeBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * AttendeeRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AttendeeRepository extends EntityRepository
{
    /**
    * Wyszukaj Attendee dla podanych id usera i id eventu.
    */
    public function findAttendeeByEvent($idUsera, $idEventu)
    {
        $query = $this -> getEntityManager()
                -> createQuery( 
                        'SELECT a, u, e FROM AkredytacjaAttendeeBundle:Attendee a'
                        . ' JOIN a.user u'
                        . ' JOIN a.event e'
                        . ' WHERE u.id = :idUsera '
                        . ' AND e.id = :idEventu'
                )
                -> setParameter( 'idUsera', $idUsera )
                -> setParameter( 'idEventu', $idEventu )
                ;
        
        try {
            $results = $query -> getResult();
        } catch (Exception $ex) {
            return null;
        }
        
        foreach( $results as $result ) :
            return $result;
        endforeach;
    }
    
    public function findAtt2UserByUserEvent($user, $event)
    {
        
        $query = $this -> getEntityManager()
                -> createQuery( 
                        'SELECT a2u FROM AkredytacjaAttendeeBundle:Att2User a2u'
                        . ' INNER JOIN a2u.attendee a'
                        . ' INNER JOIN a.tempUser tu '
                        . ' INNER JOIN a.event e'
                        . ' INNER JOIN a.user u '
                        . ' WHERE (u.id = :idUsera OR tu.id = :idUsera) '
                        . ' AND e.id = :idEventu '
                )
                -> setParameter( 'idUsera', $user->getId() )
                -> setParameter( 'idEventu', $event->getId() )
                ;
        
        return $query -> getOneOrNullResult();
    }
    
    
    /**
     * Znajdź wszystkich userów dla eventu
     * 
     * $sortingColumn - kolumna usera. u.MAZWA
     * $sorting - ASC, DESC
     * 
     * @param int $idEventu
     * @param int $page
     * @param string $sorting
     * @param string $sortingColumn
     */
    public function findAllAttendeesByEvent( $idEventu, $page, $sorting = 'ASC', $sortingColumn = 'surname' )
    {
        //Obliczam offset
        $limit = 20;
        $offset = ($page - 1) * $limit; //Obliczam offset
        
        switch ($sortingColumn) {
            case('name'):
                $order = 'a.name ' .$sorting;
                break;
            case('surname'):
                $order = 'a.surname ' .$sorting;
                break;
            case('rokurodzenia'):
                $order = 'a.rokUrodzenia ' .$sorting;
                break;
            case('status'):
                $order = 'a.czyPotwierdzilZgloszenie ' .$sorting. ', a.czyOplacilSkladke ' .$sorting. ', a.rezygnacja ' .$sorting;
                break;
            case('skladka'):
                $order = 'a.wysokoscOplaconejSkladki ' .$sorting;
                break;
            case('datazgloszenia'):
                $order = 'a.dataZgloszenia ' .$sorting;
                break;
        }
        
        $query = $this -> getEntityManager()
                -> createQuery(
                        'SELECT a FROM AkredytacjaAttendeeBundle:Attendee a '
                        . ' LEFT JOIN a.tempUser tu '
                        . ' LEFT JOIN a.user u '
                        . ' LEFT JOIN a.event e '
                        . ' WHERE e.id = :idEventu '
                        . ' ORDER BY  '
                        . $order
                )
                ->setParameter('idEventu', $idEventu)
                ->setMaxResults($limit)
                ->setFirstResult($offset)
                ;
        
        try {
            $results = $query -> getResult();
        } catch (Exception $ex) {
            return null;
        }
        
        return $results;
    }
    
    public function searchAllAttendeesByEvent( $idEventu, $page, $search = null )
    {
        
        //Obliczam offset
        $limit = 20;
        $offset = ($page - 1) * $limit; //Obliczam offset
        
        $query = $this -> getEntityManager()
                -> createQuery(
                        'SELECT a FROM AkredytacjaAttendeeBundle:Attendee a '
                        . ' LEFT JOIN a.tempUser tu '
                        . ' LEFT JOIN a.user u '
                        . ' LEFT JOIN a.event e '
                        . ' WHERE e.id = :idEventu AND '
                        . ' ( '
                        . ' a.name LIKE :search OR '
                        . ' a.surname LIKE :search OR '
                        . ' a.orgComment LIKE :search OR '
                        . ' u.username LIKE :search OR '
                        . ' u.nickname LIKE :search OR '
                        . ' u.email LIKE :search OR '
                        . ' u.phone LIKE :search OR '
                        . ' u.city LIKE :search OR '
                        . ' u.club LIKE :search OR '
                        . ' u.bio LIKE :search OR '
                        . ' tu.nickname LIKE :search OR '
                        . ' tu.email LIKE :search OR '
                        . ' tu.phone LIKE :search OR '
                        . ' tu.city LIKE :search OR '
                        . ' tu.club LIKE :search OR '
                        . ' a.skladkaComment LIKE :search OR '
                        . ' a.wysokoscOplaconejSkladki LIKE :search '
                        . ' ) '
                        . 'ORDER BY a.surname ASC'
                )
                ->setParameter('idEventu', $idEventu)
                ->setParameter('search', '%' .$search. '%')
                ->setMaxResults($limit)
                ->setFirstResult($offset)
                ;
        
        try {
            $results = $query -> getResult();
        } catch (Exception $ex) {
            return null;
        }
        
        return $results;
    }
    
    public function countAtts( $idEventu )
    {      
        $query = $this -> getEntityManager()
                -> createQuery(
                        'SELECT count(a.id) FROM AkredytacjaAttendeeBundle:Attendee a '
                        . ' LEFT JOIN a.user u '
                        . ' LEFT JOIN a.tempUser tu '
                        . ' JOIN a.event e '
                        . ' WHERE e.id = :idEventu '
                )
                -> setParameter( 'idEventu', $idEventu )
                ;

        try {
            return $query -> getSingleScalarResult();
        } catch (Exception $ex) {
            return null;
        } 
    }
    
    public function countNewAtts( $idEventu )
    {      
        $newDate = new \DateTime('-7 days');
        $query = $this -> getEntityManager()
                -> createQuery(
                        'SELECT count(a.id) FROM AkredytacjaAttendeeBundle:Attendee a '
                        . ' LEFT JOIN a.user u '
                        . ' LEFT JOIN a.tempUser tu '
                        . ' JOIN a.event e '
                        . ' WHERE e.id = :idEventu '
                        . ' AND a.dataZgloszenia > :date'
                )
                ->setParameter( 'idEventu', $idEventu )
                ->setParameter( 'date', $newDate )
                ;

        try {
            return $query -> getSingleScalarResult();
        } catch (Exception $ex) {
            return null;
        } 
    }
    
    public function findTempUserByEvent( $email, $id_eventu )
    {
        $query = $this -> getEntityManager()
                -> createQuery(
                        'SELECT a FROM AkredytacjaAttendeeBundle:Attendee a '
                        .' JOIN a.tempUser tp '
                        .' JOIN a.event e '
                        .' WHERE tp.email = :email '
                        .' AND e.id = :id_eventu '
                )
                -> setParameter( 'email', $email )
                -> setParameter( 'id_eventu', $id_eventu )
                ;
        
        try {
            return $query -> getResult();
        } catch (Exception $ex) {
            return null;
        }
    }
    
    public function countArrivedAtts($id_eventu)
    {
        $query = $this -> getEntityManager()
                -> createQuery(
                        'SELECT count(a) FROM AkredytacjaAttendeeBundle:Attendee a '
                        .' JOIN a.event e '
                        .' WHERE a.arrived = :arrived '
                        .' AND e.id = :id_eventu '
                )
                -> setParameter('arrived', true)
                -> setParameter('id_eventu', $id_eventu)
                ;
        
        try {
            return $query->getSingleScalarResult();
        } catch (Exception $ex) {
            return null;
        }
    }
    
    public function countDeclaredUsers($id_eventu)
    {
        $query = $this->getEntityManager()
                ->createQuery(
                        'SELECT count(u) FROM AkredytacjaUsersBundle:User u '
                        .' JOIN u.attendees a'
                        .' JOIN a.event e '
                        .' WHERE e.id = :id_eventu '
                )
                ->setParameter('id_eventu', $id_eventu)
                ;
        
        try {
            return $query->getSingleScalarResult();
        } catch (Exception $ex) {
            return null;
        }
    }
    
    public function countArrivedUserAtts($id_eventu)
    {
        $query = $this->getEntityManager()
                ->createQuery(
                        'SELECT count(u) FROM AkredytacjaUsersBundle:User u '
                        .' JOIN u.attendees a'
                        .' JOIN a.event e '
                        .' WHERE a.arrived = :arrived '
                        . ' AND e.id = :id_eventu '
                )
                ->setParameter('arrived', true)
                ->setParameter('id_eventu', $id_eventu)
                ;
        
        try {
            return $query->getSingleScalarResult();
        } catch (Exception $ex) {
            return null;
        }
    }
}
