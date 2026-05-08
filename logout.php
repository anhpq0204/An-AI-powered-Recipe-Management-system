<?php
require_once('includes/session.php');
session_unset();
session_destroy();
header('location:index.php');
exit;
