(function($) {
	"use strict";

	$(document).ready(function(){

		/**
		 *
		 * SHOW SORT RESULTS
		 * 
		 */
		var $sort_value   = '';
		var $sort_mode    = '';
		var $shortcode_id = '';
		var $location     = '';
		var $page_num     = '';
		var data          = '';
		var $nonce        = '';

		$( 'body' ).on( "change", ".rfp-sorting-form select", function() { 

			window.scrollTo({top: 0, behavior: 'smooth'});

			$('#rfp-replaceable-div').replaceWith('<div id="rfp-replaceable-div"></div>');
			$('.rfp-loader').show();
			$sort_value   = $(this).find(":selected").val();
			$sort_mode    = $(this).parent().find('.rfp-sort-mode').val();
			$shortcode_id = $(this).parent().find('.rfp-shortcode-id').val();
			$nonce        = $(this).parent().find('.rssfp-nonce').val();

			var   data    = {
				'action': 'rssfp_sort',
				'sort_value': $sort_value,
				'sort_mode': $sort_mode,
				'shortcode_id': $shortcode_id,
				'nonce': $nonce,
			};

			if ( $sort_value ) {
				$('body').addClass('rfp-show-modal');
				$('#rfp-sort-title-ext').replaceWith( '<span id="rfp-sort-title-ext">'+$sort_mode+':'+' '+$sort_value+'</span>' );

				$.ajax({ 
					type: 'POST', 
					url: rssfp_object.url, 
					data: data,
					success: function ( data ) {
						if ( data.success ) {
							$('#rfp-replaceable-div').replaceWith( data.html );
							$('.rfp-loader').hide();
						}else {
							$('#rfp-replaceable-div').replaceWith( data.html );
							$('.rfp-loader').hide();
						}
					},//success
					error: function(XMLHttpRequest, textStatus, errorThrown) { 
					},//Error
				});

			}
	       
	    });


	    /**
		 *
		 *
		 * CLOSE MODAL
		 * 
		 */
	    $( "#rfp-x-out-btn" ).on( "click", function(e) {
	    	$('body').removeClass('rfp-show-modal');
	    }); 


	    /**
		 *
		 * 
		 * PAGINATIONS
		 * 
		 */
	    $('body').on( "click", "#rfp-pagination .page-numbers li a", function(e) {

	    	window.scrollTo({top: 0, behavior: 'smooth'});
	    	
	    	$shortcode_id = $('#rfp-ajax-shortcode-id').val();
	        $('#rfp-replaceable-div').replaceWith('<div id="rfp-replaceable-div"></div>');
			$('.rfp-loader').show(); 
			e.preventDefault();

			$location     = $(this).attr('href');
			$location     = getUrlVars( $location );
			$sort_value   = $location['sort-value'];
		    $sort_mode    = $location['rss-feed-pro-sortby'];
		    $page_num     = $location['paged'];
		    $nonce        = $('#_rssf_pagination_nonce').val();

			data    = {
				'action':       'rssfp_sort',
				'sort_value':   $sort_value,
				'sort_mode':    $sort_mode,
				'shortcode_id': $shortcode_id,
				'page_num':     $page_num,
				'nonce':        $nonce,
			};

			rssfp_ajax_req( data );

		});


		/**
		 *
		 * 
		 * CUSTOM FUNCTIONS
		 * 
		 */
		function rssfp_ajax_req( data ){
			$.ajax({ 
				type: 'POST', 
				url: rssfp_object.url, 
				data: data,
				success: function ( data ) {
					if ( data.success ) {
						$('#rfp-replaceable-div').replaceWith( data.html );
						$('.rfp-loader').hide();
					}
				},//success
				error: function(XMLHttpRequest, textStatus, errorThrown) { 
				},//Error
			});
		}

		function getUrlVars( $location ) {
		    var vars = [], hash;
		    var hashes = $location.slice($location.indexOf('?') + 1).split('&');
		    for(var i = 0; i < hashes.length; i++)
		    {
		        hash = hashes[i].split('=');
		        vars.push(hash[0]);
		        vars[hash[0]] = hash[1];
		    }
		    return vars;
		}

	}); //document ready
})(jQuery);