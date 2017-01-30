(function($) {
	"use strict";
	$('.tallythemesetup_bootstrapguru_import').click(function(){
		
		var $import_true = confirm('are you sure to import dummy content ? it will overwrite the existing data');
        if($import_true == false) return;
		$('.tallythemesetup_import_message').html(' Data is being imported please be patient, while the awesomeness is being created :)');
			
        var data = {
			'action': 'tallythemesetup_demo_import',
			'target': 'xml_import'
        };
		var data2 = {
			'action': 'tallythemesetup_demo_import',
			'target': 'widget_import'
        };
		var data3 = {
			'action': 'tallythemesetup_demo_import',
			'target': 'setup_home'
        };
		var data4 = {
			'action': 'tallythemesetup_demo_import',
			'target': 'setup_menu'
        };
		var data5 = {
			'action': 'tallythemesetup_demo_import',
			'target': 'builder_import'
        };
		var data6 = {
			'action': 'tallythemesetup_demo_import',
			'target': 'revolution_slider_import'
        };

      	// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
	   	$( ".tallythemesetup_import_message1" ).css( "display", 'block' );
        $.post(ajaxurl, data, function(response) {
            $('.tallythemesetup_import_message1').html('<div class="import_message_success">'+ response +'</div>');
        })
		.then( function( response ) {
			$( ".tallythemesetup_import_message2" ).css( "display", 'block' );
			 $.post(ajaxurl, data2, function(response) {
				  $('.tallythemesetup_import_message2').html('<div class="import_message_success">'+ response +'</div>');
			 });
		})
		.then( function( response ) {
			$( ".tallythemesetup_import_message3" ).css( "display", 'block' );
			 $.post(ajaxurl, data3, function(response) {
				  $('.tallythemesetup_import_message3').html('<div class="import_message_success">'+ response +'</div>');
			 });
		})
		.then( function( response ) {
			$( ".tallythemesetup_import_message4" ).css( "display", 'block' );
			 $.post(ajaxurl, data4, function(response) {
				  $('.tallythemesetup_import_message4').html('<div class="import_message_success">'+ response +'</div>');
			 });
		})
		.then( function( response ) {
			 $( ".tallythemesetup_import_message5" ).css( "display", 'block' );
			 $.post(ajaxurl, data5, function(response) {
				  $('.tallythemesetup_import_message5').html('<div class="import_message_success">'+ response +'</div>');
			 });
		}).then( function( response ) {
			$( ".tallythemesetup_import_message6" ).css( "display", 'block' );
			 $.post(ajaxurl, data6, function(response) {
				  $('.tallythemesetup_import_message6').html('<div class="import_message_success">'+ response +'</div>');
			 });
		});
    });
})(jQuery);