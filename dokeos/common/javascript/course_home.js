( function($) 
{
	var handle_drop = function(ev, ui) 
	{ 
	       //$(this).empty();
		var target = $(this).attr("id");
		var source = $(ui.draggable).attr("id");
		var course_code = $("#coursecode").html();
	    
	    $(ui.draggable).parent().remove();
	    
	    $.post("./run.php?go=courseviewer&course=" + course_code + "&tool=course_sections&application=weblcms&tool_action=change_section", 
	    {
			target : target,
			source : source
		},  function(data) 
			{
	    		//alert(data);
	    		$("#" + target + " > * > .description").empty();
	    		$("#" + target + " > * > .description").append(data);
	    		$(".tooldrag").css('display', 'inline');
	    	}
	    );
	} 
	
	var handle_visible_clicked = function(ev, ui)
	{
		var visible_img = 'layout/aqua/img/common/action_visible.png';
		var invisible_img = 'layout/aqua/img/common/action_invisible.png';
	
		var parent = $(this).parent().parent();
		var tool = parent.attr('id');
		tool = tool.substring(5, tool.length);

		// Determine visibility icon		
		var img = $(this).attr("src");
		var imgtag = $(this);
		var pos = img.indexOf('invisible'); 
		
		var new_visible = 1;
		if(pos == -1)
			new_visible = 0;
		
		var new_img = new_visible?visible_img:invisible_img;
		
		// Determine tool icon
		var tool_img = $(".tool_image", parent);
		var src = tool_img.attr('src');
		
		// Determine tool text class
		var tool_text = $("#tool_text", parent);
		
		$.post("./application/lib/weblcms/ajax/change_course_module_visibility.php", 
	    {
	    	tool:  tool,
	    	visible: new_visible
	    },	function(data)
	    	{
	    		//alert(data);
	    		imgtag.attr('src', new_img);
	    		if(new_visible == 0)
	    		{
	    			tool_text.addClass('invisible');
	    			var new_src = src.replace('.png', '_na.png');
	    		}
	    		else
	    		{
	    			tool_text.removeClass('invisible');
	    			var new_src = src.replace('_na.png', '.png');
	    		}
	    		
	    		tool_img.attr('src', new_src);
	    	}
	    );
		
		return false;
	}

	$(document).ready( function() 
	{
		$(".toolblock").droppable(
		{	 
			accept: ".tool", 
			drop: handle_drop
		});
		
		$(".tool_visible").bind('click', handle_visible_clicked);
		
		$(".tooldrag").css('display', 'inline');
	});
	
})(jQuery);