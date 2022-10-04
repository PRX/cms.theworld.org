jQuery(function ( $ ) {
	var isAdding = false;

	function clear( $wrapper ) {
		$( '#emwi-urls', $wrapper ).val( '' );
		$( '#emwi-hidden', $wrapper ).hide();
		$( '#emwi-error', $wrapper ).text( '' );
	}

  function validateAttachment(attachment, frame) {
    var allowedTypes = frame.options.mimeType;
    var allowedExts = frame.acf && frame.acf.data.allowedTypes.split(', ');
    var fileExt = attachment.filename.split('.')[1].toLowerCase();
    var allowed = false;

    // Allow only attachments of the correct mime type or file ext.
    if ( allowedTypes && allowedTypes.length ) {
      allowed = allowedTypes.indexOf( attachment.type ) > -1;
    }
    else if ( allowedExts && allowedExts.length ) {
      allowed = allowedExts.indexOf( fileExt ) > -1;
    }

    return allowed;
  }

	$( 'body' ).on( 'click', '#emwi-clear', function ( e ) {
    var $wrapper = $(this).closest('#emwi-in-upload-ui');
		clear( $wrapper );
	});

	$( 'body' ).on( 'click', '#emwi-show', function ( e ) {
    var $wrapper = $(this).closest('#emwi-in-upload-ui');
		$( '#emwi-media-new-panel', $wrapper ).show();
		e.preventDefault();
	});

	$( 'body' ).on( 'click', '#emwi-add', function ( e ) {
		e.preventDefault();

    var $wrapper = $(this).closest('#emwi-in-upload-ui');

		if ( isAdding ) {
			return;
		}
		isAdding = true;

		$( '#emwi-add', $wrapper ).prop('disabled', true);

		var postData = {
			'urls': $( '#emwi-urls', $wrapper ).val()
		};
		wp.media.post( 'add_external_media_without_import', postData )
			.done(function ( response ) {
				if ( response['error'] ) {
					$( '#emwi-error', $wrapper ).text( response['error'] );
					$( '#emwi-hidden', $wrapper ).show();
				} else {
					// Reset the input.
					clear( $wrapper );

          // Update the attachment list in browser.
          var frame = wp.media.frame && (wp.media.frame.acf ? wp.media.frame.acf.frame : wp.media.frame);

          if ( frame ) {
            frame.content.mode( 'browse' );
            // The frame variable may be MediaFrame.Manage or MediaFrame.EditAttachments.
            // In the later case, library = frame.library.
            var frameState = frame.state();
            var library = (frameState && frameState.get( 'library' )) || frame.library;
            var selection = frameState.get( 'selection' );

            if (library && selection) {
              response.attachments.forEach( function ( elem ) {
                // Don't add invalid attachments to current media library view.
                if ( ! validateAttachment(elem, frame) ) {
                  return true;
                }

                var attachment = wp.media.model.Attachment.create( elem );
                attachment.fetch();

                library.add( attachment ? [ attachment ] : [] );

                selection.add( attachment );
              } );
            }
          }
				}
        $( '#emwi-urls', $wrapper ).val( response['urls'].replace(',', '\n') );
				$( '#emwi-buttons-row .spinner', $wrapper ).css( 'visibility', 'hidden' );
				$( '#emwi-add', $wrapper ).prop('disabled', false);
				isAdding = false;
			}).fail(function (response ) {
				$( '#emwi-error', $wrapper ).text( 'An unknown network error occured' );
				$( '#emwi-buttons-row .spinner', $wrapper ).css( 'visibility', 'hidden' );
				$( '#emwi-add', $wrapper ).prop('disabled', false);
				isAdding = false;
			});

		$( '#emwi-buttons-row .spinner', $wrapper ).css( 'visibility', 'visible' );
	});

	$( 'body' ).on( 'click', '#emwi-cancel', function (e ) {
    var $wrapper = $(this).closest('#emwi-in-upload-ui');
		clear( $wrapper );
		$( '#emwi-media-new-panel', $wrapper ).hide();
		$( '#emwi-buttons-row .spinner', $wrapper ).css( 'visibility', 'hidden' );
		$( '#emwi-add', $wrapper ).prop('disabled', false);
		isAdding = false;
		e.preventDefault();
	});
});
