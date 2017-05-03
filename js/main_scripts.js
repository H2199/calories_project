/*
function reverse_table(table, append_to){
	$.fn.reverse = [].reverse;
	$(table).reverse().appendTo(append_to);
}
	*/		
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
			location.reload(); 
		}
	});
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
			var h_list = "<table id='table_to_reverse'> <tbody>";
			var h_forms = "";
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
					stat.kcal[n] = day_kcal/100;
					h_list += "<tr class='historical_list_day_summary' onclick=\"open_popup('.table_day"+n+"')\"><th>Day: </th><th> " + stat.day[n] + "</th><th> sum: </th><th> " + stat.kcal[n] + " kcal</th></tr>"; //DAY SUMMARY
					day_kcal = 0;
					n++;
				}
				day_kcal = day_kcal+h_data.mass_in_grams[i]*h_data.kcal_density[i];
				if(i == limit-1){
					month = prev_date.getMonth()+1;
					stat.kcal[n] = day_kcal/100;
					day_kcal = 0;
				}
				
				h_list += "<tr class='table_day"+n+"' style='display:none'>"
					+ "<td>" +h_data.food_name[i]+ "</td>"
					+ "<td>" +h_data.mass_in_grams[i]+ "g</td>"
					+ "<td>" +h_data.mass_in_grams[i]*h_data.kcal_density[i]/100+"kcal </td>"
					+ "<td> (" +h_data.kcal_density[i]+ "kcal/100g )</td>"
					+ "<td class='delete_historical_food_eating_img' onclick=\"form_submit('#historical_list_id"+i+"', 'action.php?action=delete_user_historical_food')\">"
					+ 	"<img src='img/cross.png'>"
					+ "</td>"
					+ "</tr>";
				
				h_forms += "<form id='historical_list_id" +i+ "'><input type='hidden' value='" +h_data.historical_food_eating_id[i]+ "' name='historical_food_id'></form>";
			}
			h_list += "<tr class='historical_list_day_summary' onclick=\"open_popup('.table_day"+n+"')\"><th>Day:</th><th> " + stat.day[n] + "</th><th> sum: </th><th> " + stat.kcal[n] + " kcal</th></tr>"; //DAY SUMMARY (end day)
			
			h_list += "</tbody></table>";
			$('#historical_list_of_eatings').html(h_list);
		
			
			$('#historical_list_of_eatings').append(h_forms);
			
			$('#table_to_reverse').html($('tr').get().reverse()); //reverse the table to keep newest on the top
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
		//create_historical_list();
	});
	
	create_historical_list();
	
	
	

}