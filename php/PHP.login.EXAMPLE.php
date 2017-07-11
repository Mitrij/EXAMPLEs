<?php
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

define('ETC_REPORTS', true);
define('INCLUDES_PATH', './includes');

require_once (INCLUDES_PATH . "/" . "settings_GS.php");
require_once (INCLUDES_PATH . "/" . "functions_GF.php");
require_once (INCLUDES_PATH . "/" . 'securimage_MA7/securimage.php');

if(!isset($settingsDebug_m) || 0 == $settingsDebug_m)
{
	error_reporting(0);
}
else
{
	error_reporting(E_ALL );
}


$p_login = "";
$p_login_message = "";
$p_password = "";
$p_password_message = "";
$p_captcha = "";
$p_captcha_message = "";
$p_error = false;
$p_error_message = "";


$jsonArr = array
(
	"result" => ""
	,"message" => ""
	,"p_login_message" => ""
	,"p_password_message" => ""
	,"p_captcha_message" => ""
	,"p_error_message" => ""
);


if(isset($_REQUEST['l_action']) && "login" == $_REQUEST["l_action"])
{
	$securimage = new Securimage();
	if(u_checkIfIpBlocked($_SERVER['REMOTE_ADDR']))
	{
		$p_error = true;
		$p_error_message = "Your IP was blocked for " . $settingsHoursToBlockBadLogins . " hour(s). Please try to login later";
	}
	else
	{
		if(isset($_POST["u_login"]) && strlen($_POST["u_login"]) > 0)
		{
			$p_login = $_POST["u_login"];
			if(strlen($p_login) > 20)
			{
				$p_login = substr($p_login, 0, 20);
			}
		}
		else
		{
			$p_error = true;
			$p_login_message = "login not specified";
		}
		if(isset($_POST["u_pass"]) && strlen($_POST["u_pass"]) > 0)
		{
			$p_password = $_POST["u_pass"];
			if(strlen($p_password) > 20)
			{
				$p_password = substr($p_password, 0, 20);
			}
		}
		else
		{
			$p_error = true;
			$p_password_message = "password not specified";
		}
		
		if(isset($_POST["u_cpatcha"]))
		{
			$p_captcha = $_POST["u_cpatcha"];
			if(strlen($p_captcha) > 20)
			{
				$p_captcha = substr($p_captcha, 0, 20);
			}

			if(false == $securimage->check($p_captcha))
			{
				$p_error = true;
				$p_captcha_message = "entered cpatcha incorrect, try again";
			}
		}
		else
		{
			$p_error = true;
			$p_captcha_message = "cpatcha not specified";
		}
		
		if(!$p_error)
		{
			if(u_do_login($p_login, $p_password))
			{
				u_resetBadLoginAttemptCount($_SERVER['REMOTE_ADDR'], $p_login);
				$jsonArr["result"] = "success";
				$jsonArr["message"] = "login successful";
			}
			else
			{
				$jsonArr["result"] = "error";
				$p_error_message = "Bad login attempt. ";
				u_processBadLoginAttempt($_SERVER['REMOTE_ADDR'], $p_login);
				$p_error_message .= ($settingsMaxBadLoginsAttempts - u_badLoginAttemptCount($_SERVER['REMOTE_ADDR']));
				$p_error_message .= " attempt(s) left";
			}
		}
		else
		{
			$jsonArr["result"] = "error";
			$p_error_message = "not all fields was filled correctly";
		}
	}
	
	if($p_error)
	{
		$jsonArr["result"] = "error";
	}
	
	$jsonArr["p_login_message"] = $p_login_message;
	$jsonArr["p_password_message"] = $p_password_message;
	$jsonArr["p_captcha_message"] = $p_captcha_message;
	$jsonArr["p_error_message"] = $p_error_message;
}
else
{
	$jsonArr["result"] = "error";
	$jsonArr["message"] = "wrong request to this page.";
}

echo json_encode($jsonArr);
?>