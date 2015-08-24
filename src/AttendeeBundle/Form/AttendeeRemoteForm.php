<?php

namespace Akredytacja\AttendeeBundle\Form;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Formularz zgłoszenia na konwent
 *
 * @author Mateusz Zachciał
 */
class AttendeeRemoteForm
{
    /**
     * @Assert\NotBlank(message = "Musisz podać swój login lub hasło.")
     * @Assert\Length(
     *      min = 1,
     *      max = 100,
     *      maxMessage = "Login lub hasło nie mogą być dłuższe niż {{ limit }} znaków"
     * )
     */
    protected $loginEmail;
    
    function getLoginEmail() {
        return $this->loginEmail;
    }

    function setLoginEmail($loginEmail) {
        $this->loginEmail = $loginEmail;
    }
    
}


