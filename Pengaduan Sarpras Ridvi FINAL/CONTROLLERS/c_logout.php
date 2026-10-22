<?php
session_start();
session_unset();
session_destroy();

header("Location: ../VIEWS/v_login.php");
exit();
?>