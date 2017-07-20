
$(function()
{
	
	var jsDebugOn = false;
	var sentAlready = false;
	$("#sendFeedbackMsg").on('click', a_sendMsg);

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

	function setMsgNotification(type, htmlTxt, hideInterval = 0)
	{
		var imgHtml = "";
		var msgHtml = "";
		if(typeof type !== 'undefined')
		{
			if(0 == type)
			{
				imgHtml = ''
			}
			if(1 == type)
			{
				imgHtml = '<img src="images/fm-spinning_wheel.gif" title="sending" alt="sending" />'
			}
			if(2 == type)
			{
				imgHtml = '<img src="images/fm-tick.png" title="sending" alt="sending" />'
			}
			if(3 == type)
			{
				imgHtml = '<img src="images/fm-rc.png" title="sending" alt="sending" />'
			}
			
			if(-1 == type)
			{
				if($("#contact-form #msgStat").html().indexOf('<img src="images/fm-spinning_wheel.gif" title="sending" alt="sending" />') >= 0)
				{
					msgHtml = "";
				}
				else
				{
					msgHtml = $("#contact-form #msgStat").html();
					hideInterval = 5;
				}
			}
			else
			{
				msgHtml = imgHtml + ' ' + htmlTxt;
			}
		}
		
		if($("#contact-form #msgStat").length)
		{
			$("#contact-form #msgStat").removeClass('mhidd');
			$("#contact-form #msgStat").html(msgHtml);
		}
		
		if(hideInterval > 0)
		{
			setTimeout(hideMsgNotification, hideInterval * 1000);
		}
	}
	
	function hideMsgNotification()
	{
		$("#contact-form #msgStat").addClass('mhidd');
	}

	function a_sendMsg()
	{
		var funcName = "";
		if(jsDebugOn)
		{
			funcName = arguments.callee.toString().match(/function ([^\(]+)/)[1] + '()';
			console.log(funcName);
		}
		
		$("#sendFeedbackMsg").prop("disabled", true);
		
		var formData = new FormData();
		formData.append("name", $("#contact-form #a_msgName").val());
		formData.append("email", $("#contact-form #a_msgEmail").val());
		formData.append("phone", $("#contact-form #a_msgPhone").val());
		formData.append("comments", $("#contact-form #a_msgComments").val());
		
		setMsgNotification(1, "Sending");
		
		if(sentAlready)
		{
			setMsgNotification(3, "Message already sent.", 5);
			$("#sendFeedbackMsg").prop("disabled", false);
		}
		else
		{
			$.ajax(
			{
				url: 'a_sendM.php?action=sendmsg',
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
						console.log(funcName + " .ajax -> success:");
					}
					
					var imgType = 0;
					var msg = "";
					if(typeof data.result !== 'undefined')
					{
						if(data.result == "success")
						{
							imgType = 2;
							msg = "Message sent. Thank you!";
							sentAlready = true;
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
						msg = "There was problems during sending email, please try once more.";
					}
					
					setMsgNotification(imgType, msg, 5);
				},
				error: function(jqXHR, textStatus, errorThrown)
				{
					if(jsDebugOn)
					{
						console.log(funcName + " .ajax -> error:");
					}
					
					setMsgNotification(3, "Error during request. Try again please.", 5);
				},
				complete: function()
				{
					if(jsDebugOn)
					{
						console.log(funcName + " .ajax -> complete:");
					}
					
					setMsgNotification(-1, "");
					$("#sendFeedbackMsg").prop("disabled", false);
				}
			});
		}
	}
});