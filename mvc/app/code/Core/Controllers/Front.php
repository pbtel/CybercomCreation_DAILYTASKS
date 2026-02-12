<?php 
    class Core_Controllers_Front{
        function __construct(){
            $_Admin = new Core_Controller_Admin();
            echo "<pre>";
            print_r($_Admin);
            echo "</pre>";
        }
    }
?>