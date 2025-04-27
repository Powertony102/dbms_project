<?php
// clear information in the session
session_start();
session_unset();
session_destroy();
header("Location: login.html");
exit;

?>