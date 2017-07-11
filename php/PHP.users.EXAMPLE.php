<?php

define('INCLUDES_PATH', './includes');
define('ETC_REPORTS', true);

require_once (INCLUDES_PATH . "/" . "settings_GS.php");
require_once (INCLUDES_PATH . "/" . "functions_GF.php");

if(!isset($settingsDebug_m) || 0 == $settingsDebug_m)
{
	error_reporting(0);
}
else
{
	error_reporting(E_ALL );
}


// - page setting section
$pageUrlPath = $_SERVER['REQUEST_URI'];
$mustBeLogedIn = true;
$isUserLogedIn = u_isLogedIn();


if($mustBeLogedIn && !$isUserLogedIn)
{
	header("Location: " . $settingsPages["login"]["path"] . "?redir=" . urlencode($_SERVER['REQUEST_URI']));
	exit();
}

if(!u_isUserAllowed($settingsPages["users"]["access"]))
{
	header("Location: " . $settingsPages["main"]["path"]);
	exit();
}
// ---

$p_users_list = array();
$p_groups_list = array();
$p_action = "";
$p_user_id = -1;
$p_login = "";
$p_name = "";
$p_password = "";
$p_group_id = -1;
$p_email = "";
$p_u_enabled = false;
$p_e_submit = "";
$p_error = false;
$p_error_message = "";
$p_success_message = "";
$p_cur_name = "";
$p_cur_group_id = -1;
$p_cur_email = "";
$p_cur_u_enabled = false;


if(isset($_POST["action"]))
{
	$p_action = $_POST["action"];
}
if(isset($_POST["e_user_sel"]))
{
	$p_user_id = intval($_POST["e_user_sel"]);
}
if(isset($_POST["u_login"]))
{
	$p_login = $_POST["u_login"];
	if(strlen($p_name) > 20)
	{
		$p_login = substr($p_login, 0, 20);
	}
}
if(isset($_POST["u_name"]))
{
	$p_name = $_POST["u_name"];
	if(strlen($p_name) > 50)
	{
		$p_name = substr($p_name, 0, 50);
	}
}
if(isset($_POST["u_pass"]))
{
	$p_password = $_POST["u_pass"];
	if(strlen($p_password) > 20)
	{
		$p_password = substr($p_password, 0, 20);
	}
}
if(isset($_POST["e_group_sel"]))
{
	$p_group_id = intval($_POST["e_group_sel"]);
}
if(isset($_POST["u_email"]))
{
	$p_email = $_POST["u_email"];
	if(strlen($p_email) > 80)
	{
		$p_error = true;
		$p_error_message = "email longer than 80 chars";
		$p_email = substr($p_email, 0, 80);
	}
}
if(isset($_POST["u_enabled"]) && $_POST["u_enabled"])
{
	$p_u_enabled = true;
}

if(isset($_POST["e_submit_edit"]))
{
	$p_e_submit = "edit";
}
if(isset($_POST["e_submit_delete"]))
{
	$p_e_submit = "delete";
}
if(isset($_POST["e_submit_show"]))
{
	$p_e_submit = "show";
}
if(isset($_POST["e_submit_add"]))
{
	$p_e_submit = "add";
}

if(!$p_error)
{
	if("editdelete" == $p_action)
	{
		if("edit" == $p_e_submit)
		{
			if($p_user_id > 0 && $p_group_id > 0)
			{
				if(u_editUser($p_user_id, $p_group_id, $p_u_enabled , $p_password, $p_name, $p_email))
				{
					$p_success_message = "user record updated";
				}
				else
				{
					$p_error = true;
					$p_error_message = "error while user record update";
				}
			}
			else
			{
				$p_error = true;
				$p_error_message = "failed to update, not all necessary data was specified";
			}
		}
		else if("delete" == $p_e_submit)
		{
			if($p_user_id > 0)
			{
				if(u_deleteUser($p_user_id))
				{
					$p_success_message = "user was deleted";
				}
				else
				{
					$p_error = true;
					$p_error_message = "error while deleting user record";
				}
			}
			else
			{
				$p_error = true;
				$p_error_message = "failed to delete, not all necessary data was specified";
			}
		}
		else if("show" == $p_e_submit)
		{
			
		}
		else
		{
			$p_error = true;
			$p_error_message = "unknown action";
		}
	}
	else if("add" == $p_action)
	{
		if(strlen($p_login)> 0 && strlen($p_password) > 0 && $p_group_id > 0)
		{
			$uArr = DBgetUserDataByLogin($p_login);

			if(false !== $uArr)
			{
				$p_error = true;
				$p_error_message = "there is already user in DB with such login";
			}
			else if(u_addUser($p_login, $p_password, $p_group_id, $p_u_enabled , $p_name, $p_email))
			{
				$p_success_message = "user record added";
			}
			else
			{
				$p_error = true;
				$p_error_message = "error while adding user record";
			}
		}
		else
		{
			$p_error = true;
			$p_error_message = "failed to add, not all necessary data was specified";
		}
	}

}

