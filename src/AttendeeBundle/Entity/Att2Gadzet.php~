<?php

namespace Akredytacja\AttendeeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
//use Doctrine\Common\Collections\ArrayCollection;

/**
 * Att2Gadzet
 * Łączy uczestnika z gadżetem
 *
 * @ORM\Table(name="att2gadzet")
 * @ORM\Entity(repositoryClass="Akredytacja\AttendeeBundle\Entity\Att2GadzetRepository")
 */
class Att2Gadzet
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
     * @var integer
     *
     * @ORM\Column(name="ilosc", type="integer", nullable=true)
     */
    private $ilosc;

    /**
     * @var string
     *
     * @ORM\Column(name="uwagi", type="string", length=500, nullable=true)
     */
    private $uwagi;
    
    
    /**
     * @var string
     * 
     * @ORM\Column(name="nazwa", type="string", length=500, nullable=true)    
     */
    private $nazwa;
    
// =============================================================================
// Relacja Many To Many do Attendee
// =============================================================================

    /**
     * 
     * @ORM\ManyToOne(targetEntity="Attendee", inversedBy="att2Gadzet")
     * @ORM\JoinColumn(name="attendee_att2gadzet", referencedColumnName="id") 
     */ 
    private $attendee;
    
// =============================================================================
// Relacja Many To One do Gadzet
// =============================================================================
    
    /**
     * 
     * @ORM\ManyToOne(targetEntity="\Akredytacja\EventBundle\Entity\Gadzet", inversedBy="att2Gadzet")
     * @ORM\JoinColumn(name="att2gadzet_gadzet", referencedColumnName="id")
     * 
     */
    private $gadzet;
    
// =============================================================================

    public function __construct()
    {
        //$this -> attendee = new ArrayCollection();
        //$this -> gadzets = new ArrayCollection();
    }
    
    public static function create()
    {
        $instance = new self();
        return $instance;
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
     * Set ilosc
     *
     * @param integer $ilosc
     * @return Att2Gadzet
     */
    public function setIlosc($ilosc)
    {
        $this->ilosc = $ilosc;

        return $this;
    }

    /**
     * Get ilosc
     *
     * @return integer 
     */
    public function getIlosc()
    {
        return $this->ilosc;
    }

    /**
     * Set uwagi
     *
     * @param string $uwagi
     * @return Att2Gadzet
     */
    public function setUwagi($uwagi)
    {
        $this->uwagi = $uwagi;

        return $this;
    }

    /**
     * Get uwagi
     *
     * @return string 
     */
    public function getUwagi()
    {
        return $this->uwagi;
    }

    /**
     * Set gadzet
     *
     * @param \Akredytacja\EventBundle\Entity\Gadzet $gadzet
     * @return Att2Gadzet
     */
    public function setGadzet(\Akredytacja\EventBundle\Entity\Gadzet $gadzet = null)
    {
        $this->gadzet = $gadzet;

        return $this;
    }

    /**
     * Get gadzet
     *
     * @return \Akredytacja\EventBundle\Entity\Gadzet 
     */
    public function getGadzet()
    {
        return $this->gadzet;
    }

   

    /**
     * Set attendee
     *
     * @param \Akredytacja\AttendeeBundle\Entity\Attendee $attendee
     * @return Att2Gadzet
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
     * Set nazwa
     *
     * @param string $nazwa
     * @return Att2Gadzet
     */
    public function setNazwa($nazwa)
    {
        $this->nazwa = $nazwa;

        return $this;
    }

    /**
     * Get nazwa
     *
     * @return string 
     */
    public function getNazwa()
    {
        return $this->nazwa;
    }
}
