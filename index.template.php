<?php

/*	@	Origo theme									*/
/*	@ Bloc 2019										*/
/*	@	SMF 2.0.x										*/

function template_init()
{
	global $context, $settings, $options, $txt, $style_sheets;

	$settings['use_default_images'] = 'never';
	$settings['doctype'] = 'xhtml';
	$settings['theme_version'] = '2.0';
	$settings['use_tabs'] = true;
	$settings['use_buttons'] = true;
	$settings['separate_sticky_lock'] = true;
	$settings['strict_doctype'] = false;
	$settings['message_index_preview'] = false;
	$settings['require_theme_strings'] = true;
	$settings['show_member_bar'] = true;
	$nonadmin = array( '',
		'help','search','profile','pm','calendar','mlist','login','register','unread','unreadreplies','recent','stats','who',
	);
	if(!in_array($context['current_action'], $nonadmin))
		$settings['is_admin_template'] = true;
	else
		$settings['is_admin_template'] = false;

	$settings['tversion'] = '1.0';
}

function template_html_above()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	echo '
<!DOCTYPE html>
<html', $context['right_to_left'] ? ' dir="rtl"' : '', '>
<head>
	
	<link href="https://fonts.googleapis.com/css?family=Libre+Franklin:500,500i,700,700i,900&display=swap" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/css/index.css?v1" />';
	
	if($settings['is_admin_template'])
		echo '
	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/css/adm.css?v1" />';

	echo '
	<script type="text/javascript" src="', $settings['default_theme_url'], '/scripts/script.js?fin20"></script>
	<script type="text/javascript" src="', $settings['theme_url'], '/scripts/theme.js?fin20"></script>
	<script type="text/javascript"><!-- // --><![CDATA[
		var smf_theme_url = "', $settings['theme_url'], '";
		var smf_default_theme_url = "', $settings['default_theme_url'], '";
		var smf_images_url = "', $settings['images_url'], '";
		var smf_scripturl = "', $scripturl, '";
		var smf_iso_case_folding = ', $context['server']['iso_case_folding'] ? 'true' : 'false', ';
		var smf_charset = "', $context['character_set'], '";', $context['show_pm_popup'] ? '
		var fPmPopup = function ()
		{
			if (confirm("' . $txt['show_personal_messages'] . '"))
				window.open(smf_prepareScriptUrl(smf_scripturl) + "action=pm");
		}
		addLoadEvent(fPmPopup);' : '', '
		var ajax_notification_text = "', $txt['ajax_in_progress'], '";
		var ajax_notification_cancel_text = "', $txt['modify_cancel'], '";
	// ]]></script>

	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="Content-Type" content="text/html; charset=', $context['character_set'], '" />
	<meta name="description" content="', $context['page_title_html_safe'], '" />', !empty($context['meta_keywords']) ? '
	<meta name="keywords" content="' . $context['meta_keywords'] . '" />' : '', '
	<title>', $context['page_title_html_safe'], '</title>';

	if (!empty($context['robot_no_index']))
		echo '
	<meta name="robots" content="noindex" />';

	if (!empty($context['canonical_url']))
		echo '
	<link rel="canonical" href="', $context['canonical_url'], '" />';

	echo '
	<link rel="help" href="', $scripturl, '?action=help" />
	<link rel="search" href="', $scripturl, '?action=search" />
	<link rel="contents" href="', $scripturl, '" />';

	if (!empty($modSettings['xmlnews_enable']) && (!empty($modSettings['allow_guestAccess']) || $context['user']['is_logged']))
		echo '
	<link rel="alternate" type="application/rss+xml" title="', $context['forum_name_html_safe'], ' - ', $txt['rss'], '" href="', $scripturl, '?type=rss;action=.xml" />';

	if (!empty($context['current_topic']))
		echo '
	<link rel="prev" href="', $scripturl, '?topic=', $context['current_topic'], '.0;prev_next=prev" />
	<link rel="next" href="', $scripturl, '?topic=', $context['current_topic'], '.0;prev_next=next" />';

	if (!empty($context['current_board']))
		echo '
	<link rel="index" href="', $scripturl, '?board=', $context['current_board'], '.0" />';

	echo $context['html_headers'];

	if(!empty($settings['mycss']))
		echo '
	<style>' , $settings['mycss'] , '</style>';
	
	echo '
</head>
<body class="origo' , !empty($options['simplify']) ? ' simple_all' : '' , !empty($options['purify']) ? ' simple_pure' : '' ,'" id="origobody">';
}

