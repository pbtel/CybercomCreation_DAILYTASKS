<?php
require_once __DIR__ . '/../core/View.php';

class View_User_Dashboard extends View
{
    public function toHtml()
    {
        ob_start();
        $this->renderTemplate('user/dashboard');
        return ob_get_clean();
    }
}
