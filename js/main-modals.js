function TakeFormData(FormID, FormAction, Message, Clear, Timeout) {
	tinyMCE.triggerSave();
	var formData = $(FormID).serialize();
	var dataString = formData+'&action='+FormAction
	Clear = Clear || false;
	Timeout = Timeout || 0;

	if($(FormID+"-result").length === 0) {
		$(FormID).append("<div id='"+FormAction+"-result'></div>");
	} else {
		$(FormID).append("<div id='"+FormAction+"-result'></div>");
		$('#'+FormAction+'-result').remove();
	}

	$.ajax({
		type: "POST",
		url: "modal-requests.php",
		data: dataString,
		cache: false,
		beforeSend: function(){
			$('#'+FormAction + '-result').val('Please wait..');
		},
		success: function(data){
			if(data) {
				$('#'+FormAction + '-result').html('<div class="text-danger">'+data+'</div>');
			} else {
				$('#'+FormAction + '-result').html('<div class="text-success">'+Message+'</div>');
				if(Clear == true) {
					$(FormID).trigger("reset");
					$('select').prop('selectedIndex', 0);
				}
			}

			if(Timeout != 0) {
				$('#'+FormAction + '-result').delay(5000).fadeOut(Timeout, function() {
					this.remove();
				});
			}
		}
	});
}

function CategoryEdit(CategoryID) {
	$.ajax({
		type: "POST",
		url: "modal-requests.php",
		data: 'action=get-category-details&CategoryID='+CategoryID,
		cache: false,
		success: function(data){
			if(data) {
				var obj = jQuery.parseJSON(data);

				$('#EditCategoryID').val(CategoryID);
				$('#EditCategoryName').val(obj.CategoryName);
				$('#EditCategoryDescription').val(obj.CategoryDescription);
				$('#EditCategoryActive').val(obj.CategoryActive);
				$('#EditCategoryActive').html(obj.CategoryActive);
			}
		}
	});

	$("#edit-category-modal").modal();
}

function CategoryDelete() {
	TakeFormData('#save-category', 'delete-category', 'Category was deleted.', false, 1500);
	$('#categories').dataTable()._fnAjaxUpdate();

	$(function () {
		$('#edit-category-modal').modal('toggle');
	});
}

function CategorySave() {
	TakeFormData('#save-category', 'save-category', 'Category was saved successfully.', false, 1500);
	$('#categories').dataTable()._fnAjaxUpdate();
}

function CategoryCreateModal() {
	$("#create-category-modal").modal();
}

function CategoryCreate() {
	TakeFormData('#create-category', 'create-category', 'Category is successfully created.', false, 1500);
	$('#categories').dataTable()._fnAjaxUpdate();
}

function NewsCreateModal() {
	$("#add-news-modal").modal();
}

function NewsDelete() {
	TakeFormData('#save-news', 'delete-news', 'New was deleted.', false, 1500);

	$('#news').dataTable()._fnAjaxUpdate();

	$(function () {
		$('#edit-news-modal').modal('toggle');
	});
}

function NewsSave() {
	TakeFormData('#save-news', 'save-news', 'New was saved successfully.', false, 1500);
	$('#news').dataTable()._fnAjaxUpdate();
}

function NewsEdit(NewsID) {
	$.ajax({
		type: "POST",
		url: "modal-requests.php",
		data: 'action=get-news-details&NewsID='+NewsID,
		cache: false,
		success: function(data){
			if(data) {
				var obj = jQuery.parseJSON(data);

				$('#EditNewID').val(NewsID);
				$('#EditNewTitle').val(obj.NewTitle);
				$('#EditNewIcon').val(obj.NewIcon);
				$('#EditNewContent').val(obj.NewContent);
				tinyMCE.activeEditor.setContent(obj.NewContent);
			}
		}
	});

	$("#edit-news-modal").modal();
}

function NewsAdd() {
	TakeFormData('#add-news', 'add-news', 'New is successfully created.', false, 1500);
	$('#news').dataTable()._fnAjaxUpdate();
}



function ServiceCreateModal() {
	$("#create-service-modal").modal();
}

function ServiceCreate() {
	TakeFormData('#create-service', 'create-service', 'Service is successfully created.', false, 1500);
	$('#services').dataTable()._fnAjaxUpdate();
}

