/**
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
* We offer the best and most useful modules PrestaShop and modifications for your online store. 
*
* @category  PrestaShop Module
* @author    knowband.com <support@knowband.com>
* @copyright 2016 Knowband
* @license   see file: LICENSE.txt
*/

var Tree = function (element, options)
{
	this.$element = $(element);
	this.options = $.extend({}, $.fn.tree.defaults, options);
	this.init();
};

Tree.prototype =
{
	constructor: Tree,

	init: function ()
	{
		var that = $(this);

		this.$element.find("label.tree-toggler, .folder-close-icon, .folder-open-icon").click(
			function ()
			{
				if ($(this).parent().parent().children("ul.tree").is(":visible"))
				{
					$(this).parent().children(".folder-open-icon")
						.removeClass("folder-open-icon")
						.addClass("folder-close-icon");
                
                    $(this).parent().children(".folder-close-icon").html('&#xe2c7;');

					that.trigger('collapse');
				}
				else
				{
					$(this).parent().children(".folder-close-icon")
						.removeClass("folder-close-icon")
						.addClass("folder-open-icon");
                                        $(this).parent().children(".folder-open-icon").html('&#xe2c8;');
					that.trigger('expand');
				}
				$(this).parent().parent().children("ul.tree").toggle(300);
			}
		);
		this.$element.find("li").click(
			function ()
			{
				$('.tree-selected').removeClass("tree-selected");
				$('li input:checked').parent().addClass("tree-selected");
			}
		);

		return $(this);
	},
	
	collapseAll : function($speed)
	{
		this.$element.find("label.tree-toggler").each(
			function()
			{
				$(this).parent().children(".folder-open-icon")
					.removeClass("folder-open-icon")
					.addClass("folder-close-icon");
                $(this).parent().children(".folder-close-icon").html('&#xe2c8;');
				$(this).parent().parent().children("ul.tree").hide($speed);
			}
		);

		return $(this);
	},

	expandAll : function($speed)
	{
		this.$element.find("label.tree-toggler").each(
			function()
			{
				$(this).parent().children(".folder-close-icon")
					.removeClass("folder-close-icon")
					.addClass("folder-open-icon");
                $(this).parent().children(".folder-open-icon").html('&#xe2c7;');
				$(this).parent().parent().children("ul.tree").show($speed);
			}
		);

		return $(this);
	},
};

$.fn.tree = function (option, value)
{
	var methodReturn;
	var $set = this.each(
		function ()
		{
			var $this = $(this);
			var data = $this.data('tree');
			var options = typeof option === 'object' && option;

			if (!data){
				$this.data('tree', (data = new Tree(this, options)));
			}
			if (typeof option === 'string') {
				methodReturn = data[option](value);
			}
		}
	);

	return (methodReturn === undefined) ? $set : methodReturn;
};

$.fn.tree.Constructor = Tree;