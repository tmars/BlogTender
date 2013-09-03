<?php

namespace mh\BTBundle\Form\Frontend;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('login', 'text', array(
                'label' => 'Логин',
                'required' => true
            ))
            ->add('email', 'email', array(
                'label' => 'Email',
                'required' => true,
            ))
            ->add('name', 'text', array(
                'label' => 'Имя пользователя',
                'required' => true,
            ))
            ->add('foto', 'file', array(
                'label' => 'Фотография',
                'required' => false,
            ))
            ->add('password', 'password', array(
                'label' => 'Пароль',
                'required' => false,
            ))
            ->add('password_repeat', 'password', array(
                'label' => 'Повторите пароль',
                'required' => false,
            ))
            ->add('about', 'textarea', array(
                'label' => 'О себе',
                'required' => false,
            ))
        ;
    }

    public function getName()
    {
        return 'profile';
    }

     public function getDefaultOptions(array $options)
    {
        $collectionConstraint = new Assert\Collection(array(
            'allowExtraFields' => true,
            'fields' => array(
                'email' => array(
                    new Assert\NotBlank(array('message' => 'необходимо заполнить')),
                    new Assert\Email(array(
                        'message' => 'неверный адрес.',
                        'checkMX' => true,
                    ))
                ),
                'login' => array(
                    new Assert\NotBlank(array('message' => 'необходимо заполнить')),
                    new Assert\MaxLength(array(
                        'message' => 'макимум 30 символов.',
                        'limit' => 30,
                    )),
                    new Assert\MinLength(array(
                        'message' => 'минимум 6 символов.',
                        'limit' => 6,
                    )),
                    new Assert\Regex(array(
                        'message' => 'неверный формат.',
                        'pattern' => '/^[a-z0-9][a-z0-9_]+[a-z0-9]$/i',
                    ))
                ),
                'password' => array(
                    new Assert\Regex(array(
                        'message' => 'неверный формат.',
                        'pattern' => '/^[a-z0-9]+$/i',
                    )),
                    new Assert\MinLength(array(
                        'message' => 'минимум 6 символов.',
                        'limit' => 6,
                    )),
                    new Assert\MaxLength(array(
                        'message' => 'максимум 18 символов.',
                        'limit' => 18,
                    )),
                ),
                'name' => array(
                    new Assert\MaxLength(array(
                        'message' => 'макимум 50 символов.',
                        'limit' => 50,
                    )),
                    new Assert\MinLength(array(
                        'message' => 'минимум 6 символов.',
                        'limit' => 6,
                    )),
                    new Assert\Regex(array(
                        'message' => 'неверный формат.',
                        'pattern' => '/^[a-яa-z0-9][a-яa-z0-9 ]+[a-яa-z0-9]$/iu',
                    ))
                ),
                'about' => array(
                    new Assert\MaxLength(array(
                        'message' => 'макимум 300 символов.',
                        'limit' => 300,
                    ))
                ),
                'foto' => array(
                    new Assert\File(array(
                        'maxSize' => '2048k',
                        'mimeTypes' => array(
                            'image/png',
                            'image/jpeg',
                        ),
                        'mimeTypesMessage' => 'выберите изображение в формате JPG или PNG.',
                        'maxSizeMessage' => 'размер изображения не должен превышать 2Мб8.',
                        'notFoundMessage' => 'файл не найден.',
                    ))
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