function template_body_above()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	echo '
<button id="menu_adm" class="ham mobile' , $context['user']['is_logged'] && !empty($context['user']['unread_messages']) ? ' pm' : '' , '" type="button" onclick="addclass2(\'menu_top\' , \'show\', \'menu_adm\', \'open\'); return false;">
	<span></span><span></span><span></span>
</button>

<header id="header_top">
	<h1><a href="', $scripturl, '">' , $context['forum_name'] , '</a></h1>
	<menu id="menu_top" class="dropmenu">' , template_menu() , '</menu>
</header>
<nav id="nav_linktree">' , theme_linktree() , '</nav>

<article id="article_content">
	<button id="menu_user" class="toggle mobile" type="button" onclick="addclass3(\'section_user\' , \'show\', \'title_user\' , \'show\', \'menu_user\', \'open\'); return false;">
		<span></span><span></span><span></span>
	</button>';
	if ($context['user']['is_logged'])
		echo '
	<strong class="mobile" id="title_user">', $txt['hello_member_ndt'], ' <span>', $context['user']['name'], '</span></strong>';
	else
		echo '
	<strong class="mobile" id="title_user">', sprintf($txt['welcome_guest'], $txt['guest_title']), '</strong>';
	
	echo '
	</h3>
	<aside id="aside_content">
		<section id="section_user">' , logic_user() , '</section>
		' , function_exists('logic_aside_pre') ? logic_aside_pre() : '', '
		' , function_exists('logic_aside') ? logic_aside() : '', '
	</aside>
	<main id="main_content">';
}

function template_body_below()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	echo '
	</main>
</article>
<footer id="footer_bottom">
	<section id="section_copyright">', theme_copyright(),'</section>';

	if ($context['show_load_time'])
		echo '
	<section id="section_showtime">', $txt['page_created'], $context['load_time'], $txt['seconds_with'], $context['load_queries'], $txt['queries'], '</section>';

	echo '
	<section id="section_themecredits"><a href="https://github.com/blocthemes/Origo" target="_blank">Origo theme v' , $settings['tversion'] , ' by Bloc</a></section>
</footer>';
}

function template_html_below()
{
	echo '
</body></html>';
}

