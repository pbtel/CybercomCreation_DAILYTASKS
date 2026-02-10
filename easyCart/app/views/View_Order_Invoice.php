<?php
require_once __DIR__ . '/../core/View.php';

class View_Order_Invoice extends View
{
    public function toHtml()
    {
        ob_start();
        $this->renderTemplate('orders/invoice');
        return ob_get_clean();
    }
}
