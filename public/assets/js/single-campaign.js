;( function( $ ){

	var $pledge_options, 
		$custom_pledge;

	// Set the currently selected pledge.
	var select_pledge = function( $pledge ) {
		var input = $pledge.find( 'input' )[0];

		console.log( $pledge )
		console.log( input );

		// This pledge was already checked, so unset it.
		if ( input.checked ) {
			input.checked = false;
			update_custom_pledge( $pledge, 'decrease' );
		} 
		else {
			input.checked = true;
			update_custom_pledge( $pledge, 'increase' );
		}

		$pledge.toggleClass('selected');
	};

	// Update the custom pledge field after a pledge is selected.
	var update_custom_pledge = function( $pledge, direction ) {
		if ( 'undefined' === typeof $custom_pledge ) {
			return;
		}

		var amount = parseFloat( $custom_pledge.val() ), 
			pledge_amount = parseFloat( $pledge.data('price') );

		if ( isNaN( amount ) ) {
			amount = 0;
		}

		if ( 'decrease' === direction ) {
			$custom_pledge.val( amount - pledge_amount );
		}
		else {
			$custom_pledge.val( amount + pledge_amount );
		}
	};

	// Find the nearest pledge amount for the given custom pledge.
	var find_nearest_pledge = function() {
		var amount = $custom_pledge.val( $pledge.data('price') );

		console.log( amount );
	};

	// Hide pledge option input
	var hide_pledge_input = function() {
		var $input = $('.pledge-option-input');
		$input.hide();
	};

	$(document).ready( function() {
		$pledge_options = $('.pledge-option');
		$custom_pledge = $('#eddcf_custom_price');

		hide_pledge_input();

		$pledge_options.on( 'click', function() {
			select_pledge( $(this) );
		} );

		$custom_pledge.on( 'change', function() {
			select_pledge( find_nearest_pledge() );
		} );


	} );

} )( jQuery );