<?php
/*
WP Live Blogroll Ajax script
Part of a WP Live Blog Roll plugin
*/

require_once("../../../wp-config.php");
require_once(ABSPATH . WPINC . '/feed.php');

// fetch information from GET method and return the result
WPLiveRoll_HandleAjax($_GET['link_url']);

function WPLiveRoll_GetExcerpt($text, $length = 20) {
	$text = strip_tags($text);
	$words = explode(' ', $text, $length + 1);

	if (count($words) > $length) {
		array_pop($words);
		$text = implode(' ', $words);
	}

	return $text;
}

function WPLiveRoll_feed_cache_lifetime() {
	return 1800;
}

function WPLiveRoll_HandleAjax($feed_url) {
	// check security
	check_ajax_referer("wp-live-blogroll");

	// we will return final HTML code in this variable
	$result = '';

	$options = WPLiveRoll_GetOptions();

	// number of posts we are showing
	$number = $options['number'];

	// number of words in the excerpt we are showing
	$excerpt = $options['excerpt'];

	// where clicked links should be opened
	// XXX should be in options?
	$link_target = "_blank";

	if ($feed_url && $feed_url != "none") {
		add_filter('wp_feed_cache_transient_lifetime', 'WPLiveRoll_feed_cache_lifetime');

		// use WordPress to fetch the RSS feed
		$feed = fetch_feed($feed_url);

		remove_filter('wp_feed_cache_transient_lifetime', 'WPLiveRoll_feed_cache_lifetime');

		// check if we got valid response
		if (is_wp_error($feed) && (is_admin() || current_user_can('manage_options'))) {
			$result .= "RSS Error: " . wp_specialchars($feed->get_error_message());
		} else if (!is_wp_error($feed) && ($items = $feed->get_item_quantity($number))) {
			// create HTML out of posts
			$result .= '<ul>';

			foreach ($feed->get_items(0, $items) as $item) {
				// fetch the information
				$item_title = esc_attr(strip_tags($item->get_title()));

				if (empty($item_title)) {
					$item_title = __('Untitled');
				}

				$item_link = $item->get_link();

				while (stristr($item_link, 'http') != $item_link) {
					$item_link = substr($item_link, 1);
				}

				$item_link = esc_url(strip_tags($item_link));

				$item_description = str_replace(array("\n", "\r"), ' ', esc_attr(strip_tags(@html_entity_decode($item->get_description(), ENT_QUOTES, get_option('blog_charset')))));
				//$item_description = wp_html_excerpt($item_description, 360) . ' [&hellip;]';
				$item_description = WPLiveRoll_GetExcerpt($item_description, $excerpt) . ' [&hellip;]';
				$item_description = esc_html($item_description);

				$item_author = $item->get_author();

				if (is_object($item_author)) {
					$item_author = $item_author->get_name();
					$item_author = '<p class="lb_author"><cite>' . esc_html(strip_tags($item_author)) . '</cite></p>';
				}

				$item_pubdate = $item->get_date();

				if ($item_pubdate) {
					if ($date_stamp = strtotime($item_pubdate)) {
						$item_pubdate = '<p class="lb_pubdate">' . date_i18n(get_option('date_format'), $date_stamp) . '</p>';
					} else {
						$item_pubdate = '';
					}
				}

				// form result
				$result .= '<li><a class="lb_link" target="' . $link_target . '" href="' . $item_link . '" >' . $item_title . '</a>' . $item_author . $item_pubdate . '<p class="lb_desc">' . $item_description . '</p></li>';
			}

			$result .= '</ul>';
		} else {
			// in case we were unable to parse the feed
			$result .= "No posts available.";
		}

		// return the HTML code
		die($result);
	} else {
		die('No RSS Feed Found');
	}
}

?>
