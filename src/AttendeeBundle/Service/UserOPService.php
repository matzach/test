<?php
namespace Akredytacja\AttendeeBundle\Service;

use Akredytacja\MainBundle\Service\MainService;
//use Doctrine\ORM\EntityManager;
use \Akredytacja\AttendeeBundle\Entity\Att2User;


/**
 * Serwis obsługujący akcje z UserOP
 *
 * @author Mateusz Zachciał
 */
class UserOPService extends MainService
{
    
    /**
     * Obsługuje rezygnacje z UseraOP
     * 
     * @param \Akredytacja\AttendeeBundle\Entity\Attendee $attendee
     */
    public function resignation(\Akredytacja\AttendeeBundle\Entity\Attendee $attendee)
    {
        $att2User = $attendee->getAtt2User();
        if ($att2User != null) {
            $attendee->setAtt2User(null);
            $attendee->setZgloszePozniej(true);
            $att2User->setActualUserOP(null);
            $this->em->persist($attendee);
            $this->em->remove($att2User);
            $this->em->flush();
        }
    }
    
    public function setUserOP(Att2User $att2User, $attendeeForm, $user) 
    {
        
    }
    
}
