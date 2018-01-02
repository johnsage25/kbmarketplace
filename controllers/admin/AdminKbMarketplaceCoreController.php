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

require_once(_PS_MODULE_DIR_ . 'kbmarketplace/libraries/kbmarketplace/KbGlobal.php');
class AdminKbMarketplaceCoreController extends ModuleAdminControllerCore
{
    protected $kb_module_name = 'kbmarketplace';
    public $bootstrap = true;
    public $kbtemplate = 'not_found_page.tpl';
    public $custom_smarty;
    protected $approval_statuses = array();
    protected $statuses = array();
    protected $render_ajax_html = false;

    public function __construct()
    {
        $this->allow_export = true;
        $this->approval_statuses = KbGlobal::getApporvalStatus();
        $this->statuses = KbGlobal::getStatuses();
        $this->context = Context::getContext();
        $this->list_no_link = true;
        $this->custom_smarty = new Smarty();

        $this->custom_smarty->setTemplateDir(_PS_MODULE_DIR_ . $this->kb_module_name . '/views/templates/admin/');

        parent::__construct();
//        $this->processResetFilters();
    }

    public function initProcess()
    {
        parent::initProcess();
        $this->object = new $this->className(Tools::getValue($this->identifier));
    }

    public function processFilter()
    {
        parent::processFilter();
        $prefix = str_replace(array('admin', 'controller'), '', Tools::strtolower(get_class($this)));
        $filters = $this->context->cookie->getFamily($prefix . $this->list_id . 'Filter_');
        $has_active_filter = false;
        $value = 1;
        $active_filter_key = $this->list_id . 'Filter_active';
        if (isset($filters[$prefix . $this->list_id . 'Filter_active'])) {
            $value = $filters[$prefix . $this->list_id . 'Filter_active'];
            $has_active_filter = true;
        } elseif (Tools::getIsset($active_filter_key)) {
            $value = Tools::getValue($active_filter_key);
            $has_active_filter = true;
        }

        if ($has_active_filter) {
            if (isset($this->fields_list['active']['filter_key'])) {
                $key = $this->fields_list['active']['filter_key'];
                $this->_filter = str_replace(' AND a.`active` = ' . $value . ' ', '', $this->_filter);
                $this->_filter = str_replace(' AND a.`active` = ' . $value . ' ', '', $this->_filter);
                $this->_filter = str_replace(' AND a.active = ' . $value . ' ', '', $this->_filter);
                $this->_filter = str_replace(' AND `a.active` = ' . $value . ' ', '', $this->_filter);
                $tmp_tab = explode('!', $key);
                $key = isset($tmp_tab[1]) ? $tmp_tab[0] . '.`' . $tmp_tab[1] . '`' : '`' . $tmp_tab[0] . '`';
                $this->_filter .= ' AND ' . $key . ' = ' . $value . ' ';
            }
        }
    }

    public function postProcess()
    {
        parent::postProcess();

        if (Tools::isSubmit('ajax')) {
            $return = null;
            Hook::exec('actionAjaxKbAdmin' . Tools::ucfirst($this->action) . 'Before', array('controller' => $this));
            Hook::exec(
                'actionAjaxKb' . get_class($this) . Tools::ucfirst($this->action) . 'Before',
                array('controller' => $this)
            );
            if (Tools::getIsset('ajaxView' . $this->table)) {
                if (method_exists($this, 'processKbAjaxView')) {
                    $return = $this->processKbAjaxView();
                }
            } elseif (Tools::isSubmit('action')) {
                $this->action = Tools::getValue('action');
                if (!empty($this->action)
                    && method_exists($this, 'ajaxKbProcess' . Tools::toCamelCase($this->action))) {
                    $return = $this->{'ajaxKbProcess' . Tools::toCamelCase($this->action)}();
                }
            }
            Hook::exec(
                'actionAjaxKbAdmin' . Tools::ucfirst($this->action) . 'After',
                array('controller' => $this,'return' => $return)
            );
            Hook::exec(
                'actionAjaxKb' . get_class($this) . Tools::ucfirst($this->action) . 'After',
                array('controller' => $this, 'return' => $return)
            );
            if ($this->render_ajax_html) {
                echo $return;
            } else {
                echo Tools::jsonEncode($return);
            }
            die;
        }
    }

