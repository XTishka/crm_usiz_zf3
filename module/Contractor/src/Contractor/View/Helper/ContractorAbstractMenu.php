<?php

namespace Contractor\View\Helper;

use Zend\View\Helper\AbstractHelper;

class ContractorAbstractMenu extends AbstractHelper {

    protected $pages = [];

    public function __construct(array $pages) {
        $this->pages = $pages;
    }

    public function __invoke($route, array $params = [], $class = 'contractor-menu') {
        $html = sprintf('<ul class="%s">', $class);
        foreach ($this->pages as $page) {
            $params['contractor'] = $page['value'];
            $href = $this->view->url($route, $params);
            $label = $page['label'];
            $html .= sprintf('<li><a href="%s">%s</a></li>', $href, $label);
        }
        $html .= '</ul>';
        return $html;
    }


}