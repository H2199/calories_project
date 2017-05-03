function open_popup(popup){ // takes cottages through ajax query
	$(popup).toggle();	
}

function form_submit(form_div, destination){
	$.ajax({
		type: 'POST',
		url: destination,
		async: false,
		data: $(form_div).serialize(),
		success: function(data){
			$("#answer").html(data);
		}
	});
	location.reload(); 
}
function add_new_food_eatings(){
	
	var food_eatings = "<li class='food_type_li'>"
	+	"<select id='food_type' class='styleSelect' name='food_id[]'>"
	+		"<option value='' selected='selected'>what</option>"
	+		food_list
	+	"</select>"
	+"</li>"
	+"<li class='food_amount_li'>"
	+	"<input id='food_amount' name='food_mass[]' type='text' value=''>g/ml</li>"
	+"<li class='food_time_li'>"
	+	"<input class='food_eating_time' name='food_eating_time[]' type='text' value='when'>"
	+"</li>";
	
	$('#add_food_eatings_ul').append(food_eatings);
	
	$(function() {
		$( ".food_eating_time" ).datepicker({
		showOn: "both",
		buttonImage: "https://savin.fi/img/calendar.gif",
		dateFormat: "yy-mm-dd",
		buttonText: "Calendar",
		buttonImageOnly: true
		});
	});
}
function delete_food_eatings(){
	$('#add_food_eatings_ul > li').slice(-3).remove();

}
 function create_historical_list(){
	$.ajax({
		type: 'POST',
		url: 'action.php?action=get_user_history&user_unique_id='+$('#user_unique_id').html(),
		dataType: "json",
		success: function(data){
			var h_data = data; // food_name, mass_in_grams, date_time, kcal_density, historical_food_eating_id
			var limit = h_data.historical_food_eating_id.length;
			var h_list = "<table>";
			var day_kcal = 0;
			var stat = {'day':{}, 'kcal':{}};
			var n = 0;
			d = new Date(h_data.date_time[0]);
			month = d.getMonth()+1;
			stat.day[0] = d.getUTCDate()+"."+month+"."+d.getFullYear();
			for (var i = 0; i < limit; i++) {

				var prev = i-1;
				var current_date = new Date(h_data.date_time[i]);
				var prev_date = new Date(h_data.date_time[prev]);
				if(current_date > prev_date){
					month = prev_date.getMonth()+1;
					stat.day[n+1] = current_date.getUTCDate()+"."+month+"."+current_date.getFullYear();
					stat.kcal[n] = day_kcal;
					day_kcal = 0;
					n++;
					//alert(JSON.stringify(stat));
				}
				
				day_kcal = day_kcal+h_data.mass_in_grams[i]*h_data.kcal_density[i];
				
				
				h_list += "<tr><td>" +h_data.food_name[i]+ "</td><td>" +h_data.mass_in_grams[i]+ "g</td><td>"+h_data.mass_in_grams[i]*h_data.kcal_density[i]+"kcal (" +h_data.kcal_density[i]+ "kcal/g )</td><td> on " +h_data.date_time[i]+ "</td></tr>";
			}
			h_list += "</table>";
			$('#historical_list_of_eatings').html(h_list);
			
			var daily_report = "<table>";
			
			for(i = 0; i < n; i++){
				daily_report += "<tr><td>Day: " + stat.day[i] + " sum:</td> <td>" + stat.kcal[i] + "kcal</td></tr>";
			}
			daily_report += "</table>";
			//alert(JSON.stringify(stat));
			//alert(daily_report);
			$('#historical_list_of_eatings').append(daily_report);
		}
	});
}

window.onload = function(){
	//gathering information on food
	$.ajax({
		type: 'POST',
		url: 'action.php?action=get_food_info',
		dataType: "json",
		async: false,
		success: function(data){
			var food_info = data; // food_id, food_name, kcal_density, food_status
			var limit = food_info.food_id.length;
			var food_list = "";
			for (var i = 0; i < limit; i++) {
				food_list += "<option value='" + food_info.food_id[i] + "'>" + food_info.food_name[i] + "(" + food_info.kcal_density[i] + " kcal/100g)</option>";
			}	
			window.food_list = food_list;
		}
	});
	
	$('#food_type').append(food_list);
	$('.add_food_button').click(add_new_food_eatings(food_list));
	$('.delete_food_button').click(delete_food_eatings());
	$('.save_food_button').click( function(){
		form_submit('#add_food_eatings', 'action.php?action=add_user_historical_food');
		create_historical_list();
	});
	
	create_historical_list();
	
	
	
	

}