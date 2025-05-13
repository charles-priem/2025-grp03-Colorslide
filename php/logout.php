<?php
	session_start();
	if(isset($_COOKIE["language_preference"])){
		setcookie("language_preference", "", time() - 3600);
	}
	if($_SESSION["user"]){ // si un utilisateur est authentifié (session en cours)
		
		unset($_SESSION["user"]);
		session_destroy();
		
		header("Location: ../index.php");
		exit();
	}
?>