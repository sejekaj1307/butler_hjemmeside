<?php
    session_start(); 
    // Unset all of the session variables.
    $_SESSION = array();

    session_unset();
    // Finally, destroy the session.
    session_destroy();

    // Redirect to logIn page.
    echo "<script> window.location.href = '../index.php'; </script>";
?>