<?php

namespace ContactBookBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use ContactBookBundle\Entity\ContactBook;
use ContactBookBundle\Form\ContactBookType;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="main_page")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        
        $contacts = $em->getRepository('ContactBookBundle:ContactBook')->findBy(array(), array('lastName' => 'ASC'));
        return array('contacts' => $contacts);
    }
    
    /**
     * @Route("/create", name="create_contact")
     * @Template()
     */
    public function createAction(Request $request)
    {
        $contact = new ContactBook();
        
        $form = $this -> createForm(new ContactBookType(), $contact);
        $form -> handleRequest($request);
        
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($contact);
            $em->flush();
            
            $this->addFlash(
                'success',
                'Contact was added succesfuly!'
            );
            
            return $this -> redirect($this->generateUrl('main_page', array()));
        }
        
        return array(
            'form' => $form->createView()
        );
    }
    
    /**
     * @Route("/edit/{id}", name="edit_contact",  requirements={"id" = "\d+"})
     * @Template()
     */
    public function editAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $contacts = $em->getRepository('ContactBookBundle:ContactBook')->findById($id);
        
        if (count($contacts) == 0) {
            $this->addFlash(
                'error',
                'Contact doesn\'t exist!'
            );
            return $this -> redirect($this->generateUrl('main_page', array()));
        }
        
        $contact = $contacts[0];
        $form = $this->createForm(new ContactBookType(), $contact);
        $form->handleRequest($request);
        
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($contact);
            $em->flush();
            
            $this->addFlash(
                'success',
                'Contact was edited succesfuly!'
            );
            
            return $this -> redirect($this->generateUrl('main_page', array()));
        }
        
        return array(
            'form' => $form->createView()
        );
    }
    
    /**
     * @Route("/delete/{id}", name="delete_contact",  requirements={"id" = "\d+"})
     * @Template()
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $contacts = $em->getRepository('ContactBookBundle:ContactBook')->findById($id);
        
        if (count($contacts) == 0) {
            $this->addFlash(
                'error',
                'Contact doesn\'t exist!'
            );
            return $this -> redirect($this->generateUrl('main_page', array()));
        }
        
        $contact = $contacts[0];
        
        $em->remove($contact);
        $em->flush();

        $this->addFlash(
            'success',
            'Contact was deleted succesfuly!'
        );

        return $this -> redirect($this->generateUrl('main_page', array()));
    }
    
    /**
     * @Route("/deleteall", name="delete_all_contacts")
     * @Route("/deleteall&{id}", name="delete_all_contacts_id")
     * @Template()
     */
    public function deleteAllAction(Request $request)
    { 
        $idsStr = $request->query->get('id');
        
        $ids = explode('_', $idsStr);
        
        $em = $this->getDoctrine()->getManager();
        
        
        foreach ($ids as $id) {
            $contacts = $em->getRepository('ContactBookBundle:ContactBook')->findById($id);
            if (count($contacts) <> 0) {
                $contact = $contacts[0];
                $em->remove($contact);
                $em->flush();
            }
        }
        
        $this->addFlash(
            'success',
            'All contacts were deleted succesfuly!'
        );

        return $this -> redirect($this->generateUrl('main_page', array()));
    }
    
    
}
