var API_1484_11 = new Object();
var API = new Object();

API_1484_11.Initialize = DokeosInitialize;
API_1484_11.Terminate = DokeosTerminate;
API_1484_11.GetValue = DokeosGetValue;
API_1484_11.SetValue = DokeosSetValue;
API_1484_11.Commit = DokeosCommit;
API_1484_11.GetLastError = DokeosGetLastError;
API_1484_11.GetErrorString = DokeosGetErrorString;
API_1484_11.GetDiagnostic = DokeosGetDiagnostic;
API_1484_11.values = new Array();
API_1484_11.version = "1.0";

API.LMSInitialize = DokeosInitialize;
API.LMSFinish = DokeosTerminate;
API.LMSGetValue = DokeosGetValue;
API.LMSSetValue = DokeosSetValue;
API.LMSCommit = DokeosCommit;
API.LMSGetLastError = DokeosGetLastError;
API.LMSGetErrorString = DokeosGetErrorString;
API.LMSGetDiagnostic = DokeosGetDiagnostic;
API.values = new Array();
API.version = "1.0";

var last_error = 0;
var initialized = false;

function DokeosInitialize(params)
{
	if(params && params != "")
	{
		last_error = 201;
		return "false";
	}
	
	if(initialized)
	{
		last_error = 103;
		return "false"
	}
	
	initialized = true;	
	last_error = 0;
	
	return "true";
}

function DokeosTerminate(params)
{
	if(params && params != "")
	{
		last_error = 201;
		return "false";
	}
	
	if(!initialized)
	{
		last_error = 301;
		return "false";
	}

	initialized = false;
	last_error = 0;
	
	var response = jQuery.ajax({
		type: "POST",
		url: "./application/lib/weblcms/tool/learning_path/javascript/scorm/ajax/terminate.php",
		data: { tracker_id: tracker_id },
		async: false
	}).responseText; //alert(response);
	
	check_redirect_conditions(this.values);
	
	return "true";
}

function check_redirect_conditions(values)
{
	var url = null;
	var request = values['adl.nav.request'];
	if(request == null)
		return

	if(request == 'continue')
	{
		url = continue_url;
	}
	
	if(request == 'previous')
	{
		url = previous_url;
	}

	 var re = new RegExp('{target=.*}jump');
	 if(request.match(re))
	 {
		 var re = new RegExp('{.*}');
		 var m = re.exec(request);
		 var identifier = m[0];
		 identifier = identifier.substr(8, identifier.length - 9);
		 url = jump_urls[identifier];
	 }
	
	if(url)
	{
		window.location = url;
	}
}

function DokeosGetValue(variable)
{
	if(!initialized)
	{
		last_error = 122;
		last_error = 301;
		return "";
	}
	
	if(variable == "")
	{
		last_error = 301;
		return "";
	}
	//alert(variable);
	last_error = 0;
	
	var value = check_for_special_requests(variable);
	if(value)
		return value;
	
	value = this.values[variable]; 
	if(!value)
	{ 
		value = get_existing_value(variable);
	}

	if(value == "")
	{
		last_error = 403;
	}
	
	return value;
}

function check_for_special_requests(variable)
{
	 if(variable == "adl.nav.request_valid.continue")
	 {
		 if (continue_url != null)
			 return "true";
		 else
			 return "false";
	 }
	 
	 if(variable == "adl.nav.request_valid.previous")
	 {
		 if (previous_url != null)
			 return "true";
		 else
			 return "false";
	 }
	 
	 var re = new RegExp('adl.nav.request_valid.choice.{target=.*}');
	 if(variable.match(re))
	 {
		 var re = new RegExp('{.*}');
		 var m = re.exec(variable);
		 var identifier = m[0];
		 identifier = identifier.substr(8, identifier.length - 9);
		 
		 if(jump_urls[identifier] != null)
			 return "true";
		 else
			 return "false";
		 
	 }
}

function DokeosSetValue(variable, value)
{
	if(!initialized)
	{
		last_error = 132;
		last_error = 301;
		return "false";
	}
	
	if(variable == "")
	{
		last_error = 351;
		return "false";
	}
	
	if(!validate_set_variable(variable, value))
		return "false";
	
	this.values[variable] = value;
	last_error = 0;
	
	var response = jQuery.ajax({
		type: "POST",
		url: "./application/lib/weblcms/tool/learning_path/javascript/scorm/ajax/set_value.php",
		data: { tracker_id: tracker_id, variable: variable, value: value },
		async: false
	}).responseText; //alert(response);
	
	if(response.substr(0, 5) == 'error')
	{
		last_error = parseInt(response.substr(6, response.length - 6));
		return "false";
	}
	
	return "true";
}

function validate_set_variable(variable, value)
{
	 var re = new RegExp('cmi.objectives.[0-9]*.id');
	 if(variable.match(re))
	 {
		 var existing_value = get_existing_value(variable);
		 if(existing_value.length != 0 && existing_value != value)
		 {
			 last_error = 351;
			 return false;
		 }
		 else
			 return true;
	 }
	 
	 if(variable == 'cmi.completion_status')
	 {
		 var possible_values = ['incomplete', 'completed', 'not attempted', 'unknown'];
		 if(!in_array(value, possible_values))
		 {
			 last_error = 406;
			 return false;
		 }
	 }
	 
	 return true;
}

function get_existing_value(variable)
{
	var value = jQuery.ajax({
		type: "POST",
		url: "./application/lib/weblcms/tool/learning_path/javascript/scorm/ajax/get_value.php",
		data: { tracker_id: tracker_id, variable: variable},
		async: false
	}).responseText; 
	
	return value;
}

function DokeosCommit(params)
{
	if(params && params != "")
	{
		last_error = 201;
		return "false";
	}
	
	if(!initialized)
	{
		last_error = 142;
		last_error = 301;
		return "false";
	}
	
	last_error = 0;
	
	return "true";
}

function DokeosGetLastError()
{
	return last_error;
}

function DokeosGetErrorString(error_code)
{
	return "";
}

function DokeosGetDiagnostic()
{
	return "";
}

// Helper function

function translation(string, application) {		
	var translated_string = $.ajax({
		type: "POST",
		url: "./common/javascript/ajax/translation.php",
		data: { string: string, application: application },
		async: false
	}).responseText;
	
	return translated_string;
}

function in_array(needle, haystack) 
{
	for (var i = 0; i < haystack.length; i++) 
    {
    	if(haystack[i] == needle)
    	{
    		return true;
    	}
    }
 
    return false;
}