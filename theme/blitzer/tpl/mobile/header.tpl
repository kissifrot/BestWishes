<!DOCTYPE html>
<html>
	<head>
		<title>{$pageTitle}</title>
		<!--<meta http-equiv="Content-Language" content="fr" />-->
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="robots" content="noindex, nofollow" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		
		<link rel="stylesheet" type="text/css" href="{$themeWebDir}/css/jquery.mobile.min.css" />
		<link rel="shortcut icon" href="{$webDir}/favicon.ico" />

		<script src="{$webDir}/js/jquery.min.js" type="text/javascript"></script>
		<script src="{$webDir}/js/jquery.mobile.min.js" type="text/javascript"></script>
		<script src="{$webDir}/js/tools.min.js" type="text/javascript"></script>
		<script src="{$webDir}/js/common.js" type="text/javascript"></script>
		<script src="{$webDir}/js/functions.js" type="text/javascript"></script>
		<script src="{$webDir}/js/mobile_functions.js" type="text/javascript"></script>
	</head>

	<body>
		<div data-role="page" data-theme="b">
			<div data-role="header" data-theme="b">
				<h1>{$siteName}</h1>
			</div><!-- /header -->
			<div data-role="content">
				{if $pageViewed == 'home'}
				<strong>{$lngBwDesciption}</strong><br />
				{/if}
				<div id="left_menu">
					{if $pageViewed != 'home'}
						<h2><a href="{$webDir}/index.php" data-role="button" data-icon="home">{if $sessionOk} {$lngHome} {else} {$lngHomeLogin} {/if}</a></h2>
					{/if}
					{if !empty($lists) }
						{if $pageViewed != 'list_display' && $pageViewed != 'gift_display'}
							<h2>{$lngLists}</h2>
							{foreach from=$lists item=list}
								<a href="{$webDir}/list/{$list->slug}" data-role="button" data-iconpos="right" data-icon="arrow-r">{$list->name|ucfirst}</a>
							{/foreach}
						{/if}
					{/if}
				</div>
				<div id="right_content">
					<h2>{$subTitle}</h2>
