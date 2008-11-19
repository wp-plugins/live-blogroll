<?php

/*
WP Live Blogroll JavaScript
Part of a WP Live Blog Roll plugin
- Relies on jQuery
*/

	require_once("../../../wp-config.php");
	$options=WPLiveRoll_GetOptions();
        $nonce = wp_create_nonce( 'wp-live-blogroll' );

?>

// setup everything when document is ready
jQuery(document).ready(function($) {

		// connect to hover event of <a> in .livelinks
    $('.livelinks a').hover(function(e) {
              
        $(this).append('<div id="lb_popup"></div>');					
				
				// get coordinates
        var mouseX = e.pageX || (e.clientX ? e.clientX + document.body.scrollLeft: 0);
        var mouseY = e.pageY || (e.clientY ? e.clientY + document.body.scrollTop: 0);

				// offset them a little
        mouseX += <?php echo $options['setx']; ?>;
        mouseY += <?php echo $options['sety']; ?>;
				
				// position our div
        $('#lb_popup').css({
            left: mouseX + "px",
            top: mouseY + "px"
        });
				
		
				$.ajax({
				    type: "GET",
				    url: '<?php echo $wp_live_blogroll_plugin_url ?>/wp-live-blogroll-ajax.php',
				    timeout: 3000,
				    data: {
				        link_url: this.href,
				        _ajax_nonce: '<?php echo $nonce; ?>'
				    },
				    success: function(msg) {
				       
				        jQuery('#lb_popup').html(msg);
				        jQuery('#lb_popup').fadeIn(300);
				        
				    },
				    error: function(msg) {
				    	 //jQuery('#lb_popup').html('Error: ' + msg.responseText);
				    	
				    }
				})
            
    },
    // when the mouse hovers out
    function() {
				// fade out the div
        $('#lb_popup').fadeOut(100);
        
        // remove it
        $(this).children().remove();
    });

});

	