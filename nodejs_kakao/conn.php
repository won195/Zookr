<?php
  @session_start();

  $db_host = "localhost";
	$db_user = "root";
	$db_passwd = "";
	$db_name = "kakaotalk";

	$db_link = mysqli_connect($db_host, $db_user, $db_passwd); // 데이터베이스 연결
	mysqli_select_db($db_link, $db_name); //내부 database 선택
?>