function ServiceEdit(ServiceID) {
	$.ajax({
		type: "POST",
		url: "modal-requests.php",
		data: 'action=get-service-details&ServiceID='+ServiceID,
		cache: false,
		success: function(data){
			if(data) {
				var obj = jQuery.parseJSON(data);

				$('#EditServiceID').val(ServiceID);
				$('#EditServiceName').val(obj.ServiceName);
				$('#EditServiceDescription').val(obj.ServiceDescription);
				$('#EditServiceCategoryID').val(obj.ServiceCategoryID);
				$('#EditServiceCategoryID').html(obj.ServiceCategoryName);
				$('#EditServiceAPI').val(obj.ServiceAPI);
				$('#EditServiceOrderAPI').val(obj.ServiceOrderAPI);
				$('#EditServiceType').val(obj.ServiceType);
				$('#EditServiceType').html(obj.ServiceType);
				$('#EditServicePrice').val(obj.ServicePrice);
				$('#EditServiceMinQuantity').val(obj.ServiceMinQuantity);
				$('#EditServiceMaxQuantity').val(obj.ServiceMaxQuantity);
				$('#EditServiceResellerPrice').val(obj.ServiceResellerPrice);
				$('#EditServiceActive').val(obj.ServiceActive);
				$('#EditServiceActive').html(obj.ServiceActive)
			}
		}
	});

	$("#edit-service-modal").modal();
}

function ServiceSave() {
	TakeFormData('#save-service', 'save-service', 'Service was saved successfully.', false, 1500);
	$('#services').dataTable()._fnAjaxUpdate();
}

function ServiceDelete() {
	TakeFormData('#save-service', 'delete-service', 'Service was deleted.', false, 1500);
	$('#services').dataTable()._fnAjaxUpdate();

	$(function () {
		$('#save-service-modal').modal('toggle');
	});
}

function WhitelistCreateModal() {
	$("#add-whitelist-modal").modal();
}

function WhitelistAdd() {
	TakeFormData('#add-whitelist', 'add-whitelist', 'Targeted IP address was saved successfully whitelisted.', false, 1500);
	$('#whitelist').dataTable()._fnAjaxUpdate();
}

function WhitelistEdit(WhitelistIPID) {
	$.ajax({
		type: "POST",
		url: "modal-requests.php",
		data: 'action=get-whitelist-details&WhitelistIPID='+WhitelistIPID,
		cache: false,
		success: function(data){
			if(data) {
				var obj = jQuery.parseJSON(data);

				$('#EditWhitelistID').val(WhitelistIPID);
				$('#EditWhitelistIPAddress').val(obj.WhitelistIPAddress);
			}
		}
	});

	$("#edit-whitelist-modal").modal();
}

function WhitelistSave() {
	TakeFormData('#save-whitelist', 'save-whitelist', 'Whitelisted IP was saved successfully.', false, 1500);
	$('#whitelist').dataTable()._fnAjaxUpdate();
}

function WhitelistDelete() {
	TakeFormData('#save-whitelist', 'delete-whitelist', 'Whitelisted IP was deleted.', false, 1500);

	$(function () {
		$('#save-whitelist-modal').modal('toggle');
	});

	$('#whitelist').dataTable()._fnAjaxUpdate();
}

function BlacklistCreateModal() {
	$("#add-blacklist-modal").modal();
}

function BlacklistAdd() {
	TakeFormData('#add-blacklist', 'add-blacklist', 'Targeted IP address was saved successfully blacklisted.', false, 1500);
	$('#blacklist').dataTable()._fnAjaxUpdate();
}

function BlacklistEdit(BlacklistID) {
	$.ajax({
		type: "POST",
		url: "modal-requests.php",
		data: 'action=get-blacklist-details&BlacklistID='+BlacklistID,
		cache: false,
		success: function(data){
			if(data) {
				var obj = jQuery.parseJSON(data);

				$('#EditBlacklistID').val(BlacklistID);
				$('#EditBlacklistIP').val(obj.BannedIP);
				$('#EditBlacklistExpireDate').val(obj.BannedExpireDateFormat);
			}
		}
	});

	$("#edit-blacklist-modal").modal();
}

function BlacklistSave() {
	TakeFormData('#save-blacklist', 'save-blacklist', 'Blacklisted IP was saved successfully.', false, 1500);
	$('#blacklist').dataTable()._fnAjaxUpdate();
}

function BlacklistDelete() {
	TakeFormData('#save-blacklist', 'delete-blacklist', 'Blacklisted IP was deleted.', false, 1500);

	$(function () {
		$('#edit-blacklist-modal').modal('toggle');
	});

	$('#blacklist').dataTable()._fnAjaxUpdate();
}

function UserCreateModal() {
	$("#create-user-modal").modal();
}

function UserCreate() {
	TakeFormData('#create-user', 'create-user', 'User is successfully created.', false, 1500);
	$('#users').dataTable()._fnAjaxUpdate();
}

function UserEdit(UserID) {
	$.ajax({
		type: "POST",
		url: "modal-requests.php",
		data: 'action=get-user-details&UserID='+UserID,
		cache: false,
		success: function(data){
			if(data) {
				var obj = jQuery.parseJSON(data);

				$('#EditUserID').val(UserID);
				$('#EditUserName').val(obj.UserName);
				$('#EditUserEmail').val(obj.UserEmail);
				$('#EditUserGroup').val(obj.UserGroup);
				$('#EditUserGroup').html(obj.UserGroup);
				$('#EditUserAPI').val(obj.UserAPI);
				$('#EditUserBalance').val(obj.UserBalance);

				if($("#EditUserFirstName").length == 1) {
					$('#EditUserFirstName').val(obj.UserFirstName);
				}

				if($("#EditUserLastName").length == 1) {
					$('#EditUserLastName').val(obj.UserLastName);
				}

				if($("#EditUserSkypeID").length == 1) {
					$('#EditUserSkypeID').val(obj.UserSkypeID);
				}
			}
		}
	});

	$("#edit-user-modal").modal();
}

