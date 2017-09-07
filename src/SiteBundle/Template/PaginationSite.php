<?php

namespace SiteBundle\Template;


use Pagerfanta\View\Template\DefaultTemplate;

class PaginationSite extends DefaultTemplate
{
    static protected $defaultOptions = array(
        'previous_message' => 'Anterior',
        'next_message' => 'Próximo',
        'css_disabled_class' => 'hide',
        'css_dots_class' => 'dots',
        'css_current_class' => 'current',
        'dots_text' => '...',
        'container_template' => '%pages%',
        'page_template' => '<a href="%href%"%rel% class="navlinks">%text%</a>',
        'span_template' => '<a href="javascript:void(0)" class="%class% navlinks">%text%</a>',
        'rel_previous' => 'prev',
        'rel_next' => 'next'
    );
}