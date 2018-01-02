<div class="panel">
	{if isset($header)}
        {$header nofilter}
    {/if}
	{if isset($nodes)}
	<ul id="{$id}" class="cattree tree">
		{$nodes nofilter}
	</ul>
	{/if}
</div>
<script type="text/javascript">
	{if isset($use_checkbox) && $use_checkbox == true}
		function checkAllAssociatedCategories($tree)
		{
			$tree.find(":input[type=checkbox]").each(
				function()
				{
					$(this).prop("checked", true);
					$(this).parent().addClass("tree-selected");
				}
			);
		}

		function uncheckAllAssociatedCategories($tree)
		{
			$tree.find(":input[type=checkbox]").each(
				function()
				{
					$(this).prop("checked", false);
					$(this).parent().removeClass("tree-selected");
				}
			);
		}
	{/if}
	{if isset($use_search) && $use_search == true}
		$("#{$id}-categories-search").bind("typeahead:selected", function(obj, datum) {
		    $("#{$id}").find(":input").each(
				function()
				{
					if ($(this).val() == datum.id_category)
					{
						{if (!(isset($use_checkbox) && $use_checkbox == true))}
							$("#{$id} label").removeClass("tree-selected");
						{/if}
						$(this).prop("checked", true);
						$(this).parent().addClass("tree-selected");
						$(this).parents('ul.tree').each(function(){
							$(this).show();
							$(this).prev().find('.folder-close-icon').removeClass('folder-close-icon').addClass('folder-open-icon');
                            $(this).prev().find(".folder-open-icon").html('');
						});
					}
				}
			);
		});
	{/if}
	$(document).ready(function () {
		$("#{$id}").tree("collapseAll");

		{if isset($selected_categories)}
			{assign var=imploded_selected_categories value='","'|implode:$selected_categories}
			var selected_categories = new Array("{$imploded_selected_categories nofilter}");

			$("#{$id}").find(":input").each(
				function()
				{
					if ($.inArray($(this).val(), selected_categories) != -1)
					{
						$(this).prop("checked", true);
						$(this).parent().addClass("tree-selected");
						$(this).parents('ul.tree').each(function(){
							$(this).show();
							$(this).prev().find('.folder-close-icon').removeClass('folder-close-icon').addClass('folder-open-icon');
                            $(this).prev().find(".folder-open-icon").html('');
						});
					}
				}
			);
		{/if}
	});
</script>
{*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer tohttp://www.prestashop.com for more information.
* We offer the best and most useful modules PrestaShop and modifications for your online store.
*
* @category  PrestaShop Module
* @author    knowband.com <support@knowband.com>
* @copyright 2016 knowband
* @license   see file: LICENSE.txt
*}