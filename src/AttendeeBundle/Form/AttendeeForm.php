<?php

namespace Akredytacja\AttendeeBundle\Form;

use Doctrine\Common\Collections\ArrayCollection;

class AttendeeForm 
{
    
    private $id;
    
    private $czyCalosc;
    
    private $noce;
    
    private $skadWieszOEvencie;
    
    private $uwagiDoOrgow;
    
    private $zgloszePozniej; //W przypadku pelnoleltniego oznacza to, że nie chce być Opiekunem
    
    private $emailAtt2User; //Email Opiekuna lub Podopiecznego
    
    private $att2gadzety;
    
    private $skladkiDodatkowe;
    
    public function __construct()
    {
        $this -> att2gadzety = new ArrayCollection();
    }
    
    public function getId() {
        return $this->id;
    }
    
    public function getCzyCalosc() {
        return $this->czyCalosc;
    }
    
    public function getNoce() {
        return $this->noce;
    }

    public function getSkadWieszOEvencie() {
        return $this->skadWieszOEvencie;
    }

    public function getUwagiDoOrgow() {
        return $this->uwagiDoOrgow;
    }
    
    public function getZgloszePozniej() {
        return $this->zgloszePozniej;
    }

    public function getEmailAtt2User() {
        return $this->emailAtt2User;
    }

    public function getAtt2gadzety() {
        return $this->att2gadzety;
    }
    
    public function getSkladkiDodatkowe() {
        return $this->skladkiDodatkowe;
    }
    
    public function setId($id) {
        $this->id = $id;
    }
    
    public function setCzyCalosc($czyCalosc) {
        $this->czyCalosc = $czyCalosc;
    }
    
    public function setNoce($noce) {
        $this->noce = $noce;
    }

    public function setSkadWieszOEvencie($skadWieszOEvencie) {
        $this->skadWieszOEvencie = $skadWieszOEvencie;
    }

    public function setUwagiDoOrgow($uwagiDoOrgow) {
        $this->uwagiDoOrgow = $uwagiDoOrgow;
    }
    
    public function setZgloszePozniej($zgloszePozniej) {
        $this->zgloszePozniej = $zgloszePozniej;
    }

    public function setEmailAtt2User($emailAtt2User) {
        $this->emailAtt2User = $emailAtt2User;
    }

    public function setAtt2gadzety($att2gadzety) {
        $this->att2gadzety = $att2gadzety;
    }
    
    public function setSkladkiDodatkowe($skladkiDodatkowe) {
        $this->skladkiDodatkowe = $skladkiDodatkowe;
    }

    
    public function populate(\Akredytacja\AttendeeBundle\Entity\Attendee $attendee)
    {
        $noce = $attendee->getNoce();
        if (in_array('all', $noce)) {
            $this->setCzyCalosc(true);
        } else {
            $this->setNoce($noce);
        }
        
        $this->setSkadWieszOEvencie($attendee->getSkadWieszOEvencie());
        $this->setUwagiDoOrgow($attendee->getUwagiDoOrgow());
        $this->setZgloszePozniej($attendee->getZgloszePozniej());
        $this->setSkladkiDodatkowe($attendee->getWybraneSkladkiDodatkowe());
        $this->setAtt2gadzety($attendee->getAtt2Gadzet());
        $email = $this->getEmailUserOP($attendee);
        $this->setEmailAtt2User($email);
        $this->setZgloszePozniej($attendee->getZgloszePozniej());

    }
    
    /**
     * Pobiera maila UseraOP, niezależnie od statusu.
     * 
     * @param \Akredytacja\AttendeeBundle\Entity\Attendee $attendee
     * @return string
     */
    public function getEmailUserOP(\Akredytacja\AttendeeBundle\Entity\Attendee $attendee) 
    {
        switch ($attendee->statusUseraOP()) {
            case(4):
            case(5):
            case(10):
            case(11):
                $email = $attendee->getAtt2User()->getEmail();
                return $email;
            case(100):
            case(101):
            case(110):
            case(111):
                $email = $attendee->getUserOPEmail();
                return $email;
        }
    }

// =============================================================================
// 
// Funkcja isNoce - callback validator:
// sprawdza czy user zaznaczył przynajmniej jeden checkbox
// 
// =============================================================================
    
    public function isNoceCalosc()
    {
        if( (!$this -> czyCalosc) && empty ($this -> noce) ) :
            return false;
        endif;
        
        return true;
    }

// =============================================================================
// 
// Funkcja isCheckEmail - callback validator:
// sprawdza czy podany jest mail gdy checkbox $zgloszePozniej jest false
// 
// =============================================================================
    
    public function isCheckEmail()
    {
        if( (!$this -> zgloszePozniej) && ($this -> emailAtt2User == '') ) :
            return false;
        endif;
        return true;
    }

    
}

