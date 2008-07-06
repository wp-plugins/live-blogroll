<?php

/*
WP Live Blogroll JavaScript
Part of a WP Live Blog Roll plugin
- Relies on jQuery
*/

	require_once("../../../wp-config.php");
	$options=WPLiveRoll_GetOptions();
?>

// setup everything when document is ready
jQuery(document).ready(function($) {

		// connect to hover event of <a> in .livelinks
    $('.livelinks a').hover(function(e) {
        
        // show this message while we are loading        
        this.tip = '<p align="right">Getting recent posts...</p>';
        
        // set the text we want to display
        //this.tip="Recent posts from " + this.href + " will be displayed here...";
        
        // create a new div and display a tip inside
        //$(this).append('<div id="WPLinkRoll_Popup">' + this.tip + '</div>');					
        $(this).append('<div id="WPLinkRoll_Popup"></div>');					

				// use load() method to make an AJAX request
				
				// get coordinates
        var mouseX = e.pageX || (e.clientX ? e.clientX + document.body.scrollLeft: 0);
        var mouseY = e.pageY || (e.clientY ? e.clientY + document.body.scrollTop: 0);

				// offset them a little
        mouseX += <?php echo $options['setx']; ?>;
        mouseY += <?php echo $options['sety']; ?>;
				
				// position our div
        $('#WPLinkRoll_Popup').css({
            left: mouseX + "px",
            top: mouseY + "px"
        });
				
				var popup = $('#WPLinkRoll_Popup'); 				
				
				$.ajax({
				    type: "GET",
				    url: '<?php echo $wp_live_blogroll_plugin_url ?>/wp-live-blogroll-ajax.php',
				    timeout: 3000,
				    data: {
				        link_url: this.href
				    },
				    success: function(msg) {
				        popup.attr('innerHTML', msg);
				        popup.fadeIn(400);
				    },
				    error: function(msg) {
				    		//popup.attr('innerHTML', 'Error: ' + msg.responseText);
				    	
				    }
				})
            
				

				
        
        // show it using a fadeIn function
       // $('#WPLinkRoll_Popup').fadeIn(800);
    },
    // when the mouse hovers out
    function() {
				// fade out the div
        $('#WPLinkRoll_Popup').fadeOut(100);
        
        // remove it
        $(this).children().remove();
    });

});

	/*$j.ajax({
				type: "get",
				url: '<?php bloginfo( "wpurl" ); ?>/wp-content/plugins/wp-live-blogroll/wp-live-blogroll-ajax.php',
				timeout: 3000,
				global: false,
				data: {rss_name: this.href},
				success: function(msg) {										
					if (document.getElementById('WPLinkRoll_Popup'))
						document.getElementById('WPLinkRoll_Popup').innerHTML = msg;
					},
				error: function(msg) {if (document.getElementById('WPLinkRoll_Popup'))
						document.getElementById('WPLinkRoll_Popup').innerHTML = "No posts currently available"; }
				})*/
/*
var mysack = new sack( "<?php bloginfo( 'wpurl' ); ?>/wp-content/plugins/wp-live-blogroll/wp-live-blogroll-ajax.php" );    

  mysack.execute = 1;
  mysack.method = 'POST';
  mysack.setVar( "rss_name", this.href );  
  mysack.onError = function() { alert('Ajax error in voting' )};
  mysack.runAJAX();
  */			