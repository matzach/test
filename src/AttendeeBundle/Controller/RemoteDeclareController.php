<?php

namespace Akredytacja\AttendeeBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Akredytacja\AttendeeBundle\Entity\Attendee;
use Akredytacja\AttendeeBundle\Form\AttendeeRemoteForm;
use Akredytacja\AttendeeBundle\Form\AttendeeRemoteFormType;

/**
 * Zgłoszenia na eventy przez iframe
 *
 * @author Mateusz Zachciał
 */
class RemoteDeclareController extends \Akredytacja\MainBundle\Controller\MainController
{
    /**
     * @param Request $request
     */
    public function remoteDeclareAction(Request $request, $token = null)
    {
        $message = null;
        if ($token == null) {
            $message = 'Niestety wystąpił nieoczekiwany błąd.';
            $class = 'alert';
            return $this->message($message, $class, $event);
        }
        
        $em = $this->getDoctrine()->getManager();
        $eventArray = $em->getRepository('AkredytacjaEventBundle:Event')->findByToken($token);
        
        if ($eventArray == null) {
            $message = 'Niestety wystąpił nieoczekiwany błąd.';
            $class = 'alert';
            return $this->message($message, $class, $event);
        }
        
        $event = $eventArray[0];
        
        if ($event->czyOdbylSie()) {
            $message = 'Konwent się już odbył.';
            $class = 'warning';
            return $this->message($message, $class, $event);
        }
        
        $attendeeForm = new AttendeeRemoteForm();
        $form = $this->createForm(new AttendeeRemoteFormType(), $attendeeForm);
        $form->add('save', 'submit', 
                    array(
                            'label' => 'Zgłaszam się',
                            'attr'  => array('class' => 'tiny iframe-button'),
                        )
                );
        
        $form->handleRequest($request);
        
        if ($form->isValid())
        {
            $loginEmail = $attendeeForm->getLoginEmail();
            if (filter_var($loginEmail, FILTER_VALIDATE_EMAIL)) {
                $userArray = $em->getRepository('AkredytacjaUsersBundle:User')->findByEmail($loginEmail);
            } else {
                $userArray = $em->getRepository('AkredytacjaUsersBundle:User')->findByUsername($loginEmail);
            }
            
            if ($userArray == null) {
                return $this->registerMessage($event);
            }
            
            $user = $userArray[0];
            
            $attendeeTest = $em->getRepository('AkredytacjaAttendeeBundle:Attendee')->findAttendeeByEvent($user->getId(), $event->getId());
            
            if ($attendeeTest != null) {
                $message = 'Już zgłosiłeś się na ten konwent.';
                $class = 'warning';
                return $this->message($message, $class, $event);
            }
            
            $attendee = new Attendee();$noce = array();
            $noce[] = 'all';
            $attendee->setNoce($noce);
            $attendee->setCzyOplacilSkladke(false);
            $attendee->setWysokoscOplaconejSkladki(0);
            $attendee->setWybraneSkladkiDodatkowe(array());
            $attendee->setCzyPotwierdzilZgloszenie(true);
            $attendee->setRezygnacja(false);
            $attendee->setToken($this->generujToken($user));
            $attendee->setDataZgloszenia(new \DateTime('now'));
            $attendee->setCzyPotwierdzilZgloszenie(true);
            $attendee->setZgloszePozniej(true);
            $attendee->setEvent($event);
            $attendee->setUser($user);
            $attendee->setRokUrodzenia($user->getRokUrodzenia());
            
            $em->persist($attendee);
            
            $user->addAttendee($attendee);
            $event->addAttendee($attendee);
            $em->persist($user);
            $em->persist($event);            
            $em->flush();
            
            $subjectAtt = 'SNAILBook - zgłoszenie na konwent '. $event->getNazwa();
            $templateAtt= 'AkredytacjaAttendeeBundle:Remote:remoteAttMail.html.twig';
            $optionsAtt = array(
                'user'  => $user,
                'event' => $event,
            );
            $this->mainSendMail($subjectAtt, $user->getEmail(), $templateAtt, $optionsAtt);
            
            //Mail do orgów o nowym uczestniku
            $mailer = $this->get('mymailer');
            $subject = 'SNAILBook - zgłoszenie uczestnika na twój konwent';
            $template = 'AkredytacjaMainBundle:Emails:attAdded.html.twig';
            $options = array( 
                                'event'     => $event,
                                'user'      => $user,
                            );
            $mailer->mailToOrgs($subject, $template, $this, $options);

            
            /**
             * @todo Wysłać email do orgów
             */
            $message = 'Zostałeś zgłoszony na konwent.';
            $class = 'success';
            return $this->message($message, $class, $event);
        }
        
        
        return $this->render('AkredytacjaAttendeeBundle:Remote:remoteTemplate.html.twig', array(
            'form'  => $form->createView(),
            'event' => $event,
        ));
    }
    
    public function iframeDemoAction()
    {
        $attendeeForm = new AttendeeRemoteForm();
        $form = $this->createForm(new AttendeeRemoteFormType(), $attendeeForm);
        $form->add('save', 'submit', 
                    array(
                            'label' => 'Zgłaszam się',
                            'attr'  => array('class' => 'tiny iframe-button'),
                        )
                );
        
        $event = new \stdClass();
        $event->nazwa = 'Demo wtyczki społecznościowej';
        $event->imgFile = '/uploads/pp/20150220/14244638472124739.png';
        
        return $this->render('AkredytacjaAttendeeBundle:Remote:remoteTemplate.html.twig', array(
            'form'  => $form->createView(),
            'event' => $event,
        ));
    }
    
    protected function message($message, $class, $event)
    {
        return $this->render('AkredytacjaAttendeeBundle:Remote:remoteMessageTemplate.html.twig', array(
            'class'     => $class,
            'message'   => $message,
            'event' => $event,
        ));
    }
    
    protected function registerMessage($event)
    {
        return $this->render('AkredytacjaAttendeeBundle:Remote:remoteRegisterTemplate.html.twig', array(
            'event' => $event,
        ));
    }
}
