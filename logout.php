<?php

session_start(); //initialise la session
session_unset(); //desactive la session
setcookie('Id', '', time()-1);
setcookie('Email', '', time()-1);

header('location: index.php');
exit();

?>