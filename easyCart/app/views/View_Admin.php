<?php
require_once __DIR__ . '/../core/View.php';

class View_Admin_Dashboard extends View
{
    public function toHtml()
    {
        ob_start();
        $this->renderTemplate('admin/dashboard');
        return ob_get_clean();
    }
}

class View_Admin_Orders extends View
{
    public function toHtml()
    {
        ob_start();
        $this->renderTemplate('admin/orders');
        return ob_get_clean();
    }
}

class View_Admin_Products extends View
{
    public function toHtml()
    {
        ob_start();
        $this->renderTemplate('admin/products');
        return ob_get_clean();
    }
}

class View_Admin_ImportExport extends View
{
    public function toHtml()
    {
        ob_start();
        $this->renderTemplate('admin/import_export');
        return ob_get_clean();
    }
}
