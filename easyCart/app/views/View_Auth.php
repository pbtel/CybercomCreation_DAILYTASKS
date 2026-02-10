<?php
require_once __DIR__ . '/../core/View.php';

class View_Auth_Login extends View
{
    public function toHtml()
    {
        ob_start();
        $this->renderTemplate('auth/login');
        return ob_get_clean();
    }
}

class View_Auth_Signup extends View
{
    public function toHtml()
    {
        ob_start();
        $this->renderTemplate('auth/signup');
        return ob_get_clean();
    }
}
