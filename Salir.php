<?php
  session_start();
  //unset($_SESSION["vgnombre"]);
  //unset($_SESSION["vgusuario"]);
  session_destroy();
  header("Location: /gesman");
  exit;
?>



