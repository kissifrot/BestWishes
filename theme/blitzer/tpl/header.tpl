<!DOCTYPE html>
<html>
	<head>
		<title>{$pageTitle}</title>
		<!--<meta http-equiv="Content-Language" content="fr" />-->
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="robots" content="noindex, nofollow" />
		<meta name="description" content="{$lngBwDesciption}" />
		
		<link rel="stylesheet" type="text/css" href="{$themeWebDir}/css/general.css" />
		<link rel="stylesheet" type="text/css" href="{$themeWebDir}/css/jquery-ui.min.css" />
		<link rel="stylesheet" type="text/css" href="{$themeWebDir}/css/jquery-ui-adjust.css" />
		<link rel="shortcut icon" href="{$webDir}/favicon.ico" />

		<script src="{$webDir}/js/jquery.min.js" type="text/javascript"></script>
		<script src="{$webDir}/js/jquery-ui.min.js" type="text/javascript"></script>
		<script src="{$webDir}/js/tools.min.js" type="text/javascript"></script>
		<script src="{$webDir}/js/variables.js" type="text/javascript"></script>
		<script src="{$webDir}/js/common_functions.js" type="text/javascript"></script>
		<script src="{$webDir}/js/functions.js" type="text/javascript"></script>
	</head>

	<body>
		<div id="flash_message" class="ui-widget">
			<div class="ui-state-error ui-corner-all" style="padding: 0pt 0.7em;"> 
				<p><span class="ui-icon ui-icon-alert floatingIcon"></span></p>
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
					<h2><a href="{$webDir}/index.php">{$lngHome}</a></h2>
					{else}
					<h2><a href="{$webDir}/index.php">{$lngHomeLogin}</a></h2>
					{/if}
					{if !empty($lists) }
						<h2>{$lngLists}</h2>
						<ul>
						{foreach from=$lists item=list}
							<li><a href="{$webDir}/list/{$list->slug}">{$list->name|ucfirst}</a>
							</li>
						{/foreach}
						</ul>
					{/if}
					{if $sessionOk}
					<h2><a href="{$webDir}/options.php">{$lngOptions}</a></h2>
					<h2><a href="{$webDir}/logout.php">{$lngLogout}</a></h2>
					{/if}
				</div>
				{include file='floating_menu.tpl'}
				<div id="right_content" class="ui-widget-content ui-corner-all" style="padding: 0.2em 6px;">