function UserSave() {
	TakeFormData('#save-user', 'save-user', 'User was saved successfully.', false, 1500);
	$('#users').dataTable()._fnAjaxUpdate();
}

function UserBan() {
	TakeFormData('#save-user', 'ban-user', 'User was banned.', false, 1500);
	$('#users').dataTable()._fnAjaxUpdate();
}

function UserCreate() {
	TakeFormData('#create-user', 'create-user', 'User was registered.', false, 1500);
	$('#users').dataTable()._fnAjaxUpdate();
}

function UserUnban(UserID) {
	$.ajax({
		type: "POST",
		url: "modal-requests.php",
		data: 'action=unban-user&UserID='+UserID,
		cache: false,
		success: function(data){
			$('#users-banned').dataTable()._fnAjaxUpdate();
		}
	});
}

function UserDelete() {
	TakeFormData('#save-user', 'delete-user', 'User was deleted.', false, 1500);
	$('#services').dataTable()._fnAjaxUpdate();

	$(function () {
		$('#edit-user-modal').modal('toggle');
	});

	$('#users').dataTable()._fnAjaxUpdate();
}


// Individual Prices

function IPCreateModal() {
	$("#add-ip-modal").modal();
}

function IPAdd() {
	TakeFormData('#add-ip', 'add-ip', 'Indivudal price was successfully added.', false, 1500);
	$('#individual-prices').dataTable()._fnAjaxUpdate();
}

function IPEdit(IPID) {
	$.ajax({
		type: "POST",
		url: "modal-requests.php",
		data: 'action=get-ip-details&IPID='+IPID,
		cache: false,
		success: function(data){
			if(data) {
				var obj = jQuery.parseJSON(data);

				$('#EditIPID').val(IPID);
				$('#EditIPUserID').val(obj.IPUserName);
				$('#EditIPServiceID').val(obj.IPServiceName);
				$('#EditIPServiceID').html(obj.IPServiceName);
				$('#EditIPPrice').val(obj.IPPrice);
			}
		}
	});

	$("#edit-ip-modal").modal();
}

function IPSave() {
	TakeFormData('#save-ip', 'save-ip', 'Individual price was saved successfully.', false, 1500);
	$('#individual-prices').dataTable()._fnAjaxUpdate();
}

function IPDelete() {
	TakeFormData('#save-ip', 'delete-ip', 'Individual price was deleted.', false, 1500);

	$(function () {
		$('#edit-ip-modal').modal('toggle');
	});

	$('#individual-prices').dataTable()._fnAjaxUpdate();
}

// Deposits

function DepositDelete(DepositID) {
	$.ajax({
		type: "POST",
		url: "modal-requests.php",
		data: 'action=delete-deposit&DepositID='+DepositID,
		cache: false,
		success: function(data){}
	});

	$('#deposits').dataTable()._fnAjaxUpdate();
}

function DepositUpdate(DepositID, DepositRefunded) {
	$.ajax({
		type: "POST",
		url: "modal-requests.php",
		data: 'action=update-deposit&DepositID='+DepositID+'&DepositRefunded='+DepositRefunded,
		cache: false,
		success: function(data){}
	});

	$('#deposits').dataTable()._fnAjaxUpdate();
}

// FAQ

function FAQCreateModal() {
	$("#add-faq-modal").modal();
}

function FAQCreate() {
	TakeFormData('#add-faq', 'add-faq', 'FAQ was successfully added.', false, 1500);
	$('#faq').dataTable()._fnAjaxUpdate();
}

function FAQEdit(FAQID) {
	$.ajax({
		type: "POST",
		url: "modal-requests.php",
		data: 'action=get-faq-details&FAQID='+FAQID,
		cache: false,
		success: function(data){
			if(data) {
				var obj = jQuery.parseJSON(data);

				$('#EditFAQID').val(FAQID);
				$('#EditFAQQuestion').val(obj.FAQQuestion);
				$('#EditFAQAnswer').val(obj.FAQAnswer);
			}
		}
	});

	$("#edit-faq-modal").modal();
}

function FAQSave() {
	TakeFormData('#save-faq', 'save-faq', 'FAQ was saved successfully.', false, 1500);
	$('#faq').dataTable()._fnAjaxUpdate();
}

function FAQDelete() {
	TakeFormData('#save-faq', 'delete-faq', 'FAQ was deleted.', false, 1500);

	$(function () {
		$('#edit-faq-modal').modal('toggle');
	});

	$('#faq').dataTable()._fnAjaxUpdate();
}
