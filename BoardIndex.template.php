<?php

/*	@	Origo theme									*/
/*	@ Bloc 2019										*/
/*	@	SMF 2.0.x										*/

function logic_aside()
{
	show_slider();
}

/*
function add_user_menu()
{
	return; 
}
*/

function show_slider()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings;

	// Show the news fader?  (assuming there are things to show...)
	if ($settings['show_newsfader'] && !empty($context['fader_news_lines']))
	{
		echo '
	<section id="newsfader">
		<h3>', $txt['news'], '</h3>
		<ul class="reset" id="smfFadeScroller"', empty($options['collapse_news_fader']) ? '' : ' style="display: none;"', '>';

			foreach ($context['news_lines'] as $news)
				echo '
			<li>', $news, '</li>';

	echo '
		</ul>
	</section>
	<script type="text/javascript" src="', $settings['default_theme_url'], '/scripts/fader.js"></script>
	<script type="text/javascript"><!-- // --><![CDATA[
		// Create a news fader object.
		var oNewsFader = new smf_NewsFader({
			sSelf: \'oNewsFader\',
			sFaderControlId: \'smfFadeScroller\',
			sItemTemplate: ', JavaScriptEscape('<strong>%1$s</strong>'), ',
			iFadeDelay: ', empty($settings['newsfader_time']) ? 5000 : $settings['newsfader_time'], '
		});
	// ]]></script>';
	}
}

