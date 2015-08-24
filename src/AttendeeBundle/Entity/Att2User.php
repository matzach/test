<?php

namespace Akredytacja\AttendeeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Przechowuje informacje o opiekunie lub podopiecznym Attendee.
 *
 * @ORM\Table(name="att2user")
 * @ORM\Entity(repositoryClass="Akredytacja\AttendeeBundle\Entity\Att2UserRepository")
 */
class Att2User
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
     * 
     * Typ relacji usera do Attendde: opiekun, czy podopieczny Attendee
     * 
     * @var string
     *
     * @ORM\Column(name="typ", type="string", length=20, nullable=true)
     */
    private $typ;

    /**
     * @var string
     *
     * @ORM\Column(name="mail", type="string", length=100, nullable=true)
     */
    private $email;

    /**
     * @var boolean
     *
     * @ORM\Column(name="czy_potwierdzil", type="boolean")
     */
    private $czyPotwierdzil;
    
    /**
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
     * @var boolean
     *
     * @ORM\Column(name="czy_user_op_istnieje", type="boolean")
     */
    private $czyUserOPIstnieje;
    
// =============================================================================
// Relacja ManyToOne Att2User do User
// =============================================================================

    /**
     * Relacja ManyToOne Att2User do User
     * 
     * @ORM\ManyToOne(targetEntity="Akredytacja\UsersBundle\Entity\User", inversedBy="atts2User")
     * @ORM\JoinColumn(name="att2user_user_op", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     * 
     */
    private $userOP;
    
// =============================================================================
// Relacja OneToOne TempUser do Att2User (UserOP)
// =============================================================================
    
    /**
     * Relacja OneToOne TempUser do Att2User (UserOP)
     * 
     * @ORM\OneToOne(targetEntity="Akredytacja\TempUserBundle\Entity\TempUser", inversedBy="att2User")
     * @ORM\JoinColumn(name="att2user_tempuser_op", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    
    private $tempUserOp;
    
// =============================================================================
// Relacja OneToOne Att2User do Attendee
// =============================================================================

    /**
     * Relacja OneToOne Att2User do Attendee
     * 
     * @ORM\OneToOne(targetEntity="Attendee", mappedBy="att2User")
     * 
     */
    private $attendee;

// =============================================================================


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
     * Set typ
     *
     * @param string $typ
     * @return Att2User
     */
    public function setTyp($typ)
    {
        $this->typ = $typ;

        return $this;
    }

    /**
     * Get typ
     *
     * @return string 
     */
    public function getTyp()
    {
        return $this->typ;
    }

    /**
     * Set mail
     *
     * @param string $mail
     * @return Att2User
     */
    public function setMail($mail)
    {
        $this->mail = $mail;

        return $this;
    }

    /**
     * Get mail
     *
     * @return string 
     */
    public function getMail()
    {
        return $this->mail;
    }

    /**
     * Set czyPotwierdzil
     *
     * @param boolean $czyPotwierdzil
     * @return Att2User
     */
    public function setCzyPotwierdzil($czyPotwierdzil)
    {
        $this->czyPotwierdzil = $czyPotwierdzil;

        return $this;
    }

    /**
     * Get czyPotwierdzil
     *
     * @return boolean 
     */
    public function getCzyPotwierdzil()
    {
        return $this->czyPotwierdzil;
    }

    /**
     * Set token
     *
     * @param string $token
     * @return Att2User
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
     * Set user
     *
     * @param \Akredytacja\UsersBundle\Entity\User $user
     * @return Att2User
     */
    public function setUser(\Akredytacja\UsersBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Akredytacja\UsersBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set attendee
     *
     * @param \Akredytacja\AttendeeBundle\Entity\Attendee $attendee
     * @return Att2User
     */
    public function setAttendee(\Akredytacja\AttendeeBundle\Entity\Attendee $attendee = null)
    {
        $this->attendee = $attendee;

        return $this;
    }

    /**
     * Get attendee
     *
     * @return \Akredytacja\AttendeeBundle\Entity\Attendee 
     */
    public function getAttendee()
    {
        return $this->attendee;
    }

    /**
     * Set userOP
     *
     * @param \Akredytacja\UsersBundle\Entity\User $userOP
     * @return Att2User
     */
    public function setUserOP( $userOP = null)
    {
        if( $userOP instanceof \Akredytacja\UsersBundle\Entity\User ) :
            $this->userOP = $userOP;
        elseif( $userOP instanceof \Akredytacja\TempUserBundle\Entity\TempUser ) :
        //else :
            $this->setTempUserOp($userOP);
        endif;

        return $this;
    }

    /**
     * Get userOP
     *
     * @return \Akredytacja\UsersBundle\Entity\User 
     */
    public function getUserOP()
    {
        return $this->userOP;
    }

    /**
     * Set czyUserOPIstnieje
     *
     * @param boolean $czyUserOPIstnieje
     * @return Att2User
     */
    public function setCzyUserOPIstnieje($czyUserOPIstnieje)
    {
        $this->czyUserOPIstnieje = $czyUserOPIstnieje;

        return $this;
    }

    /**
     * Get czyUserOPIstnieje
     *
     * @return boolean 
     */
    public function getCzyUserOPIstnieje()
    {
        return $this->czyUserOPIstnieje;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Att2User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }
    

    /**
     * Set rezygnacja
     *
     * @param boolean $rezygnacja
     * @return Att2User
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
     * Set tempUserOp
     *
     * @param \Akredytacja\TempUserBundle\Enity\TempUser $tempUserOp
     * @return Att2User
     */
    public function setTempUserOp(\Akredytacja\TempUserBundle\Entity\TempUser $tempUserOp = null)
    {
        $this->tempUserOp = $tempUserOp;

        return $this;
    }

    /**
     * Get tempUserOp
     *
     * @return \Akredytacja\TempUserBundle\Enity\TempUser 
     */
    public function getTempUserOp()
    {
        return $this->tempUserOp;
    }
    
    public function setActualUserOP($userOP)
    {
        if ($userOP instanceof \Akredytacja\UserBundle\Entity\User) {
            $this->userOP = $userOP;
        } elseif ($userOP instanceof \Akredytacja\TempUserBundle\Entity\TempUser) {
            $this->userOP = $userOP;
        } 
    }
}
