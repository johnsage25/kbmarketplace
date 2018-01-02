<script type="text/javascript">
    $(document).ready(function(){
        tinySetup({
                    editor_selector :"kb_autoload_rte",
                    setup : function(ed) {
                            ed.on('init', function(ed)
                            {
                                    
                            });

                            ed.on('keydown', function(ed, e) {
                                    tinyMCE.triggerSave();
                                    var textarea = $('#'+tinymce.activeEditor.id);
                                    $(textarea).html(tinyMCE.activeEditor.getContent());
                                    var max = textarea.parent('div').find('span.counter').data('max');
                                    if (max != 'none')
                                    {
                                            count = tinyMCE.activeEditor.getBody().textContent.length;
                                            rest = max - count;
                                            if (rest < 0)
                                                    textarea.parent('div').find('span.counter').html('<span style="color:red;">Maximum '+ max +' characters : '+rest+'</span>');
                                            else
                                                    textarea.parent('div').find('span.counter').html(' ');
                                    }
                            });
                    }
            });
    
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