function template_main()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings;

	echo '
	<section id="boardindex">';

	foreach ($context['categories'] as $category)
	{
		if (empty($category['boards']) && !$category['is_collapsed'])
			continue;

		echo '
		<header class="header_category" id="category_', $category['id'], '">
			<h3>', $category['link'];

		// If this category even can collapse, show a link to collapse it.
		if ($category['can_collapse'])
			echo '
				<a class="collapse" href="', $category['collapse_href'], '"><span class="symbol">' , $category['is_collapsed'] ? '+' : '-' , '</span></a>';

		if (!$context['user']['is_guest'] && !empty($category['show_unread']))
			echo '
				<a class="unreadlink" href="', $scripturl, '?action=unread;c=', $category['id'], '">', $txt['view_unread_category'], '</a>';

		echo '
			</h3>
		</header>';

		if (!$category['is_collapsed'])
		{
			echo '
		<div class="board" id="category_', $category['id'], '_boards">';
			
			foreach ($category['boards'] as $board)
			{
				echo '
			<ul class="grid_board">				
				<li class="icon">
					<a href="', ($board['is_redirect'] || $context['user']['is_guest'] ? $board['href'] : $scripturl . '?action=unread;board=' . $board['id'] . '.0;children'), '">';

				// If the board or children is new, show an indicator.
				if ($board['new'] || $board['children_new'])
					echo '
						<span class="on', $board['new'] ? '' : '2', '" title="', $txt['new_posts'], '"></span>';
				// Is it a redirection board?
				elseif ($board['is_redirect'])
					echo '
						<span class="redirect"></span>';
				// No new posts at all! The agony!!
				else
					echo '
						<span class="off" title="', $txt['old_posts'], '"></span>';

				echo '
					</a>
				</li>
				<li class="info">
					<h4><a class="subject" href="', $board['href'], '" name="b', $board['id'], '">', $board['name'], '</a></h4>';

				// Has it outstanding posts for approval?
				if ($board['can_approve_posts'] && ($board['unapproved_posts'] || $board['unapproved_topics']))
					echo '
					<a href="', $scripturl, '?action=moderate;area=postmod;sa=', ($board['unapproved_topics'] > 0 ? 'topics' : 'posts'), ';brd=', $board['id'], ';', $context['session_var'], '=', $context['session_id'], '"> | <span class="notice">' , $board['unapproved_topics'], '</span> | <span class="notice">',  $board['unapproved_posts'], '</span></a>';

				echo '

					<p class="description purify">', $board['description'] , '</p>';

				// Show the "Moderators: ". Each has name, href, link, and id. (but we're gonna use link_moderators.)
				if (!empty($board['moderators']))
					echo '
					<p class="moderators simplify">', count($board['moderators']) == 1 ? $txt['moderator'] : $txt['moderators'], ': ', implode(', ', $board['link_moderators']), '</p>';

				// Show some basic information about the number of posts, etc.
					echo '
				</li>
				<li class="stats simplify">
					<p>', comma_format($board['posts']), ' ', $board['is_redirect'] ? $txt['redirects'] : $txt['posts'], ' |
						', $board['is_redirect'] ? '' : comma_format($board['topics']) . ' ' . $txt['board_topics'], '
					</p>
				</li>
				<li class="lastpost simplify">';

				/* The board's and children's 'last_post's have:
				time, timestamp (a number that represents the time.), id (of the post), topic (topic id.),
				link, href, subject, start (where they should go for the first unread post.),
				and member. (which has id, name, link, href, username in it.) */
				if (!empty($board['last_post']['id']))
					echo '
					<p><strong>', $txt['last_post'], '</strong>  ', $txt['by'], ' ', $board['last_post']['member']['link'] , ' 
						', $txt['in'], ' ', $board['last_post']['link'], ' 
						', $txt['on'], ' ', $board['last_post']['time'],'
					</p>';
				echo '
				</li>
				<li class="children' , empty($board['children']) ? ' empty' : '' , ' purify">';
				// Show the "Child Boards: ". (there's a link_children but we're going to bold the new ones...)
				if (!empty($board['children']))
				{
					// Sort the links into an array with new boards bold so it can be imploded.
					$children = array();
					/* Each child in each board's children has:
							id, name, description, new (is it new?), topics (#), posts (#), href, link, and last_post. */
					foreach ($board['children'] as $child)
					{
						if (!$child['is_redirect'])
							$child['link'] = '<a href="' . $child['href'] . '" ' . ($child['new'] ? 'class="new_posts" ' : '') . 'title="' . ($child['new'] ? $txt['new_posts'] : $txt['old_posts']) . ' (' . $txt['board_topics'] . ': ' . comma_format($child['topics']) . ', ' . $txt['posts'] . ': ' . comma_format($child['posts']) . ')">' . $child['name'] . ($child['new'] ? '</a> <a href="' . $scripturl . '?action=unread;board=' . $child['id'] . '" title="' . $txt['new_posts'] . ' (' . $txt['board_topics'] . ': ' . comma_format($child['topics']) . ', ' . $txt['posts'] . ': ' . comma_format($child['posts']) . ')"><img src="' . $settings['lang_images_url'] . '/new.gif" class="new_posts" alt="" />' : '') . '</a>';
						else
							$child['link'] = '<a href="' . $child['href'] . '" title="' . comma_format($child['posts']) . ' ' . $txt['redirects'] . '">' . $child['name'] . '</a>';

						// Has it posts awaiting approval?
						if ($child['can_approve_posts'] && ($child['unapproved_posts'] || $child['unapproved_topics']))
							$child['link'] .= ' <a href="' . $scripturl . '?action=moderate;area=postmod;sa=' . ($child['unapproved_topics'] > 0 ? 'topics' : 'posts') . ';brd=' . $child['id'] . ';' . $context['session_var'] . '=' . $context['session_id'] . '" title="' . sprintf($txt['unapproved_posts'], $child['unapproved_topics'], $child['unapproved_posts']) . '" class="moderation_link">(!)</a>';

						$children[] = $child['new'] ? '<strong>' . $child['link'] . '</strong>' : $child['link'];
					}
					echo '
					<strong>', $txt['parent_boards'], '</strong>: ', implode(', ', $children);
				}
				echo '
				</li>
			</ul>';
			}
		echo '
		</div>';
		}
	}
	echo '
	</section>';

	if ($context['user']['is_logged'])
	{
		echo '
	<section>';

		// Mark read button.
		$mark_read_button = array(
			'markread' => array('text' => 'mark_as_read', 'image' => 'markread.gif', 'lang' => true, 'url' => $scripturl . '?action=markasread;sa=all;' . $context['session_var'] . '=' . $context['session_id']),
		);

		// Show the mark all as read button?
		if ($settings['show_mark_read'] && !empty($context['categories']))
			template_button_strip($mark_read_button, 'right');
		echo '
	</section>';
	}
	template_info_center();
}

