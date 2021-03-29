function Notification(Message, Type) {
	if ($(".pgn")[0]) {
		$('.pgn').remove();
	}

	var position = $('.tab-pane.active .position.active').attr('data-placement');

	$('body').pgNotification({
		style: 'bar',
		message: Message,
		position: position,
		timeout: 3200,
		type: Type
	}).show();
}

function UpdateInputData(InputName, InputValue, CustomMessage) {
	CustomMessage = CustomMessage || 'All settings were successfully saved.';

	var dataString = 'action='+InputName+'&value='+InputValue;

	$.ajax({
		type: "POST",
		url: "requests.php",
		data: dataString,
		cache: false,
		success: function(data){
			if(data) {
                var message = CustomMessage;
                var type = 'success';
			} else {
                var message = data;
                var type = 'danger';
			}

			Notification(message, type);
		}
	});
}

function updateIPLock() {
	var dataString = 'action=UpdateIPLock';
	$.ajax({
		type: "POST",
		url: "requests.php",
		data: dataString,
		cache: false,
		success: function(data){
			Notification('IP Lock is turned: '+data, 'success');
		}
	});
}

function updateMaintenanceMode() {
	var dataString = 'action=UpdateMaintenanceMode';
	$.ajax({
		type: "POST",
		url: "requests.php",
		data: dataString,
		cache: false,
		success: function(data){
			Notification('Maintenance mode is turned: '+data, 'success');
		}
	});
}

function updateRequireFirstName() {
	var dataString = 'action=UpdateRequireFirstName';
	$.ajax({
		type: "POST",
		url: "requests.php",
		data: dataString,
		cache: false,
		success: function(data){
			Notification('Require first name is turned: '+data, 'success');
		}
	});
}

function updateRequireLastName() {
	var dataString = 'action=UpdateRequireLastName';
	$.ajax({
		type: "POST",
		url: "requests.php",
		data: dataString,
		cache: false,
		success: function(data){
			Notification('Require last name is turned: '+data, 'success');
		}
	});
}

function updateRequireSkypeID() {
	var dataString = 'action=UpdateRequireSkypeID';
	$.ajax({
		type: "POST",
		url: "requests.php",
		data: dataString,
		cache: false,
		success: function(data){
			Notification('Require Skype ID is turned: '+data, 'success');
		}
	});
}

function updateRequireInstagramID() {
	var dataString = 'action=UpdateRequireInstagramID';
	$.ajax({
		type: "POST",
		url: "requests.php",
		data: dataString,
		cache: false,
		success: function(data){
			Notification('Require Instagram ID is turned: '+data, 'success');
		}
	});
}

function updateWhitelist() {
	var dataString = 'action=UpdateWhitelist';
	$.ajax({
		type: "POST",
		url: "requests.php",
		data: dataString,
		cache: false,
		success: function(data){
			Notification('Whitelist is turned: '+data, 'success');
		}
	});
}

function takeChatHistory(UserID) {
	var dataString = 'action=TakeChatHistory&user-id='+UserID;
	$('#my-conversation').html('');

	$.ajax({
		type: "POST",
		url: "requests.php",
		data: dataString,
		cache: false,
		success: function(data){
			$('#my-conversation').append(data);
		}
	});

	var secData = 'action=ChatInformation&user-id='+UserID;

	$.ajax({
		type: "POST",
		url: "requests.php",
		data: secData,
		cache: false,
		success: function(data){
			if(data) {
				$('#chat-information').html(data);
			}
		}
	});
}

function sendChat(Chat) {
	var UserID = $('#user_id').val();
	var dataString = 'action=SendChat&user-id='+UserID+'&chat='+Chat;

	$.ajax({
		type: "POST",
		url: "requests.php",
		data: dataString,
		cache: false,
		success: function(data){
			if(data) {
				Notification(data, 'danger');
			}
		}
	});
}

function addAPI() {
	$('#service-api-input').html('<label>Service API</label><input type="text" placeholder="http://reseller.com/api/v2/?key=your_key&action=add&service=service_id&quantity=[QUANTITY]&link=[LINK]" id="ServiceAPI" name="ServiceAPI" class="form-control">');
}

function addOrderAPI() {
	$('#service-order-api-input').html('<label>Service Order API - Checking order status</label><input type="text" placeholder="http://reseller.com/api/v2?key=your_key&action=status&order=[OrderID]" id="ServiceOrderAPI" name="ServiceOrderAPI" class="form-control">');

}

function updateOrderStatusAdmin(OrderID, Status) {
	var dataString = 'action=UpdateOrderStatus&order-id='+OrderID+'&order-status='+Status;

	$.ajax({
		type: "POST",
		url: "requests.php",
		data: dataString,
		cache: false,
		success: function(data){
			if(data) {
				Notification(data, 'danger');
			} else {
				Notification('Order status updated successfully.', 'success');
			}
		}
	});
}
