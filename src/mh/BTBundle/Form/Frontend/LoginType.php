<?php

namespace mh\BTBundle\Form\Frontend;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class LoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', 'text', array(
                'label' => 'Логин или E-mail',
                'required' => true
            ))
            ->add('password', 'password', array(
                'label' => 'Пароль',
                'required' => true,
            ))
        ;
    }

    public function getName()
    {
        return 'login';
    }

     public function getDefaultOptions(array $options)
    {
        $collectionConstraint = new Collection(array(
            'allowExtraFields' => true,
            'fields' => array(
                'email' => array(
                    new NotBlank(array('message' => 'необходимо заполнить')),
                ),

                'password' => array(
                    new NotBlank(array('message' => 'необходимо заполнить')),
                ),
            ),
        ));

        return array(
            'validation_constraint' => $collectionConstraint,
            'csrf_protection' => true,
            'intention'       => $this->getName(),
        );
    }
}
