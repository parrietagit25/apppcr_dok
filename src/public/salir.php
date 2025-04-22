<?php
session_start();
session_destroy();
header("Location: login.php"); // Asegúrate de que login.php es la página correcta
exit();
