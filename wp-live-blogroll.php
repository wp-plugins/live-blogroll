<?php

/*
Plugin Name: Live Blogroll
Version: 0.5.2
Description: Shows a number of 'recent posts' for each link in your Blogroll using Ajax.
Author: Vladimir Prelovac
Author URI: http://www.prelovac.com/vladimir
Plugin URI: http://www.prelovac.com/vladimir/wordpress-plugins/live-blogroll
*/

/*  
Copyright 2008  Vladimir Prelovac  (email : vprelovac@gmail.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


global $wp_version;	

$exit_msg='Live BlogRoll requires WordPress 2.3 or newer. <a href="http://codex.wordpress.org/Upgrading_WordPress">Please update!</a>';

if (version_compare($wp_version,"2.3","<"))
{
	exit ($exit_msg);
}

$wp_live_blogroll_plugin_url = trailingslashit( get_bloginfo('wpurl') ).PLUGINDIR.'/'. dirname( plugin_basename(__FILE__) );

require_once(ABSPATH . WPINC . '/rss.php');

//add_filter('get_bookmarks', WPLiveRoll_GetBookmarksFilter);

function WPLiveRoll_GetBookmarksFilter($items)
{
    
    // do nothing if in the admin menu
    if (is_admin()) {
        return $items;
    }
   
    // parse all items on the blogroll
    foreach($items as $item)
    {
    		// check if the link is public
    		if ($item->link_visible=='Y') {
		        $link_url=trailingslashit($item->link_url);
		        
		        // pick the rss feed based on the site
		        if (strstr($link_url,"blogspot")) {
		            // blogspot blog
		            $feed_url=$link_url."feeds/posts/default/";
		        } else if (strstr($link_url,"typepad")) {
		            // typepad blog
		            $feed_url=$link_url."atom.xml";
		        } else {
		            // own domain or wordpress blog
		            $feed_url=$link_url."feed/";		            
		        }
		        
		        
		        // use WordPress to fetch the RSS feed
		        $feedfile = fetch_rss($feed_url);
		       
		        
		        // check if we got valid response
		        if (is_array($feedfile->items ) && !empty($feedfile->items ) ) {		       		
		        		// this is the last post
		            $feeditem=$feedfile->items[0];
		            
		            // replace name and url with post link and title
		            $item->link_url=$feeditem['link'];
		            $item->link_name=$feeditem['title'];		            
		        }
      	}
        
    }
    // return the items back
    return $items;
}


add_filter('wp_list_bookmarks', WPLiveRoll_ListBookmarksFilter);

function WPLiveRoll_ListBookmarksFilter($content)
{
	return '<span class="livelinks">'.$content.'</span>';
}

add_action('wp_print_scripts', 'WPLiveRoll_ScriptsAction');

function WPLiveRoll_ScriptsAction() 
{ 
	global $wp_live_blogroll_plugin_url;
	if (!is_admin())
	{
		wp_enqueue_script('jquery');
		wp_enqueue_script('wp_live_roll_script', $wp_live_blogroll_plugin_url.'/wp-live-blogroll.js.php', array('jquery')); 
	}
}

add_action('wp_head', 'WPLiveRoll_HeadAction' );

function WPLiveRoll_HeadAction()
{
	global $wp_live_blogroll_plugin_url;
	
	echo '<link rel="stylesheet" href="'.$wp_live_blogroll_plugin_url.'/wp-live-blogroll.css" type="text/css" />'; 
}

function WPLiveRoll_GetOptions()
{
	
 $options = array(
	 
	 'number' => 4,
	 'setx'=> -260,
	 'sety'=> 5,
	 'excerpt' => 25
	
	 );
  
 $saved = get_option('live_blogroll');
 
 
 if (!empty($saved)) {
	 foreach ($saved as $key => $option)
 			$options[$key] = $option;
 }
	
 if ($saved != $options)	
 	update_option('live_blogroll', $options);
 	
 return $options;
}

add_action('admin_menu', 'WPLiveRoll_AdminMenu');

	// Hook the options mage
function WPLiveRoll_AdminMenu() {
	add_options_page('Live BlogRoll Options', 'Live Blogroll', 8, basename(__FILE__),'WPLiveRoll_Options');	
} 

function WPLiveRoll_Options()
{
	global $wp_live_blogroll_plugin_url;
	$options = WPLiveRoll_GetOptions();
	
	if ( isset($_POST['submitted']) ) {
		
		//print_r($_POST);
		
		$options['number']=(int) ($_POST['number']);		
		$options['setx']=(int) ($_POST['setx']);		
		$options['sety']=(int) ($_POST['sety']);		
		$options['excerpt']=(int) ($_POST['excerpt']);		
							
		update_option('live_blogroll', $options);
		echo '<div class="updated fade"><p>Plugin settings saved.</p></div>';
	}

	


	$action_url = $_SERVER['REQUEST_URI'];	

	
	$number=$options['number'];
	$setx=$options['setx'];
	$sety=$options['sety'];
	$excerpt=$options['excerpt'];
	
			
	$imgpath=$wp_live_blogroll_plugin_url.'/i';	
	
	echo <<<END

<div class="wrap" style="max-width:950px !important;">
	<h2>Live Blogroll</h2>
				
	<div id="poststuff" style="margin-top:10px;">
	
	<div id="sideblock" style="float:right;width:220px;margin-left:10px;"> 
		 <h3>Information</h3>
		 <div id="dbx-content" style="text-decoration:none;">
			 <img src="$imgpath/home.png"><a style="text-decoration:none;" href="http://www.prelovac.com/vladimir/wordpress-plugins/live-blogroll"> Live Blogroll Home</a><br /><br />
			 <img src="$imgpath/help.png"><a style="text-decoration:none;" href="http://www.prelovac.com/vladimir/forum"> Plugin Forums</a><br /><br />
			 <img src="$imgpath/rate.png"><a style="text-decoration:none;" href="http://wordpress.org/extend/plugins/live-blogroll/"> Rate Live Blogroll</a><br /><br />
			 <img src="$imgpath/more.png"><a style="text-decoration:none;" href="http://www.prelovac.com/vladimir/wordpress-plugins"> My WordPress Plugins</a><br /><br />
			 <br />
		
			 <p align="center">
			 <img src="$imgpath/p1.png"></p>
			
			 <p> <img src="$imgpath/idea.png"><a style="text-decoration:none;" href="http://www.prelovac.com/vladimir/services"> Need a WordPress Expert?</a></p>
 		</div>
 	</div>
	
	 <div id="mainblock" style="width:710px">
	 
		<div class="dbx-content">
		 	<form name="liveblogrollform" action="$action_url" method="post">
					<input type="hidden" name="submitted" value="1" /> 
					<h3>General Options</h3>
					
					<p>Live Blogroll shows real-time RSS feeds for sites in your BlogRoll.</p>
						
					<input type="text" name="number" size="10" value="$number"/>
					<label for="number">Number of posts to show (default 4)</label> <br /><br />	
					
						<input type="text" name="excerpt" size="10" value="$excerpt"/>
					<label for="excerpt">Length of shown post excerpt, in words (default 25)</label> <br /><br />	
					
					<h4>Position</h4>
					<p>You can set the opening position of the live posts preview. Set the x and y offset from the mouse pointer where the box should appear.</p>
					
					<input type="text" name="setx" size="10" value="$setx"/>
					<label for="setx">X offset</label> <br />
					
					<input type="text" name="sety" size="10" value="$sety"/>
					<label for="sety">Y offset</label> <br /><br />			
					
					
					<br />					
				
					
					<div class="submit"><input type="submit" name="Submit" value="Update" /></div>
			</form>
		</div>
		
		<br/><br/><h3>Appeareance</h3>	
		<p>You can edit your Live Blogroll looks by editing wp-live-blogroll.css file. Be sure to make backup if you upgrade to newer version.</p>
		<br/><br/><h3>&nbsp;</h3>	
	 </div>

	</div>
	
<h5>WordPress plugin by <a href="http://www.prelovac.com/vladimir/">Vladimir Prelovac</a></h5>
</div>
END;
}
?>