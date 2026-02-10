<?php
require_once __DIR__ . '/../core/View.php';

class View_Home extends View
{
    public function toHtml()
    {
        ob_start();
        $this->renderTemplate('home/index');
        return ob_get_clean();
    }
}
