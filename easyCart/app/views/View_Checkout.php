<?php
require_once __DIR__ . '/../core/View.php';

class View_Checkout extends View
{
    public function toHtml()
    {
        ob_start();
        $this->renderTemplate('checkout/index');
        return ob_get_clean();
    }
}