    public function initContent()
    {
        if (isset($this->context->cookie->kb_redirect_error)) {
            $this->errors[] = $this->context->cookie->kb_redirect_error;
            unset($this->context->cookie->kb_redirect_error);
        }

        if (isset($this->context->cookie->kb_redirect_success)) {
            $this->confirmations[] = $this->context->cookie->kb_redirect_success;
            unset($this->context->cookie->kb_redirect_success);
        }
        parent::initContent();
    }

    public function renderView()
    {
        return parent::renderView();
    }

    public function setMedia()
    {
        parent::setMedia();
        $this->addCSS($this->getKbModuleDir() . 'views/css/admin/kb-marketplace.css');
        $this->addJS($this->getKbModuleDir() . 'views/js/admin/kb-marketplace.js');
    }

    public function init()
    {
        parent::init();
    }

    protected function getKbModuleDir()
    {
        return _PS_MODULE_DIR_ . $this->kb_module_name . '/';
    }

    /*
     * render seller account approval status
     */

    public function showApprovedStatus($id_row, $tr)
    {
        unset($id_row);
        return $this->approval_statuses[$tr['approved']];
    }

    /**
     * Display account approval link link
     */
    public function displayApproveLink($token = null, $id = 0, $name = null)
    {
        unset($name);
        $tpl = $this->custom_smarty->createTemplate('list_action.tpl');

        $tpl->assign(array(
            'display_confirm_popup' => true,
            'href' => AdminController::$currentIndex . '&' . $this->identifier . '=' . $id . '&approve'
            . $this->table . '&token=' . ($token != null ? $token : $this->token),
            'action' => 'Approve',
            'icon' => 'icon-check'
        ));

        return $tpl->fetch();
    }

    /**
     * Display disapproval link
     */
    public function displayDisapproveLink($token = null, $id = 0, $name = null)
    {
        unset($name);
        $tpl = $this->custom_smarty->createTemplate('list_action.tpl');

        $tpl->assign(array(
            'display_popup' => true,
            'popup_show' => true,
            'href' => AdminController::$currentIndex . '&' . $this->identifier . '=' . $id . '&dissapprove'
            . $this->table . '&token=' . ($token != null ? $token : $this->token),
            'action' => 'Disapprove',
            'icon' => 'icon-times'
        ));

        return $tpl->fetch();
    }
    /**
     * Display disapproval link
     */
    public function displayDisapproveApprovalProductLink($token = null, $id = 0, $name = null)
    {
        
        $id_seller_product = Db::getInstance()->getRow(
            'Select id_seller_product from ' . _DB_PREFIX_ . 'kb_mp_seller_product'
            . ' where id_product = ' . (int) $id
        );
        $seller_product = new KbSellerProduct($id_seller_product['id_seller_product']);
        if ($seller_product->approved != 2 && !empty($seller_product)) {
            unset($name);
            $tpl = $this->custom_smarty->createTemplate('list_action.tpl');

            $tpl->assign(array(
                'display_popup' => true,
                'popup_show' => true,
                'href' => AdminController::$currentIndex . '&' . $this->identifier . '=' . $id . '&dissapprove'
                . $this->table . '&token=' . ($token != null ? $token : $this->token),
                'action' => 'Disapprove',
                'icon' => 'icon-times'
            ));

            return $tpl->fetch();
        }
    }
    
