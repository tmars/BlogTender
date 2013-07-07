<?php

namespace mh\BTBundle\Form\Frontend;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class SearchFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('query', 'text', array(
                'label' => 'Поиск',
                'required' => true
            ))
        ;
    }

    public function getName()
    {
        return 'search_from';
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'csrf_protection' => true,
            'intention'       => $this->getName(),
        );
    }
}
