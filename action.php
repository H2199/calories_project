<?php
	require 'privat_info.php';
	mysql_con();
	require 'functions.php';

	switch($_GET['action']){
		case 'get_user_history':
			$user_unique_id = clean_var($_GET['user_unique_id']);
			$q = mysqli_query($lnk, "
			SELECT
				translation.name AS food_name,
				food_eating.mass_in_grams AS mass_in_grams,
				food_eating.date_time AS date_time,
				food.kcal_density AS kcal_density,
				food_eating.id AS historical_food_eating_id
			FROM
				users,
				food_eating,
				translation,
				food,
				lang_list
			WHERE
				users.user_unique_id = '$user_unique_id' AND
				users.id = food_eating.user_id AND
				food_eating.food_id = food.id AND
				food.status = 'active' AND
				food.id = translation.item_id AND
				translation.type = 'food' AND
				translation.lang_id = lang_list.id AND
				lang_list.shortcut = 'en'
			ORDER BY food_eating.date_time")
			or die("Invalid query: " . mysqli_error($lnk));
			while ($arr = mysqli_fetch_array($q)) {
				$user_history['food_name'][] = $arr['food_name'];
				$user_history['mass_in_grams'][] = $arr['mass_in_grams'];
				$user_history['date_time'][] = $arr['date_time'];
				$user_history['kcal_density'][] = $arr['kcal_density'];
				$user_history['historical_food_eating_id'][] = $arr['historical_food_eating_id'];
			}
			echo json_encode($user_history);
		break;
		
		case 'get_food_info':
			$q = mysqli_query($lnk, "
			SELECT
				food.id AS food_id,
				translation.name AS food_name,
				food.kcal_density AS kcal_density,
				food.status AS food_status
			FROM
				translation,
				food,
				lang_list
			WHERE
				food.id = translation.item_id AND
				translation.type = 'food' AND
				translation.lang_id = lang_list.id AND
				lang_list.shortcut = 'en'")
			or die("Invalid query: " . mysqli_error($lnk));//?NOT EMPTY
			while ($arr = mysqli_fetch_array($q)) {
				$food_info['food_id'][] = $arr['food_id'];
				$food_info['food_name'][] = $arr['food_name'];
				$food_info['kcal_density'][] = $arr['kcal_density'];
				$food_info['food_status'][] = $arr['food_status'];
			}
			echo json_encode($food_info);
		break;
		
		case 'add_food':
			$food_name = clean_var($_POST['food_name']);
			$food_calories_density = clean_var($_POST['food_calories_density']);
			$user_lang = clean_var($_POST['user_lang']);
			
			$ins1 = "
			INSERT
				INTO `food` (kcal_density, status)
				VALUES('$food_calories_density', 'active')";
			$ins2 = "
			INSERT
				INTO `translation` (item_id, name, type, lang_id) 
				VALUES(LAST_INSERT_ID(),'$food_name', 'food', (SELECT id FROM lang_list WHERE shortcut = '$user_lang'))";
			mysqli_query($lnk, $ins1)or die(mysqli_error($lnk));
			mysqli_query($lnk, $ins2)or die(mysqli_error($lnk));
			echo "GREAT SUCCESS (add_food)";
		break;
		
		case 'delete_food':
			$food_id = clean_var($_POST['food_id']);
			$del1 = "DELETE FROM food WHERE food.id = '$food_id'"; 
			$del2 = "DELETE FROM translation WHERE translation.item_id = '$food_id'"; 
			$del3 = "DELETE FROM food_eating WHERE food_eating.food_id = '$food_id'"; 
			mysqli_query($lnk,$del1)or die(mysqli_error($lnk));
			mysqli_query($lnk,$del2)or die(mysqli_error($lnk));
			mysqli_query($lnk,$del3)or die(mysqli_error($lnk));
			echo "GREAT SUCCESS (delete_food)";
		break;
		
		case 'delete_user_historical_food':
			$historical_food_eating_id = clean_var($_POST['historical_food_id']);
			
			$del = "
			DELETE FROM food_eating WHERE food_eating.id = $historical_food_eating_id"; 
			mysqli_query($lnk,$del)or die(mysqli_error($lnk));
			echo "GREAT SUCCESS (delete_historical_food_eating)";
		break;
		
		case 'add_user_historical_food':
			//print_r($_POST);	
			$user_unique_id = clean_var($_POST['user_unique_id']);
			$q = mysqli_query($lnk, "SELECT id FROM users WHERE user_unique_id = '$user_unique_id'")or die(mysqli_error($lnk));
			$arr = mysqli_fetch_array($q);
			$user_id = $arr[0];
			
			$limit = count($_POST['food_id']);
			for($i = 0; $i<$limit; $i++){
				$user_id = clean_var($user_id);
				$food_id = clean_var($_POST['food_id'][$i]);
				$mass = clean_var($_POST['food_mass'][$i]);
				$date_time = clean_var($_POST['food_eating_time'])[$i];
				$ins = "
				INSERT 
					INTO food_eating (food_id, user_id, mass_in_grams, date_time) 
					VALUES ('$food_id', '$user_id', '$mass', '$date_time')";
				mysqli_query($lnk, $ins)or die(mysqli_error($lnk));
			}
			echo "GREAT SUCCESS (add eating food)";
		break;
	}
?>