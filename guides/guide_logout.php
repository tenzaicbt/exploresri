<?php
session_start();
session_unset();
session_destroy();

header("Location: guide_login.php");
exit;
?>