$p_users_list = u_getUsersList();
$p_groups_list = u_getGroupsList();
if(false === $p_users_list)
{
	$p_users_list = array();
}
if(false === $p_groups_list)
{
	$p_groups_list = array();
}

foreach($p_users_list as $k => $v)
{
	if($v["id"] == $p_user_id)
	{
		$p_cur_name = $v["name"];
		$p_cur_group_id = $v["u_group"];
		$p_cur_email = $v["email"];
		$p_cur_u_enabled = $v["enabled"];
	}
}


require_once('header.php');
?>



	<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      <div class="container-fluid">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#">Excel to Csv</a>
        </div>
        <div class="navbar-collapse collapse">
          
		  <?php require_once('./nav_top.php'); ?>
          
        </div>
      </div>
    </div>

    <div class="container-fluid">
      <div class="row">
        <div class="col-md-2 sidebar">
          
		  <?php require_once('./nav_side.php'); ?>

        </div>
        <div class="col-md-10 col-md-offset-2 main">
			<h1 class="page-header">User manage page</h1>
<!-- !!!!!!!!!!!!!!!!!! -->
			<div class="row">
				<div class="col-md-5">
					<h3>Add new user</h3>
					<br />
					<form action="" method="post" id="e_user_form_add">
						<input name="action" id="add_acion" type="hidden" value="add">
						
						<div class="row form-group">
							<div class="col-md-12">
								<label for="u_login">*Login:</label>
								<input name="u_login" id="u_login"  class="form-control input-sm" maxlength="20">
							</div>
						</div>
						
						<div class="row form-group">
							<div class="col-md-12">
								<label for="u_name">Name:</label>
								<input name="u_name" id="u_name" class="form-control input-sm" maxlength="50">
							</div>
						</div>
						
						<div class="row form-group">
							<div class="col-md-12">
								<label for="u_pass">*Password:</label>
								<input name="u_pass" id="u_pass" type="password" class="form-control input-sm"  maxlength="20">
							</div>
						</div>
						
						<div class="row form-group">
							<div class="col-md-12">
								<label for="e_group_sel">*Group:</label>
								<select name="e_group_sel" id="e_group_sel" class="form-control input-sm" style="width: 100%;">
									<?php
										echo "\n";
										foreach($p_groups_list as $k => $v)
										{
											echo '<option value="' . $v["g_id"] . '">' . $v["g_name"] . '</option>' . "\n";
										}
									?>
								</select>
							</div>
						</div>
						
						<div class="row form-group">
							<div class="col-md-12">
								<label for="u_email">Email:</label>
								<input name="u_email" id="u_email" class="form-control input-sm" maxlength="80">
							</div>
						</div>

						<div class="row form-group">
							<div class="col-md-12" class="checkbox checkbox-primary">
								<label for="u_enabled" class="checkbox-inline">
									<input name="u_enabled" id="u_enabled" class="" type="checkbox" checked> - <strong>Enabled</strong>
								</label>
							</div>
						</div>
						
						<div class="row form-group">
							<div class="col-md-11">
								<input type="submit" name="e_submit_add" id="e_submit_add" value="Add user" class="btn btn-default">
							</div>
						</div>
						
						<div class="row form-group">
							<div class="col-md-12" id="img_proc_01">
							</div>
						</div>
							
						<div id="l_err_msg2">
						<?php
							/*
							if(strlen($p_error_message) > 0)
							//{
						?>
								<div class="alert alert-danger" style="padding: 5;">
									<strong>Error: </strong><?php echo $p_error_message; ?>
								</div>
								<br />
						<?php
							}
							*/
						?>
						</div>
					</form>
					
					
					<div id="l_msg_01">
						<?php
							if("add" == $p_action && strlen($p_success_message) > 0)
							{
						?>
								<div class="alert alert-success" style="padding: 5;">
									<strong>Success: </strong><?php echo $p_success_message; ?>
								</div>
								<br />
						<?php
							}
							
							if("add" == $p_action && strlen($p_error_message) > 0)
							{
						?>
								<div class="alert alert-danger" style="padding: 5;">
									<strong>Error: </strong><?php echo $p_error_message; ?>
								</div>
								<br />
						<?php
							}
						?>
					</div>

				</div>
				
				
				<div class="col-md-1">
				</div>
				
				
				<div class="col-md-5">
					<h3>Manage existing users</h3>
					<br /><a name="meu"></a>
					<form action="#meu" method="post" id="e_user_form_manage">
						<input name="action" id="editdelete_acion" type="hidden" value="editdelete">
						
						<div class="row form-group">
							<div class="col-md-12">
								<label for="e_user_sel">*Select user:</label>
								<select name="e_user_sel" id="e_user_sel" size="5" class="form-control input-medium" style="width: 100%;">
									<?php
										echo "\n";
										foreach($p_users_list as $k => $v)
										{
											$selected = "";
											if($v["id"]  == $p_user_id)
											{
												$selected = "selected";
											}
											echo '<option value="' . $v["id"] . '" ' . $selected . '>' . $v["login"] . ' - (' . $v["name"] . ')</option>' . "\n";
										}
									?>
								</select>
							</div>
						</div>
						
						<div class="row form-group">
							<div class="col-md-12" id="show_btn_div">
								<input type="submit" name="e_submit_show" id="e_submit_show" value="Show user info" class="btn btn-default">
								<br /><br />
							</div>
						</div>
						
						<!--
						<div class="row form-group">
							<div class="col-md-12">
								<label for="login">Login:</label>
								<input name="u_login" id="u_login" disabled class="form-control input-sm" maxlength="20">
							</div>
						</div>
						-->
						
						<div class="row form-group">
							<div class="col-md-12">
								<label for="u_name">Name:</label>
								<input name="u_name" id="u_name" class="form-control input-sm"  maxlength="50" value="<?php echo $p_cur_name ?>">
							</div>
						</div>
						
						<div class="row form-group">
							<div class="col-md-12">
								<label for="u_pass">Password:</label>
								<input name="u_pass" id="u_pass" type="password" class="form-control input-sm"  maxlength="20">
							</div>
						</div>
						
						<div class="row form-group">
							<div class="col-md-12">
								<label for="e_group_sel">Group:</label>
								<select name="e_group_sel" id="e_group_sel" class="form-control input-sm" style="width: 100%;">
									<?php
										echo "\n";
										foreach($p_groups_list as $k => $v)
										{
											$selected = "";
											if($v["g_id"]  == $p_cur_group_id)
											{
												$selected = "selected";
											}
											echo '<option value="' . $v["g_id"] . '" ' . $selected . '>' . $v["g_name"] . '</option>' . "\n";
										}
									?>
								</select>
							</div>
						</div>
						
						<div class="row form-group">
							<div class="col-md-12">
								<label for="u_email">Email:</label>
								<input name="u_email" id="u_email" class="form-control input-sm" maxlength="80" value="<?php echo $p_cur_email ?>">
							</div>
						</div>
						
						<div class="row form-group">
							<div class="col-md-12" class="checkbox checkbox-primary">
								<label for="u_enabled2" class="checkbox-inline">
									<input name="u_enabled" id="u_enabled2" class="" type="checkbox" <?php if($p_cur_u_enabled){echo "checked";}?>> - <strong>Enabled</strong>
								</label>
							</div>
						</div>

						<div class="row form-group">
							<div class="col-md-5">
								<br />
								<input type="submit" name="e_submit_edit" id="e_submit_edit" value="Save changes" class="btn btn-default">
							</div>
							<div class="col-md-3">
								<br />
								<input type="submit" name="e_submit_delete" id="e_submit_delete" value="Delete" class="btn btn-default" onclick="return confirm('Are you sure?')">
							</div>
						</div>
						
						<div class="row form-group">
							<div class="col-md-12" id="img_proc_02">
							</div>
						</div>
							
					</form>
					
					<div id="l_msg_02">
						<?php
							if("editdelete" == $p_action && strlen($p_success_message) > 0)
							{
						?>
								<div class="alert alert-success" style="padding: 5;">
									<strong>Success: </strong><?php echo $p_success_message; ?>
								</div>
								<br />
						<?php
							}
							
							if("editdelete" == $p_action && strlen($p_error_message) > 0)
							{
						?>
								<div class="alert alert-danger" style="padding: 5;">
									<strong>Error: </strong><?php echo $p_error_message; ?>
								</div>
								<br />
						<?php
							}
						?>
					</div>
					
				</div>
				
			</div>
			<hr />
			
			<div class="row">
				<div id="l_msg_03">
						<?php
							if("editdelete" != $p_action && "add" != $p_action && strlen($p_success_message) > 0)
							{
						?>
								<div class="alert alert-success" style="padding: 5;">
									<strong>Success: </strong><?php echo $p_success_message; ?>
								</div>
								<br />
						<?php
							}
							
							if("editdelete" != $p_action && "add" != $p_action && strlen($p_error_message) > 0)
							{
						?>
								<div class="alert alert-danger" style="padding: 5;">
									<strong>Error: </strong><?php echo $p_error_message; ?>
								</div>
								<br />
						<?php
							}
						?>
					</div>
			</div>
<!-- !!!!!!!!!!!!!!!!!! -->
        </div>
    </div>


<script>
	//user_getlist();
	//user_getlistWraper();
	//window.user_getlistWraper();
	//App.user_getlistWraper();
	//alert(window.adr);
</script>


<?php
require_once('footer.php');
?>