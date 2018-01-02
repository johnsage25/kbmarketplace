<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 * We offer the best and most useful modules PrestaShop and modifications for your online store.
 *
 * @author    knowband.com <support@knowband.com>
 * @copyright 2017 Knowband
 * @license   see file: LICENSE.txt
 * @category  PrestaShop Module
 *
 */

include_once('kbmarketplace.php');

class KBMPMainTab extends AdminTab
{
    public function __construct()
    {
        parent::__construct();
    }

    public function display()
    {
        $module = new KbMarketPlace();
        $link = $this->context->link->getAdminLink('AdminModules');
        Tools::redirect(
            $link . '&configure=' . $module->name . '&tab_module=' . $module->tab
            . '&module_name=' . $module->name
        );
    }
}
