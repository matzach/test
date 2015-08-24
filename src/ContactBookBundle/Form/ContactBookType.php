<?php

namespace ContactBookBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ContactBookType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', 'text', array(
                'label' => 'Fistname',
                'required' => true
            ))
            ->add('lastName', 'text', array(
                'label' => 'Lastname',
                'required' => true
            ))
            ->add('phoneNumber', 'text', array(
                'label' => 'Phone number',
                'required' => false
            ))
            ->add('email', 'email', array(
                'label' => 'Email',
                'required' => false
            ))
            ->add('address', 'text', array(
                'label' => 'Address (street and home no.)',
                'required' => false
            ))
            ->add('city', 'text', array(
                'label' => 'City',
                'required' => false
            ))
            ->add('zip', 'text', array(
                'label' => 'Zip',
                'required' => false
            ))
            ->add('isFriend', 'checkbox', array(
                'label' => 'Is your friend?',
                'required' => false
            ))
            -> add( 'Submit', 'submit', array())
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ContactBookBundle\Entity\ContactBook'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'contactbookbundle_contactbook';
    }
}
