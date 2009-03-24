( function($) 
{
	var item_clicked = function(ev, ui) 
	{ 
		//alert('test');
		
		var visible_img = 'bullet_toggle_plus.png';
		var invisible_img = 'bullet_toggle_minus.png';
		var current = $(this).attr('src');
		
		if(current.indexOf(visible_img) > -1)
			current = current.replace(visible_img, invisible_img)
		else
			current = current.replace(invisible_img, visible_img);
	
		$(this).attr('src', current);
		
		$(".buddy_list", $(this).parent()).toggle();
	}
	
	$(document).ready( function() 
	{
		$(".category_toggle").bind('click', item_clicked);
	});
	
})(jQuery);