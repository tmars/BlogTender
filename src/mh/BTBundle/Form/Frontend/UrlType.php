<?php

namespace mh\BTBundle\Form\Frontend;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\NotBlank;

class UrlType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('url', 'url', array(
                'label' => 'Ссылка',
                'required' => true
            ))
        ;
    }

    public function getName()
    {
        return 'url';
    }

     public function getDefaultOptions(array $options)
    {
        $collectionConstraint = new Collection(array(
            'allowExtraFields' => true,
            'fields' => array(
                'url' => array(
                    //new Email(array('message' => 'неверный формат')),
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