    public function displayDisapproveSellerAccountLink($token = null, $id = 0, $name = null)
    {
        $seller = new KbSeller($id);
        if ($seller->approved != 2 && !empty($seller)) {
            unset($name);
            $tpl = $this->custom_smarty->createTemplate('list_action.tpl');

            $tpl->assign(array(
                'display_popup' => true,
                'popup_show' => true,
                'href' => AdminController::$currentIndex . '&' . $this->identifier . '=' . $id . '&dissapprove'
                . $this->table . '&token=' . ($token != null ? $token : $this->token),
                'action' => 'Disapprove',
                'icon' => 'icon-times'
            ));

            return $tpl->fetch();
        }
    }
    
    public function displayDisapproveSCategoryRequestLink($token = null, $id = 0, $name = null)
    {
        $seller = new KbSellerCRequest($id);
        if ($seller->approved != 2 && !empty($seller)) {
            unset($name);
            $tpl = $this->custom_smarty->createTemplate('list_action.tpl');

            $tpl->assign(array(
                'display_popup' => true,
                'popup_show' => true,
                'href' => AdminController::$currentIndex . '&' . $this->identifier . '=' . $id . '&dissapprove'
                . $this->table . '&token=' . ($token != null ? $token : $this->token),
                'action' => 'Disapprove',
                'icon' => 'icon-times'
            ));

            return $tpl->fetch();
        }
    }
    
    public function displayDisapproveSellerReviewLink($token = null, $id = 0, $name = null)
    {
        $seller = new KbSellerReview($id);
        if ($seller->approved != 2 && !empty($seller)) {
            unset($name);
            $tpl = $this->custom_smarty->createTemplate('list_action.tpl');

            $tpl->assign(array(
                'display_popup' => true,
                'popup_show' => true,
                'href' => AdminController::$currentIndex . '&' . $this->identifier . '=' . $id . '&dissapprove'
                . $this->table . '&token=' . ($token != null ? $token : $this->token),
                'action' => 'Disapprove',
                'icon' => 'icon-times'
            ));

            return $tpl->fetch();
        }
    }

    /**
     * Display Delete link with reason popup
     */
    public function displayDeleteLink($token = null, $id = 0, $name = null)
    {
        unset($name);
        $tpl = $this->custom_smarty->createTemplate('list_action.tpl');

        $tpl->assign(array(
            'display_popup' => true,
            'href' => AdminController::$currentIndex . '&' . $this->identifier . '=' . $id . '&delete'
            . $this->table . '&token=' . ($token != null ? $token : $this->token),
            'action' => 'Delete',
            'icon' => 'icon-trash'
        ));

        return $tpl->fetch();
    }

    /**
     * Display Delete link with reason popup
     */
    public function displayDeleteWReasonLink($token = null, $id = 0, $name = null)
    {
        unset($name);
        $tpl = $this->custom_smarty->createTemplate('list_action.tpl');

        $tpl->assign(array(
            'display_popup' => false,
            'href' => AdminController::$currentIndex . '&' . $this->identifier . '=' . $id . '&delete'
            . $this->table . '&token=' . ($token != null ? $token : $this->token),
            'action' => 'Delete',
            'icon' => 'icon-trash'
        ));

        return $tpl->fetch();
    }

    /**
     * Display view popup link
     */
    public function displayViewModalLink($token = null, $id = 0, $name = null)
    {
        unset($name);
        $tpl = $this->custom_smarty->createTemplate('list_action_view.tpl');
        $tpl->assign(array(
            'display_popup' => true,
            'href' => AdminController::$currentIndex . '&' . $this->identifier . '=' . $id . '&ajaxView'
            . $this->table . '&token=' . ($token != null ? $token : $this->token),
            'action' => 'View',
            'icon' => 'icon-search-plus'
        ));

        return $tpl->fetch();
    }