function template_info_center()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings;

	echo '
	<section id="infocenter">
		<h3>', sprintf($txt['info_center_title'], $context['forum_name_html_safe']), '</h3>
		<div id="infocenters">';

	// This is the "Recent Posts" bar.
	if (!empty($settings['number_recent_posts']) && (!empty($context['latest_posts']) || !empty($context['latest_post'])))
	{
		echo '
			<div class="icenter">
				<h4><a href="', $scripturl, '?action=recent">', $txt['recent_posts'], '</a></h4>';

		// Only show one post.
		if ($settings['number_recent_posts'] == 1)
		{
			// latest_post has link, href, time, subject, short_subject (shortened with...), and topic. (its id.)
			echo '
				<p>', $txt['recent_view'], ' &quot;', $context['latest_post']['link'], '&quot; ', $txt['recent_updated'], ' (', $context['latest_post']['time'], ')</p>';
		}
		// Show lots of posts.
		elseif (!empty($context['latest_posts']))
		{
			echo '
				<ul id="ic_recentposts" class="autogrid_posts">';

			/* Each post in latest_posts has:
					board (with an id, name, and link.), topic (the topic's id.), poster (with id, name, and link.),
					subject, short_subject (shortened with...), time, link, and href. */
			foreach ($context['latest_posts'] as $post)
				echo '
					<li><strong>', $post['link'], '</strong>
						<ul class="simplify">
							<li>', $txt['by'] , ' ' , $post['poster']['link'], '</li>
							<li>', $txt['in'] , ' ' , $post['board']['link'], '</li>
							<li>', $txt['on'] , ' ' , $post['time'], '</li>
						</ul>
					</li>';
			echo '
				</ul>';
		}
		echo '
			</div>';
	}

	// Show information about events, birthdays, and holidays on the calendar.
	if ($context['show_calendar'])
	{
		echo '
			<div class="icenter">
				<h4><a href="', $scripturl, '?action=calendar' . '">
					', $context['calendar_only_today'] ? $txt['calendar_today'] : $txt['calendar_upcoming'], '
				</h4>';

		// Holidays like "Christmas", "Chanukah", and "We Love [Unknown] Day" :P.
		if (!empty($context['calendar_holidays']))
				echo '
				<p class="holiday">', $txt['calendar_prompt'], ' ', implode(', ', $context['calendar_holidays']), '</p>';

		// People's birthdays. Like mine. And yours, I guess. Kidding.
		if (!empty($context['calendar_birthdays']))
		{
			echo '
				<p class="birthday">', $context['calendar_only_today'] ? $txt['birthdays'] : $txt['birthdays_upcoming'], ' ';
			
			foreach ($context['calendar_birthdays'] as $member)
			{
				echo '
					<a href="', $scripturl, '?action=profile;u=', $member['id'], '">', $member['is_today'] ? '<strong>' : '', $member['name'], $member['is_today'] ? '</strong>' : '', isset($member['age']) ? ' (' . $member['age'] . ')' : '', '</a>';
			}
			echo '
				</p>';
		}
		// Events like community get-togethers.
		if (!empty($context['calendar_events']))
		{
			echo '
				<p class="event">', $context['calendar_only_today'] ? $txt['events'] : $txt['events_upcoming'], ' ';

			foreach ($context['calendar_events'] as $event)
			{
				echo 
					$event['href'] == '' ? '' : '<a href="' . $event['href'] . '">', $event['is_today'] ? '<strong>' . $event['title'] . '</strong>' : $event['title'], $event['href'] == '' ? '' : '</a>';
			}
			echo '
				</p>';
		}
		echo '
			</div>';
	}

	// Show statistical style information...
	if ($settings['show_stats_index'])
	{
		echo '
			<div class="icenter">
				<h4><a href="', $scripturl, '?action=stats">', $txt['forum_stats'], '</a></h4>
				<p>
					', $context['common_stats']['total_posts'], ' ', $txt['posts_made'], ' ', $txt['in'], ' ', $context['common_stats']['total_topics'], ' ', $txt['topics'], ' ', $txt['by'], ' ', $context['common_stats']['total_members'], ' ', $txt['members'], '. ', !empty($settings['show_latest_member']) ? $txt['latest_member'] . ': <strong> ' . $context['common_stats']['latest_member']['link'] . '</strong>' : '', ' |
					', (!empty($context['latest_post']) ? $txt['latest_post'] . ': <strong>&quot;' . $context['latest_post']['link'] . '&quot;</strong>  ( ' . $context['latest_post']['time'] . ' ) | ' : ''), '
					<a href="', $scripturl, '?action=recent">', $txt['recent_view'], '</a>', $context['show_stats'] ? ' | 
					<a href="' . $scripturl . '?action=stats">' . $txt['more_stats'] . '</a>' : '', '
				</p>
			</div>';
	}

	// "Users online" - in order of activity.
	echo '
			<div class="icenter">
				<h4>', $context['show_who'] ? '<a href="' . $scripturl . '?action=who' . '">' : '', $txt['online_users'], $context['show_who'] ? '</a>' : '', '</h4>
				<p>
					', $context['show_who'] ? '<a href="' . $scripturl . '?action=who">' : '', comma_format($context['num_guests']), ' ', $context['num_guests'] == 1 ? $txt['guest'] : $txt['guests'], ', ' . comma_format($context['num_users_online']), ' ', $context['num_users_online'] == 1 ? $txt['user'] : $txt['users'];

	// Handle hidden users and buddies.
	$bracketList = array();
	if ($context['show_buddies'])
		$bracketList[] = comma_format($context['num_buddies']) . ' ' . ($context['num_buddies'] == 1 ? $txt['buddy'] : $txt['buddies']);
	if (!empty($context['num_spiders']))
		$bracketList[] = comma_format($context['num_spiders']) . ' ' . ($context['num_spiders'] == 1 ? $txt['spider'] : $txt['spiders']);
	if (!empty($context['num_users_hidden']))
		$bracketList[] = comma_format($context['num_users_hidden']) . ' ' . $txt['hidden'];

	if (!empty($bracketList))
		echo ' (' . implode(', ', $bracketList) . ')';

	echo $context['show_who'] ? '</a>' : '', '
				</p>
				<p>';

	// Assuming there ARE users online... each user in users_online has an id, username, name, group, href, and link.
	if (!empty($context['users_online']))
	{
		echo '
				', sprintf($txt['users_active'], $modSettings['lastActive']), ':<br />', implode(', ', $context['list_users_online']);

		// Showing membergroups?
		if (!empty($settings['show_group_key']) && !empty($context['membergroups']))
			echo '
				<br />[' . implode(']&nbsp;&nbsp;[', $context['membergroups']) . ']';
	}

	echo '
				</p>
				<p>
					', $txt['most_online_today'], ': <strong>', comma_format($modSettings['mostOnlineToday']), '</strong>.
					', $txt['most_online_ever'], ': ', comma_format($modSettings['mostOnline']), ' (', timeformat($modSettings['mostDate']), ')
				</p>
			</div>';

	echo '
		</div>
	</section>';
}

?>