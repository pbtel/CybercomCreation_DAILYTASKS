<?php
// Set a cookie named 'user_preference' to 'dark_mode', expires in 1 hour
setcookie("user_preference", "dark_mode", time() + 3600, "/");

// Check if cookie is set and display its value
if(isset($_COOKIE["user_preference"])) {
    echo "User Preference: " . $_COOKIE["user_preference"];
} else {
    echo "Cookie 'user_preference' is not set yet!";
}
?>
