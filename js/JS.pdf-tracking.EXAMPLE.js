// PDF tracking
// Script for changing old links to pdf files to new ones for tracking purposes (Google Analytics, etc.)

jQuery(document).ready(function()
{
	var gReferrers = 
	[
		{'id' :  0, 'domain' : "undefined", 'ga-tracking-id' : ""},
		{'id' :  1, 'domain' : "br***t.ca", 'ga-tracking-id' : "UA-1028278-31"},
		{'id' :  2, 'domain' : "ca***fo.ca", 'ga-tracking-id' : "UA-1028278-14"},
		{'id' :  3, 'domain' : "ne***on.com", 'ga-tracking-id' : "UA-1028278-31"},
		{'id' :  4, 'domain' : "ca***ip.ca", 'ga-tracking-id' : "UA-1028278-31"},
		{'id' :  5, 'domain' : "ci***te.ca", 'ga-tracking-id' : "UA-1028278-31"},
		{'id' :  6, 'domain' : "www.im***up.com", 'ga-tracking-id' : "UA-1028278-1"},
		{'id' :  7, 'domain' : "www.im***ts.ca", 'ga-tracking-id' : "UA-1028278-30"},
		{'id' :  8, 'domain' : "pr***da.ca", 'ga-tracking-id' : "UA-1028278-31"},
		{'id' :  9, 'domain' : "re***ng.ca", 'ga-tracking-id' : "UA-1028278-31"},
		{'id' : 10, 'domain' : "vi***us.ca", 'ga-tracking-id' : "UA-1028278-31"},
		{'id' : 11, 'domain' : "ma***an.com", 'ga-tracking-id' : "UA-1028278-31"},
		{'id' : 12, 'domain' : "ca***rt.com", 'ga-tracking-id' : "UA-1028278-6"}
	];
	
	var gFilesList = 
	[
		{'id' : 100, 'file' : "/forms/uk-passport-kit-first-time-applicant-16-and-over.pdf", 'coordX' : 10, 'coordY' : 0},
		{'id' : 101, 'file' : "/forms/uk-passport-kit-first-time-applicant-15-and-under.pdf", 'coordX' : 10, 'coordY' : 0},
		{'id' : 102, 'file' : "/forms/uk-passport-kit-renewal-16-and-over.pdf", 'coordX' : 10, 'coordY' : 0},
		{'id' : 103, 'file' : "/forms/uk-passport-kit-renewal-15-and-under.pdf", 'coordX' : 10, 'coordY' : 0},
		{'id' : 104, 'file' : "/forms/uk-passport-kit-first-time-applicants-born-outside-the-uk-to-british-mothers-before-1983.pdf", 'coordX' : 10, 'coordY' : 0},
		{'id' : 105, 'file' : "/forms/uk-passport-kit-first-time-applicants-born-outside-the-uk-to-british-fathers-before-2006.pdf", 'coordX' : 10, 'coordY' : 0},
		
		{'id' : 200, 'file' : "/forms/nexus-application.pdf", 'coordX' : 10, 'coordY' : 0},
		
		{'id' : 300, 'file' : "/forms/citizenship-application.pdf", 'coordX' : 10, 'coordY' : 0},
		
		{'id' : 400, 'file' : "/forms/replacement-citizenship-card-kit.pdf", 'coordX' : 10, 'coordY' : 0},
		{'id' : 401, 'file' : "/forms/first-citizenship-card-kit.pdf", 'coordX' : 10, 'coordY' : 0},
		
		{'id' : 500, 'file' : "/application-pdfs/canadian-documents/permanent-resident-card-application.pdf", 'coordX' : 10, 'coordY' : 0},
		{'id' : 501, 'file' : "/application-pdfs/canadian-documents/permanent-resident-card-residence-questionnaire.pdf", 'coordX' : 10, 'coordY' : 0},
		{'id' : 502, 'file' : "/application-pdfs/canadian-documents/pr-travel-document-application-kit.pdf", 'coordX' : 10, 'coordY' : 0},
		{'id' : 503, 'file' : "/application-pdfs/canadian-documents/refugee-travel-document-application-child.pdf", 'coordX' : 10, 'coordY' : 0},
		{'id' : 504, 'file' : "/application-pdfs/canadian-documents/refugee-travel-document-application.pdf", 'coordX' : 10, 'coordY' : 0},
		{'id' : 505, 'file' : "/application-pdfs/canadian-documents/request-to-amend-record-of-landing.pdf", 'coordX' : 10, 'coordY' : 0},
		{'id' : 506, 'file' : "/application-pdfs/canadian-documents/verification-of-status-imm-1000-form.pdf", 'coordX' : 10, 'coordY' : 0},
		{'id' : 507, 'file' : "/application-pdfs/citizenship/citizenship-application.pdf", 'coordX' : 10, 'coordY' : 0},
		{'id' : 508, 'file' : "/application-pdfs/citizenship/first-citizenship-card-kit.pdf", 'coordX' : 10, 'coordY' : 0},
		{'id' : 509, 'file' : "/application-pdfs/citizenship/replacement-citizenship-card-kit.pdf", 'coordX' : 10, 'coordY' : 0},
		{'id' : 510, 'file' : "/application-pdfs/citizenship/residence-questionnaire.pdf", 'coordX' : 10, 'coordY' : 0},
		{'id' : 511, 'file' : "/application-pdfs/citizenship/Search_of_Records_Application.pdf", 'coordX' : 10, 'coordY' : 0},
		{'id' : 512, 'file' : "/application-pdfs/fast-card/fast-card-application-us-canada-mexico.pdf", 'coordX' : 10, 'coordY' : 0},
		{'id' : 513, 'file' : "/application-pdfs/fast-card/hazardous-materials-endorsement-application.pdf", 'coordX' : 10, 'coordY' : 0},
		{'id' : 514, 'file' : "/application-pdfs/fast-card/transportation-worker-identification-credential-application.pdf", 'coordX' : 10, 'coordY' : 0},
		{'id' : 515, 'file' : "/application-pdfs/german/First-German-Passport-Application.pdf", 'coordX' : 10, 'coordY' : 0},
		{'id' : 516, 'file' : "/application-pdfs/german/First-German-Passport-Born-Before-1975-Application.pdf", 'coordX' : 10, 'coordY' : 0},
		{'id' : 517, 'file' : "/application-pdfs/german/First-German-Passport-Under-18-Application.pdf", 'coordX' : 10, 'coordY' : 0},
		{'id' : 518, 'file' : "/application-pdfs/german/German-Passport-Renewal-Application.pdf", 'coordX' : 10, 'coordY' : 0},
		{'id' : 519, 'file' : "/application-pdfs/german/German-Passport-Renewal-Under-18-Application.pdf", 'coordX' : 10, 'coordY' : 0},
		{'id' : 520, 'file' : "/application-pdfs/uk-documents/uk-passport-in-usa-first-time-applicant-15-and-under.pdf", 'coordX' : 10, 'coordY' : 0},
		{'id' : 521, 'file' : "/application-pdfs/uk-documents/uk-passport-in-usa-first-time-applicant-16-and-over.pdf", 'coordX' : 10, 'coordY' : 0},
		{'id' : 522, 'file' : "/application-pdfs/uk-documents/uk-passport-in-usa-first-time-applicants-born-outside-the-uk-to-british-mothers-before-1983.pdf", 'coordX' : 10, 'coordY' : 0},
		{'id' : 523, 'file' : "/application-pdfs/uk-documents/uk-passport-in-usa-renewal-15-and-under.pdf", 'coordX' : 10, 'coordY' : 0},
		{'id' : 524, 'file' : "/application-pdfs/uk-documents/uk-passport-in-usa-renewal-16-and-over.pdf", 'coordX' : 10, 'coordY' : 0},
		{'id' : 525, 'file' : "/application-pdfs/uk-documents/uk-passport-kit-first-time-applicant-15-and-under-campaign.pdf", 'coordX' : 10, 'coordY' : 0},
		{'id' : 526, 'file' : "/application-pdfs/uk-documents/uk-passport-kit-first-time-applicant-15-and-under.pdf", 'coordX' : 10, 'coordY' : 0},
		{'id' : 527, 'file' : "/application-pdfs/uk-documents/uk-passport-kit-first-time-applicant-16-and-over-campaign.pdf", 'coordX' : 10, 'coordY' : 0},
		{'id' : 528, 'file' : "/application-pdfs/uk-documents/uk-passport-kit-first-time-applicant-16-and-over.pdf", 'coordX' : 10, 'coordY' : 0},
		{'id' : 529, 'file' : "/application-pdfs/uk-documents/uk-passport-kit-first-time-applicant-before-1983.pdf", 'coordX' : 10, 'coordY' : 0},
		{'id' : 530, 'file' : "/application-pdfs/uk-documents/uk-passport-kit-first-time-applicants-born-outside-the-uk-to-british-fathers-before-2006.pdf", 'coordX' : 10, 'coordY' : 0},
		{'id' : 531, 'file' : "/application-pdfs/uk-documents/uk-passport-kit-first-time-applicants-born-outside-the-uk-to-british-mothers-before-1983.pdf", 'coordX' : 10, 'coordY' : 0},
		{'id' : 532, 'file' : "/application-pdfs/uk-documents/uk-passport-kit-renewal-15-and-under-campaign.pdf", 'coordX' : 10, 'coordY' : 0},
		{'id' : 533, 'file' : "/application-pdfs/uk-documents/uk-passport-kit-renewal-15-and-under.pdf", 'coordX' : 10, 'coordY' : 0},
		{'id' : 534, 'file' : "/application-pdfs/uk-documents/uk-passport-kit-renewal-16-and-over-campaign.pdf", 'coordX' : 10, 'coordY' : 0},
		{'id' : 535, 'file' : "/application-pdfs/uk-documents/uk-passport-kit-renewal-16-and-over.pdf", 'coordX' : 10, 'coordY' : 0},
		{'id' : 536, 'file' : "/application-pdfs/us-visa/goesform.pdf", 'coordX' : 10, 'coordY' : 0},
		{'id' : 537, 'file' : "/application-pdfs/us-visa/nexus-application-renew.pdf", 'coordX' : 10, 'coordY' : 0},
		{'id' : 538, 'file' : "/application-pdfs/us-visa/nexus-application.pdf", 'coordX' : 10, 'coordY' : 0},
		{'id' : 539, 'file' : "/application-pdfs/us-visa/sentri-card-goes-application.pdf", 'coordX' : 10, 'coordY' : 0},
		{'id' : 540, 'file' : "/application-pdfs/us-visa/sentri-goes-additional-vehicle.pdf", 'coordX' : 10, 'coordY' : 0},
		{'id' : 541, 'file' : "/application-pdfs/us-visa/tsa-precheck-application.pdf", 'coordX' : 10, 'coordY' : 0},
		{'id' : 542, 'file' : "/application-pdfs/us-visa/us-passport-application-adult.pdf", 'coordX' : 10, 'coordY' : 0},
		{'id' : 543, 'file' : "/application-pdfs/us-visa/us-passport-application-child.pdf", 'coordX' : 10, 'coordY' : 0},
		{'id' : 544, 'file' : "/application-pdfs/us-visa/us-passport-application-mail.pdf", 'coordX' : 10, 'coordY' : 0},
		{'id' : 545, 'file' : "/application-pdfs/us-visa/us-visa-ds-160-order-form.pdf", 'coordX' : 10, 'coordY' : 0},
		{'id' : 546, 'file' : "/application-pdfs/canadian-documents/replacement-citizenship-card-kit.pdf", 'coordX' : 10, 'coordY' : 0},
		
		{'id' : 600, 'file' : "/immigration-facts/citizenship-application.pdf", 'coordX' : 10, 'coordY' : 0},
		{'id' : 601, 'file' : "/immigration-facts/fast-card-application-us-canada-mexico.pdf", 'coordX' : 10, 'coordY' : 0},
		{'id' : 602, 'file' : "/immigration-facts/first-citizenship-card-kit.pdf", 'coordX' : 10, 'coordY' : 0},
		{'id' : 603, 'file' : "/immigration-facts/hazardous-materials-endorsement-application.pdf", 'coordX' : 10, 'coordY' : 0},
		{'id' : 604, 'file' : "/immigration-facts/nexus-application.pdf", 'coordX' : 10, 'coordY' : 0},
		{'id' : 605, 'file' : "/immigration-facts/permanent-resident-card-application.pdf", 'coordX' : 10, 'coordY' : 0},
		{'id' : 606, 'file' : "/immigration-facts/replacement-citizenship-card-kit.pdf", 'coordX' : 10, 'coordY' : 0},
		{'id' : 607, 'file' : "/immigration-facts/request-to-amend-record-of-landing.pdf", 'coordX' : 10, 'coordY' : 0},
		{'id' : 608, 'file' : "/immigration-facts/residence-questionnaire.pdf", 'coordX' : 10, 'coordY' : 0},
		{'id' : 609, 'file' : "/immigration-facts/sentri-card-goes-application.pdf", 'coordX' : 10, 'coordY' : 0},
		{'id' : 610, 'file' : "/immigration-facts/SENTRI-en-espanol.pdf", 'coordX' : 10, 'coordY' : 0},
		{'id' : 611, 'file' : "/immigration-facts/sentri-goes-additional-vehicle.pdf", 'coordX' : 10, 'coordY' : 0},
		{'id' : 612, 'file' : "/immigration-facts/sentri-goes-vehicle.pdf", 'coordX' : 10, 'coordY' : 0},
		{'id' : 613, 'file' : "/immigration-facts/transportation-worker-identification-credential-application.pdf", 'coordX' : 10, 'coordY' : 0},
		{'id' : 614, 'file' : "/immigration-facts/tsa-precheck-application.pdf", 'coordX' : 10, 'coordY' : 0},
		{'id' : 615, 'file' : "/immigration-facts/uk-passport-in-usa-first-time-applicant-15-and-under.pdf", 'coordX' : 10, 'coordY' : 0},
		{'id' : 616, 'file' : "/immigration-facts/uk-passport-in-usa-first-time-applicant-16-and-over.pdf", 'coordX' : 10, 'coordY' : 0},
		{'id' : 617, 'file' : "/immigration-facts/uk-passport-in-usa-renewal-15-and-under.pdf", 'coordX' : 10, 'coordY' : 0},
		{'id' : 618, 'file' : "/immigration-facts/uk-passport-in-usa-renewal-16-and-over.pdf", 'coordX' : 10, 'coordY' : 0},
		{'id' : 619, 'file' : "/immigration-facts/uk-passport-kit-first-time-applicant-15-and-under.pdf", 'coordX' : 10, 'coordY' : 0},
		{'id' : 620, 'file' : "/immigration-facts/uk-passport-kit-first-time-applicant-16-and-over.pdf", 'coordX' : 10, 'coordY' : 0},
		{'id' : 621, 'file' : "/immigration-facts/uk-passport-kit-renewal-15-and-under.pdf", 'coordX' : 10, 'coordY' : 0},
		{'id' : 622, 'file' : "/immigration-facts/uk-passport-kit-renewal-16-and-over.pdf", 'coordX' : 10, 'coordY' : 0},
		{'id' : 623, 'file' : "/immigration-facts/us-passport-application-adult.pdf", 'coordX' : 10, 'coordY' : 0},
		{'id' : 624, 'file' : "/immigration-facts/us-passport-application-child.pdf", 'coordX' : 10, 'coordY' : 0},
		{'id' : 625, 'file' : "/immigration-facts/us-passport-application-mail.pdf", 'coordX' : 10, 'coordY' : 0},
		{'id' : 626, 'file' : "/immigration-facts/us-visa-ds-160-order-form.pdf", 'coordX' : 10, 'coordY' : 0},
		{'id' : 627, 'file' : "/immigration-facts/verification-of-status-imm-1000-form.pdf", 'coordX' : 10, 'coordY' : 0},
		
		{'id' : 700, 'file' : "/forms/permanent-resident-card-application.pdf", 'coordX' : 10, 'coordY' : 0},
		{'id' : 701, 'file' : "/forms/pr-travel-document-application-kit.pdf", 'coordX' : 10, 'coordY' : 0},
		
		{'id' : 800, 'file' : "/forms/verification-of-status-imm-1000-form.pdf", 'coordX' : 10, 'coordY' : 0},
		{'id' : 801, 'file' : "/forms/request-to-amend-record-of-landing.pdf", 'coordX' : 10, 'coordY' : 0},
		
		{'id' : 900, 'file' : "/forms/us-visa-ds-160-order-form.pdf", 'coordX' : 10, 'coordY' : 0}
	];
	
	


	var GAUA = 'UA-1028278-1'; // GA tracking ID
	var attempts = 1000;
	if(typeof window.getParameterByName === 'undefined')
	{
		function getParameterByName(name, url)
		{
			if (!url)
			{
				url = window.location.href;
			}
			name = name.replace(/[\[\]]/g, "\\$&");
			var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
				results = regex.exec(url);
			if (!results) return null;
			if (!results[2]) return '';
			return decodeURIComponent(results[2].replace(/\+/g, " "));
		}
	}

	if(typeof window.whenAvailable === 'undefined')
	{
		function whenAvailable(name, callback)
		{
			var interval = 10; // ms
			attempts--;
			window.setTimeout(function()
			{
				if (window[name] || attempts < 1)
				{
					callback(window[name]);
				}
				else
				{
					window.setTimeout(arguments.callee, interval);
				}
			}, interval);
		}
	}

	whenAvailable("ga", function(t)
	{
		ga(function()
		{
			try
			{
				var pdfSignUrl = '//www.im***up.com/PDFS/PDF.php';
				var curDomain = window.location.hostname.substring(window.location.hostname.lastIndexOf(".", window.location.hostname.lastIndexOf(".") - 1) + 1);
				var curRefId = 0;
				for(var j = 0; j < gFilesList.length; j++)
				{
					var curRowDomain = gReferrers[j]['domain'];
					curRowDomain = curRowDomain.substring(curRowDomain.lastIndexOf(".", curRowDomain.lastIndexOf(".") - 1) + 1);
					if(curRowDomain.toLowerCase() == curDomain.toLowerCase())
					{
						curRefId = gReferrers[j]['id'];
						GAUA = gReferrers[j]['ga-tracking-id'];
						break;
					}
				}
				
				jQuery("a").each(function(index, element)
				{
					for(var j = 0; j < gFilesList.length; j++)
					{
						if(element.pathname.toLowerCase() == gFilesList[j]['file'].toLowerCase())
						{
							var newHref = pdfSignUrl + '?action=sign&fid=' + gFilesList[j]['id'] + '&refererid=' + curRefId;
							jQuery(this).attr("href", newHref);
							jQuery(this).removeClass('pdftrackingmarker');
							jQuery(this).addClass('pdftrackingmarker');
							break;
						}
					}
				});
		
				var cid = "";
				var trackers = ga.getAll();
				for (var i = 0; i < trackers.length; i ++)
				{
					if (trackers[i].get('trackingId') === GAUA)
					{
						//console.log(trackers[i].get('clientId'));
						cid = trackers[i].get('clientId');
					}
				}
				jQuery(".pdftrackingmarker").each(function()
				{
					var hrefAttr = jQuery(this).attr("href");
					//console.log(hrefAttr);
					var cidReq = getParameterByName("cid", hrefAttr);
					if(null !== cidReq)
					{
						hrefAttr = hrefAttr.replace(/cid=[^&#]*/g, "cid=" + cid);
					}
					else
					{
						hrefAttr += '&cid=' + cid;
					}
					jQuery(this).attr("href", hrefAttr);
					//console.log(hrefAttr);
				});
				
			}
			catch(e)
			{
				console.log(e);
			}  
			return 'false';
		});
	});
});
// PDF tracking
