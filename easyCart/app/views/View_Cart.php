<?php
require_once __DIR__ . '/../core/View.php';

class View_Cart extends View
{
    public function toHtml()
    {
        ob_start();
        $this->renderTemplate('cart/index');
        return ob_get_clean();
    }
}
