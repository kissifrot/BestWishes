<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>{$pageTitle}</title>
		<!--<meta http-equiv="Content-Language" content="fr" />-->
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="robots" content="noindex, nofollow" />
		
		<link rel="stylesheet" type="text/css" href="{$themeWebDir}/css/general.css" />
		<link rel="stylesheet" type="text/css" href="{$themeWebDir}/css/jquery-ui.min.css" />
		<link rel="stylesheet" type="text/css" href="{$themeWebDir}/css/jquery-ui-adjust.css" />
		<link rel="shortcut icon" href="{$webDir}/favicon.ico" />

		<script language="javascript" src="{$webDir}/js/jquery.min.js" type="text/javascript"></script>
		<script language="javascript" src="{$webDir}/js/jquery-ui.min.js" type="text/javascript"></script>
		<script language="javascript" src="{$webDir}/js/tools.min.js" type="text/javascript"></script>
		<script language="javascript" src="{$webDir}/js/variables.js" type="text/javascript"></script>
		<script language="javascript" src="{$webDir}/js/common_functions.js" type="text/javascript"></script>
		<script language="javascript" src="{$webDir}/js/adm_functions.js" type="text/javascript"></script>
	</head>

	<body>
		<div id="flash_message" class="ui-widget">
			<div class="ui-state-error ui-corner-all" style="padding: 0pt 0.7em;"> 
				<p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: 0.3em;"></span></p>
			</div>
		</div>
		<div id="global" class="ui-widget">
			<div id="header">
				<h1>{$siteName}</h1>
				<br /><h2>{$subTitle}</h2><br />
			</div>
			<div id="content">
				<div id="left_menu" class="ui-widget-content ui-corner-all" style="padding: 0.2em;">
					{if $sessionOk}
					<h2><a href="adm_lists.php">Manage lists</a></h2>
					<h2><a href="adm_users.php">Manage users</a></h2>
					<h2><a href="adm_events.php">Manage events</a></h2>
					<h2><a href="adm_options.php">Manage options</a></h2>
					<h2><a href="#">Manage cache</a></h2>
					<a id="" href="#"><i>Show advanced</i></a>
					{/if}
					<h3><a href="{$webDir}">Back to main</a></h3>
				</div>
				{include file='floating_menu.tpl'}
				<div id="right_content" class="ui-widget-content ui-corner-all" style="padding: 0.2em 6px;">
