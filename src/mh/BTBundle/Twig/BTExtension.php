<?php

namespace mh\BTBundle\Twig;

use Symfony\Component\HttpKernel\KernelInterface;

class BTExtension extends \Twig_Extension
{
    public function __construct()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            'russian_date' => new \Twig_Function_Method($this, 'russianDate'),
        );
    }

    public function russianDate (\DateTime $date, $format = 'j F Y H:i:s')
    {
        $res = $date->format($format);

        if (preg_match('/F/', $format)) {
            $res = strtr($res, array (
                'January' => 'января',
                'February' => 'февраля',
                'March' => 'марта',
                'April' => 'апреля',
                'May' => 'мая',
                'June' => 'июня',
                'July' => 'июля',
                'August' => 'августа',
                'September' => 'сентября',
                'October' => 'октября',
                'November' => 'ноября',
                'December' => 'декабря',
            ));
        }

        if (preg_match('/M/', $format)) {
            $res = strtr($res, array (
                'Jan' => 'Январь',
                'Feb' => 'Февраль',
                'Mar' => 'Март',
                'Apr' => 'Апрель',
                'May' => 'Май',
                'Jun' => 'Июнь',
                'Jul' => 'Июль',
                'Aug' => 'Август',
                'Sep' => 'Сентябрь',
                'Oct' => 'Октябрь',
                'Nov' => 'Ноябрь',
                'Dec' => 'Декабрь',
            ));
        }

        return $res;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'my_bundle';
    }
}