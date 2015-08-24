<?php

namespace Akredytacja\AttendeeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Attendee
 *
 * @ORM\Table(name="attendee")
 * @ORM\Entity(repositoryClass="Akredytacja\AttendeeBundle\Entity\AttendeeRepository")
 */
class Attendee
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="skad_wiesz_o_evencie", type="string", length=500, nullable=true)
     */
    private $skadWieszOEvencie;

    /**
     * @var string
     *
     * @ORM\Column(name="uwagi_do_orgow", type="string", length=1000, nullable=true)
     */
    private $uwagiDoOrgow;

    /**
     * @var string
     *
     * @ORM\Column(name="noce", type="string", length=1000)
     */
    private $noce;

    /**
     * @var boolean
     *
     * @ORM\Column(name="czy_oplacil_skladke", type="boolean")
     */
    private $czyOplacilSkladke;

    /**
     * @var float
     *
     * @ORM\Column(name="wysokosc_oplaconej_skladki", type="float")
     */
    private $wysokoscOplaconejSkladki;

    /**
     * @var string
     *
     * @ORM\Column(name="wybrane_skladki_dodatkowe", type="string", length=1000)
     */
    private $wybraneSkladkiDodatkowe;

    /**
     * Przyjmuje wartość true gdy user dokonał aktywacji przez maila
     * (niezależnie od tego czy weźmie udział czy rezygnuje z eventu)
     * 
     * @var boolean
     *
     * @ORM\Column(name="czy_potwierdzil_zgloszenie", type="boolean")
     */
    private $czyPotwierdzilZgloszenie;
    
    /**
     * Informuje o tym, czy user zrezygnował z udziału.
     * Może być użyta dopiero po potwierdzeniu zgłoszenia.
     * @var boolean
     *
     * @ORM\Column(name="rezygnacja", type="boolean")
     */
    private $rezygnacja;
    
    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=100)
     */
    private $token;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="data_zgloszenia", type="datetime")
     */
    private $dataZgloszenia;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="zglosze_pozniej", type="boolean")
     */
    private $zgloszePozniej;
    
    /**
     * @var string
     *
     * @ORM\Column(name="skladka_comment", type="string", length=150, nullable=true)
     */
    private $skladkaComment;
    
    /**
     * @var string
     *
     * @ORM\Column(name="org_comment", type="string", length=1000, nullable=true)
     */
    private $orgComment;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="arrived", type="boolean", nullable=true)
     */
    private $arrived;

// =============================================================================
// Relacja One To Many Attendee do Att2Gadzet
// =============================================================================

    /**
     *
     * @ORM\OneToMany(targetEntity="Att2Gadzet", mappedBy="attendee")
     *      
     */
    private $att2Gadzet;

// =============================================================================
// Relacja One To One Attendee do Att2User (UserOP)
// =============================================================================

    /**
     * 
     * @ORM\OneToOne(targetEntity="Att2User", inversedBy="attendee")
     * @ORM\JoinColumn(name="att2user_attendee", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     * 
     */
    private $att2User;
    
// =============================================================================
// Relacja Many To One Attendee do User 
// =============================================================================

    /**
     * 
     * @ORM\ManyToOne(targetEntity="Akredytacja\UsersBundle\Entity\User", inversedBy="attendees")
     * @ORM\JoinColumn(name="user_attendees", referencedColumnName="id", onDelete="SET NULL")
     * 
     */
    private $user;

// =============================================================================
// Relacja One To One Attendee do TempUser 
// =============================================================================
    
    /**
     * @ORM\OneToOne(targetEntity="Akredytacja\TempUserBundle\Entity\TempUser", inversedBy="attendee")
     * @ORM\JoinColumn(name="tempuser_attendee", referencedColumnName="id")
     */
    
    private $tempUser;
    
// =============================================================================
// Relacja Many To One Attendee do Event 
// =============================================================================
 
    /**
     * 
     * @ORM\ManyToOne(targetEntity="Akredytacja\EventBundle\Entity\Event", inversedBy="attendees")
     * @ORM\JoinColumn(name="event_attendees", referencedColumnName="id")
     * 
     */
    private $event;
    
    /**
     * @ORM\OneToMany(targetEntity="Akredytacja\PPBundle\Entity\Creators", mappedBy="attendee")
     **/
    private $creators;
    
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=150, nullable=true)
     */
    private $name;
    
    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    /**
     * @var string
     *
     * @ORM\Column(name="surname", type="string", length=150, nullable=true)
     */
    private $surname;
    
    public function getSurname() {
        return $this->surname;
    }

    public function setSurname($surname) {
        $this->surname = $surname;
    }

    /**
     * @var integer
     *
     * @ORM\Column(name="rok_urodzenia", type="integer")
     */
    private $rokUrodzenia;
    
    public function getRokUrodzenia() {
        return $this->rokUrodzenia;
    }

    public function setRokUrodzenia($rokUrodzenia) {
        $this->rokUrodzenia = $rokUrodzenia;
    }


    
    
