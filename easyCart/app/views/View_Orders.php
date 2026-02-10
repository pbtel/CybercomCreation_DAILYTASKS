<?php
require_once __DIR__ . '/../core/View.php';

class View_Order_List extends View
{
    public function toHtml()
    {
        ob_start();
        $this->renderTemplate('orders/index');
        return ob_get_clean();
    }
}

class View_Order_Detail extends View
{
    public function toHtml()
    {
        ob_start();
        $this->renderTemplate('orders/detail');
        return ob_get_clean();
    }
}

class View_Order_Track extends View
{
    public function toHtml()
    {
        ob_start();
        $this->renderTemplate('orders/track');
        return ob_get_clean();
    }
}
