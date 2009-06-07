/*
Copyright (c) 2009, Hans De Bisschop, conversion to seperate (non ui-tabs based) plugin
*/

(function($){
	$.fn.extend({ 
		elementfinder: function(options) {

			//Settings list and the default values
			var defaults = {
					name: '',
					search: '',
					nodesSelectable: false
			};
			
			var settings = $.extend(defaults, options);
			var self = this, id, activatedElements, excludedElements,
				inactiveBox, activeBox;
			var timer;
			
			function collapseItem(e) {
				$("ul:first", $(this).parent()).hide();
				if ($(this).hasClass("lastCollapse"))
				{
					$(this).removeClass("lastCollapse");
					$(this).addClass("lastExpand");
				}
				else if ($(this).hasClass("collapse"))
				{
					$(this).removeClass("collapse");
					$(this).addClass("expand");
				}
			}
			
			function expandItem(e) {
				$("ul:first", $(this).parent()).show();
				if ($(this).hasClass("lastExpand"))
				{
					$(this).removeClass("lastExpand");
					$(this).addClass("lastCollapse");
				}
				else if ($(this).hasClass("expand"))
				{
					$(this).removeClass("expand");
					$(this).addClass("collapse");
				}
			}
			
			function processTree()
			{
				$("ul li:last-child > div", self).addClass("last");
				$("ul li:last-child > ul", self).css("background-image", "none");
				
				$("ul li:not(:last-child):has(ul) > div", self).addClass("collapse");
				$("ul li:last-child:has(ul) > div").addClass("lastCollapse");
				
				$("ul li:has(ul) > div", self).toggle(collapseItem, expandItem);
				$("ul li:has(ul) > div > a", self).click(function(e){e.stopPropagation();});
			}
			
			function displayMessage(message, element)
			{
				element.html(message);
			};
			
			function getExcludedElements()
			{
				var elements = eval(settings.name + '_excluded');
				
				return elements;
			}
			
			function getSearchResults()
			{
				var query = $('#attachments_search_field').val();
				
				var response = $.ajax({
					type: "GET",
					dataType: "xml",
					url: settings.search,
					data: { query: query, 'exclude[]': getExcludedElements() },
					async: false
				}).responseText;
				
				return response;
			}
			
			function buildElementTree(response)
			{
				var ul = $('<ul class="tree-menu"></ul>');
				
				var tree = $.xml2json(response, true);
				
				if((tree.node && $(tree.node).size() > 0) || (tree.leaf && $(tree.leaf).size() > 0))
				{
					if (tree.node && $(tree.node).size() > 0)
					{
						$.each(tree.node, function(i, the_node){
								var li = $('<li><div><a href="#" id="' + the_node.id + '" class="category">' + the_node.title + '</a></div></li>');
								$(ul).append(li);
								buildElement(the_node, li);
							});
					}
					
					if (tree.leaf && $(tree.leaf).size() > 0)
					{
						$.each(tree.leaf, function(i, the_leaf){
							var li = $('<li><div><a href="#" id="' + the_leaf.id + '" class="' + the_leaf.class + '">' + the_leaf.title + '</a></div></li>');
							$(ul).append(li);
						});
					}
					
					$(inactiveBox).html(ul);
				}
				else
				{
					displayMessage('No results', inactiveBox);
				}
			}
			
			function buildElement(the_node, element)
			{
				if((the_node.node && $(the_node.node).size() > 0) || (the_node.leaf && $(the_node.leaf).size() > 0))
				{
					var ul = $('<ul></ul>');
					$(element).append(ul);
					
					if (the_node.node && $(the_node.node).size() > 0)
					{
						$.each(the_node.node, function(i, a_node){
							var li = $('<li><div><a href="#" id="' + a_node.id + '" class="category">' + a_node.title + '</a></div></li>');
							$(ul).append(li);
							buildElement(a_node, li);
						});
					}
					
					if (the_node.leaf && $(the_node.leaf).size() > 0)
					{
						$.each(the_node.leaf, function(i, a_leaf){
							var li = $('<li><div><a href="#" id="' + a_leaf.id + '" class="' + a_leaf.class + '">' + a_leaf.title + '</a></div></li>');
							$(ul).append(li);
						});
					}
				}
			}
			
			function updateSearchResults()
			{
				displayMessage('<div class="element_finder_loading"></div>', inactiveBox);
				var searchResults = getSearchResults();
				buildElementTree(searchResults);
				processTree();
			}
			
			function setActivatedElements()
			{
				activatedElements = $.json.deserialize($("#elf_attachments_active_hidden", self).val());
				
				var ul = $('<ul class="tree-menu"></ul>');
				
				$.each(activatedElements.elements, function(i, activatedElement){
					var li = $('<li><div><a href="#" id="' + activatedElement.id + '" class="' + activatedElement.class + '">' + activatedElement.title + '</a></div></li>');
					ul.append(li);
				});
				
				$(activeBox).html(ul);
			}
			
			function disableActivatedElements()
			{
				$.each(activatedElements.elements, function(i, activatedElement){
					var current_element = $('#' + activatedElement.id, inactiveBox);
					current_element.addClass('disabled');
					current_element.css("background-image", current_element.css("background-image").replace(".png", "_na.png"));
				});
			}
			
			function deactivateElement(e)
			{
				e.preventDefault();
				var the_element = $('#' + $(this).attr('id'), inactiveBox);
				the_element.removeClass('disabled');
				the_element.css("background-image", the_element.css("background-image").replace("_na.png", ".png"));
				$(this).parent().parent().remove();
			}
			
			function activateElement(e)
			{
				e.preventDefault();
				var elementHtml = $(this).parent().parent().html();
				
				var the_element = $('#' + $(this).attr('id'), inactiveBox);
				the_element.addClass('disabled');
				the_element.css("background-image", the_element.css("background-image").replace(".png", "_na.png"));
				
//				var the_element = $('#' + $(this).attr('id'), inactiveBox);
//				the_element.removeClass('disabled');
//				the_element.css("background-image", the_element.css("background-image").replace("_na.png", ".png"));
//				$(this).parent().parent().remove();
			}
			
			function init()
			{
				id = $(self).attr('id');
				inactiveBox = $('#elf_' + settings.name + '_inactive');
				activeBox = $('#elf_' + settings.name + '_active');
				
				setActivatedElements();
				updateSearchResults();
				disableActivatedElements();
				
				$("a", activeBox).live("click", deactivateElement);
				$("a:not(.disabled)", inactiveBox).live("click", activateElement);
				
				$('#attachments_search_field').keypress( function() {
						// Avoid searches being started after every character
						clearTimeout(timer);
						timer = setTimeout(updateSearchResults, 750);
					});
			}
			
			init();
    	}
	});
})(jQuery);