function logic_user()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	if ($context['user']['is_logged'])
	{
		echo '
			<div id="div_user">';
		
		if (!empty($context['user']['avatar']))
			echo '
				<figure class="mavatar">
					<a href="' , $scripturl , '?action=profile"><img src="', $context['user']['avatar']['href'], '" alt="" /></a>
				</figure>';
		else
			echo '
				<figure class="mavatar"></figure>';
			
		echo '
				<h2>', $txt['hello_member_ndt'], ' <span>', $context['user']['name'], '</span></h2>
				<ul class="listie">
					<li><a href="', $scripturl, '?action=unread">', $txt['a_unread'], '</a></li>
					<li><a href="', $scripturl, '?action=unreadreplies">', $txt['a_replies'], '</a></li>
					<li><a href="', $scripturl, '?action=profile;area=showposts">', $txt['a_ownposts'], '</a></li>
					<li>
						<a id="a_simplify" href="#" onclick="addclass2(\'origobody\',\'simple_all\',\'a_simplify\',\'on\'); return false;"' , !empty($options['simplify']) ? ' class="on"' : '' ,'>' , $txt['origo_extend'] , '</a>
						( <a href="' , $scripturl , '?action=profile;area=theme#a_extend">' , $txt['switch'] , '</a> )
					</li>
					<li>
						<a id="a_purify" href="#" onclick="addclass2(\'origobody\',\'simple_pure\',\'a_purify\',\'on\'); return false;"' , !empty($options['purify']) ? ' class="on"' : '' ,'>' , $txt['origo_extend2'], '</a>
						( <a href="' , $scripturl , '?action=profile;area=theme#a_extend2">' , $txt['switch'] , '</a> )
					</li>
					';

		// Is the forum in maintenance mode?
		if ($context['in_maintenance'] && $context['user']['is_admin'])
			echo '
					<li class="notice">', $txt['maintain_mode_on'], '</li>';

		// Are there any members waiting for approval?
		if (!empty($context['unapproved_members']))
			echo '
					<li class="unapp">', $context['unapproved_members'] == 1 ? $txt['approve_thereis'] : $txt['approve_thereare'], ' <a href="', $scripturl, '?action=admin;area=viewmembers;sa=browse;type=approve">', $context['unapproved_members'] == 1 ? $txt['approve_member'] : $context['unapproved_members'] . ' ' . $txt['approve_members'], '</a> ', $txt['approve_members_waiting'], '</li>';

		if (!empty($context['open_mod_reports']) && $context['show_open_reports'])
			echo '
					<li class="openm"><a href="', $scripturl, '?action=moderate;area=reports">', sprintf($txt['mod_reports_waiting'], $context['open_mod_reports']), '</a></li>';
		
		// for subtemplates to hook in
		if(function_exists('add_usermenu'))
		{
			$links = add_usermenu();
			foreach($links as $a => $menu)
			{
				if($menu['logged_in'])
					echo '
					<li><a href="', $menu['url'], '">', $menu['title'], '</a></li>';
			}
		}
		
		echo '
				</ul>
			</div>';
	}
	// Otherwise they're a guest - this time ask them to either register or login - lazy bums...
	elseif (!empty($context['show_login_bar']))
	{
		echo '
				<script type="text/javascript" src="', $settings['default_theme_url'], '/scripts/sha1.js"></script>
				<form id="guest_form" action="', $scripturl, '?action=login2" method="post" accept-charset="', $context['character_set'], '" ', empty($context['disable_login_hashing']) ? ' onsubmit="hashLoginPassword(this, \'' . $context['session_id'] . '\');"' : '', '>
					
					<fieldset>
						<legend>', sprintf($txt['welcome_guest'], $txt['guest_title']), '</legend>
						<div class="autogrid_form">
							<input type="text" name="user" class="input_text" />
							<input type="password" name="passwrd" class="input_text input_password" />
							<select name="cookielength" class="input_select">
								<option value="60">', $txt['one_hour'], '</option>
								<option value="1440">', $txt['one_day'], '</option>
								<option value="10080">', $txt['one_week'], '</option>
								<option value="43200">', $txt['one_month'], '</option>
								<option value="-1" selected="selected">', $txt['forever'], '</option>
							</select>
							<input type="submit" value="', $txt['login'], '" class="button_submit" />';

		if (!empty($modSettings['enableOpenID']))
			echo '
							<p class="openid"><label>OpenID<input type="text" name="openid_identifier" id="openid_url" class="input_text openid_login" /></label></p>';

		echo '
						</div>
						<input type="hidden" name="hash_passwrd" value="" /><input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />
					</fieldset>				
					<p class="info">', $txt['quick_login_dec'], '</p>
					<ul class="listie">
							<li><a href="', $scripturl, '?action=recent">', $txt['a_recent'], '</a></li>
							<li><a id="a_simplify" href="#" onclick="addclass2(\'origobody\',\'simple_all\',\'a_simplify\',\'on\'); return false;">' , $txt['origo_extend'] , '</a></li>
							<li><a id="a_purify" href="#" onclick="addclass2(\'origobody\',\'simple_pure\',\'a_purify\',\'on\'); return false;">' , $txt['origo_extend2'] , '</a></li>
					</ul>
				</form>';
	}
}

function theme_linktree($force_show = false)
{
	global $context, $settings, $options, $shown_linktree;

	if (empty($context['linktree']) || (!empty($context['dont_default_linktree']) && !$force_show))
		return;

	echo '
		<ul class="reset">';

	foreach ($context['linktree'] as $link_num => $tree)
	{
		echo '
			<li', ($link_num == count($context['linktree']) - 1) ? ' class="last"' : '', '>';

		// Show something before the link?
		if (isset($tree['extra_before']))
			echo $tree['extra_before'];

		// Show the link, including a URL if it should have one.
		echo isset($tree['url']) ? '
				<a href="' . $tree['url'] . '"><span>' . $tree['name'] . '</span></a>' : '<span>' . $tree['name'] . '</span>';

		// Show something after the link...?
		if (isset($tree['extra_after']))
			echo $tree['extra_after'];

		echo '
			</li>';
	}
	echo '
		</ul>';

	$shown_linktree = true;
}