// =============================================================================
    
    public function __construct()
    {
        $this -> att2Gadzet = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set skadWieszOEvencie
     *
     * @param string $skadWieszOEvencie
     * @return Attendee
     */
    public function setSkadWieszOEvencie($skadWieszOEvencie)
    {
        $this->skadWieszOEvencie = $skadWieszOEvencie;

        return $this;
    }

    /**
     * Get skadWieszOEvencie
     *
     * @return string 
     */
    public function getSkadWieszOEvencie()
    {
        return $this->skadWieszOEvencie;
    }

    /**
     * Set uwagiDoOrgow
     *
     * @param string $uwagiDoOrgow
     * @return Attendee
     */
    public function setUwagiDoOrgow($uwagiDoOrgow)
    {
        $this->uwagiDoOrgow = $uwagiDoOrgow;

        return $this;
    }

    /**
     * Get uwagiDoOrgow
     *
     * @return string 
     */
    public function getUwagiDoOrgow()
    {
        return $this->uwagiDoOrgow;
    }

    /**
     * Set noce
     *
     * @param string $noce
     * @return Attendee
     */
    public function setNoce($noce)
    {
        $this->noce = serialize( $noce );

        return $this;
    }

    /**
     * Get noce
     *
     * @return string 
     */
    public function getNoce()
    {
        return unserialize( $this->noce );
    }

    /**
     * Set czyOplacilSkladke
     *
     * @param boolean $czyOplacilSkladke
     * @return Attendee
     */
    public function setCzyOplacilSkladke($czyOplacilSkladke)
    {
        $this->czyOplacilSkladke = $czyOplacilSkladke;

        return $this;
    }

    /**
     * Get czyOplacilSkladke
     *
     * @return boolean 
     */
    public function getCzyOplacilSkladke()
    {
        return $this->czyOplacilSkladke;
    }

    /**
     * Set wysokoscOplaconejSkladki
     *
     * @param float $wysokoscOplaconejSkladki
     * @return Attendee
     */
    public function setWysokoscOplaconejSkladki($wysokoscOplaconejSkladki)
    {
        $this->wysokoscOplaconejSkladki = $wysokoscOplaconejSkladki;

        return $this;
    }

    /**
     * Get wysokoscOplaconejSkladki
     *
     * @return float 
     */
    public function getWysokoscOplaconejSkladki()
    {
        return $this->wysokoscOplaconejSkladki;
    }

    /**
     * Set wybraneSkladkiDodatkowe
     *
     * @param string $wybraneSkladkiDodatkowe
     * @return Attendee
     */
    public function setWybraneSkladkiDodatkowe($wybraneSkladkiDodatkowe)
    {
        $this->wybraneSkladkiDodatkowe = serialize( $wybraneSkladkiDodatkowe );

        return $this;
    }

    /**
     * Get wybraneSkladkiDodatkowe
     *
     * @return string 
     */
    public function getWybraneSkladkiDodatkowe()
    {
        return unserialize( $this->wybraneSkladkiDodatkowe );
    }

    /**
     * Set czyPotwierdzilZgloszenie
     *
     * @param boolean $czyPotwierdzilZgloszenie
     * @return Attendee
     */
    public function setCzyPotwierdzilZgloszenie($czyPotwierdzilZgloszenie)
    {
        $this->czyPotwierdzilZgloszenie = $czyPotwierdzilZgloszenie;

        return $this;
    }

    /**
     * Get czyPotwierdzilZgloszenie
     *
     * @return boolean 
     */
    public function getCzyPotwierdzilZgloszenie()
    {
        return $this->czyPotwierdzilZgloszenie;
    }
    
    /**
     * Set rezygnacja
     *
     * @param boolean $rezygnacja
     * @return Attendee
     */
    public function setRezygnacja($rezygnacja)
    {
        $this->rezygnacja = $rezygnacja;

        return $this;
    }

    /**
     * Get rezygnacja
     *
     * @return boolean 
     */
    public function getRezygnacja()
    {
        return $this->rezygnacja;
    }

    /**
     * Set token
     *
     * @param string $token
     * @return Attendee
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return string 
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set dataZgloszenia
     *
     * @param \DateTime $dataZgloszenia
     * @return Attendee
     */
    public function setDataZgloszenia($dataZgloszenia)
    {
        $this->dataZgloszenia = $dataZgloszenia;

        return $this;
    }

    /**
     * Get dataZgloszenia
     *
     * @return \DateTime 
     */
    public function getDataZgloszenia()
    {
        return $this->dataZgloszenia;
    }
    
    /**
     * Set attendee
     *
     * @param \Akredytacja\AttendeeBundle\Entity\Att2User $attendee
     * @return Attendee
     */
    public function setAttendee(\Akredytacja\AttendeeBundle\Entity\Att2User $attendee = null)
    {
        $this->attendee = $attendee;

        return $this;
    }

    /**
     * Get attendee
     *
     * @return \Akredytacja\AttendeeBundle\Entity\Att2User 
     */
    public function getAttendee()
    {
        return $this->attendee;
    }

    /**
     * Set userOP
     *
     * @param \Akredytacja\AttendeeBundle\Entity\Att2User $userOP
     * @return Attendee
     */
    public function setUserOP(\Akredytacja\AttendeeBundle\Entity\Att2User $userOP = null)
    {
        $this->userOP = $userOP;

        return $this;
    }

    /**
     * Get userOP
     *
     * @return \Akredytacja\AttendeeBundle\Entity\Att2User 
     */
    public function getUserOP()
    {
        return $this->userOP;
    }

    /**
     * Add user
     *
     * @param \Akredytacja\UsersBundle\Entity\User $user
     * @return Attendee
     */
    public function addUser(\Akredytacja\UsersBundle\Entity\User $user)
    {
        $this->user[] = $user;

        return $this;
    }

    /**
     * Remove user
     *
     * @param \Akredytacja\UsersBundle\Entity\User $user
     */
    public function removeUser(\Akredytacja\UsersBundle\Entity\User $user)
    {
        $this->user->removeElement($user);
    }

    /**
     * Get user
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set user
     *
     * @param \Akredytacja\UsersBundle\Entity\User $user
     * @return Attendee
     */
    public function setUser(\Akredytacja\UsersBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Set event
     *
     * @param \Akredytacja\EventBundle\Entity\Event $event
     * @return Attendee
     */
    public function setEvent(\Akredytacja\EventBundle\Entity\Event $event = null)
    {
        $this->event = $event;

        return $this;
    }

    /**
     * Get event
     *
     * @return \Akredytacja\EventBundle\Entity\Event 
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * Set att2User
     *
     * @param \Akredytacja\AttendeeBundle\Entity\Att2User $att2User
     * @return Attendee
     */
    public function setAtt2User(\Akredytacja\AttendeeBundle\Entity\Att2User $att2User = null)
    {
        $this->att2User = $att2User;

        return $this;
    }

    /**
     * Get att2User
     *
     * @return \Akredytacja\AttendeeBundle\Entity\Att2User 
     */
    public function getAtt2User()
    {
        return $this->att2User;
    }

    /**
     * Set zgloszePozniej
     *
     * @param boolean $zgloszePozniej
     * @return Attendee
     */
    public function setZgloszePozniej($zgloszePozniej)
    {
        $this->zgloszePozniej = $zgloszePozniej;

        return $this;
    }

    /**
     * Get zgloszePozniej
     *
     * @return boolean 
     */
    public function getZgloszePozniej()
    {
        return $this->zgloszePozniej;
    }
    
    /**
     * Add att2Gadzet
     *
     * @param \Akredytacja\AttendeeBundle\Entity\Att2Gadzet $att2Gadzet
     * @return Attendee
     */
    public function addAtt2Gadzet(\Akredytacja\AttendeeBundle\Entity\Att2Gadzet $att2Gadzet)
    {
        $this->att2Gadzet[] = $att2Gadzet;

        return $this;
    }

    /**
     * Remove att2Gadzet
     *
     * @param \Akredytacja\AttendeeBundle\Entity\Att2Gadzet $att2Gadzet
     */
    public function removeAtt2Gadzet(\Akredytacja\AttendeeBundle\Entity\Att2Gadzet $att2Gadzet)
    {
        $this->att2Gadzet->removeElement($att2Gadzet);
    }

    /**
     * Get att2Gadzet
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAtt2Gadzet()
    {
        return $this->att2Gadzet;
    }
    
    public function getSkladkaComment() {
        return $this->skladkaComment;
    }

    public function setSkladkaComment($skladkaComment) {
        $this->skladkaComment = $skladkaComment;
    }

        /**
     * Set tempUser
     *
     * @param \Akredytacja\TempUserBundle\Entity\TempUser $tempUser
     * @return Attendee
     */
    public function setTempUser(\Akredytacja\TempUserBundle\Entity\TempUser $tempUser = null)
    {
        $this->tempUser = $tempUser;

        return $this;
    }

    /**
     * Get tempUser
     *
     * @return \Akredytacja\TempUserBundle\Entity\TempUser 
     */
    public function getTempUser()
    {
        return $this->tempUser;
    }

    
// =============================================================================
// 
// populate()
// status() - określa status usera w evencie (niezgloszony - 0, nowozgloszony - 1, 
//      zgloszony nieoplacony - 2, zgloszony oplacony - 4)
// nazwijStatus() - podaje nazwę statusu
// 
// =============================================================================
    
    public function populate( \Akredytacja\AttendeeBundle\Form\AttendeeForm $attendeeForm, $edit = false )
    {
        $this -> setSkadWieszOEvencie( $attendeeForm -> getSkadWieszOEvencie() );
        $this -> setUwagiDoOrgow( $attendeeForm -> getUwagiDoOrgow() );
        if ($edit === false) {
            $this -> setCzyOplacilSkladke( false );
            $this -> setWysokoscOplaconejSkladki( 0 );
            $this -> setCzyPotwierdzilZgloszenie( false );
            $this -> setRezygnacja( false );
            $this -> setDataZgloszenia( new \DateTime('now') );
        }
    }

    /**
     * określa status usera w evencie:<br>
     * - 0 - niezgloszony<br>
     * - 1 - nowozgloszony<br>
     * - 3 - zgloszony nieoplacony<br>
     * - 7 - zgloszony oplacony<br>
     * - 6 - rezygnacja
     * 
     * @return int
     */
    public function status()
    {
        
        $status = 0;
        
        if( isset( $this -> id ) ) :
            $status = $status + 1;
        endif;
        
        if( $this -> getCzyPotwierdzilZgloszenie() ) : 
            $status = $status + 2;
        endif;
        
        if( $this -> getCzyOplacilSkladke() ) : 
            $status = $status + 4;
        endif;
        
        if( $this -> getRezygnacja() ) :
            $status = 6;
        endif;
            
        return $status;
    }
    
    /**
     * Statusy<br>
     * 0. Niezgłoszony - instancja nie istnieje<br>
     * 1. Nowozgłoszony - user się zgłosił, ale nie potwierdził maila<br>
     * 3. Zgłoszony, nieopłacony - user zgłosił się, potwierdził maila, ale nie opłacił składki<br>
     * 7. Zgłoszony, opłacony - user zgłosił się, potwierdził maila, ale opłacił składkę<br>
     * 6. Rezygnacja
     * 
     * @return string
     */
    public function nazwijStatus()
    {
        $status = $this -> status();
        
        switch( $status ) : 
            case(0) : return 'Niezgłoszony';
            case(1) : return 'Nowozgłoszony';
            case(3) : return 'Zgłoszony, niepłacony';
            case(7) : return 'Zgłoszony, opłacony';
            case(6) : return 'Rezygnacja';
        endswitch;
    }
    
    public function setStatus( $status )
    {
        if($this -> status() == $status ) :
            return null;
        endif;
        
        switch( $status ) :
            case(1):
                $this -> rezygnacja = false;
                $this -> czyPotwierdzilZgloszenie = false;
                $this -> czyOplacilSkladke = false;
                $this -> wysokoscOplaconejSkladki = 0;
                $this -> skladkaComment = null;
                break;
            case(3):
                $this -> rezygnacja = false;
                $this -> czyPotwierdzilZgloszenie = true;
                $this -> czyOplacilSkladke = false;
                $this -> wysokoscOplaconejSkladki = 0;
                $this -> skladkaComment = null;
                break;
            case(7):
                $this -> rezygnacja = false;
                $this -> czyPotwierdzilZgloszenie = true;
                $this -> czyOplacilSkladke = true;
                break;
            case(6):
                $this -> rezygnacja = true;
                break;
        endswitch;
    }
    
    /**
     * Status usera OP:<br>
     * - 0 - Att 18+, wskazany UserOP nie istnieje<br>
     * - 1 - Att 18-, wskazany UserOP nie istnieje<br>
     * - 2 - Att 18+, wybiorę później.<br>
     * - 3 - Att 18-, wybiorę później.<br>
     * - 4 - Att 18+, UserOP istnieje, nie potwierdził.<br>
     * - 5 - Att 18-, UserOP istnieje, nie potwierdził.<br>
     * - 10 - Att 18+, UserOP potwierdzil.<br>
     * - 11 - Att 18-, UserOP potwierdzil.<br>
     * 
     * @return int
     */
    public function statusUseraOP()
    {
       
        $status = 0;
        if( $this -> getActualUser() -> czyPelnoletni() ) :
            $status = 0; //18+
        else :
            $status = 1; //18-
        endif;
        
        if( $this -> isUserOP() == true ) { 
            $status += 100;
            if($this -> isUserOPConfirmed() ) :
                $status += 10;
            endif;
            return $status;
        }
        
        // Czy Attendee wybrał opcję: zgłoszę później
        if( $this->zgloszePozniej ) :
            $status = $status + 2;
            return $status;
        endif;
        
        //Pobieram Att2User
        $att2User = $this->att2User;
        //Czy UserOp istnieje w DB
        if( $att2User->getCzyUserOPIstnieje() ) :
            $status = $status + 4;
        else :
           return $status;
        endif;
        
        //Czy UserOp potwierdzil
        if( $att2User -> getCzyPotwierdzil() ) :
            $status = $status + 6;
        else:
            return $status;
        endif;
        
        return $status;
    }
    
    public function getActualUser()
    {
        if( $this -> user != null ) :
            return $this-> user;
        //elseif( $this -> tempUser != null ) :
        else :
            return $this -> tempUser;
        endif;
    }
    
    /**
     * Sprawdza, czy jest userem OP wybranym przez Att dla eventu.
     * 
     * @return boolean
     */
    public function isUserOP()
    {
        $isUserOP = false;
        $user = $this -> getActualUser();
        
        if($user !== null ) :
            $usersOP = $user -> getAtts2User();
            foreach( $usersOP as $userOP ) :
                if($userOP !== null) :
                    $eventId = $userOP -> getAttendee() -> getEvent() -> getId();
                    if( $eventId == $this -> event -> getId() ) :
                        return true;
                    endif;
                endif;
            endforeach;
        endif;
        
        return $isUserOP;
    }
    
    /**
     * Sprawdza, czy user op potwierdził zaproszenie do bycia OP.
     * 
     * @return boolean
     */
    public function isUserOPConfirmed()
    {
        $user = $this -> getActualUser();
        
        if($user !== null ) :
            $atts2User = $user -> getAtts2User();
            foreach( $atts2User as $att2User ) :
                if($att2User !== null) :
                    $eventId = $att2User -> getAttendee() -> getEvent() -> getId();
                    if( $eventId == $this -> event -> getId() && $att2User -> getCzyPotwierdzil() ) :
                        return true;
                    endif;
                    
                endif;
            endforeach;
        endif;
        
        return false;
    }
    
    /**
     * Zwraca maila usera OP, wybranego przez Attendee.
     */
    public function getUserOPEmail()
    {
        $user = $this -> getActualUser();
        if($user !== null ) :
            $usersOP = $user -> getAtts2User();
            foreach( $usersOP as $userOP ) :
                if($userOP !== null) :
                    return $userOP -> getAttendee() -> getActualUser() -> getEmail();
                endif;
            endforeach;
        endif;
    }


    /**
     * Set orgComment
     *
     * @param string $orgComment
     * @return Attendee
     */
    public function setOrgComment($orgComment)
    {
        $this->orgComment = $orgComment;

        return $this;
    }

    /**
     * Get orgComment
     *
     * @return string 
     */
    public function getOrgComment()
    {
        return $this->orgComment;
    }

    /**
     * Set arrived
     *
     * @param boolean $arrived
     * @return Attendee
     */
    public function setArrived($arrived)
    {
        $this->arrived = $arrived;

        return $this;
    }

    /**
     * Get arrived
     *
     * @return boolean 
     */
    public function getArrived()
    {
        return $this->arrived;
    }

    /**
     * Add creators
     *
     * @param \Akredytacja\PPBundle\Entity\Creators $creators
     * @return Attendee
     */
    public function addCreator(\Akredytacja\PPBundle\Entity\Creators $creators)
    {
        $this->creators[] = $creators;

        return $this;
    }

    /**
     * Remove creators
     *
     * @param \Akredytacja\PPBundle\Entity\Creators $creators
     */
    public function removeCreator(\Akredytacja\PPBundle\Entity\Creators $creators)
    {
        $this->creators->removeElement($creators);
    }

    /**
     * Get creators
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCreators()
    {
        return $this->creators;
    }

    /**
     * Set creators
     *
     * @param \Akredytacja\PPBundle\Entity\Creators $creators
     * @return Attendee
     */
    public function setCreators(\Akredytacja\PPBundle\Entity\Creators $creators = null)
    {
        $this->creators = $creators;

        return $this;
    }
}
