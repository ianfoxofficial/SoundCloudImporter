
(function($) {
$(document).ready(function() {
	$(".delete_button").click(function(e) {
		e.preventDefault();
		
		var row = $(this).closest("tr");
		
		
		row.remove();
		
		$("#setting-error-idv_SCImporter_message_updated").addClass("notice").show();
	}); 

});
})( jQuery );