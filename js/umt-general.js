(function($){
$(document).ready(function(){ 
	$('#sw-ultimate-metabox-tab-list li a').click(function(event){
		event.preventDefault ? event.preventDefault() : event.returnValue = false;
		var groupID = $(this).parent().attr('id');
		// Remove all umt_group_ classes
		var classes = $('body').attr("class").split(" ");
		for (i = 0; i < classes.length; i++)
		{
			if (classes[i].substr(0,10) == 'umt_group_')
			{
				classes.splice(i,1);
			}
		}
		// Add correct umt_group_ class
		$('body').attr("class", classes.join(" ")).addClass('umt_group_'+groupID+'_class');
		
		// Make the button active
		$('#sw-ultimate-metabox-tab-list li a').removeClass('active');
		$(this).addClass('active');
		return false;
	});
});
})(jQuery);