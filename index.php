<?php
session_start();

require 'privat_info.php';
mysql_con();
require 'functions.php';

$lang = 'en'; //future multilinguistic possible extension

//Recognizing the user OR defining the secret id
if(isset($_GET['user'])){
	$user_unique_id = clean_var($_GET['user']);
}else{
	if(isset($_SESSION['user'])){
		$user_unique_id = clean_var($_SESSION['user']);
		header('location: index.php?user='.$user_unique_id);
	}else{
		$user_unique_id = generate_user_unique_id();
		header('location: index.php?user='.$user_unique_id);
	}
}
if(!user_unique_id_exist($user_unique_id)){
	$ins = "INSERT INTO users (user_unique_id) VALUES ( '$user_unique_id')";
	mysqli_query($lnk, $ins)or die(mysqli_error($lnk));
}



//functions needed
/**
+generate_unique_user_id(); 8 letters
user_unique_id_exist($user_unique_id); //return true/false




Graph generation = JS library = https://developers.google.com/chart/ OR https://plot.ly/javascript/line-charts/
area chart https://google-developers.appspot.com/chart/interactive/docs/gallery/areachart
List&graph update on click (action.php) 

/search of the food
droplist for the activities list

+ add_food
+delete_food
form the list
graph
+add_eating food. (list point as well as writing into BD)

+calendar with the time



**/

?>

<!DOCTYPE html>
<html>
	<head>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
		<script type="text/javascript" src="js/main_scripts.js"></script>
		<link rel="stylesheet" href="css/style.css" type="text/css"/>
		<link rel="stylesheet" type="text/css" href="https://savin.fi/js/jquery-ui.css" />
		<script type="text/javascript" src="https://savin.fi/js/jquery-ui.js"></script>
		

	</head>
	<body>
		<div id="add_food_popup" style="display:none">
			<form id ="add_food_form" autocomplete="on" action="javascript:void(null);" onsubmit="form_submit('#add_food_form', 'action.php?action=add_food')" >
				<p><input id="food_name" name="food_name" type="text" value="NAME"></p>
				<p><input id="food_calories_density" name="food_calories_density" type="text" value="">kcal per 100g</p>
				<input name="user_lang" value="<?php echo $lang;?>" type="hidden">
				<input value="submit" type="submit">
			</form>
		</div>
		<div id="delete_food_popup" style="display:none">
			<form id ="delete_food_form" autocomplete="on" action="javascript:void(null);" onsubmit="form_submit('#delete_food_form', 'action.php?action=delete_food')" >
				<select id="food_type" class="styleSelect" name="food_id">
					<option value="" selected="selected">what</option>
				</select>
				<input value="sumbit" type="submit">
			</form>
		</div>
	
	
		<div class="wrapper">
			<div class="main_menu">
				<ul>
					<li onclick ="open_popup('#add_food_popup')">Add food</li>
					<li onclick ="open_popup('#delete_food_popup')">Delete food</li>
					<li>Your unique link: http://savin.fi/calories_project/index.php?user=<div id="user_unique_id"><?php echo $user_unique_id; ?></div></li>
				</ul>
			</div>
			<div class="content">
				<div class="left_part">
					<h1>Hello, my darling.</h1>
					<p>What did you eat today?</p>
					<form id="add_food_eatings" autocomplete="on">
						<ul id="add_food_eatings_ul">
						
						</ul>
						<input name="user_unique_id" value="<?php echo $user_unique_id;?>" type="hidden">
					</form>
					<div class="add_food_button" onclick="add_new_food_eatings()"><img src="img/plus.png"></div>
					<div class="delete_food_button" onclick="delete_food_eatings()"><img src="img/minus.png"></div>
					<div class="save_food_button" onclick="form_submit('#add_food_eatings', 'action.php?action=add_user_historical_food')">Add the food</div>
				</div>
				<div class="right_part">
					<?php //<div class="privacy_setting">on/off</div> ?>
					<?php // <div id="historical_graph"></div> ?>
					<div class="historical_graph_average"></div>
					<div id="historical_list_of_eatings">
						
					</div>
				</div>
			</div>
		</div>
	</body>
</html>
