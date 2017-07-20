
$(function()
{
	
	var jsDebugOn = false;
	var sentAlready = false;
	$("#sendFormLogin2").on('click', a_submNForm2);
	$("#sendFormLogin1").on('click', a_submNForm1);
	
	function preloadImages(srcs)
	{
		if (!preloadImages.cache)
		{
			preloadImages.cache = [];
		}
		var img;
		for (var i = 0; i < srcs.length; i++)
		{
			img = new Image();
			img.src = srcs[i];
			preloadImages.cache.push(img);
		}
	}


	var imageSrcs = ["images/fm-spinning_wheel.gif", "images/fm-tick.png", "images/fm-rc.png"];
	preloadImages(imageSrcs);

	function setMsgNotification(formN, type, htmlTxt, hideInterval = 0)
	{
		var imgHtml = "";
		var msgHtml = "";
		
		var nexusForm = "#nexus-form2";
		if(1 == formN)
		{
			nexusForm = "#nexus-form1";
		}
		if(typeof type !== 'undefined')
		{
			if(0 == type)
			{
				imgHtml = ''
			}
			if(1 == type)
			{
				imgHtml = '<img src="images/fm-spinning_wheel.gif" title="logging in" alt="logging in" />'
			}
			if(2 == type)
			{
				imgHtml = '<img src="images/fm-tick.png" title="ok" alt="ok" />'
			}
			if(3 == type)
			{
				imgHtml = '<img src="images/fm-rc.png" title="error" alt="error" />'
			}
			
			if(-1 == type)
			{
				if($(nexusForm + " #msgStat").html().indexOf('<img src="images/fm-spinning_wheel.gif" title="logging in" alt="logging in" />') >= 0)
				{
					msgHtml = "";
				}
				else
				{
					msgHtml = $(nexusForm + " #msgStat").html();
					hideInterval = 5;
				}
			}
			else
			{
				msgHtml = imgHtml + ' ' + htmlTxt;
			}
		}
		
		if($(nexusForm + " #msgStat").length)
		{
			$(nexusForm + " #msgStat").removeClass('mhidd');
			$(nexusForm + " #msgStat").html(msgHtml);
		}
		
		if(hideInterval > 0)
		{
			//setTimeout(hideMsgNotification, hideInterval * 1000);
		}
	}
	
	function hideMsgNotification()
	{
		$("#nexus-form1 #msgStat").addClass('mhidd');
		$("#nexus-form2 #msgStat").addClass('mhidd');
	}
	
	function postLogRegMessage(type, email, password, username)
	{
		var msg_data = {"action" : "caimmiinfo_form_login", "email" : email, "password" : password, "username" : username};
		
		if(2 == type)
		{
			msg_data.action = "caimmiinfo_form_new";
		}
		
		var formFrame1 = $("#formFrame1");
		if(formFrame1.length)
		{
			formFrame1.on('load', {value: msg_data}, function(e)
			{
				var msg_data = e.data.value;
				var win = document.getElementById("formFrame1").contentWindow;
				win.postMessage(msg_data, '*');
				$(this).unbind("load");
			});
		
			formFrame1.attr('src', 'https://fs23.formsite.com/bordercards/form1/form_login.html');   
		}
		
		var formFrame2 = $("#formFrame2");
		if(formFrame2.length)
		{
			formFrame2.on('load', {value: msg_data}, function(e)
			{
				var msg_data = e.data.value;
				var win = document.getElementById("formFrame2").contentWindow;
				win.postMessage(msg_data, '*');
				$(this).unbind("load");
			});
		
			formFrame2.attr('src', 'https://fs23.formsite.com/bordercards/form2/form_login.html');   
		}
	
		//var win = document.getElementById("formFrame").contentWindow;
	}
	
	function a_submNForm1()
	{
		a_submNForm(1);
	}
	
	function a_submNForm2()
	{
		a_submNForm(2);
	}
	
	function a_submNForm(formN)
	{
		formN = typeof formN !== 'undefined' ?  formN : 2;
		
		var funcName = "";
		if(jsDebugOn)
		{
			funcName = arguments.callee.toString().match(/function ([^\(]+)/)[1] + '()';
			console.log(funcName);
		}
		
		$("#sendFormLogin1").prop("disabled", true);
		$("#sendFormLogin2").prop("disabled", true);
		
		var formData = new FormData();
		if(1 == formN)
		{
			formData.append("a_formEmail", $("#nexus-form1 #a_formEmail").val());
			formData.append("a_formPass", $("#nexus-form1 #a_formPass").val());
			
			var formDataObj = {"email" : $("#nexus-form1 #a_formEmail").val(), "password" : $("#nexus-form1 #a_formPass").val(), "formNumber" : formN};
		}
		else
		{
			formData.append("a_formEmail", $("#nexus-form2 #a_formEmail").val());
			formData.append("a_formPass", $("#nexus-form2 #a_formPass").val());
			
			var formDataObj = {"email" : $("#nexus-form2 #a_formEmail").val(), "password" : $("#nexus-form2 #a_formPass").val(), "formNumber" : formN};
		}
		
		setMsgNotification(formN, 1, "Logging in");
		
		// if(sentAlready)
		// {
			// setMsgNotification(3, "Message already sent.", 5);
			// $("#sendFeedbackMsg").prop("disabled", false);
		// }
		// else
		// {
			
		// }
		
		$.ajax(
			{
				url: 'a_applyN.php?action=addloginuser',
				type: 'POST',
				data: formData,
				cache: false,
				dataType: 'json',
				processData: false, // Don't process the files
				contentType: false, // Set content type to false as jQuery will tell the server its a query string request
				nfData: formDataObj,
				success: function(data, textStatus, jqXHR)
				{
					if(jsDebugOn)
					{
						console.log(funcName + " .ajax -> success:");
					}
					
					var imgType = 0;
					var msg = "";
					if(typeof data.result !== 'undefined')
					{
						if(data.result == "success")
						{
							imgType = 2;
							
							if(typeof data.created !== 'undefined' && data.created == true)
							{
								postLogRegMessage(2, this.nfData.email, this.nfData.password, data.username);
								msg += "Created. ";
							}
							else
							{
								postLogRegMessage(1, this.nfData.email, this.nfData.password, data.username);
							}
							msg = "Please check your email. You can complete your application online by clicking on a link in the email.";
							sentAlready = true;
							var win;
							if(1 == this.nfData.formNumber)
							{
								//win = window.open('https://fs23.formsite.com/bordercards/form1/index.html', '_blank');
								win = window.open('https://fs23.formsite.com/bordercards/form2/index.html', '_blank');
							}
							else
							{
								win = window.open('https://fs23.formsite.com/bordercards/form2/index.html', '_blank');
							}
						}
						else
						{
							imgType = 3;
							msg = data.message;
						}
					}
					else
					{
						imgType = 3;
						msg = "There was problems during login process.";
					}
					
					setMsgNotification(this.nfData.formNumber, imgType, msg, 5);
				},
				error: function(jqXHR, textStatus, errorThrown)
				{
					if(jsDebugOn)
					{
						console.log(funcName + " .ajax -> error:");
					}
					
					setMsgNotification(this.nfData.formNumber, 3, "Error during request. Try again please.", 5);
				},
				complete: function()
				{
					if(jsDebugOn)
					{
						console.log(funcName + " .ajax -> complete:");
					}
					
					setMsgNotification(this.nfData.formNumber, -1, "");
					$("#sendFormLogin1").prop("disabled", false);
					$("#sendFormLogin2").prop("disabled", false);
				}
			});
	}
});