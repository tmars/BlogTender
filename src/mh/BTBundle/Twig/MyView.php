<?php

/*
 * This file is part of the Pagerfanta package.
 *
 * (c) Pablo D?ez <pablodip@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace mh\BTBundle\Twig;

use Pagerfanta\PagerfantaInterface;
use Pagerfanta\View\ViewInterface;

/**
 * DefaultInterface.
 *
 * @author Pablo D?ez <pablodip@gmail.com>
 *
 * @api
 */
class MyView implements ViewInterface
{
    /**
     * {@inheritdoc}
     */
    public function render(PagerfantaInterface $pagerfanta, $routeGenerator, array $options = array())
    {
        $options = array_merge(array(
            'proximity'          => 3,
            'css_dots_class'     => 'dots',
        ), $options);

        $currentPage = $pagerfanta->getCurrentPage();

        $startPage = $currentPage - $options['proximity'];
        $endPage = $currentPage + $options['proximity'];

        if ($pagerfanta->getNbPages() > 1) {

            if ($startPage < 1) {
                $endPage = min($endPage + (1 - $startPage), $pagerfanta->getNbPages());
                $startPage = 1;
            }
            if ($endPage > $pagerfanta->getNbPages()) {
                $startPage = max($startPage - ($endPage - $pagerfanta->getNbPages()), 1);
                $endPage = $pagerfanta->getNbPages();
            }

            $pages = array();

            // previous
            /*if ($pagerfanta->hasPreviousPage()) {
                $pages[] = array($pagerfanta->getPreviousPage(), $options['previous_message']);
            } else {
                $pages[] = sprintf('<span class="">%s</span>', $options['css_disabled_class'], $options['previous_message']);
            }*/
            //$pages[] = sprintf('<li class="pagination-start"><a>&#171;</a></li>');
            //$pages[] = sprintf('<li class="pagination-prev"><a>&#8249;</a></li>');

            // first
            if ($startPage > 1) {
                $pages[] = array(1, 1);
                if (3 == $startPage) {
                    $pages[] = array(2, 2);
                } elseif (2 != $startPage) {
                    $pages[] = sprintf('<span class="%s">...</span>', $options['css_dots_class']);
                }
            }

            // pages
            for ($page = $startPage; $page <= $endPage; $page++) {
                if ($page == $currentPage) {
                    $pages[] = sprintf('<li><span>%s</span></li>', $page);
                } else {
                    $pages[] = array($page, $page);
                }
            }

            // last
            if ($pagerfanta->getNbPages() > $endPage) {
                if ($pagerfanta->getNbPages() > ($endPage + 1)) {
                    if ($pagerfanta->getNbPages() > ($endPage + 2)) {
                        $pages[] = sprintf('<span class="%s">...</span>', $options['css_dots_class']);
                    } else {
                        $pages[] = array($endPage + 1, $endPage + 1);
                    }
                }

                $pages[] = array($pagerfanta->getNbPages(), $pagerfanta->getNbPages());
            }

            // next
            /*if ($pagerfanta->hasNextPage()) {
                $pages[] = array($pagerfanta->getNextPage(), $options['next_message']);
            } else {
                $pages[] = sprintf('<span class="%s">%s</span>', $options['css_disabled_class'], $options['next_message']);
            }*/
            //$pages[] = sprintf('<li class="pagination-next"><a>&#8250;</a></li>');
            //$pages[] = sprintf('<li class="pagination-end"><a>&#187;</a></li>');


            // process
            $pagesHtml = '';
            foreach ($pages as $page) {
                if (is_string($page)) {
                    $pagesHtml .= $page;
                } else {
                    $pagesHtml .= sprintf('<li><a href="?page=%s">%s</a></li>', $page[0], $page[1]);
                }
            }

            return '<div class="pagination"><ul>'.$pagesHtml.'</ul></div>';
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'default';
    }
}