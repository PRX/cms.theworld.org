(function($) {
	"use strict";

	$(document).ready(function(){
		
		/*		
		*
		* Dismiss notice
		*
		*/

		var $nonce = '';
		$( 'body' ).on( "click", "#rfp-ask-for-rating a", function() { 
			$nonce  = $('#_rssfp_userfeedback').val();
			var data = {
				'action': 'rssfp_dismiss_notice',
				'final':   true,
				'nonce': $nonce,
			};

			$.ajax({ 
				type: 'POST', 
				url: ajaxurl, 
				data: data,
				success: function ( data ) {
					$('#rfp-ask-for-rating').remove();
				},//success
				error: function(XMLHttpRequest, textStatus, errorThrown) { 
				},//Error
			});
		});

		$( 'body' ).on( "click", "#rfp-ask-for-rating .notice-dismiss", function() { 
			$nonce   = $('#_rssfp_userfeedback').val();
			var data = {
				'action': 'rssfp_dismiss_notice',
				'not-final': true,
				'nonce': $nonce,
			};

			$.ajax({ 
				type: 'POST', 
				url: ajaxurl, 
				data: data,
				success: function ( data ) {
				},//success
				error: function(XMLHttpRequest, textStatus, errorThrown) { 
				},//Error
			});
		});
		

	}); //document ready
})(jQuery);
