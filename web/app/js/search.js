(function($){

	"use strict";
	
	$(document).ready(function () {
		search.init();
	});
	
	var search = {
	
		init: function () {
	
			// SEARCH
			$('.advanced-search .f-row:nth-child(2)').hide(500);
			$('input[type=radio]#oneway').click(function() {
				$('.f-row:nth-child(2)').hide(500);
			});
			$('input[type=radio]#return').click(function() {
				$('.f-row:nth-child(2)').slideToggle(500);
			});

			// DATE & TIME PICKER
			$('#dep-date,#ret-date').datetimepicker();
		}
	}

})(jQuery);