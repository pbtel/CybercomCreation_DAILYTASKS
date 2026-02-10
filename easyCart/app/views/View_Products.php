<?php
require_once __DIR__ . '/../core/View.php';

class View_Product_Index extends View
{
    public function toHtml()
    {
        ob_start();
        $this->renderTemplate('products/index');
        return ob_get_clean();
    }
}

class View_Product_Detail extends View
{
    public function toHtml()
    {
        ob_start();
        $this->renderTemplate('products/detail');
        return ob_get_clean();
    }
}
