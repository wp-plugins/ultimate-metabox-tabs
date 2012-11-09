(function($){
	// create a unique id
	function uniqid (prefix, more_entropy) {
		// +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
		// +    revised by: Kankrelune (http://www.webfaktory.info/)
		// %        note 1: Uses an internal counter (in php_js global) to avoid collision
		// *     example 1: uniqid();
		// *     returns 1: 'a30285b160c14'
		// *     example 2: uniqid('foo');
		// *     returns 2: 'fooa30285b1cd361'
		// *     example 3: uniqid('bar', true);
		// *     returns 3: 'bara20285b23dfd1.31879087'
		if (typeof prefix == 'undefined') {
			prefix = "";
		}
	 
		var retId;
		var formatSeed = function (seed, reqWidth) {
			seed = parseInt(seed, 10).toString(16); // to hex str
			if (reqWidth < seed.length) { // so long we split
				return seed.slice(seed.length - reqWidth);
			}
			if (reqWidth > seed.length) { // so short we pad
				return Array(1 + (reqWidth - seed.length)).join('0') + seed;
			}
			return seed;
		};
	 
		// BEGIN REDUNDANT
		if (!this.php_js) {
			this.php_js = {};
		}
		// END REDUNDANT
		if (!this.php_js.uniqidSeed) { // init seed with big random int
			this.php_js.uniqidSeed = Math.floor(Math.random() * 0x75bcd15);
		}
		this.php_js.uniqidSeed++;
	 
		retId = prefix; // start with prefix, add current milliseconds hex string
		retId += formatSeed(parseInt(new Date().getTime() / 1000, 10), 8);
		retId += formatSeed(this.php_js.uniqidSeed, 5); // add seed hex string
		if (more_entropy) {
			// for more entropy we add a float lower to 10
			retId += (Math.random() * 10).toFixed(8).toString();
		}
	 
		return retId;
	}
	
	$('.metabox-newgroup').live('click', function(event){
		event.preventDefault ? event.preventDefault() : event.returnValue = false;
		var newGroup = $('#meta-newgroup .group').clone()
		var newGroupID = uniqid();
		$(newGroup).attr('group-id',newGroupID);
		$(newGroup).children('div').children('table').children('tbody').children('tr').children('td').children('div').children('input').attr('name','umt_group[' + newGroupID +']');
		$(this).before($(newGroup));
		
		// add new div
		var groupID = newGroupID;
		if (groupID !== undefined)
		{
			var newDivID = $('#meta-newdiv .div').clone()
			$(newDivID).children('input').attr('name','umt_div[' + groupID +'][' + uniqid() + ']');
			$(newGroup).children('ul').append($(newDivID));
		}
	});

	$('.metabox-newdiv').live('click', function(event){
		event.preventDefault ? event.preventDefault() : event.returnValue = false;
		var groupID = $(this).parent().parent().parent().attr('group-id');
		if (groupID !== undefined)
		{
			var newDivID = $('#meta-newdiv .div').clone()
			$(newDivID).children('table').children('tbody').children('tr').children('td').children('div').children('input').attr('name','umt_div[' + groupID +'][' + uniqid() + ']');
			$(this).parent().parent().parent().children('.information').children('ul').append($(newDivID));
		}
	});
	
	$('.metabox-groupremove').live('click', function(event){
		event.preventDefault ? event.preventDefault() : event.returnValue = false;
		$(this).parent().parent().parent().parent().parent().parent().remove();
	});
	
	$('.metabox-divremove').live('click', function(event){
		event.preventDefault ? event.preventDefault() : event.returnValue = false;
		$(this).parent().parent().parent().parent().parent().parent().remove();
	});
	
	$('.metabox-divid').live('change', function(event){
		$(this).parent().children('select').val($(this).val());
	});
	
	$('.post_divselect').live('change', function(event){
		$(this).parent().children('input').val($(this).val());
	});
	
	$(document).ready(function() {
		$('.div_sort').sortable({ handle: $(this), placeholder: 'ui-state-highlight', forcePlaceholderSize: true });
		$('.meta-group').sortable({ handle: "table thead", placeholder: 'ui-state-highlight', forcePlaceholderSize: true });
	});

})(jQuery);