    /**
     * Display link to view seller detail
     */
    public function displayViewSellerLink($token = null, $id = 0, $name = null)
    {
        unset($name);
        unset($token);
        $tpl = $this->custom_smarty->createTemplate('list_action.tpl');
        $row = new KbSeller($id);
        $tpl->assign(array(
            'separate_tab' => true,
            'href' => $this->context->link->getAdminLink('AdminCustomers')
            . '&updatecustomer&id_customer=' . $row->id_customer,
            'action' => 'View',
            'icon' => 'icon-search-plus'
        ));

        return $tpl->fetch();
    }

    /**
     * Display link to view seller's product detail
     */
    public function displayViewProductLink($token = null, $id = 0, $name = null)
    {
        unset($name);
        unset($token);
        $tpl = $this->custom_smarty->createTemplate('list_action.tpl');
        $row = new KbSellerProduct($id);
        $admin_product_url = '';
        if (version_compare(_PS_VERSION_, '1.7.1.0', '<')) {
            $admin_product_url = $this->context->link->getAdminLink(
                'AdminProducts',
                true,
                array('id_product' => $row->id_product)
            );
        } else {
            $admin_product_url = $this->context->link->getAdminLink(
                'AdminProducts',
                true,
                array(),
                array('id_product' => $row->id_product)
            );
        }
        $tpl->assign(array(
            'separate_tab' => true,
            'href' => $admin_product_url,
            'action' => 'View',
            'icon' => 'icon-search-plus'
        ));

        return $tpl->fetch();
    }

    /**
     * Display link to view seller's product approval detail
     */
    public function displayViewApprovalProductLink($token = null, $id = 0, $name = null)
    {
        unset($name);
        unset($token);
        $tpl = $this->custom_smarty->createTemplate('list_action.tpl');
        $admin_product_url = '';
        if (version_compare(_PS_VERSION_, '1.7.1.0', '<')) {
            $admin_product_url = $this->context->link->getAdminLink(
                'AdminProducts',
                true,
                array('id_product' => $id)
            );
        } else {
            $admin_product_url = $this->context->link->getAdminLink(
                'AdminProducts',
                true,
                array(),
                array('id_product' => $id)
            );
        }
        $tpl->assign(array(
            'separate_tab' => true,
            'href' => $admin_product_url,
            'action' => 'View',
            'icon' => 'icon-search-plus'
        ));

        return $tpl->fetch();
    }

    /*
     * Render Reason popup modal box template
     */

    public function getReasonPopUpHtml()
    {
        $tpl = $this->custom_smarty->createTemplate('dissapprove_reason_popup.tpl');
        $tpl->assign(array(
            'min_length_msg' => sprintf(
                $this->module->l('Minimum %s characters required.'),
                KbGlobal::REASON_MIN_LENGTH
            ),
            'reson_min_length' => KbGlobal::REASON_MIN_LENGTH,
            'reason_min_length_error' =>
            $this->module->l(
                'Minimum '. KbGlobal::REASON_MIN_LENGTH . ' characters required.'
            ),
            'pop_heading' => $this->module->l('Please provide reason to doing this.'),
            'empty_field_error' => $this->module->l('Required Field'),
            'pop_action_label' => $this->module->l('Submit')
        ));
        return $tpl->fetch();
    }

    /*
     * Display product Final Price
     */

    public function showFinalPrice($id_row, $tr)
    {
        unset($id_row);
        return Product::getPriceStatic($tr['id_product'], true, null, 2, null, false, true, 1, true);
    }

    /*
     * Display active status without clickable
     */

    public function showNonClickableStatus($id_row, $tr)
    {
        unset($id_row);
        if ($tr['active'] == 1) {
            return '<a class="list-action-enable action-enabled" href="javascript:void(0)" 
				title="' . $this->module->l('Enable') . '"><i class="icon-check"></i></a>';
        } else {
            return '<a class="list-action-enable action-disabled" href="javascript:void(0)" 
				title="' . $this->module->l('Disable') . '"><i class="icon-remove"></i></a>';
        }
    }
    
    public function renderList()
    {
        $list = parent::renderList();
        $this->bulk_actions = null;

        return $list;
    }
}
