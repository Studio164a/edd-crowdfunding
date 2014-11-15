;( function( $ ){

	var $pledge_options, 
		$custom_pledge, 
		$donation_input,
		$pledge_total;

	// Set the currently selected pledge.
	var select_pledge = function( $pledge ) {
		var input = $pledge.find( 'input' )[0];

		// This pledge was already checked, so unset it.
		if ( input.checked ) {
			input.checked = false;
		} 
		else {
			input.checked = true;			
		}

		$pledge.toggleClass('selected');

		update_total_pledge();		
	};

	// When a custom pledge amount is entered, manually select the custom pledge input.
	var select_custom_pledge = function() {
		if ( isNaN( $custom_pledge.val() ) ) {
			$donation_input[0].checked = false;
		}
		else {
			$donation_input[0].checked = true;	
		}

		update_total_pledge();		
	};

	// Calculates the total amount pledged.
	var update_total_pledge = function() {
		if ( 'undefined' === typeof $pledge_total ) {
			return;
		}

		var total = parseFloat( $custom_pledge.val() );

		if ( isNaN( total ) ) {
			total = 0;
		}

		$pledge_options.each( function() {			
			if ( $(this).hasClass( 'selected' ) ) {
				total += parseFloat( $(this).data('price') );
			}		
		} );

		$pledge_total.text( parseFloat( total ) );		
	};

	// Hide pledge option input
	var hide_pledge_input = function() {
		var $input = $('.pledge-option-input');
		$input.hide();
	};

	$(document).ready( function() {
		$pledge_options = $('.pledge-option');
		$custom_pledge = $('#eddcf_donation');
		$pledge_total = $('.total-pledge-amount');
		$donation_input = $('.eddcf_donation_input');

		hide_pledge_input();

		if ( $pledge_total.length ) {			
			$pledge_options.on( 'click', function() {
				select_pledge( $(this) );
			} );

			$custom_pledge.on( 'change', function() {
				select_custom_pledge();
			} );
		}
	} );

} )( jQuery );