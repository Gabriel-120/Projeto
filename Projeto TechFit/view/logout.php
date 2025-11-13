<?php
session_start();

// Destruir a sessão
session_destroy();

// Redirecionar para Login
header('Location: Login.php');
exit;
