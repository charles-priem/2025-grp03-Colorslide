<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
    <link rel="stylesheet" href="../css/styles.css">
	<title>Contact</title>
</head>
<body>
    <header>   
		<?php require_once "header1.php"; ?>
    </header>
<main id="contact">
<form action="#" method="post"  class="formletter">
	
	<fieldset class="Informations_sur_vous">  
		<legend>Information</legend>
		
		<label>Gender:</label>
		<input type="radio" name="genre" id="mme" value="mme">  
		<label for="mme">Mrs.</label>
		<input type="radio" name="genre" id="mr" value="mr"> 
		<label for="mr">Mr.</label>

		<br><br> 
		
		<label for="nom">Last name:</label>
		<input type="text" name="nom" id="nom" placeholder="Your last name">
		<br><br>
		
		<label for="prenom">First name:</label>
		<input type="text" name="prenom" id="prenom" placeholder="Your first name">
		<br><br>
		
		<label for="courriel">Email:</label>
		<input type="email" name="courriel" id="courriel" placeholder="Email">
	</fieldset>
</form>

<br><br>

<form action="#" method="post" enctype="multipart/form-data">
	<fieldset class="Votre_demande">
		<legend>Your request</legend>
		
		<label for="objet">Subject of the message:</label>
		<select id="objet" name="objet">
			<option value="0">- Select -</option>
			<option value="sugg">Suggestions</option>
			<option value="recl">Complaints</option>
			<option value="insc">Registration</option>
			<option value="deff">Failure</option>
			<option value="prob">Technical problems</option>
		</select>
		<br><br>
		
		<label for="description">Message:</label><br>
		<textarea rows="10" cols="50" name="description" id="description" maxlength="200"></textarea>
		<br><br>
		
		<label for="document" class="custom-file-upload">Choose a file</label>
        <input type="file" id="document" name="document" class="hidden-file">

		<br><br>

		<input type="submit" value="Send" id="envoyer">
		<input type="reset" value="Reset">
	</fieldset>
    
</form>
</main>
<footer>
    <?php include 'footer.php'; ?>
</footer>
</body>
</html>
