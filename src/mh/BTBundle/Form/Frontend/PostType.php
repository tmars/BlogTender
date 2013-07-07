<?php

namespace mh\BTBundle\Form\Frontend;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', array(
                'label' => 'Заголовок',
                'required' => true
            ))
            ->add('subtitle', 'textarea', array(
                'label' => 'Подзаголовок',
                'required' => false
            ))
            ->add('image', 'file', array(
                'label' => 'Изображение',
                'required' => false,
            ))
            ->add('pub_flag', 'hidden', array(
                'label' => 'Опубликовать?',
                'data' => '0'
            ))
            ->add('categories', 'entity', array(
                'label' => 'Категории статьи',
                'class' => 'BTBundle:Category',
                'property' => 'label',
                'multiple' => true,
                'required' => true,
                'expanded' => true,
            ))
            ->add('content', 'textarea', array(
                'label' => 'Содержание статьи',
                'required' => true,
                'attr' => array(
                    'class' => 'tinymce',
                    'data-theme' => 'advanced',
                )
            ))
            ->add('tags', 'text', array(
                'label' => 'Тэги',
                'required' => false
            ))
        ;
    }

    public function getName()
    {
        return 'post';
    }

     public function getDefaultOptions(array $options)
    {
        $collectionConstraint = new Assert\Collection(array(
            'allowExtraFields' => true,
            'fields' => array(
                'title' => array(
                    new Assert\NotBlank(array('message' => 'необходимо заполнить')),
                ),
                'content' => array(
                    new Assert\NotBlank(array('message' => 'необходимо заполнить')),
                ),
                'image' => array(
                    new Assert\File(array(
                        'maxSize' => '2048k',
                        'mimeTypes' => array(
                            'image/png',
                            'image/jpeg',
                        ),
                        'mimeTypesMessage' => 'выберите изображение в формате JPG или PNG.',
                        'maxSizeMessage' => 'размер изображения не должен превышать 2Мб.',
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
