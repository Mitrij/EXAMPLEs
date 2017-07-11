
function getFileName()
{
	//this gets the full url
	var url = document.location.href;
	//this removes the anchor at the end, if there is one
	url = url.substring(0, (url.indexOf("#") == -1) ? url.length : url.indexOf("#"));
	//this removes the query after the file name, if there is one
	url = url.substring(0, (url.indexOf("?") == -1) ? url.length : url.indexOf("?"));
	//this removes everything before the last slash in the path
	url = url.substring(url.lastIndexOf("/") + 1, url.length);
	//return
	return url;
}

function parseQueryStrParam(val)
{
	var result = "Not found",
	tmp = [];
	location.search
	//.replace ( "?", "" ) 
	// this is better, there might be a question mark inside
	.substr(1)
	.split("&")
	.forEach(function (item) {
	tmp = item.split("=");
	if (tmp[0] === val) result = decodeURIComponent(tmp[1]);
	});
	return result;
}
	
$(function()
{
	var jsDebugOn = 1;

	function print_msg(el, type, msg)
	{
		var htmlT = "";
		var tt = "";
		
		htmlT = '<div class="alert ';
		if("success" == type)
		{
			htmlT += 'alert-success';
			tt = 'Success';
		}
		else if("warning" == type)
		{
			htmlT += 'alert-warning';
			tt = 'Warning';
		}
		else
		{
			htmlT += 'alert-danger';
			tt = 'Error';
		}
		htmlT += '" style="padding: 5;">';
		htmlT += '<strong>' + tt + ': </strong>';
		htmlT += msg;
		htmlT += '</div>';
		htmlT += '<br />';
		
		if(el.length > 0)
		{
			$("#" + el).html(htmlT);
		}
	}
	

	
	
	// --- pn_login ----------------------------------------------------------
	$('#e_login_form').on('submit', a_login_form);

	function login_form_upd_status(l_msg, p_msg, c_msg, e_msg)
	{
		l_msg = typeof l_msg !== 'undefined' ?  l_msg : "";
		p_msg = typeof p_msg !== 'undefined' ?  p_msg : "";
		c_msg = typeof c_msg !== 'undefined' ?  c_msg : "";
		e_msg = typeof e_msg !== 'undefined' ?  e_msg : "";

		if(jsDebugOn)
		{
			console.log("login_form_upd_status()");
		}
				
		var msg1a = "";
		var msg1b = "";
		var msg2 = "";
		var msg = "";

		msg1a = '<div class="alert alert-warning" style="padding: 5;">' + '<strong>Error: </strong>';
		msg1b = '<div class="alert alert-danger" style="padding: 5;">' + '<strong>Error: </strong>';
		msg2 = '</div>' + '<br />';
		
		if(l_msg.length > 0)
		{
			msg = msg1a + l_msg + msg2;
		}
		else
		{
			msg = "";
		}
		$("#l_login_err_msg").html(msg);
		
		if(p_msg.length > 0)
		{
			msg = msg1a + p_msg + msg2;
		}
		else
		{
			msg = "";
		}
		$("#l_password_err_msg").html(msg);
		
		if(c_msg.length > 0)
		{
			msg = msg1a + c_msg + msg2;
		}
		else
		{
			msg = "";
		}
		$("#l_captcha_err_msg").html(msg);
		
		if(e_msg.length > 0)
		{
			msg = msg1b + e_msg + msg2;
		}
		else
		{
			msg = "";
		}
		$("#l_err_msg").html(msg);
	}
	
	function a_login_form(event)
	{
		event.stopPropagation(); // Stop stuff happening
		event.preventDefault(); // Totally stop stuff happening
		
		var formData = new FormData();    
		
		formData.append("u_login", $("#u_login").val());
		formData.append("u_pass", $("#u_pass").val());
		formData.append("u_cpatcha", $("#u_cpatcha").val());
				
		$.ajax(
		{
			url: 'a_login.php?l_action=login',
			type: 'POST',
			data: formData,
			cache: false,
			dataType: 'json',
			processData: false, // Don't process the files
			contentType: false, // Set content type to false as jQuery will tell the server its a query string request
			success: function(data, textStatus, jqXHR)
			{
				if(jsDebugOn)
				{
					console.log("a_login_form() .ajax -> success:");
				}
				
				var l_msg = "";
				var p_msg = "";
				var c_msg = "";
				var e_msg = "";
				var redir = parseQueryStrParam('redir');

				if("Not found" == redir)
				{
					redir = "pn_index.php";
				}
				
				if(typeof data.result !== 'undefined')
				{
					l_msg = data.p_login_message;
					p_msg = data.p_password_message;
					c_msg = data.p_captcha_message;
					e_msg = data.p_error_message;
					
					if(data.result == "success")
					{
						//redir
						parseQueryStrParam()
						window.location = redir;
					}
					else
					{
						//
					}
					
				}
				else
				{
					e_msg = "Error during request. Try again please";
				}
				
				login_form_upd_status(l_msg, p_msg, c_msg, e_msg);
				document.getElementById('captcha').src = 'includes/securimage_MA7/securimage_show.php?' + Math.random();
			},
			error: function(jqXHR, textStatus, errorThrown)
			{
				if(jsDebugOn)
				{
					console.log("a_login_form() .ajax -> error:");
				}
				
				login_form_upd_status("", "", "", "Error during request. Try again please");
				document.getElementById('captcha').src = 'includes/securimage_MA7/securimage_show.php?' + Math.random();
			},
			complete: function()
			{
				if(jsDebugOn)
				{
					console.log("a_login_form() .ajax -> complete:");
				}
			}
		});
	}
	// /--- pn_login
	
	
	// --- pn_user ----------------------------------------------------------
	
	var usersListArr = [];
	$("#show_btn_div").html("");
	
	$("#e_user_form_add").on('submit', function(event)
	{
		event.stopPropagation(); // Stop stuff happening
		event.preventDefault(); // Totally stop stuff happening
	});
	$("#e_user_form_manage").on('submit', function(event)
	{
		event.stopPropagation(); // Stop stuff happening
		event.preventDefault(); // Totally stop stuff happening
	});
	$("#e_submit_add").on('click', user_add);
	$("#e_submit_edit").on('click', user_edit);
	$('#e_submit_delete').prop('onclick',null).off('click');
	$("#e_submit_delete").on('click', user_delete);
	
	
	

	$('#e_user_sel').change(function()
	{
		userSelChange($("#e_submit_edit").val());
	});
	

	function user_add()
	{
		if(jsDebugOn)
		{
			console.log("user_add()");
		}
				
		var formData = new FormData();
		
		formData.append("action", "add");
		formData.append("u_login", $("#e_user_form_add #u_login").val());
		formData.append("u_name", $("#e_user_form_add #u_name").val());
		formData.append("u_pass", $("#e_user_form_add #u_pass").val());
		formData.append("e_group_sel", $("#e_user_form_add #e_group_sel").val());
		formData.append("u_email", $("#e_user_form_add #u_email").val());
		formData.append("u_enabled", $("#e_user_form_add #u_enabled").prop( "checked"));
		
		a_user_form("add", formData);
	}
	
	function user_edit()
	{
		if(jsDebugOn)
		{
			console.log("user_add()");
		}
				
		var formData = new FormData();
		
		formData.append("action", "edit");
		formData.append("e_user_sel", $("#e_user_form_manage #e_user_sel").val());
		formData.append("u_name", $("#e_user_form_manage #u_name").val());
		formData.append("u_pass", $("#e_user_form_manage #u_pass").val());
		formData.append("e_group_sel", $("#e_user_form_manage #e_group_sel").val());
		formData.append("u_email", $("#e_user_form_manage #u_email").val());
		formData.append("u_enabled", $("#e_user_form_manage #u_enabled2").prop( "checked"));
		
		a_user_form("edit", formData);
	}
	
	function user_delete()
	{
		if(jsDebugOn)
		{
			console.log("user_add()");
		}
		
		if(!confirm('Are you sure?'))
		{
			return;
		}
				
		var formData = new FormData();
		
		formData.append("action", "delete");
		formData.append("e_user_sel", $("#e_user_form_manage #e_user_sel").val());
		
		a_user_form("delete", formData);
	}

	function user_getlist()
	{
		var formData = new FormData();
		formData.append("action", "userlist");
		a_user_form("userlist", formData);
	}

	function updateUsersList()
	{
		
		curId = $("#e_user_sel").val();
		$("#e_user_sel").empty();
		var user_sel = $("#e_user_sel");
		for(var i = 0; i < usersListArr.length; i++)
		{
			user_sel.append($("<option></option>").attr("value", usersListArr[i].id).text(usersListArr[i].login + ' - (' + usersListArr[i].name + ')'));
		}
		$("#e_user_sel").val(curId);
		userSelChange();
	}
	
	function userSelChange()
	{
		curId = $("#e_user_sel").val();
		for(var i = 0; i < usersListArr.length; i++)
		{
			if('undefined' !== usersListArr[i].id && usersListArr[i].id == curId)
			{
				$("#e_user_form_manage #u_name").val(usersListArr[i].name);
				$("#e_user_form_manage #e_group_sel").val(usersListArr[i].u_group);
				$("#e_user_form_manage #u_email").val(usersListArr[i].email);
				if(usersListArr[i].enabled && "0" != usersListArr[i].enabled)
				{
					$("#e_user_form_manage #u_enabled2").prop("checked", true);
				}
				else
				{
					$("#e_user_form_manage #u_enabled2").prop("checked", false);
				}
			}
		}
	}
	
	function a_user_form(action, formData)
	{
		
		if(jsDebugOn)
		{
			console.log("a_user_form()");
		}
		
		var imgel = "";
		if("add" == action)
		{
			imgel = "img_proc_01";
		}
		else if("edit" == action || "delete" == action)
		{
			imgel = "img_proc_02";
		}
		if(imgel.length > 0)
		{
			$("#" + imgel).html('<img src="images/loader_circle_16.gif" title="loading" alt="loading" />');
		}
				
		$.ajax(
		{
			url: 'a_user.php?u_action=user',
			type: 'POST',
			data: formData,
			cache: false,
			dataType: 'json',
			processData: false, // Don't process the files
			contentType: false, // Set content type to false as jQuery will tell the server its a query string request
			mAction: action,
			success: function(data, textStatus, jqXHR)
			{
				if(jsDebugOn)
				{
					console.log("a_user_form() .ajax -> success:");
				}
				
				var msg = "";
				var el = "";
				var type = "";
				var imgel = "";
				var allGood = false;
				
				if("add" == this.mAction)
				{
					el = "l_msg_01";
					imgel = "img_proc_01";
				}
				else if("edit" == this.mAction || "delete" == this.mAction)
				{
					el = "l_msg_02";
					imgel = "img_proc_02";
				}
				else
				{
					el = "l_msg_03";
				}
						
				if(typeof data.result !== 'undefined')
				{
					if(data.result == "success")
					{
						type = "success";
						msg = data.p_success_message;
						usersListArr = data.p_users_list;
						updateUsersList();
						allGood = true;
					}
					else
					{
						type = "danger";
						msg = data.p_error_message;
					}
				}
				else
				{
					type = "danger";
					msg = "Error during request. Try again please";
				}
				
				if(imgel.length > 0)
				{
					if(allGood)
					{
						$("#" + imgel).html('<img src="images/rez_1.png" title="success" alt="success" />');
					}
					else
					{
						$("#" + imgel).html('<img src="images/rez_0.png" title="error" alt="error" />');
					}
				}

				if(msg.length > 0)
				{
					print_msg(el, type, msg);
				}
			},
			error: function(jqXHR, textStatus, errorThrown)
			{
				if(jsDebugOn)
				{
					console.log("a_user_form() .ajax -> error:");
				}
				var msg = "";
				var el = "";
				var type = "danger";
				var imgel = "";
				
				if("add" == this.mAction)
				{
					el = "l_msg_01";
					imgel = "img_proc_01";
				}
				else if("edit" == this.mAction || "delete" == this.mAction)
				{
					el = "l_msg_02";
					imgel = "img_proc_02";
				}
				else
				{
					el = "l_msg_03";
				}
				
				if(imgel.length > 0)
				{
					$("#" + imgel).html('<img src="images/rez_0.png" title="error" alt="error" />');
				}
				
				msg = "Error during request. Try again please";
				print_msg(el, type, msg);
			},
			complete: function()
			{
				if(jsDebugOn)
				{
					console.log("a_user_form() .ajax -> complete:");
				}
				
				var imgel = "";
				if("add" == this.mAction)
				{
					imgel = "img_proc_01";
				}
				else if("edit" == this.mAction || "delete" == this.mAction)
				{
					imgel = "img_proc_02";
				}
				
				if(imgel.length > 0)
				{
					if('<img src="images/loader_circle_16.gif" title="loading" alt="loading" />' == $("#" + imgel).html())
					{
						$("#" + imgel).html('');
					}
				}
			}
		});
	}
	
	if("pn_users.php" == getFileName())
	{
		user_getlist();
	}
	// /--- pn_user
	
	
	// --- pn_investor_set ----------------------------------------------------------
	
	var investorsListArr = [];
	var eFilesInv;
	$("#inv_show_btn_div").html("");
	
	$("#inv_add_edit_form").on('submit', function(event)
	{
		event.stopPropagation(); // Stop stuff happening
		event.preventDefault(); // Totally stop stuff happening
	});
	$("#inv_file_upload_form").on('submit', function(event)
	{
		event.stopPropagation(); // Stop stuff happening
		event.preventDefault(); // Totally stop stuff happening
	});
	
	$("#investor_add").on('click', investor_add);
	$("#investor_change").on('click', investor_edit);
	$('#investor_delete').prop('onclick',null).off('click');
	$("#investor_delete").on('click', investor_delete);
	$("#e_upl_investors").on('click', investor_file_import);

	$('#investor_sel').change(function()
	{
		investorSelChange($("#e_submit_edit").val());
	});
	
	$('#inv_file_upload').on('change', prepareUploadInv);
	
	function prepareUploadInv(event)
	{
		eFilesInv = event.target.files;
	}
			
	
	function investor_add()
	{
		if(jsDebugOn)
		{
			console.log("investor_add()");
		}
				
		var formData = new FormData();
		
		formData.append("action", "addedit");
		formData.append("investor_add", "1");
		formData.append("investor_id", $("#inv_add_edit_form #investor_id").val());
		formData.append("investor_name", $("#inv_add_edit_form #investor_name").val());
		formData.append("investor_notes", $("#inv_add_edit_form #investor_notes").val());
		formData.append("investor_status", $("#inv_add_edit_form #investor_status").val());
		formData.append("date_time_sheet", $("#inv_add_edit_form #date_time_sheet").val());
		formData.append("date_time_phase", $("#inv_add_edit_form #date_time_phase").val());
		formData.append("date_search_range_01", $("#inv_add_edit_form #date_search_range_01").val());
		formData.append("date_search_range_02", $("#inv_add_edit_form #date_search_range_02").val());
		formData.append("date_search_range_03", $("#inv_add_edit_form #date_search_range_03").val());
		formData.append("date_search_range_04", $("#inv_add_edit_form #date_search_range_04").val());
		formData.append("date_location_01", $("#inv_add_edit_form #date_location_01").val());
		formData.append("date_location_02", $("#inv_add_edit_form #date_location_02").val());
		formData.append("time_location_01", $("#inv_add_edit_form #time_location_01").val());
		formData.append("time_location_02", $("#inv_add_edit_form #time_location_02").val());
		formData.append("date_format", $("#inv_add_edit_form #date_format").val());
		formData.append("time_format", $("#inv_add_edit_form #time_format").val());
		
		a_investor_form("add", formData);
	}
	
	function investor_edit()
	{
		if(jsDebugOn)
		{
			console.log("investor_edit()");
		}
				
		var formData = new FormData();
		
		formData.append("action", "addedit");
		formData.append("investor_change", "1");
		formData.append("investor_sel", $("#inv_add_edit_form #investor_sel").val());
		formData.append("investor_id", $("#inv_add_edit_form #investor_id").val());
		formData.append("investor_name", $("#inv_add_edit_form #investor_name").val());
		formData.append("investor_notes", $("#inv_add_edit_form #investor_notes").val());
		formData.append("investor_status", $("#inv_add_edit_form #investor_status").val());
		formData.append("date_time_sheet", $("#inv_add_edit_form #date_time_sheet").val());
		formData.append("date_time_phase", $("#inv_add_edit_form #date_time_phase").val());
		formData.append("date_search_range_01", $("#inv_add_edit_form #date_search_range_01").val());
		formData.append("date_search_range_02", $("#inv_add_edit_form #date_search_range_02").val());
		formData.append("date_search_range_03", $("#inv_add_edit_form #date_search_range_03").val());
		formData.append("date_search_range_04", $("#inv_add_edit_form #date_search_range_04").val());
		formData.append("date_location_01", $("#inv_add_edit_form #date_location_01").val());
		formData.append("date_location_02", $("#inv_add_edit_form #date_location_02").val());
		formData.append("time_location_01", $("#inv_add_edit_form #time_location_01").val());
		formData.append("time_location_02", $("#inv_add_edit_form #time_location_02").val());
		formData.append("date_format", $("#inv_add_edit_form #date_format").val());
		formData.append("time_format", $("#inv_add_edit_form #time_format").val());
		
		a_investor_form("edit", formData);
	}
	
	function investor_delete()
	{
		if(jsDebugOn)
		{
			console.log("investor_delete()");
		}
		
		if(!confirm('Are you sure?'))
		{
			return;
		}
				
		var formData = new FormData();
		
		formData.append("action", "addedit");
		formData.append("investor_delete", "1");
		formData.append("investor_sel", $("#inv_add_edit_form #investor_sel").val());
		
		a_investor_form("delete", formData);
	}
	
	function investor_file_import()
	{
		if(jsDebugOn)
		{
			console.log("investor_file_import()");
		}
		
		var formData = new FormData();
		$.each(eFilesInv, function(key, value)
		{
			formData.append(key, value);
		});
		formData.append("action", "import");
		formData.append("e_upl_investors", "1");
		
		a_investor_form("inv_import_from_file", formData);
	}

	function investor_getlist()
	{
		if(jsDebugOn)
		{
			console.log("investor_getlist()");
		}
		
		var formData = new FormData();
		formData.append("action", "investorslist");
		a_investor_form("investorslist", formData);
	}

	function updateInvestorList()
	{
		if(jsDebugOn)
		{
			console.log("updateInvestorList()");
		}
		
		curId = $("#investor_sel").val();
		$("#investor_sel").empty();
		var investor_sel = $("#investor_sel");
		for(var i = 0; i < investorsListArr.length; i++)
		{
			investor_sel.append($("<option></option>").attr("value", investorsListArr[i].id).text(investorsListArr[i].investor_name));
		}
		$("#investor_sel").val(curId);
		investorSelChange();
	}
	
	function investorSelChange()
	{
		if(jsDebugOn)
		{
			console.log("investorSelChange()");
		}
		
		curId = $("#investor_sel").val();
		for(var i = 0; i < investorsListArr.length; i++)
		{
			if('undefined' !== investorsListArr[i].id && investorsListArr[i].id == curId)
			{
				$("#inv_add_edit_form #investor_id").val(investorsListArr[i].investor_id);
				$("#inv_add_edit_form #investor_name").val(investorsListArr[i].investor_name);
				$("#inv_add_edit_form #investor_notes").val(investorsListArr[i].notes);
				$("#inv_add_edit_form #date_added").html("&nbsp;&nbsp;Added: " + investorsListArr[i].date_added);
				$("#inv_add_edit_form #date_time_sheet").val(investorsListArr[i].date_time_sheet);
				$("#inv_add_edit_form #date_time_phase").val(investorsListArr[i].date_time_phase);
				$("#inv_add_edit_form #date_search_range_01").val(investorsListArr[i].date_time_r_start_column);
				$("#inv_add_edit_form #date_search_range_02").val(investorsListArr[i].date_time_r_start_row);
				$("#inv_add_edit_form #date_search_range_03").val(investorsListArr[i].date_time_r_end_column);
				$("#inv_add_edit_form #date_search_range_04").val(investorsListArr[i].date_time_r_end_row);
				$("#inv_add_edit_form #date_location_01").val(investorsListArr[i].date_location_column);
				$("#inv_add_edit_form #date_location_02").val(investorsListArr[i].date_location_row);
				$("#inv_add_edit_form #time_location_01").val(investorsListArr[i].time_location_column);
				$("#inv_add_edit_form #time_location_02").val(investorsListArr[i].time_location_row);
				$("#inv_add_edit_form #date_format").val(investorsListArr[i].date_format_id);
				$("#inv_add_edit_form #time_format").val(investorsListArr[i].time_format_id);
			}
		}
	}
	
	function a_investor_form(action, formData)
	{
		
		if(jsDebugOn)
		{
			console.log("a_investor_form()");
		}
		
		var imgel = "";
		if("add" == action || "edit" == action || "delete" == action)
		{
			imgel = "img_proc_01";
		}
		else if("inv_import_from_file" == action)
		{
			imgel = "img_proc_02";
		}
		if(imgel.length > 0)
		{
			$("#" + imgel).html('<img src="images/loader_circle_16.gif" title="loading" alt="loading" />');
		}
				
		$.ajax(
		{
			url: 'a_investor.php?u_action=investor',
			type: 'POST',
			data: formData,
			cache: false,
			dataType: 'json',
			processData: false, // Don't process the files
			contentType: false, // Set content type to false as jQuery will tell the server its a query string request
			mAction: action,
			success: function(data, textStatus, jqXHR)
			{
				if(jsDebugOn)
				{
					console.log("a_investor_form() .ajax -> success:");
				}
				
				var msg = "";
				var msgErr = "";
				var el = "";
				var elErr = "";
				var type = "";
				var typeErr = "";
				var imgel = "";
				var allGood = false;
				
				if("add" == this.mAction || "edit" == this.mAction || "delete" == this.mAction)
				{
					el = "l_msg_inv_01";
					imgel = "img_proc_inv_01";
				}
				else if("inv_import_from_file" == this.mAction)
				{
					el = "l_msg_inv_02";
					elErr = "l_msg_inv_03";
					imgel = "img_proc_inv_02";
				}
						
				if(typeof data.result !== 'undefined')
				{
					if(data.result == "success")
					{
						type = "success";
						msg = data.p_success_message;
						typeErr = "danger";
						msgErr = data.p_error_message;
						investorsListArr = data.p_investors_list;
						updateInvestorList();
						allGood = true;
					}
					else
					{
						type = "danger";
						msg = data.p_error_message;
					}
				}
				else
				{
					type = "danger";
					msg = "Error during request. Try again please";
				}
				
				if(imgel.length > 0)
				{
					if(allGood)
					{
						$("#" + imgel).html('<img src="images/rez_1.png" title="success" alt="success" />');
					}
					else
					{
						$("#" + imgel).html('<img src="images/rez_0.png" title="error" alt="error" />');
					}
				}

				if(msg.length > 0)
				{
					print_msg(el, type, msg);
				}
				if(typeErr.length > 0)
				{
					print_msg(elErr, typeErr, msgErr);
				}
			},
			error: function(jqXHR, textStatus, errorThrown)
			{
				if(jsDebugOn)
				{
					console.log("a_investor_form() .ajax -> error:");
				}
				var msg = "";
				var el = "";
				var type = "danger";
				var imgel = "";
				
				if("add" == this.mAction || "edit" == this.mAction || "delete" == this.mAction)
				{
					el = "l_msg_01";
					imgel = "img_proc_01";
				}
				else if("inv_import_from_file" == this.mAction)
				{
					el = "l_msg_02";
					imgel = "img_proc_02";
				}
				else
				{
					el = "l_msg_03";
				}
				
				if(imgel.length > 0)
				{
					$("#" + imgel).html('<img src="images/rez_0.png" title="error" alt="error" />');
				}
				
				msg = "Error during request. Try again please";
				print_msg(el, type, msg);
			},
			complete: function()
			{
				if(jsDebugOn)
				{
					console.log("a_investor_form() .ajax -> complete:");
				}
				
				var imgel = "";
				if("add" == this.mAction || "edit" == this.mAction || "delete" == this.mAction)
				{
					imgel = "img_proc_01";
				}
				else if("..." == this.mAction)
				{
					imgel = "img_proc_02";
				}
				
				if(imgel.length > 0)
				{
					if('<img src="images/loader_circle_16.gif" title="loading" alt="loading" />' == $("#" + imgel).html())
					{
						$("#" + imgel).html('');
					}
				}
			}
		});
	}
	
	if("pn_investors.php" == getFileName())
	{
		investor_getlist();
	}
	// /--- pn_investor_set
	
	
	
	// --- pn_rep_file_upload ----------------------------------------------------------
	
	
	// Variable to store files
	var eFiles;
	var repDirList;

	// Grab the files and set them to our variable
	function prepareUpload(event)
	{
		eFiles = event.target.files;
	}

	// Catch the form submit and upload the files
	function uploadFiles(event)
	{
		event.stopPropagation(); // Stop stuff happening
		event.preventDefault(); // Totally stop stuff happening

		// START LOADING SPINNER
			
			
		var data = new FormData();
		data.append("investor_id", $("#investor_id").val());
		$.each(eFiles, function(key, value)
		{
			data.append(key, value);
		});
			
		$("#img_proc_set_02").html('<img src="images/loader_circle_16.gif" title="loading" alt="loading" />' + "Trying to upload files");
		$.ajax({
			url: 'a_upload_invf.php?files',
			type: 'POST',
			data: data,
			cache: false,
			dataType: 'json',
			processData: false, // Don't process the files
			contentType: false, // Set content type to false as jQuery will tell the server its a query string request
			success: function(data, textStatus, jqXHR)
			{
				if(data.result == 'success' && data.files.length > 0)
				{
					// Success so call function to process the form
					//submitForm(event, data);
					$("#img_proc_set_02").html('<img src="images/rez_1.png" title="success" alt="success" />' + "Uploded");
				}
				else
				{
					// Handle errors here
					//console.log('ERRORS: ' + data.result + ";" + data.files.length);
					$("#img_proc_set_02").html('<img src="images/rez_0.png" title="error" alt="error" />' + "Error during upload: " + data.message);
				}
			},
			error: function(jqXHR, textStatus, errorThrown)
			{
				// Handle errors here
				//console.log('ERRORS: ' + textStatus);
				// STOP LOADING SPINNER
				$("#img_proc_set_02").html('<img src="images/rez_0.png" title="error" alt="error" />' + "Error during upload: " + textStatus);
			},
			complete: function()
			{
				//$("#img_proc_set_02").html('<img src="images/rez_1.png" title="success" alt="success" />' + "Error during upload: " + textStatus);
				fuUpdateDirList();
			}
		});

	}

		
	function fuUpdateDirList(event, data)
	{
		var formData = new FormData();
		formData.append("diropt", "1");
		$.ajax({
			url: 'a_getdirlist.php?action=dirlist',
			type: 'POST',
			data: formData,
			cache: false,
			dataType: 'json',
			processData: false, // Don't process the files
			contentType: false, // Set content type to false as jQuery will tell the server its a query string request
			success: function(data, textStatus, jqXHR)
			{
				if(typeof data.result !== 'undefined' && data.result == "success")
				{
					repDirList = data.dirlist;
					// Success so call function to process the form
					console.log('fuUpdateDirList() => SUCCESS: ' + data.result);
				}
				else
				{
					// Handle errors here
					console.log('fuUpdateDirList() => ERRORS: ' + data.message);
				}
			},
			error: function(jqXHR, textStatus, errorThrown)
			{
				// Handle errors here
				console.log('fuUpdateDirList() => ERRORS: ' + textStatus);
			},
			complete: function()
			{
				fpShowDirList();
			}
		});
	}
		
		
	function addFolderToDirListView(dirObj)
	{

		var retVar = "";
		if(dirObj !== 'undefined' && dirObj.length > 0 && dirObj.name !== 'undefined' && dirObj.type !== 'undefined')
		{
			var dirs = "";
			var files = "";
				
			for(var i = 0; i < dirObj.length; i++)
			{
				var tmpDirs = "";
				if(dirObj[i].type == "dir")
				{
					tmpDirs += '<li class="tree">';
					tmpDirs += '<label for="">' + dirObj[i].name + '</label> <input type="checkbox" id="" /> ';
					tmpDirs += '<ol>';
					tmpDirs += addFolderToDirListView(dirObj[i].dirList);
					tmpDirs += '</ol>';
					tmpDirs += '</li>';
					tmpDirs += '';
					dirs = tmpDirs + dirs;
					//console.log('addFolderToDirListView(): dir1' + retVar);
				}
				else
				{
					files += '<li class="treeFile"><a href="' + dirObj[i].path + '" target="_blank">';
					files += dirObj[i].name;
					files += '</a></li>';
					files += '';
					//console.log('addFolderToDirListView(): file' + retVar);
				}
			}
			retVar += dirs + files;
		}
		return retVar;
	}
		
	function fpShowDirList()
	{
		//console.log('fpShowDirList(): Begin');
			
		if(repDirList !== 'undefined' && repDirList.length > 0)
		{
			var treeViewHTML = "";
				
			treeViewHTML = '<ol class="tree">';
			treeViewHTML += addFolderToDirListView(repDirList);
			treeViewHTML += '</ol>';
				
			$("#e_dir_listing").html(treeViewHTML);
		}
	}
	
	
	if("pn_rep_file_upload.php" == getFileName())
	{
		fuUpdateDirList();
	}
		
	
	// /--- pn_rep_file_upload
	
	
	// --- pn_rep_process ----------------------------------------------------------
	
	var rprocDirList;
	var rpInvArr = [];
	var rpInvInd = -1;
	var rpCurPath = "";
	
	function rpUpdateDirList(event, data)
	{
		var formData = new FormData();
		formData.append("diropt", "1");
		$.ajax({
			url: 'a_getdirlist.php?action=dirlist',
			type: 'POST',
			data: formData,
			cache: false,
			dataType: 'json',
			processData: false, // Don't process the files
			contentType: false, // Set content type to false as jQuery will tell the server its a query string request
			success: function(data, textStatus, jqXHR)
			{
				if(typeof data.result !== 'undefined' && data.result == "success")
				{
					rprocDirList = data.dirlist;
					// Success so call function to process the form
					if(jsDebugOn)
					{
						console.log('rpUpdateDirList() => SUCCESS: ' + data.result);
					}
				}
				else
				{
					// Handle errors here
					if(jsDebugOn)
					{
						console.log('rpUpdateDirList() => ERRORS: ' + data.message);
					}
				}
			},
			error: function(jqXHR, textStatus, errorThrown)
			{
				// Handle errors here
				if(jsDebugOn)
				{
					console.log('rpUpdateDirList() => ERRORS: ' + textStatus);
				}
			},
			complete: function()
			{
				rpShowDirList();
			}
		});
	}
	
	function rpShowDirList()
	{
		curVal = $("#rp_folder_sel").val();
		
		if(rprocDirList !== 'undefined' && rprocDirList.length > 0)
		{
			var treeViewHTML = "";
				
			treeViewHTML = '<ol class="tree" style="list-style-type: none">';
			for(var i = 0; i < rprocDirList.length; i++)
			{
				if("dir" == rprocDirList[i].type && curVal == rprocDirList[i].name)
				{
					treeViewHTML += addFolderToDirListView(rprocDirList[i].dirList);
				}
			}
			treeViewHTML += '</ol>';
				
			$("#fprocess_dir_listing").html(treeViewHTML);
		}
	}
	
	
	function rpProcessFilesAjax()
	{

		if(jsDebugOn)
		{
			console.log("rpProcessFilesAjax()");
		}
		
		if(rpInvInd < rpInvArr.length)
		{
			var formData = new FormData();
			formData.append("invId", rpInvArr[rpInvInd]);
			formData.append("repDir", rpCurPath);
		
			$.ajax({
				url: 'a_rep_process.php?action=repprocess',
				type: 'POST',
				data: formData,
				cache: false,
				dataType: 'json',
				processData: false, // Don't process the files
				contentType: false, // Set content type to false as jQuery will tell the server its a query string request
				success: function(data, textStatus, jqXHR)
				{
					if(typeof data.result !== 'undefined' && data.result == "success")
					{
						// Success so call function to process the form
						if(jsDebugOn)
						{
							console.log('rpProcessFiles() => SUCCESS: ' + data.result);
						}
						rpAddSuccessMsg("Investor with id " + data.investorId + " was processed");
					}
					else
					{
						// Handle errors here
						if(jsDebugOn)
						{
							console.log('rpProcessFiles() => ERRORS: ' + data.message);
						}
						rpAddErrorMsg(data.message);
					}
				},
				error: function(jqXHR, textStatus, errorThrown)
				{
					// Handle errors here
					if(jsDebugOn)
					{
						console.log('rpProcessFiles() => ERRORS: ' + textStatus);
					}
					rpAddErrorMsg("error while request: " + textStatus);
				},
				complete: function()
				{
					rpInvInd++;
					rpProcessFilesAjax();
				}
			});
		}
		
		if(rpInvArr.length <= rpInvInd || rpInvInd < 0)
		{
			$('#rp_folder_sel').prop("disabled", false);
			$('#fprocess_file_submit').prop("disabled", false);
			$('#rp_img_select_file_02').html('<img src="images/rez_1.png" title="" alt="" />');
		}
	}
	
	function rpAddSuccessMsg(msg)
	{
		var pHtml = "";
		
		if(!$('#pr_success_msg_div').length)
		{
			pHtml = $('#rp_msg_01').html();
			var sMsgDiv = "";
			sMsgDiv += '<div id="pr_success_msg_div" class="alert alert-success" style="padding: 5;">';
			sMsgDiv += '<strong>Success: </strong>';
			sMsgDiv += '</div>';
			sMsgDiv += '<br />';
			pHtml = sMsgDiv + pHtml;
			$('#rp_msg_01').html(pHtml);
		}
		if($('#pr_success_msg_div').length)
		{
			pHtml = "";
			pHtml = $('#pr_success_msg_div').html();
			pHtml = pHtml + msg + "<br />\n";
			$('#pr_success_msg_div').html(pHtml);
		}
	}
	
	function rpAddErrorMsg(msg)
	{
		var pHtml = "";
		
		if(!$('#pr_error_msg_div').length)
		{
			pHtml = $('#rp_msg_01').html();
			var sMsgDiv = "";
			sMsgDiv += '<div id="pr_error_msg_div" class="alert alert-danger" style="padding: 5;">';
			sMsgDiv += '<strong>Error: </strong>';
			sMsgDiv += '</div>';
			sMsgDiv += '<br />';
			pHtml = pHtml + sMsgDiv;
			$('#rp_msg_01').html(pHtml);
		}
		if($('#pr_error_msg_div').length)
		{
			pHtml = "";
			pHtml = $('#pr_error_msg_div').html();
			pHtml = pHtml + msg + "<br />\n";
			$('#pr_error_msg_div').html(pHtml);
		}
	}
	
	function rpProcessFiles()
	{
		rpInvArr = [];
		rpInvInd = -1;
		rpCurPath = "";
		var curVal = $("#rp_folder_sel").val();
		$('#rp_folder_sel').prop("disabled", true);
		$('#fprocess_file_submit').prop("disabled", true);
		$('#rp_img_select_file_02').html('<img src="images/loader_circle_16.gif" title="processing" alt="processing" />');
		
		if(rprocDirList !== 'undefined' && rprocDirList.length > 0)
		{
			for(var i = 0; i < rprocDirList.length; i++)
			{
				if("dir" == rprocDirList[i].type && curVal == rprocDirList[i].name)
				{
					var curDirList = rprocDirList[i].dirList;
					rpCurPath = rprocDirList[i].path;
					for(var j = 0; j < curDirList.length; j++)
					{
						if("file" == curDirList[j].type)
						{
							var addNew = true;
							var regRez = curDirList[j].name.match(/^(\d{1,5})-(\d{1,3})-(\d{1,2})\.csv/i);
							if(null != regRez)
							{
								for(var n = 0; n < rpInvArr.length; n++)
								{
									if(rpInvArr[n] == regRez[1])
									{
										addNew = false;
										break;
									}
								}
								if(addNew)
								{
									rpInvArr.push(regRez[1]);
								}
							}
						}
					}
				}
			}
		}
		
		if(rpInvArr.length > 0)
		{
			rpInvInd = 0;
			rpProcessFilesAjax();
		}
		else
		{
			rpAddErrorMsg("there no investors files were found in specified directory");
		}
	}
	
	$('#rp_folder_sel').change(function()
	{
		rpShowDirList();
	});
	
	if("pn_rep_process.php" == getFileName())
	{
		$("#rp_show_btn_div").html("");
		rpUpdateDirList();
		
		$("#rep_file_process").on('submit', function(event)
		{
			event.stopPropagation(); // Stop stuff happening
			event.preventDefault(); // Totally stop stuff happening
		});
		$("#fprocess_file_submit").on('click', rpProcessFiles);
	}
	// /--- pn_rep_process
	
	
	// --- pn_products ----------------------------------------------------------
	
	var productsListArr = [];

	
	function productAdd()
	{
		if(jsDebugOn)
		{
			console.log(arguments.callee.toString().match(/function ([^\(]+)/)[1] + '()');
		}
				
		var formData = new FormData();
		formData.append("action", "add");
		formData.append("product_sel", $("#prod_add_edit_form #product_sel").val());
		formData.append("product_id", $("#prod_add_edit_form #product_id").val());
		formData.append("product_name", $("#prod_add_edit_form #product_name").val());
		formData.append("product_notes", $("#prod_add_edit_form #product_notes").val());
		
		a_product_form("add", formData);
	}
	
	function productEdit()
	{
		if(jsDebugOn)
		{
			console.log(arguments.callee.toString().match(/function ([^\(]+)/)[1] + '()');
		}
				
		var formData = new FormData();
		formData.append("action", "edit");
		formData.append("product_sel", $("#prod_add_edit_form #product_sel").val());
		formData.append("product_id", $("#prod_add_edit_form #product_id").val());
		formData.append("product_name", $("#prod_add_edit_form #product_name").val());
		formData.append("product_notes", $("#prod_add_edit_form #product_notes").val());
		
		a_product_form("edit", formData);
	}
	
	function productDelete()
	{
		if(jsDebugOn)
		{
			console.log(arguments.callee.toString().match(/function ([^\(]+)/)[1] + '()');
		}
		
		if(!confirm('Are you sure?'))
		{
			return;
		}
				
		var formData = new FormData();
		formData.append("action", "delete");
		formData.append("product_sel", $("#prod_add_edit_form #product_sel").val());
		
		a_product_form("delete", formData);
	}
	
	function productGetlist()
	{
		if(jsDebugOn)
		{
			console.log(arguments.callee.toString().match(/function ([^\(]+)/)[1] + '()');
		}
		
		var formData = new FormData();
		formData.append("action", "productslist");
		
		a_product_form("productslist", formData);
	}

	function updateProductList()
	{
		if(jsDebugOn)
		{
			console.log(arguments.callee.toString().match(/function ([^\(]+)/)[1] + '()');
		}
		
		curId = $("#product_sel").val();
		$("#product_sel").empty();
		var product_sel = $("#product_sel");
		for(var i = 0; i < productsListArr.length; i++)
		{
			product_sel.append($("<option></option>").attr("value", productsListArr[i].id).text(productsListArr[i].product_id + ' - ' + productsListArr[i].product_name));
		}
		$("#product_sel").val(curId);
		productSelChange();
	}
	
	function productSelChange()
	{
		if(jsDebugOn)
		{
			console.log(arguments.callee.toString().match(/function ([^\(]+)/)[1] + '()');
		}
		
		curId = $("#product_sel").val();
		for(var i = 0; i < productsListArr.length; i++)
		{
			if('undefined' !== productsListArr[i].id && productsListArr[i].id == curId)
			{
				
				$("#prod_add_edit_form #product_id").val(productsListArr[i].product_id);
				$("#prod_add_edit_form #product_name").val(productsListArr[i].product_name);
				$("#prod_add_edit_form #product_notes").val(productsListArr[i].notes);
				$("#prod_add_edit_form #prod_date_added").html("&nbsp;&nbsp;Added: " + productsListArr[i].date_added);
			}
		}
	}
	
	function a_product_form(action, formData)
	{
		
		if(jsDebugOn)
		{
			console.log(arguments.callee.toString().match(/function ([^\(]+)/)[1] + '()');
		}
		
		var imgel = "";
		if("add" == action || "edit" == action || "delete" == action)
		{
			imgel = "img_proc_prod_01";
		}
		
		if(imgel.length > 0)
		{
			$("#" + imgel).html('<img src="images/loader_circle_16.gif" title="loading" alt="loading" />');
		}
				
		$.ajax(
		{
			url: 'a_product.php?u_action=product',
			type: 'POST',
			data: formData,
			cache: false,
			dataType: 'json',
			processData: false, // Don't process the files
			contentType: false, // Set content type to false as jQuery will tell the server its a query string request
			mAction: action,
			success: function(data, textStatus, jqXHR)
			{
				if(jsDebugOn)
				{
					console.log("a_product_form() .ajax -> success:");
				}
				
				var msg = "";
				var msgErr = "";
				var el = "";
				var elErr = "";
				var type = "";
				var typeErr = "";
				var imgel = "";
				var allGood = false;
				
				
				if("add" == this.mAction || "edit" == this.mAction || "delete" == this.mAction)
				{
					imgel = "img_proc_prod_01";
					el = "l_msg_prod_01";
					elErr = "l_msg_prod_02";
				}

				
				if(typeof data.result !== 'undefined')
				{
					if(data.result == "success")
					{
						type = "success";
						msg = data.p_success_message;
						typeErr = "danger";
						msgErr = data.p_error_message;
						productsListArr = data.p_products_list;
						updateProductList();
						allGood = true;
					}
					else
					{
						type = "danger";
						msg = data.p_error_message;
					}
				}
				else
				{
					type = "danger";
					msg = "Error during request. Try again please";
				}
				
				if(imgel.length > 0)
				{
					if(allGood)
					{
						$("#" + imgel).html('<img src="images/rez_1.png" title="success" alt="success" />');
					}
					else
					{
						$("#" + imgel).html('<img src="images/rez_0.png" title="error" alt="error" />');
					}
				}

				if(msg.length > 0)
				{
					print_msg(el, type, msg);
				}
				if(typeErr.length > 0 && msgErr.length > 0)
				{
					print_msg(elErr, typeErr, msgErr);
				}
			},
			error: function(jqXHR, textStatus, errorThrown)
			{
				if(jsDebugOn)
				{
					console.log("a_product_form() .ajax -> error:");
				}
				var msg = "";
				var el = "";
				var type = "danger";
				var imgel = "";
				
				if("add" == this.mAction || "edit" == this.mAction || "delete" == this.mAction)
				{
					imgel = "img_proc_prod_01";
					el = "l_msg_prod_01";
					elErr = "l_msg_prod_02";
				}
				
				if(imgel.length > 0)
				{
					$("#" + imgel).html('<img src="images/rez_0.png" title="error" alt="error" />');
				}
				
				msg = "Error during request. Try again please";
				print_msg(el, type, msg);
			},
			complete: function()
			{
				if(jsDebugOn)
				{
					console.log("a_product_form() .ajax -> complete:");
				}
				
				var imgel = "";
				if("add" == this.mAction || "edit" == this.mAction || "delete" == this.mAction)
				{
					imgel = "img_proc_prod_01";
				}
				
				if(imgel.length > 0)
				{
					if('<img src="images/loader_circle_16.gif" title="loading" alt="loading" />' == $("#" + imgel).html())
					{
						$("#" + imgel).html('');
					}
				}
			}
		});
	}
	/**/
	
	if("pn_products.php" == getFileName())
	{
		$("#prod_show_btn_div").html("");
		productGetlist();
		
		$("#prod_add_edit_form").on('submit', function(event)
		{
			event.stopPropagation(); // Stop stuff happening
			event.preventDefault(); // Totally stop stuff happening
		});
		$('#product_sel').change(function()
		{
			productSelChange();
		});
		$("#product_add").on('click', productAdd);
		$("#product_change").on('click', productEdit);
		$('#product_delete').prop('onclick',null).off('click');
		$("#product_delete").on('click', productDelete);
		/**/
	}
	// /--- pn_products

	
	
	
	// --- pn_lockdays ----------------------------------------------------------
	
	var lockdaysListArr = [];

	
	function lockdayAdd()
	{
		if(jsDebugOn)
		{
			console.log(arguments.callee.toString().match(/function ([^\(]+)/)[1] + '()');
		}
				
		var formData = new FormData();
		formData.append("action", "add");
		formData.append("lockday_sel", $("#lockday_add_edit_form #lockday_sel").val());
		formData.append("lockday_id", $("#lockday_add_edit_form #lockday_id").val());
		formData.append("lockday_name", $("#lockday_add_edit_form #lockday_name").val());
		
		a_lockday_form("add", formData);
	}
	
	function lockdayEdit()
	{
		if(jsDebugOn)
		{
			console.log(arguments.callee.toString().match(/function ([^\(]+)/)[1] + '()');
		}
				
		var formData = new FormData();
		formData.append("action", "edit");
		formData.append("lockday_sel", $("#lockday_add_edit_form #lockday_sel").val());
		formData.append("lockday_id", $("#lockday_add_edit_form #lockday_id").val());
		formData.append("lockday_name", $("#lockday_add_edit_form #lockday_name").val());
		
		a_lockday_form("edit", formData);
	}
	
	function lockdayDelete()
	{
		if(jsDebugOn)
		{
			console.log(arguments.callee.toString().match(/function ([^\(]+)/)[1] + '()');
		}
		
		if(!confirm('Are you sure?'))
		{
			return;
		}
				
		var formData = new FormData();
		formData.append("action", "delete");
		formData.append("lockday_sel", $("#lockday_add_edit_form #lockday_sel").val());
		
		a_lockday_form("delete", formData);
	}
	
	function lockdayGetlist()
	{
		if(jsDebugOn)
		{
			console.log(arguments.callee.toString().match(/function ([^\(]+)/)[1] + '()');
		}
		
		var formData = new FormData();
		formData.append("action", "lockdayslist");
		
		a_lockday_form("lockdayslist", formData);
	}

	function updateLockdayList()
	{
		if(jsDebugOn)
		{
			console.log(arguments.callee.toString().match(/function ([^\(]+)/)[1] + '()');
		}
		
		curId = $("#lockday_sel").val();
		$("#lockday_sel").empty();
		var lockday_sel = $("#lockday_sel");
		for(var i = 0; i < lockdaysListArr.length; i++)
		{
			lockday_sel.append($("<option></option>").attr("value", lockdaysListArr[i].id).text(lockdaysListArr[i].lock_day_id + " - " + lockdaysListArr[i].lock_day));
		}
		$("#lockday_sel").val(curId);
		lockdaySelChange();
	}
	
	function lockdaySelChange()
	{
		if(jsDebugOn)
		{
			console.log(arguments.callee.toString().match(/function ([^\(]+)/)[1] + '()');
		}
		
		curId = $("#lockday_sel").val();
		for(var i = 0; i < lockdaysListArr.length; i++)
		{
			if('undefined' !== lockdaysListArr[i].id && lockdaysListArr[i].id == curId)
			{
				
				$("#lockday_add_edit_form #lockday_id").val(lockdaysListArr[i].lock_day_id);
				$("#lockday_add_edit_form #lockday_name").val(lockdaysListArr[i].lock_day);
			}
		}
	}
	
	function a_lockday_form(action, formData)
	{
		
		if(jsDebugOn)
		{
			console.log(arguments.callee.toString().match(/function ([^\(]+)/)[1] + '()');
		}
		
		var imgel = "";
		if("add" == action || "edit" == action || "delete" == action)
		{
			imgel = "img_proc_lockday_01";
		}
		
		if(imgel.length > 0)
		{
			$("#" + imgel).html('<img src="images/loader_circle_16.gif" title="loading" alt="loading" />');
		}
				
		$.ajax(
		{
			url: 'a_lockday.php?u_action=lockday',
			type: 'POST',
			data: formData,
			cache: false,
			dataType: 'json',
			processData: false, // Don't process the files
			contentType: false, // Set content type to false as jQuery will tell the server its a query string request
			mAction: action,
			success: function(data, textStatus, jqXHR)
			{
				if(jsDebugOn)
				{
					console.log("a_lockday_form() .ajax -> success:");
				}
				
				var msg = "";
				var msgErr = "";
				var el = "";
				var elErr = "";
				var type = "";
				var typeErr = "";
				var imgel = "";
				var allGood = false;
				
				
				if("add" == this.mAction || "edit" == this.mAction || "delete" == this.mAction)
				{
					imgel = "img_proc_lockday_01";
					el = "l_msg_lock_01";
					elErr = "l_msg_lock_02";
				}

				
				if(typeof data.result !== 'undefined')
				{
					if(data.result == "success")
					{
						type = "success";
						msg = data.p_success_message;
						typeErr = "danger";
						msgErr = data.p_error_message;
						lockdaysListArr = data.p_lockdays_list;
						updateLockdayList();
						allGood = true;
					}
					else
					{
						type = "danger";
						msg = data.p_error_message;
					}
				}
				else
				{
					type = "danger";
					msg = "Error during request. Try again please";
				}
				
				if(imgel.length > 0)
				{
					if(allGood)
					{
						$("#" + imgel).html('<img src="images/rez_1.png" title="success" alt="success" />');
					}
					else
					{
						$("#" + imgel).html('<img src="images/rez_0.png" title="error" alt="error" />');
					}
				}

				if(msg.length > 0)
				{
					print_msg(el, type, msg);
				}
				if(typeErr.length > 0 && msgErr.length > 0)
				{
					print_msg(elErr, typeErr, msgErr);
				}
			},
			error: function(jqXHR, textStatus, errorThrown)
			{
				if(jsDebugOn)
				{
					console.log("a_lockday_form() .ajax -> error:");
				}
				var msg = "";
				var el = "";
				var type = "danger";
				var imgel = "";
				
				if("add" == this.mAction || "edit" == this.mAction || "delete" == this.mAction)
				{
					imgel = "img_proc_lockday_01";
					el = "l_msg_prod_01";
					elErr = "l_msg_lock_02";
				}
				
				if(imgel.length > 0)
				{
					$("#" + imgel).html('<img src="images/rez_0.png" title="error" alt="error" />');
				}
				
				msg = "Error during request. Try again please";
				print_msg(el, type, msg);
			},
			complete: function()
			{
				if(jsDebugOn)
				{
					console.log("a_lockday_form() .ajax -> complete:");
				}
				
				var imgel = "";
				if("add" == this.mAction || "edit" == this.mAction || "delete" == this.mAction)
				{
					imgel = "img_proc_lockday_01";
				}
				
				if(imgel.length > 0)
				{
					if('<img src="images/loader_circle_16.gif" title="loading" alt="loading" />' == $("#" + imgel).html())
					{
						$("#" + imgel).html('');
					}
				}
			}
		});
	}
	/**/
	
	if("pn_lockdays.php" == getFileName())
	{
		$("#lockday_show_btn_div").html("");
		lockdayGetlist();
		
		$("#lockday_add_edit_form").on('submit', function(event)
		{
			event.stopPropagation(); // Stop stuff happening
			event.preventDefault(); // Totally stop stuff happening
		});
		$('#lockday_sel').change(function()
		{
			lockdaySelChange();
		});
		$("#lockday_add").on('click', lockdayAdd);
		$("#lockday_change").on('click', lockdayEdit);
		$('#lockday_delete').prop('onclick',null).off('click');
		$("#lockday_delete").on('click', lockdayDelete);
		/**/
	}
	// /--- pn_lockdays
	
	
	// --- pn_report_generate ----------------------------------------------------------
	
	if("pn_rep_generate.php" == getFileName())
	{
		
		$( "#gen_rep_date" ).datepicker({dateFormat: "yy-mm-dd"});
		$( "#gen_rep_time" ).timepicker();
		
		$( "#gen_rep_date_02" ).datepicker({dateFormat: "yy-mm-dd"});
		$( "#gen_rep_time_02" ).timepicker();
	}
	// /--- pn_report_generate
});