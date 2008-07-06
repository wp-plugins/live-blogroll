<?php

/*
WP Live Blogroll Ajax script
Part of a WP Live Blog Roll plugin
*/

require_once("../../../wp-config.php");
require_once(ABSPATH . WPINC . '/rss.php');



// fetch information from GET method
$link_url = $_GET['link_url'];

// return the result

WPLiveRoll_HandleAjax($link_url);

function WPLiveRoll_GetExcerpt($text, $length = 20 )
{
		$text = strip_tags($text);		
		$words = explode(' ', $text, $length + 1);
		if (count($words) > $length) {
			array_pop($words);
			array_push($words, '[...]');
			$text = implode(' ', $words);
		}	
		return $text;
}


/* Credits to Keith Devens http://keithdevens.com/weblog/archive/2002/Jun/03/RSSAuto-DiscoveryPHP */
function getRSSLocation($html, $location){
    if(!$html or !$location){
        return false;
    }else{
        #search through the HTML, save all <link> tags
        # and store each link's attributes in an associative array
        preg_match_all('/<link\s+(.*?)\s*\/?>/si', $html, $matches);
        $links = $matches[1];
        $final_links = array();
        $link_count = count($links);
        for($n=0; $n<$link_count; $n++){
            $attributes = preg_split('/\s+/s', $links[$n]);
            foreach($attributes as $attribute){
                $att = preg_split('/\s*=\s*/s', $attribute, 2);
                if(isset($att[1])){
                    $att[1] = preg_replace('/([\'"]?)(.*)\1/', '$2', $att[1]);
                    $final_link[strtolower($att[0])] = $att[1];
                }
            }
            $final_links[$n] = $final_link;
        }
        #now figure out which one points to the RSS file
        for($n=0; $n<$link_count; $n++){
            if(strtolower($final_links[$n]['rel']) == 'alternate'){
                if(strtolower($final_links[$n]['type']) == 'application/rss+xml'){
                    $href = $final_links[$n]['href'];
                }
                if(!$href and strtolower($final_links[$n]['type']) == 'text/xml'){
                    #kludge to make the first version of this still work
                    $href = $final_links[$n]['href'];
                }
                if($href){
                    if(strstr($href, "http://") !== false){ #if it's absolute
                        $full_url = $href;
                    }else{ #otherwise, 'absolutize' it
                        $url_parts = parse_url($location);
                        #only made it work for http:// links. Any problem with this?
                        $full_url = "http://$url_parts[host]";
                        if(isset($url_parts['port'])){
                            $full_url .= ":$url_parts[port]";
                        }
                        if($href{0} != '/'){ #it's a relative link on the domain
                            $full_url .= dirname($url_parts['path']);
                            if(substr($full_url, -1) != '/'){
                                #if the last character isn't a '/', add it
                                $full_url .= '/';
                            }
                        }
                        $full_url .= $href;
                    }
                    return $full_url;
                }
            }
        }
        return false;
    }
}
	
	function get_url($url)	{
		if (function_exists('file_get_contents')) {
			$file = file_get_contents($url);
		} else {
	        $curl = curl_init($url);
	        curl_setopt($curl, CURLOPT_HEADER, 0);
	        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	        $file = curl_exec($curl);
	        curl_close($curl);
	    }
	    return $file;
	}

function WPLiveRoll_HandleAjax($link_url)
{
    // we will return final HTML code in this variable
    $result='';
    
    $options=WPLiveRoll_GetOptions();
    // number of posts we are showing
    $number = $options['number'];
    
   // $link_url=trailingslashit($link_url);
    
   /* // pick the rss feed based on the site
    if (strstr($link_url,"blogspot")) {
        // blogspot blog
        $feed_url=$link_url."feeds/posts/default/";
    } else if (strstr($link_url,"typepad")) {
        // typepad blog
        $feed_url=$link_url."atom.xml";
    } else {
        // own domain or wordpress blog
        $feed_url=$link_url."feed/";
    }*/
    
    global $wpdb;   

    
   	$link_rss = $wpdb->get_var("SELECT link_rss FROM $wpdb->links WHERE link_url LIKE '".like_escape( $link_url)."'");    	   	
   	    
   
    
    if (!$link_rss)
    {
    	$feed_url=getRSSLocation( get_url($link_url), $link_url);
    	
    	if (!$feed_url)
    		$feed_url='none';
    	
    	$wpdb->query("UPDATE $wpdb->links SET link_rss = '$feed_url' WHERE link_url LIKE '".like_escape( $link_url)."'");    	
    
    }
    else 
    	$feed_url=$link_rss;
    
    
    
    if ($feed_url && $feed_url!='none') {
    
	    // use WordPress to fetch the RSS feed
	    $feedfile = fetch_rss($feed_url);
	    
	    
	    // check if we got valid response
	    if (is_array($feedfile->items ) && !empty($feedfile->items ) ) {
	        
	        // slice the number of items we need
	        $feedfile->items = array_slice($feedfile->items, 0, $number);
	        
	        // create HTML out of posts
	        $result.= '<div><ul>';
	        foreach($feedfile->items as $item ) {
	            
	            // fetch the information
	            $item_title = $item['title'];
	            $item_link = $item['link'];
	            $item_description = WPLiveRoll_GetExcerpt($item['description'], $options['excerpt']);
	            
	            // form result
	            $result.= '<li><a target="'.$link_target.'" href="'.$item_link.'" >'.$item_title.'</a><p>'.$item_description.'</p></li>';
	        }
	        $result.= '</ul></div>';
	    } else {
	        // in case we were unable to parse the feed
	        $result.= "No posts available.";
	    }
	    
	    // return the HTML code
    	die( $result );
  	}
    else {
    	wp_die ( 'No RSS Feed Found');
    }
}

?>


