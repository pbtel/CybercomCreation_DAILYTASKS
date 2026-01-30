<?php
// Set a cookie for last viewed product, expires in 2 hours
setcookie("last_product", "Wireless Mouse", time() + 7200, "/");

// Check if cookie is set
if(isset($_COOKIE["last_product"])) {
    echo "Last Viewed Product: " . $_COOKIE["last_product"];
} else {
    echo "No product viewed recently!";
}

setcookie("last_product", "", time()-3600, "/"); //used for deleting cookie 

session_start();

$_SESSION["username"] = "Pooja_Patel";
echo "session is start and user name is" . $_SESSION["username"];
echo "welcome to website";
?>
