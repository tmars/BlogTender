<?php

namespace mh\BTBundle\Form\Frontend;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\MinLength;
use Symfony\Component\Validator\Constraints\MaxLength;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', 'email', array(
                'label' => 'E-mail',
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
        return 'profile_registration';
    }

     public function getDefaultOptions(array $options)
    {
        $collectionConstraint = new Collection(array(
            'allowExtraFields' => true,
            'fields' => array(
                'email' => array(
                    new Email(array('message' => 'неверный формат')),
                    new NotBlank(array('message' => 'необходимо заполнить')),
                ),

                'password' => array(
                    new NotBlank(array('message' => 'необходимо заполнить')),
                    new Regex(array(
                        'message' => 'только латинские буквы и цифры',
                        'pattern' => '/^[0-9a-zA-Z]+$/u'
                    )),
                     new MinLength(array(
                        'limit' => 6,
                        'message' => 'минимальная длина {{ limit }}'
                    )),
                    new MaxLength(array(
                        'limit' => 24,
                        'message' => 'максимальная длина {{ limit }}'
                    )),
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
