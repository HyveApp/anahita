/**
 * Author: Rastin Mehr
 * Email: rastin@anahitapolis.com
 * Copyright 2015 rmdStudio Inc. www.rmdStudio.com
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

;(function ($, window, document) {
	
	'use strict';
	
	$.fn.actionDelete = function () {
		
		var elem = $( this );
		
		var confirmModal = $('#an-modal');
		var header = confirmModal.find('.modal-header').find('h3');
		var body = confirmModal.find('.modal-body');
		var footer = confirmModal.find('.modal-footer'); 
		
		header.text(StringLibAnahita.action.delete);
		body.text(StringLibAnahita.prompt.confirmDelete);
		
		var triggerBtn = $('<button class="btn btn-danger"></button>').text(StringLibAnahita.action.delete);
		
		footer.append(triggerBtn);
		
		triggerBtn.on('click', function(event){
			
			$.ajax({
				method : 'post',
				url : elem.attr('href'),
				data : {
					action : elem.data('action')
				},
				
				beforeSend : function(){
					confirmModal.modal('hide');
				},
				
				success : function() {
					
					if(elem.closest('.an-entities').is('.an-entities')){
						elem.closest('.an-entity').fadeOut();
					} else {
						window.location.href = elem.data('redirect');
					}
					
				}.bind(elem)
			});
		});
			
		confirmModal.modal('show');	
	};
	
	$( 'body' ).on( 'click', 'a[data-action="delete"], a[data-action="deletecomment"]', function( event ) {
		
		event.preventDefault();
		event.stopPropagation();
		
		$(this).actionDelete();
	});
	
}(jQuery, window, document));