function template_menu()
{
	global $context, $settings, $options, $scripturl, $txt;

	echo '
				<ul class="reset">';

	foreach ($context['menu_buttons'] as $act => $button)
	{
		$button['title'] = str_replace(array("[","]"),array("",""),$button['title']);
		echo '
					<li id="button_', $act, '">
						<a class="', $button['active_button'] ? 'active ' : '', 'firstlevel" href="', $button['href'], '"', isset($button['target']) ? ' target="' . $button['target'] . '"' : '', '>
							<span class="', !empty($button['sub_buttons']) ? 'parent ' : '' , isset($button['is_last']) ? 'last ' : '', 'firstlevel">', $button['title'], '</span>
						</a>';
		if (!empty($button['sub_buttons']))
		{
			echo '
						<ul class="reset">';

			foreach ($button['sub_buttons'] as $childbutton)
			{
				echo '
							<li>
								<a href="', $childbutton['href'], '"', isset($childbutton['target']) ? ' target="' . $childbutton['target'] . '"' : '', '>
									<span', isset($childbutton['is_last']) ? ' class="last"' : '', '>', $childbutton['title'], !empty($childbutton['sub_buttons']) ? '...' : '', '</span>
								</a>';
				// 3rd level menus :)
				if (!empty($childbutton['sub_buttons']))
				{
					echo '
								<ul class="reset">';

					foreach ($childbutton['sub_buttons'] as $grandchildbutton)
						echo '
									<li>
										<a href="', $grandchildbutton['href'], '"', isset($grandchildbutton['target']) ? ' target="' . $grandchildbutton['target'] . '"' : '', '>
											<span', isset($grandchildbutton['is_last']) ? ' class="last"' : '', '>', $grandchildbutton['title'], '</span>
										</a>
									</li>';

					echo '
								</ul>';
				}
				echo '
							</li>';
			}
				echo '
						</ul>';
		}
		echo '
					</li>';
	}
	echo '
				</ul>';
}

// Generate a strip of buttons.
function template_button_strip($button_strip, $direction = 'top', $strip_options = array())
{
	global $settings, $context, $txt, $scripturl;

	if (!is_array($strip_options))
		$strip_options = array();

	// List the buttons in reverse order for RTL languages.
	if ($context['right_to_left'])
		$button_strip = array_reverse($button_strip, true);

	// test it
	$set = false;
	foreach ($button_strip as $key => $value)
	{
		if(isset($value['active']))
			$set = true;
	}
	if(!$set)
	{
		$first = true;
		foreach ($button_strip as $key => $value)
		{
			if($first)
				$button_strip[$key]['active'] = true;
			$first = false;
		}
	}


	// Create the buttons...
	$buttons = array();
	foreach ($button_strip as $key => $value)
	{
		if (!isset($value['test']) || !empty($context[$value['test']]))
		{
			if(isset($value['icon']))
			{
				if(is_array($value['icon']))
					$buttons[] = '
				<a' . (isset($value['id']) ? ' id="button_strip_' . $value['id'] . '"' : '') . ' class="button_strip_' . $key . (isset($value['active']) ? ' active' : '') . '" href="' . $value['url'] . '"' . (isset($value['custom']) ? ' ' . $value['custom'] : '') . '><span class="button_submit buts is_icon"><span style="opacity: 0.7;" class="mobile ' . (implode('"></span><span class="mobile iconbig ',$value['icon'])) . '"></span><span class="desktop">' . $txt[$value['text']] . '</span></span></a>';
				else
					$buttons[] = '
				<a' . (isset($value['id']) ? ' id="button_strip_' . $value['id'] . '"' : '') . ' class="button_strip_' . $key . (isset($value['active']) ? ' active' : '') . '" href="' . $value['url'] . '"' . (isset($value['custom']) ? ' ' . $value['custom'] : '') . '><span class="button_submit buts is_icon"><span class="' . $value['icon'] . ' mobile iconbig"></span><span class="desktop">' . $txt[$value['text']] . '</span></span></a>';
			}
			else
				$buttons[] = '
				<a' . (isset($value['id']) ? ' id="button_strip_' . $value['id'] . '"' : '') . ' class="button_strip_' . $key . (isset($value['active']) ? ' active' : '') . '" href="' . $value['url'] . '"' . (isset($value['custom']) ? ' ' . $value['custom'] : '') . '><span class="button_submit buts">' . $txt[$value['text']] . '</span></a>';
		}
	}

	// No buttons? No button strip either.
	if (empty($buttons))
		return;

	echo 	implode('', $buttons);
}

function convertPageindex($custom = '')
{
	global $context, $txt;
	
	if(!empty($custom))
	{
		$return =  '<span class="page_index">' . (str_replace(array('[',']'), array('<span>','</span>'),$custom)) . '</span>';
		return $return;
	}	
	
	if(empty($context['page_index']))
		return;

	$context['page_index'] = '<span class="page_index">' . (str_replace(array('[',']'), array('<span>','</span>'),$context['page_index'])) . '</span>';
}

function convertPages($code= '')
{
	global $context, $txt;
	
	if(!empty($code))
	{
		$code = str_replace(array('&#171;','&#187;'), array('',''), $code);
		echo $code;
	}	
}

?>