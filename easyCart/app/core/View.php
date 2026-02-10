<?php
abstract class View
{
    protected $data;

    public function __construct($data = [])
    {
        $this->data = $data;
    }

    /**
     * Convert the view to HTML
     * @return string
     */
    abstract public function toHtml();

    /**
     * Include a template file
     * @param string $templatePath Relative path to template file from app/templates/
     * @return void
     */
    protected function renderTemplate($templatePath)
    {
        // Global data available to all templates
        $globalData = [
            'cartCount' => Session::getCartCount(),
            'currentUser' => Session::get('user', ['logged_in' => false, 'name' => 'Guest'])
        ];

        extract(array_merge($globalData, $this->data));
        require __DIR__ . '/../templates/' . $templatePath . '.php';
    }

    /**
     * Get output of a template file as string
     * @param string $templatePath
     * @return string
     */
    protected function getTemplateContent($templatePath)
    {
        ob_start();
        $this->renderTemplate($templatePath);
        return ob_get_clean();
    }
}
