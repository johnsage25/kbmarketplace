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

require_once 'KbCore.php';

class KbmarketplaceProductModuleFrontController extends KbmarketplaceCoreModuleFrontController
{
    public $controller_name = 'product';
    public $kb_product;
    public $available_tabs_lang = array();
    protected $available_tabs = array();
    protected $default_form_language;
    public $custom_smarty;
    private $max_image_size = null;

    private $seller_product = null;

    public function __construct()
    {
        parent::__construct();

        $this->available_tabs_lang = array(
            'Informations' => $this->module->l('Information'),
            'Prices' => $this->module->l('Prices'),
            'Seo' => $this->module->l('SEO'),
            'Images' => $this->module->l('Images'),
            'Features' => $this->module->l('Features'),
            'Quantities' => $this->module->l('Quantities'),
            'Categories' => $this->module->l('Categories'),
            'Suppliers' => $this->module->l('Suppliers'),
            'Shipping' => $this->module->l('Shipping'),
            'Combinations' => $this->module->l('Combinations'),
            'VirtualProduct' => $this->module->l('Virtual Product'),
            'Pack' => $this->module->l('Pack')
        );

        $this->available_tabs = array(
            'Informations' => 0,
            'Prices' => 1,
            'Seo' => 2,
            'Images' => 3,
            'Features' => 4,
            'Quantities' => 5,
            'Categories' => 6,
            'Suppliers' => 7
        );

        $this->default_form_language = $this->context->language->id;

        require_once $this->getKbModuleDir() . 'classes/CategoryTree.php';
    }

    public function setMedia()
    {
        parent::setMedia();
        if (Tools::getIsset('render_type') && Tools::getValue('render_type') == 'form') {
            $this->addCSS($this->getKbModuleDir() . 'views/css/front/kb-forms.css');
            $this->addCSS($this->getKbModuleDir() . 'views/css/front/kb-product-form.css');
            $this->addJS($this->getKbModuleDir() . 'views/js/front/kb-product-form.js');
            $this->addJS($this->getKbModuleDir() . 'views/js/front/kb-common.js');
            $this->addJS($this->getKbModuleDir() . 'views/js/front/kb_category_tree.js');
            $this->addJS($this->getKbModuleDir() . 'libraries/tinymce/tinymce.min.js');
            $this->addCSS($this->getKbModuleDir().'views/css/front/multiple-select.css');
            $this->addJS($this->getKbModuleDir().'views/js/front/jquery.multiple.select.js');
            $this->context->controller->addJqueryPlugin('select2');
        }
    }

    public function postProcess()
    {
        parent::postProcess();
        if (Tools::isSubmit('ajax')) {
            $this->json = array();
            $renderhtml = false;
            if (Tools::isSubmit('method')) {
                switch (Tools::getValue('method')) {
                    case 'searchedproduct':
                        $this->getAjaxProductList();
                        die;
                    case 'addProductImage':
                        $this->json = $this->processAddProductImage();
                        break;
                    case 'deleteImage':
                        $this->json = $this->processDeleteImage();
                        break;
                    case 'getCombination':
                        $this->json = $this->processGetCombination(
                            Tools::getValue('id_product'),
                            Tools::getValue('id_product_attribute')
                        );
                        break;
                    case 'saveCombination':
                        $this->json = $this->processSaveCombination();
                        break;
                    case 'deleteCombination':
                        $this->json = $this->processDeleteCombination(
                            Tools::getValue('id_product'),
                            Tools::getValue('id_product_attribute')
                        );
                        break;
                    case 'deleteVirtualFile':
                        $this->json = $this->processDeleteVirtual(Tools::getValue('id_product'));
                        break;
                    case 'getSellerProducts':
                        $this->json = $this->getAjaxProductListHtml();
                        break;
                    case 'getAjaxCategoryTree':
                        echo $this->ajaxGetCategoryTree();
                        die;
                    case 'getAjaxSubCategoryTree':
                        echo $this->ajaxGetSubCategoryTree();
                        die;
                }
            }
            if (!$renderhtml) {
                echo Tools::jsonEncode($this->json);
            }
            die;
        } elseif (Tools::isSubmit('multiaction') && Tools::getValue('multiaction')) {
            $this->processMultiAction();
        } else {
            $id_product = Tools::getValue('id_product', 0);
            $form_key = Tools::encrypt($this->seller_info['id_seller'] . $this->controller_name . 'productform');

            if ($id_product == 0 && Tools::isSubmit('productformkey')
                && Tools::getValue('productformkey') == $form_key) {
                $this->processAdd();
            } elseif ($id_product > 0) {
                if (KbSellerProduct::isSellerProduct($this->seller_info['id_seller'], $id_product)) {
                    if (Tools::getIsset('duplicateProduct') && Tools::getValue('duplicateProduct') == 1) {
                        $this->processDuplicate();
                    } elseif (Tools::getIsset('deleteProduct') && Tools::getValue('deleteProduct') == 1) {
                        $this->processDelete();
                    } elseif (Tools::isSubmit('productformkey') && Tools::getValue('productformkey') == $form_key) {
                        $this->processUpdate();
                    }
                } else {
                    $this->context->smarty->assign('permission_error', true);
                    $this->Kberrors[] = $this->module->l(
                        'You do not have permission on this product.'
                    );
                }
            }
        }
    }

    public function initContent()
    {
        if (Tools::getIsset('render_type') && Tools::getValue('render_type') == 'form') {
            $this->renderProductForm();
        } else {
            $this->renderList();
        }

        parent::initContent();
    }
    
    public function getTemplateVarPage()
    {
        $page = parent::getTemplateVarPage();
        if (isset($page['meta']) && $this->seller_info) {
            $page_title = 'Products';
            $page['meta']['title'] =  $page_title;
            $page['meta']['keywords'] = $this->seller_info['meta_keyword'];
            $page['meta']['description'] = $this->seller_info['meta_description'];
        }
        return $page;
    }
    
    public function renderList()
    {
        $this->total_records = KbSellerProduct::getSellerProducts($this->seller_info['id_seller'], true);
        if ($this->total_records > 0) {
            $categories = $this->getCategoryList();
            $filter_category_list = array();
            foreach ($categories as $cat) {
                $filter_category_list[] = array('value' => $cat['id_category'], 'label' => $cat['name']);
            }
            $tmp = KbGlobal::getApporvalStatus();
            $approve_statuses = array();
            foreach ($tmp as $key => $val) {
                $approve_statuses[] = array(
                    'value' => $key,
                    'label' => $val
                );
            }

            $this->filter_header = $this->module->l('Filter Your Search');
            $this->filter_id = 'seller_product';
            $this->filters = array(
                array(
                    'type' => 'text',
                    'name' => 'reference',
                    'label' => $this->module->l('Reference'),
                ),
                array(
                    'type' => 'text',
                    'name' => 'name',
                    'label' => $this->module->l('Product Name'),
                ),
                array(
                    'type' => 'select',
                    'placeholder' => $this->module->l('Select'),
                    'name' => 'id_category_default',
                    'label' => $this->module->l('Default Category'),
                    'values' => $filter_category_list,
                    'validate' => 'isInt'
                ),
                array(
                    'type' => 'select',
                    'placeholder' => $this->module->l('Select'),
                    'name' => 'approved',
                    'label' => $this->module->l('Status'),
                    'values' => $approve_statuses,
                    'validate' => 'isInt'
                ),
                array(
                    'type' => 'select',
                    'placeholder' => $this->module->l('Select'),
                    'name' => 'active',
                    'label' => $this->module->l('Active'),
                    'values' => array(
                        array('value' => 0, 'label' => $this->module->l('No')),
                        array('value' => 1, 'label' => $this->module->l('Yes'))),
                    'validate' => 'isInt'
                )
            );
            $this->filter_action_name = 'getSellerProducts';
            $this->context->smarty->assign('kbfilter', $this->renderKbListFilter());

            $this->table_id = $this->filter_id;
            $this->table_header = array(
                array(
                    'label' => $this->module->l('ID'),
                    'align' => 'right',
                    'class' => '',
                    'width' => '60'
                ),
                array(
                    'label' => $this->module->l('Product Name'),
                    'align' => 'left',
                    'class' => '',
                ),
                array(
                    'label' => $this->module->l('Reference'),
                    'align' => 'left',
                    'class' => '',
                    'width' => '120',
                ),
                array(
                    'label' => $this->module->l('Type'),
                    'align' => 'left',
                ),
                array(
                    'label' => $this->module->l('Default Category'),
                    'align' => 'left',
                    'class' => '',
                ),
                array(
                    'label' => $this->module->l('Price'),
                    'align' => 'right',
                    'class' => '',
                    'width' => '90',
                ),
                array(
                    'label' => $this->module->l('Status'),
                    'align' => 'left',
                    'width' => '80',
                ),
                array(
                    'label' => $this->module->l('Active'),
                    'align' => 'left',
                    'class' => '',
                    'width' => '40',
                ),
                array(
                    'label' => $this->module->l('Action'),
                    'align' => 'left',
                    'class' => '',
                    'width' => '90',
                )
            );

            $orderby = null;
            if (Tools::getIsset('orderby') && Tools::getValue('orderby') != '') {
                $orderby = Tools::getValue('orderby');
            }

            $orderway = null;
            if (Tools::getIsset('orderway') && Tools::getValue('orderway') != '') {
                $orderway = Tools::getValue('orderway');
            }

            $sellers_products = KbSellerProduct::getSellerProducts(
                $this->seller_info['id_seller'],
                false,
                $this->getPageStart(),
                $this->tbl_row_limit,
                $orderby,
                $orderway
            );

            foreach ($sellers_products as $val) {
                $product = new Product($val['id_product'], false, $this->seller_info['id_default_lang']);
                $seller_product = Db::getInstance()->getRow(
                    'SELECT * FROM '._DB_PREFIX_.'kb_mp_seller_product_tracking'
                    . ' WHERE id_product='. (int) $val['id_product']
                );
                $cat = new Category($product->id_category_default, $this->seller_info['id_default_lang']);

                $edit_link = $this->context->link->getModuleLink(
                    $this->kb_module_name,
                    $this->controller_name,
                    array('render_type' => 'form', 'step' => 2, 'id_product' => $product->id),
                    (bool)Configuration::get('PS_SSL_ENABLED')
                );

                $view_link = $this->context->link->getProductLink(
                    $product,
                    null,
                    null,
                    null,
                    $this->seller_info['id_default_lang']
                );

                $yes_txt = $this->module->l('Yes');
                $this->table_content[$product->id] = array(
                    array('value' => '#' . $product->id),
                    array(
                        'link' => array(
                            'href' => $view_link,
                            'function' => '',
                            'title' => $this->module->l('Click to view product'),
                            'target' => '_blank'
                        ),
                        'value' => $product->name,
                        'class' => '',
                    ),
                    array('value' => $product->reference),
                    array('value' => $this->getProductType($product)),
                    array('value' => $cat->name),
                    array(
                        'value' => Tools::displayPrice(
                            Tools::convertPrice($product->price),
                            $this->seller_currency
                        ),
                        'align' => 'kb-tright'
                    ),
                    array('value' => KbGlobal::getApporvalStatus($val['approved'])),
                    array('value' => (!empty($seller_product) || $product->active) ? $yes_txt : $this->module->l('No')),
                    array(
                        'actions' => array(
                            array(
                                'href' => $edit_link,
                                'title' => $this->module->l('Click to edit product'),
                                'icon-class' => '&#xe22b'
                            ),
                            array(
                                'title' => $this->module->l('Click to delete product'),
                                'function' => 'KbDeleteAction('.$product->id.')',
                                'icon-class' => '&#xe872'
                            )
                        )
                    ),
                );
            }

            $this->table_enable_multiaction = true;
            $this->list_row_callback = $this->filter_action_name;

            //Show Multi actions
            $this->kb_multiaction_params['multiaction_values'] = array(
                array(
                    'label' => $this->module->l('Status Update'),
                    'value' => KbGlobal::MULTI_ACTION_TYPE_STATUS
                ),
                array(
                    'label' => $this->module->l('Delete'),
                    'value' => KbGlobal::MULTI_ACTION_TYPE_DELETE
                )
            );

            $this->kb_multiaction_params['show_status_on_multiaction_value'] = 1;
            $this->kb_multiaction_params['has_status_dropdown'] = true;

            $this->kb_multiaction_params['status_dropdown_values'] = array(
                array('label' => $this->module->l('Enable'), 'value' => 1),
                array('label' => $this->module->l('Disable'), 'value' => 0)
            );

            $this->kb_multiaction_params['multiaction_related_to_table'] = $this->table_id;
            $this->kb_multiaction_params['has_reason_popup'] = true;
            $this->kb_multiaction_params['submit_action'] = $this->context->link->getModuleLink(
                $this->kb_module_name,
                $this->controller_name,
                array('multiaction' => true),
                (bool)Configuration::get('PS_SSL_ENABLED')
            );

            $this->context->smarty->assign('kbmutiaction', $this->renderKbMultiAction());
        }

        $this->context->smarty->assign('kblist', $this->renderKbList());
        $this->context->smarty->assign(array(
            'new_product_link' => $this->context->link->getModuleLink(
                $this->kb_module_name,
                $this->controller_name,
                array('render_type' => 'form'),
                (bool)Configuration::get('PS_SSL_ENABLED')
            )
        ));

        $this->setKbTemplate('product/list.tpl');
    }

    public function renderProductForm()
    {
        $step = 1;
        $url_param = array(
            'render_type' => 'form',
            'step' => 1
        );

        $id_product = 0;
        $product_form_heading = $this->module->l('New Product');
        if (Tools::getIsset('id_product') && Tools::getValue('id_product') > 0) {
            $step = 2;
            $id_product = (int)Tools::getValue('id_product');
        }

        //check for product limit
        if ($id_product == 0 && (!$this->seller_obj->isApprovedSeller() || $this->seller_obj->active == 0)) {
            $added_product_count = $this->seller_obj->product_limit_wout_approval;
            $product_limit = KbSellerSetting::getSellerSettingByKey($this->seller_obj->id, 'kbmp_product_limit');
            $error_txt = 'Your limit of adding new products has been over as your account is not approved.';
            $error_txt .= 'To add more products, please contact to admin.';
            if ($added_product_count >= $product_limit) {
                $this->context->cookie->__set(
                    'redirect_error',
                    $this->module->l($error_txt)
                );

                Tools::redirect($this->context->link->getModuleLink(
                    $this->kb_module_name,
                    $this->controller_name,
                    array(),
                    (bool)Configuration::get('PS_SSL_ENABLED')
                ));
            }
        }

        if (Tools::getIsset('step') && Tools::getValue('step') == 2) {
            $step = (int)Tools::getValue('step');
            $url_param['step'] = $step;
        }

        if ($step == 1 && Tools::isSubmit('submitproducttype')) {
            $step = 2;
            $url_param['step'] = 2;
        }

        $default_lang_js_path = $this->getKbModuleDir() . 'libraries/tinymce/langs/'
            .Language::getIsoById($this->default_form_language).'.js';
        if (file_exists($default_lang_js_path)) {
            $editor_lang = Language::getIsoById($this->default_form_language);
        } else {
            $editor_lang = 'en';
        }
        if ($step == 2) {
            //$id_product = 5;
            if ($id_product > 0 && !KbSellerProduct::isSellerProduct($this->seller_info['id_seller'], $id_product)) {
                $this->context->smarty->assign('permission_error', true);
                $this->Kberrors[] = $this->module->l(
                    'You do not have permission to edit this product.'
                );
            } else {
                if (!empty($id_product)) {
                    $this->kb_product = new Product($id_product, false, $this->default_form_language);
                    $product_form_heading = $this->module->l('Edit')
                        . ': ' . $this->kb_product->name;

                    $this->seller_product = KbSellerProduct::getLoadedObject(
                        $this->seller_info['id_seller'],
                        $id_product
                    );
                } else {
                    $this->kb_product = new Product();
                }

                if ($id_product > 0) {
                    $product_type = (int)$this->kb_product->getType();
                } else {
                    $product_type = (int)Tools::getValue('kb_product_type');
                }

                if ($product_type == Product::PTYPE_SIMPLE) {
                    $this->available_tabs = array_merge($this->available_tabs, array(
                        'Shipping' => 6,
                        'Combinations' => 7
                    ));
                } elseif ($product_type == Product::PTYPE_PACK) {
                    $this->available_tabs = array_merge($this->available_tabs, array(
                        'Shipping' => 6,
                        'Pack' => 7
                    ));
                } elseif ($product_type == Product::PTYPE_VIRTUAL) {
                    $this->available_tabs = array_merge($this->available_tabs, array(
                        'VirtualProduct' => 6
                    ));
                }

                asort($this->available_tabs, SORT_NUMERIC);

                $this->context->smarty->assign('available_tabs', $this->available_tabs);
                $this->context->smarty->assign('available_tabs_lang', $this->available_tabs_lang);
                $this->context->smarty->assign('product_type', $product_type);
                $this->context->smarty->assign('id_product', $id_product);
                $this->context->smarty->assign('editor_lang', $editor_lang);
                $this->context->smarty->assign('default_lang', $this->default_form_language);

                if ($id_product > 0) {
                    $this->context->smarty->assign(
                        'duplicate_link',
                        $this->context->link->getModuleLink(
                            $this->kb_module_name,
                            $this->controller_name,
                            array(
                                'render_type' => 'form',
                                'step' => 2,
                                'id_product' => $id_product,
                                'duplicateProduct' => 1
                            ),
                            (bool)Configuration::get('PS_SSL_ENABLED')
                        )
                    );

                    $del_link = $this->context->link->getModuleLink(
                        $this->kb_module_name,
                        $this->controller_name,
                        array(
                            'render_type' => 'form',
                            'step' => 2,
                            'id_product' => $id_product,
                            'deleteProduct' => 1
                        ),
                        (bool)Configuration::get('PS_SSL_ENABLED')
                    );
                    $delete_link_js = 'if (confirm("' . $this->module->l('Are You Sure?')
                        . '")){document.location.href = "' . Tools::safeOutput($del_link) . '"; return false;}';

                    $this->context->smarty->assign(
                        'delete_link_js',
                        $delete_link_js
                    );
                }

                $this->initForm();
            }
        }

        $this->context->smarty->assign('type_simple', Product::PTYPE_SIMPLE);
        $this->context->smarty->assign('type_virtual', Product::PTYPE_VIRTUAL);
        $this->context->smarty->assign('type_pack', Product::PTYPE_PACK);
        $this->context->smarty->assign('product_form_heading', $product_form_heading);
        $formkey = Tools::encrypt($this->seller_info['id_seller'] . $this->controller_name . 'productform');
        $this->context->smarty->assign('formkey', $formkey);
        $this->context->smarty->assign('id_product', $id_product);
        $this->context->smarty->assign('editor_lang', $editor_lang);
        $this->context->smarty->assign('default_lang', $this->default_form_language);

        $this->context->smarty->assign(
            'form_submit_url',
            $this->context->link->getModuleLink(
                $this->kb_module_name,
                $this->controller_name,
                $url_param,
                (bool)Configuration::get('PS_SSL_ENABLED')
            )
        );

        $this->context->smarty->assign('product_template_dir', $this->getKbTemplateDir() . 'product/');
        $this->context->smarty->assign('step', $step);
        $this->context->smarty->assign('kb_img_frmats', $this->img_formats);
        $this->setKbTemplate('product/form.tpl');
    }

    public function getAjaxProductList()
    {
        $query = Tools::getValue('q', false);
        if (!$query || $query == '' || Tools::strlen($query) < 1) {
            die;
        }

        $excludeIds = Tools::getValue('excludeIds', false);
        if ($excludeIds && $excludeIds != 'NaN') {
            $excludeIds = implode(',', array_map('intval', explode(',', $excludeIds)));
        } else {
            $excludeIds = '';
        }

        $excludeVirtuals = (bool)Tools::getValue('excludeVirtuals', false);
        $exclude_packs = (bool)Tools::getValue('exclude_packs', false);

        $sql = 'SELECT p.`id_product`, pl.`link_rewrite`, p.`reference`, pl.`name`, 
			MAX(image_shop.`id_image`) id_image, il.`legend` FROM `' . _DB_PREFIX_ . 'product` p 
			INNER JOIN ' . _DB_PREFIX_ . 'kb_mp_seller_product as sl on (p.id_product = sl.id_product 
			AND sl.id_seller = ' . (int)$this->seller_obj->id . ') 
			LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (pl.id_product = p.id_product 
			AND pl.id_lang = ' . (int)$this->default_form_language . Shop::addSqlRestrictionOnLang('pl') . ') 
			LEFT JOIN `' . _DB_PREFIX_ . 'image` i ON (i.`id_product` = p.`id_product`) '
            . Shop::addSqlAssociation('image', 'i', false, 'image_shop.cover=1')
            . ' LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il ON (i.`id_image` = il.`id_image` 
			AND il.`id_lang` = ' . (int)$this->default_form_language . ') 
			WHERE (pl.name LIKE \'%' . pSQL($query) . '%\' OR p.reference LIKE \'%' . pSQL($query) . '%\') '
            . (!empty($excludeIds) ? ' AND p.id_product NOT IN (' . pSQL($excludeIds) . ') ' : ' ') .
            ($excludeVirtuals ? 'AND p.id_product NOT IN (SELECT pd.id_product 
				FROM `' . _DB_PREFIX_ . 'product_download` pd WHERE (pd.id_product = p.id_product))' : '') .
            ($exclude_packs ? 'AND (p.cache_is_pack IS NULL OR p.cache_is_pack = 0)' : '') .
            ' GROUP BY p.id_product';

        $items = Db::getInstance()->executeS($sql);

        if ($items) {
            $img_tmp1 = 'home';
            $img_tmp2 = '_default';
            $img_thumb_type = $img_tmp1 . $img_tmp2;

            // packs
            $results = array();
            foreach ($items as $item) {
                if (Combination::isFeatureActive()) {
                    $sql = 'SELECT pa.`id_product_attribute`, pa.`reference`, ag.`id_attribute_group`, 
								pai.`id_image`, agl.`name` AS group_name, al.`name` AS attribute_name, 
								a.`id_attribute` FROM `' . _DB_PREFIX_ . 'product_attribute` pa '
                        . Shop::addSqlAssociation('product_attribute', 'pa') . ' 
								LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_combination` pac 
								ON pac.`id_product_attribute` = pa.`id_product_attribute` 
								LEFT JOIN `' . _DB_PREFIX_ . 'attribute` a ON a.`id_attribute` = pac.`id_attribute` 
								LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group` ag 
								ON ag.`id_attribute_group` = a.`id_attribute_group` 
								LEFT JOIN `' . _DB_PREFIX_ . 'attribute_lang` al 
								ON (a.`id_attribute` = al.`id_attribute` 
								AND al.`id_lang` = ' . (int)$this->default_form_language . ') 
								LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group_lang` agl 
								ON (ag.`id_attribute_group` = agl.`id_attribute_group` 
								AND agl.`id_lang` = ' . (int)$this->default_form_language . ') 
								LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_image` pai 
								ON pai.`id_product_attribute` = pa.`id_product_attribute` 
								WHERE pa.`id_product` = ' . (int)$item['id_product'] . ' 
								GROUP BY pa.`id_product_attribute`, ag.`id_attribute_group` 
								ORDER BY pa.`id_product_attribute`';

                    $combinations = Db::getInstance()->executeS($sql);
                    if (!empty($combinations)) {
                        foreach ($combinations as $combination) {
                            $tmp1 = $combination['id_product_attribute'];
                            $results[$tmp1]['id'] = $item['id_product'];
                            $results[$tmp1]['id_product_attribute'] = $combination['id_product_attribute'];
                            if (!empty($results[$tmp1]['name'])) {
                                $results[$tmp1]['name'] .= ' ' . $combination['group_name']
                                    . '-' . $combination['attribute_name'];
                            } else {
                                $results[$combination['id_product_attribute']]['name'] = $item['name']
                                    . ' ' . $combination['group_name'] . '-' . $combination['attribute_name'];
                            }

                            if (!empty($combination['reference'])) {
                                $results[$tmp1]['ref'] = $combination['reference'];
                            } else {
                                $results[$tmp1]['ref'] = !empty($item['reference']) ? $item['reference'] : '';
                            }
                            if (empty($results[$tmp1]['image'])) {
                                $results[$tmp1]['image'] = str_replace(
                                    'http://',
                                    Tools::getShopProtocol(),
                                    $this->context->link->getImageLink(
                                        $item['link_rewrite'],
                                        $combination['id_image'],
                                        $img_thumb_type
                                    )
                                );
                            }
                        }
                    } else {
                        $product = array(
                            'id' => (int)($item['id_product']),
                            'name' => $item['name'],
                            'ref' => (!empty($item['reference']) ? $item['reference'] : ''),
                            'image' => str_replace(
                                'http://',
                                Tools::getShopProtocol(),
                                $this->context->link->getImageLink(
                                    $item['link_rewrite'],
                                    $item['id_image'],
                                    $img_thumb_type
                                )
                            ),
                        );
                        array_push($results, $product);
                    }
                } else {
                    $product = array(
                        'id' => (int)($item['id_product']),
                        'name' => $item['name'],
                        'ref' => (!empty($item['reference']) ? $item['reference'] : ''),
                        'image' => str_replace(
                            'http://',
                            Tools::getShopProtocol(),
                            $this->context->link->getImageLink(
                                $item['link_rewrite'],
                                $item['id_image'],
                                $img_thumb_type
                            )
                        ),
                    );
                    array_push($results, $product);
                }
            }
            $results = array_values($results);
            echo Tools::jsonEncode($results);
        } else {
            Tools::jsonEncode(new stdClass);
        }
    }

    private function initForm()
    {
        $tabs = array();
        if (count($this->available_tabs) > 0) {
            foreach ($this->available_tabs as $tab => $sort_order) {
                $tmp = $sort_order;
                unset($tmp);
                if (method_exists($this, 'initForm' . $tab)) {
                    $tabs[] = $this->{'initForm' . $tab}();
                }
            }
        }

        $this->context->smarty->assign('tabs_display', $tabs);
    }

    public function initFormInformations()
    {
        $this->context->smarty->assign('form_title', $this->available_tabs_lang['Informations']);

        $properties = array('name', 'description_short', 'description');
        foreach ($properties as $prop) {
            $this->context->smarty->assign(
                $prop,
                $this->getFieldValue($this->kb_product, $prop, $this->default_form_language)
            );
        }

        $properties = array('reference', 'ean13', 'upc', 'active', 'visibility', 'condition',
            'available_for_order', 'show_price', 'online_only', 'id_manufacturer');
        $seller_product = Db::getInstance()->getRow(
            'SELECT * FROM '._DB_PREFIX_.'kb_mp_seller_product_tracking'
            . ' WHERE id_product='. (int) Tools::getValue('id_product')
        );
        foreach ($properties as $prop) {
            $this->context->smarty->assign($prop, $this->getFieldValue($this->kb_product, $prop));
        }

        $short_description_limit = Configuration::get('PS_PRODUCT_SHORT_DESC_LIMIT')
            ? Configuration::get('PS_PRODUCT_SHORT_DESC_LIMIT') : 400;

        $manufacturers = Manufacturer::getManufacturers(false, $this->default_form_language, true);
        $this->context->smarty->assign('manufacturers', $manufacturers);
        $this->context->smarty->assign('seller_product', $seller_product);
        $this->context->smarty->assign('short_description_limit', $short_description_limit);
        $this->context->smarty->assign('tags', $this->kb_product->getTags($this->default_form_language, true));
        return $this->context->smarty->fetch($this->getKbTemplateDir() . 'product/informations.tpl');
    }

    public function initFormPrices()
    {
        $properties = array('wholesale_price', 'price', 'on_sale');

        $this->context->smarty->assign('form_title', $this->available_tabs_lang['Prices']);
        foreach ($properties as $prop) {
            $this->context->smarty->assign($prop, $this->getFieldValue($this->kb_product, $prop));
        }

        if ($this->kb_product->unit_price_ratio != 0) {
            $this->context->smarty->assign(
                'unit_price',
                Tools::ps_round($this->kb_product->price / $this->kb_product->unit_price_ratio, 2)
            );
        } else {
            $this->context->smarty->assign('unit_price', 0);
        }

        $p_actual_price = $this->getFieldValue($this->kb_product, 'price');
        $specific_price = Tools::ps_round(0, 2);
        $specific_price_from = '';
        $specific_price_to = '';

        $specific_prices = SpecificPrice::getByProductId((int)$this->kb_product->id);
        
        foreach ($specific_prices as $specific) {
            $tmp = (float)($p_actual_price - $specific['reduction']);
            $specific_price = Tools::ps_round($tmp, 2);

            if ($specific['from'] != '0000-00-00 00:00:00') {
                $temp = new DateTime($specific['from']);
                $specific_price_from = $temp->format('Y-m-d');
            } else {
                $specific_price_from = '';
            }

            if ($specific['to'] != '0000-00-00 00:00:00') {
                $temp1 = new DateTime($specific['to']);
                $specific_price_to = $temp1->format('Y-m-d');
            } else {
                $specific_price_to = '';
            }
        }

        $this->context->smarty->assign('specific_price', $specific_price);
        $this->context->smarty->assign('specific_price_from', $specific_price_from);
        $this->context->smarty->assign('specific_price_to', $specific_price_to);
        if ($this->seller_currency->prefix != '') {
            $this->context->smarty->assign('currency_prefix', $this->seller_currency->prefix);
        } elseif ($this->context->currency->suffix != '') {
            $this->context->smarty->assign('currency_suffix', $this->seller_currency->suffix);
        }

        return $this->context->smarty->fetch($this->getKbTemplateDir() . 'product/price.tpl');
    }
    
    #BOOKMARK SUPPLIERS
    public function initFormSuppliers()
    {
        $id_product = 0;
        if (Tools::getIsset('id_product')) {
            $id_product = Tools::getValue('id_product');
        }

        $this->context->smarty->assign('form_title', $this->available_tabs_lang['Suppliers']);

        // Get all available suppliers
        $suppliers = Supplier::getSuppliers();

        // Get already associated suppliers
        $associated_suppliers = ProductSupplier::getSupplierCollection($id_product);

        // Get already associated suppliers and force to retreive product declinaisons

        $default_supplier = 0;
        foreach ($suppliers as &$supplier) {
            $supplier['is_selected'] = false;
            $supplier['is_default']  = false;

            foreach ($associated_suppliers as $associated_supplier) {
                /** @var ProductSupplier $associated_supplier */
                if ($associated_supplier->id_supplier == $supplier['id_supplier']) {
                    $associated_supplier->name = $supplier['name'];
                    $supplier['is_selected'] = true;

                    if ($id_product == $supplier['id_supplier']) {
                        $supplier['is_default'] = true;
                        $default_supplier = $supplier['id_supplier'];
                    }
                }
            }
        }
        $obj_product = new Product($id_product);
        $default_supplier = $obj_product->id_supplier;

        $this->context->smarty->assign('suppliers', $suppliers);
        $this->context->smarty->assign('default_supplier', $default_supplier);
        $this->context->smarty->assign('id_product', $id_product);
        return $this->context->smarty->fetch($this->getKbTemplateDir().'product/suppliers.tpl');
    }

    public function initFormSeo()
    {
        $properties = array('meta_title', 'meta_description', 'meta_keywords', 'link_rewrite');

        $this->context->smarty->assign('form_title', $this->available_tabs_lang['Seo']);
        foreach ($properties as $prop) {
            $this->context->smarty->assign(
                $prop,
                $this->getFieldValue($this->kb_product, $prop, $this->default_form_language)
            );
        }

        $this->context->smarty->assign('curent_shop_url', $this->context->shop->getBaseURL());
        return $this->context->smarty->fetch($this->getKbTemplateDir() . 'product/seo.tpl');
    }

    public function initFormImages()
    {
        $this->context->smarty->assign('form_title', $this->available_tabs_lang['Images']);

        $images = Image::getImages($this->default_form_language, $this->kb_product->id);
        foreach ($images as $k => $image) {
            $img_obj = new Image($image['id_image']);
            $img_obj->cover = (int)$img_obj->cover;
            $legend = $img_obj->legend[$this->default_form_language];
            $legend = addcslashes($legend, '\"');
            $img_obj->legend[$this->default_form_language] = $legend;
            $images[$k] = $img_obj;
        }
        $this->context->smarty->assign('id_default_category', $this->kb_product->id_category_default);
        $this->context->smarty->assign('product_name', $this->kb_product->name);
        $this->max_image_size = ((int)Configuration::get('PS_PRODUCT_PICTURE_MAX_SIZE') / 1024 / 1024);
        $this->context->smarty->assign('max_image_size', $this->max_image_size);
        $this->context->smarty->assign('images', $images);
        return $this->context->smarty->fetch($this->getKbTemplateDir() . 'product/image.tpl');
    }

    public function initFormFeatures()
    {
        $this->context->smarty->assign('form_title', $this->available_tabs_lang['Features']);

        $kb_features = array();
        $available_store_features = Feature::getFeatures((int) $this->context->language->id);
        if ($available_store_features) {
            foreach ($available_store_features as $features) {
                $features_value = FeatureValue::getFeatureValuesWithLang(
                    (int) $this->context->language->id,
                    $features['id_feature']
                );
                if (count($features_value)) {
                    $kb_features[$features['id_feature']] = array(
                        'name' => $features['name'],
                        'id_feature' => $features['id_feature']
                    );
                    foreach ($features_value as $feature_value) {
                        $kb_features[$features['id_feature']]['value'][] = array(
                            'id_feature_value' => $feature_value['id_feature_value'],
                            'value' => $feature_value['value'],
                        );
                    }
                }
            }
        }
        
        $product_features = array();
        $get_product_features = $this->kb_product->getFeatures();
        if (count($get_product_features)) {
            foreach ($get_product_features as $single_feature) {
                $product_features[$single_feature['id_feature']] = $single_feature['id_feature_value'];
            }
        }
       
        $this->context->smarty->assign('product_features', $product_features);
        $this->context->smarty->assign('features', $kb_features);
        return $this->context->smarty->fetch($this->getKbTemplateDir() . 'product/features.tpl');
    }

    public function processAddProductImage()
    {
        $product = new Product((int)Tools::getValue('id_product'));
        $legend = Tools::getValue('legend', '');

        if (!Validate::isLoadedObject($product)) {
            $files = array();
            $files[0]['error'] = $this->module->l(
                'Cannot add image because product creation failed.'
            );
        }

        $this->max_image_size = ((int)Configuration::get('PS_PRODUCT_PICTURE_MAX_SIZE') / 1024 / 1024);
        $image_uploader = new HelperImageUploader('file');
        $image_uploader->setAcceptTypes($this->img_formats)->setMaxSize($this->max_image_size);
        $files = $image_uploader->process();

        foreach ($files as &$file) {
            $image = new Image();
            $image->id_product = (int)($product->id);
            $image->position = Image::getHighestPosition($product->id) + 1;
            if (!empty($legend)) {
                $image->legend[(int)$this->seller_info['id_default_lang']] = $legend;
            }

            if (!Image::getCover($image->id_product)) {
                $image->cover = 1;
            } else {
                $image->cover = 0;
            }

            if (($validate = $image->validateFieldsLang(false, true)) !== true) {
                $file['error'] = $validate;
            }

            if (isset($file['error']) && (!is_numeric($file['error']) || $file['error'] != 0)) {
                continue;
            }

            if (!$image->add()) {
                $file['error'] = $this->module->l('Error while creating additional image.');
            } else {
                if (!$new_path = $image->getPathForCreation()) {
                    $file['error'] = $this->module->l(
                        'An error occurred during new folder creation.'
                    );
                    continue;
                }

                $error = 0;

                if (!ImageManager::resize(
                    $file['save_path'],
                    $new_path . '.' . $image->image_format,
                    null,
                    null,
                    'jpg',
                    false,
                    $error
                )) {
                    switch ($error) {
                        case ImageManager::ERROR_FILE_NOT_EXIST:
                            $file['error'] = $this->module->l('An error occurred while copying image, 
								file does not exist anymore.');
                            break;
                        case ImageManager::ERROR_FILE_WIDTH:
                            $file['error'] = $this->module->l('An error occurred while copying image, 
								file width is 0px.');
                            break;
                        case ImageManager::ERROR_MEMORY_LIMIT:
                            $file['error'] = $this->module->l('An error occurred while copying image, 
								check your memory limit.');
                            break;
                        default:
                            $file['error'] = $this->module->l(
                                'An error occurred while copying image.'
                            );
                            break;
                    }
                    continue;
                } else {
                    $imagesTypes = ImageType::getImagesTypes('products');
                    foreach ($imagesTypes as $imageType) {
                        if (!ImageManager::resize(
                            $file['save_path'],
                            $new_path . '-' . Tools::stripslashes($imageType['name']) . '.' . $image->image_format,
                            $imageType['width'],
                            $imageType['height'],
                            $image->image_format
                        )
                        ) {
                            $file['error'] = sprintf(
                                $this->module->l('An error occurred while copying image: %s'),
                                Tools::stripslashes($imageType['name'])
                            );
                            continue;
                        }
                    }
                }

                unlink($file['save_path']);

                //Necesary to prevent hacking
                unset($file['save_path']);
                Hook::exec('actionWatermark', array('id_image' => $image->id, 'id_product' => $product->id));

                if (!$image->update()) {
                    $file['error'] = $this->module->l('Error while updating status.');
                    continue;
                }

                // Associate image to shop from context
                $shops = array($this->seller_info['id_shop']);
                $image->associateTo($shops);
                $json_shops = array();

                foreach ($shops as $id_shop) {
                    $json_shops[$id_shop] = true;
                }

                $file['status'] = $this->module->l('Image successfully uploaded.');
                $file['id'] = $image->id;
                $file['position'] = $image->position;
                $file['cover'] = (int)$image->cover;
                $file['legend'] = $image->legend[(int)$this->seller_info['id_default_lang']];
                $file['path'] = $image->getExistingImgPath();
                $file['shops'] = $json_shops;

                @unlink(_PS_TMP_IMG_DIR_ . 'product_' . (int)$product->id . '.jpg');
                @unlink(
                    _PS_TMP_IMG_DIR_ . 'product_mini_' . (int)$product->id
                    . '_' . $this->seller_info['id_shop'] . '.jpg'
                );
            }
        }

        return array($image_uploader->getName() => $files);
    }

    public function processDeleteImage()
    {
        $res = true;

        /* Delete product image */
        $image = new Image((int)Tools::getValue('id_image'));
        $this->content['id'] = $image->id;
        $res &= $image->delete();

        // if deleted image was the cover, change it to the first one
        if (!Image::getCover($image->id_product)) {
            $res &= Db::getInstance()->execute(
                'UPDATE `' . _DB_PREFIX_ . 'image_shop` image_shop, ' . _DB_PREFIX_ . 'image i 
                SET image_shop.`cover` = 1, 
                i.cover = 1 
                WHERE image_shop.`id_image` = (SELECT id_image FROM 
                (SELECT image_shop.id_image 
                FROM ' . _DB_PREFIX_ . 'image i ' .
                Shop::addSqlAssociation('image', 'i') . ' 
                WHERE i.id_product =' . (int)$image->id_product . ' LIMIT 1
                ) tmpImage) 
                AND id_shop=' . (int)$this->seller_info['id_shop'] . ' 
                AND i.id_image = image_shop.id_image
                '
            );
        }

        if (file_exists(_PS_TMP_IMG_DIR_ . 'product_' . $image->id_product . '.jpg')) {
            $res &= @unlink(_PS_TMP_IMG_DIR_ . 'product_' . $image->id_product . '.jpg');
        }
        if (file_exists(
            _PS_TMP_IMG_DIR_ . 'product_mini_' . $image->id_product
            . '_' . $this->seller_info['id_shop'] . '.jpg'
        )
        ) {
            $res &= @unlink(
                _PS_TMP_IMG_DIR_ . 'product_mini_' . $image->id_product
                . '_' . $this->seller_info['id_shop'] . '.jpg'
            );
        }

        if ($res) {
            return array(
                'status' => true,
                'msg' => $this->module->l('Image successfully deleted.')
            );
        } else {
            return array(
                'status' => true,
                'msg' => $this->module->l('Error occurred while deleting image.')
            );
        }
    }

    public function initFormQuantities()
    {
        $properties = array('minimal_quantity', 'available_now', 'available_later', 'available_date');

        $this->context->smarty->assign('form_title', $this->available_tabs_lang['Quantities']);
        foreach ($properties as $prop) {
            $this->context->smarty->assign(
                $prop,
                $this->getFieldValue($this->kb_product, $prop, $this->default_form_language)
            );
        }

        $this->context->smarty->assign(
            $prop,
            $this->getFieldValue($this->kb_product, 'minimal_quantity', $this->default_form_language, 1)
        );
        $this->context->smarty->assign(
            $prop,
            $this->getFieldValue($this->kb_product, 'available_date', $this->default_form_language, 1)
        );

        $this->context->smarty->assign('has_attributes', $this->kb_product->hasAttributes());
        $this->context->smarty->assign(
            'qty',
            StockAvailable::getQuantityAvailableByProduct((int)$this->kb_product->id, 0)
        );
        if ($this->kb_product->id == null) {
            $out_of_stock = 2;
        } else {
            $out_of_stock = StockAvailable::outOfStock((int) $this->kb_product->id);
        }

        $this->context->smarty->assign('out_of_stock', $out_of_stock);
        $this->context->smarty->assign('order_out_of_stock', Configuration::get('PS_ORDER_OUT_OF_STOCK'));
        return $this->context->smarty->fetch($this->getKbTemplateDir() . 'product/qty.tpl');
    }

    public function initFormCategories()
    {
        $this->context->smarty->assign('form_title', $this->available_tabs_lang['Categories']);
        
        $all_categories = $this->getCategoryList();
        if (!empty($all_categories)) {
            $this->context->smarty->assign('categories', $all_categories);
        }

        $selected_cat = Product::getProductCategories($this->kb_product->id);
        $this->context->smarty->assign('selected_categories', $selected_cat);
        $this->context->smarty->assign('default_category', $this->kb_product->id_category_default);

        return $this->context->smarty->fetch($this->getKbTemplateDir() . 'product/category.tpl');
    }

    public function ajaxGetCategoryTree()
    {
        $temp = $this->getCategoryList($this->seller_obj->id, true);
        $unassigned_categories = array();
        foreach ($temp as $val) {
            $unassigned_categories[] = $val['id_category'];
        }

        $temp = $this->getCategoryList($this->seller_obj->id);
        $assigned_categories = array();
        foreach ($temp as $val) {
            $assigned_categories[] = $val['id_category'];
        }
        $selected_cat = Product::getProductCategories(Tools::getValue('id_product', 0));
        
        $root = Category::getRootCategory();

        $tree = new CategoryTree('seller-categories-tree');
        $tree->lang = $this->default_form_language;

        $tree->setRootCategory($root->id)
            ->setTitle(false)
            ->setTemplateDirectory($this->getKbTemplateDir() . 'product/category_tree/')
            ->setUseCheckBox(true)
            ->setUseSearch(false)
            ->setDisabledCategories($unassigned_categories)
            ->setEnabledCategories($assigned_categories)
            ->setSelectedCategories($selected_cat);
        
        return $tree->render();
    }

    public function ajaxGetSubCategoryTree()
    {
        $category = Tools::getValue('category', Category::getRootCategory()->id);
        $unassigned_categories = $this->getCategoryList($this->seller_obj->id);

        $full_tree = Tools::getValue('fullTree', 0);

        $use_check_box = Tools::getValue('useCheckBox', 1);
        $selected = Tools::getValue('selected', array());
        $id_tree = Tools::getValue('type');
        $input_name = str_replace(array('[', ']'), '', Tools::getValue('inputName', null));
        
        $tree = new CategoryTree('subtree_associated_categories');
        $tree->lang = $this->default_form_language;
        $tree->setTemplate('subtree_associated_categories.tpl');
        $tree->setRootCategory($category);
        $tree->setTitle(false);
        $tree->setTemplateDirectory($this->getKbTemplateDir() . 'product/category_tree/');
        $tree->setUseCheckBox($use_check_box);
        $tree->setUseSearch(false);
        $tree->setIdTree($id_tree);
        $tree->setFullTree($full_tree);
        $tree->setChildrenOnly(true);
        $tree->setNoJS(true);
        $tree->setDisabledCategories($unassigned_categories);
        $tree->setSelectedCategories($selected);

        if ($input_name) {
            $tree->setInputName($input_name);
        }

        return $tree->render();
    }

    public function initFormShipping()
    {
        $properties = array('width', 'height', 'depth', 'weight', 'additional_shipping_cost');

        $this->context->smarty->assign('form_title', $this->available_tabs_lang['Shipping']);
        foreach ($properties as $prop) {
            $this->context->smarty->assign($prop, $this->getFieldValue($this->kb_product, $prop));
        }
        
        $carrier_list = KbSellerShipping::getShippingForProducts(
            $this->default_form_language,
            $this->seller_obj->id,
            false,
            false,
            false,
            false,
            Carrier::ALL_CARRIERS
        );
        $carrier_selected_list = $this->kb_product->getCarriers();
        foreach ($carrier_list as &$carrier) {
            foreach ($carrier_selected_list as $carrier_selected) {
                if ($carrier_selected['id_reference'] == $carrier['id_reference']) {
                    $carrier['selected'] = true;
                    continue;
                }
            }
        }
        
        $this->context->smarty->assign('carrier_list', $carrier_list);
        $this->context->smarty->assign('product_has_shipping', !empty($carrier_selected_list));
        $this->context->smarty->assign('ps_dimension_unit', Configuration::get('PS_DIMENSION_UNIT'));
        $this->context->smarty->assign('ps_weight_unit', Configuration::get('PS_WEIGHT_UNIT'));
        return $this->context->smarty->fetch($this->getKbTemplateDir() . 'product/shipping.tpl');
    }

    public function initFormCombinations()
    {
        if (!Combination::isFeatureActive()) {
            return;
        }

        $this->context->smarty->assign('form_title', $this->available_tabs_lang['Combinations']);
        $attributes = $this->kb_product->getAttributesResume($this->default_form_language);
        if ($attributes && count($attributes) > 0) {
            foreach ($attributes as &$attribute) {
                $attribute['attribute_designation'] = addcslashes($attribute['attribute_designation'], '\"');
                $attribute['stock_available'] = StockAvailable::getQuantityAvailableByProduct(
                    (int)$this->kb_product->id,
                    $attribute['id_product_attribute']
                );
            }
        } else {
            $attributes = array();
        }

        $this->context->smarty->assign('attributes', $attributes);

        $attribute_js = array();
        $system_attributes = Attribute::getAttributes($this->default_form_language, true);
        foreach ($system_attributes as $k => $attr) {
            $attribute_js[$attr['id_attribute_group']][$attr['id_attribute']] = $attr['name'];
        }

        $this->context->smarty->assign('attributeJs', $attribute_js);
        $this->context->smarty->assign(
            'attributes_groups',
            AttributeGroup::getAttributesGroups($this->default_form_language)
        );

        $images = Image::getImages($this->default_form_language, $this->kb_product->id);
        $i = 0;
        $img_tmp1 = 'small';
        $img_tmp2 = '_default';
        $type = ImageType::getByNameNType($img_tmp1 . $img_tmp2, 'products');
        $img_thumb_type = '';
        if (isset($type['name'])) {
            $img_thumb_type = $type['name'];
        }
        $this->context->smarty->assign('imageType', $img_thumb_type);
        $this->context->smarty->assign('imageWidth', 64 + 25);
        foreach ($images as $k => $image) {
            $images[$k]['obj'] = new Image($image['id_image']);
            ++$i;
        }
        $this->context->smarty->assign('images', $images);

        return $this->context->smarty->fetch($this->getKbTemplateDir() . 'product/combination.tpl');
    }

    public function processGetCombination($id_product, $id_product_attribute)
    {
        $product = new Product($id_product);

        $attributes = $product->getAttributeCombinationsById($id_product_attribute, $this->default_form_language);

        foreach ($attributes as &$attr) {
            $attr['stock_available'] = StockAvailable::getQuantityAvailableByProduct(
                (int)$id_product,
                $id_product_attribute
            );
            $attr['id_img_attr'] = Product::_getAttributeImageAssociations($id_product_attribute);
        }
        return $attributes;
    }

    public function processSaveCombination()
    {
        $id_product = (int)Tools::getValue('id_product');
        $product = new Product($id_product);
        $errors = array();
        $attribute_combination_list = explode(',', Tools::getValue('attribute_combination_list'));
        if (count($attribute_combination_list) == 0) {
            $errors[] = $this->module->l('Atleast one attribute required.');
        }

        $id_product_attribute = $product->productAttributeExists(
            $attribute_combination_list,
            false,
            null,
            true,
            true
        );
        if ($id_product_attribute) {
            if ($id_product_attribute != (int)Tools::getValue('id_product_attribute', 0)) {
                $errors[] = $this->module->l('This combination already exists.');
            }
        }

        $array_checks = array(
            'reference' => 'isReference',
            'ean13' => 'isEan13',
            'upc' => 'isUpc',
            'stock_available' => 'isInt',
            'available_date' => 'isDateFormat'
        );
        foreach ($array_checks as $property => $check) {
            if (Tools::getValue('attribute_' . $property) !== false
                && !call_user_func(array('Validate', $check), Tools::getValue('attribute_' . $property))) {
                $errors[] = sprintf($this->module->l('Field %s is not valid'), $property);
            }
        }

        if (!count($errors)) {
            $msg = '';
            if (($id_product_attribute = (int)Tools::getValue('id_product_attribute'))
                || ($id_product_attribute = $product->productAttributeExists(
                    $attribute_combination_list,
                    false,
                    null,
                    true,
                    true
                )
            )
            ) {
                if (Tools::getValue('attribute_default')) {
                    $product->deleteDefaultAttributes();
                }
                $product->updateAttribute(
                    (int)$id_product_attribute,
                    0,
                    0,
                    0,
                    0,
                    0,
                    Tools::getValue('id_image_attr'),
                    Tools::getValue('attribute_reference'),
                    Tools::getValue('attribute_ean13'),
                    Tools::getIsset('attribute_default') ? Tools::getValue('attribute_default') : null,
                    null,
                    Tools::getValue('attribute_upc'),
                    1,
                    Tools::getIsset('available_date_attribute') ? Tools::getValue('available_date_attribute') : null,
                    false
                );

                StockAvailable::setProductDependsOnStock(
                    (int)$product->id,
                    $product->depends_on_stock,
                    null,
                    (int)$id_product_attribute
                );
                StockAvailable::setProductOutOfStock(
                    (int)$product->id,
                    $product->out_of_stock,
                    null,
                    (int)$id_product_attribute
                );
                $msg = $this->module->l('Combination successfully updated.');
            } else {
                if (Tools::getValue('attribute_default')) {
                    $product->deleteDefaultAttributes();
                }
                $id_product_attribute = $product->addCombinationEntity(
                    Tools::getValue('attribute_wholesale_price'),
                    0,
                    0,
                    0,
                    0,
                    0,
                    Tools::getValue('id_image_attr'),
                    Tools::getValue('attribute_reference'),
                    null,
                    Tools::getValue('attribute_ean13'),
                    Tools::getValue('attribute_default'),
                    null,
                    Tools::getValue('attribute_upc'),
                    1,
                    array(),
                    Tools::getValue('available_date_attribute')
                );
                StockAvailable::setProductDependsOnStock(
                    (int)$product->id,
                    $product->depends_on_stock,
                    null,
                    (int)$id_product_attribute
                );
                StockAvailable::setProductOutOfStock(
                    (int)$product->id,
                    $product->out_of_stock,
                    null,
                    (int)$id_product_attribute
                );
                $msg = $this->module->l('Combination successfully created.');
            }

            StockAvailable::setQuantity(
                $product->id,
                (int)$id_product_attribute,
                (int)Tools::getValue('attribute_stock_available'),
                (int)$this->seller_info['id_shop']
            );

            $combination = new Combination((int)$id_product_attribute);
            $combination->setAttributes($attribute_combination_list);

            // images could be deleted before
            $id_images = Tools::getValue('id_image_attr');
            if (!empty($id_images)) {
                $combination->setImages($id_images);
            }

            $product->checkDefaultAttributes();
            if (Tools::getValue('attribute_default')) {
                Product::updateDefaultAttribute((int)$product->id);
                if (isset($id_product_attribute)) {
                    $product->cache_default_attribute = (int)$id_product_attribute;
                }
                if ($available_date = Tools::getValue('available_date_attribute')) {
                    $product->setAvailableDate($available_date);
                }
            }

            $json = array();
            $json['status'] = true;

            $attributes = $product->getAttributesResume($this->default_form_language);
            $json['data'] = array();
            if (count($attributes) > 0) {
                foreach ($attributes as &$attribute) {
                    if ($attribute['id_product_attribute'] == $id_product_attribute) {
                        $attribute['stock_available'] = StockAvailable::getQuantityAvailableByProduct(
                            $id_product,
                            $id_product_attribute
                        );
                        $json['data'] = $attribute;
                        break;
                    }
                }
            }

            $json['msg'] = $msg;
        } else {
            $json['status'] = false;
            $json['errors'] = $errors;
        }

        return $json;
    }

    public function processDeleteCombination()
    {
        if (!Combination::isFeatureActive()) {
            return array(
                'status' => 'error',
                'message' => $this->module->l('This feature has been disabled.')
            );
        }

        $id_product = (int)Tools::getValue('id_product');
        $id_product_attribute = (int)Tools::getValue('id_product_attribute');
        if ($id_product && Validate::isUnsignedId($id_product)
            && Validate::isLoadedObject($product = new Product($id_product))) {
            $product->deleteAttributeCombination((int)$id_product_attribute);
            $product->checkDefaultAttributes();
            if (!$product->hasAttributes()) {
                $product->cache_default_attribute = 0;
                $product->update();
            } else {
                Product::updateDefaultAttribute($id_product);
            }

            $json = array(
                'status' => 'ok',
                'message' => $this->module->l('Combination successfully deleted.')
            );
        } else {
            $json = array(
                'status' => 'error',
                'message' => $this->module->l('You cannot delete this attribute.')
            );
        }

        return $json;
    }

    public function initFormVirtualProduct()
    {
        $this->context->smarty->assign('form_title', $this->available_tabs_lang['VirtualProduct']);
        
        $sql = 'SELECT `id_product_download`
            FROM `'._DB_PREFIX_.'product_download`
            WHERE `id_product` = '.(int) $this->kb_product->id
            .' AND `active` = 1 ORDER BY `id_product_download` DESC';
        $id_product_download = (int)Db::getInstance()->getValue($sql);

        $product_download = new ProductDownload($id_product_download);

        $this->kb_product->{'productDownload'} = $product_download;

        if ($this->kb_product->productDownload->id && empty($this->kb_product->productDownload->display_filename)) {
            $this->errors[] = Tools::displayError('A file name is required in order to associate a file.');
            $this->tab_display = 'VirtualProduct';
        }

        // @todo handle is_virtual with the value of the product
        $exists_file = realpath(_PS_DOWNLOAD_DIR_) . '/' . $this->kb_product->productDownload->filename;

        $this->context->smarty->assign('product_downloaded', $this->kb_product->productDownload->id);

        if (!Tools::file_exists_no_cache($exists_file)
            && !empty($this->kb_product->productDownload->display_filename)
            && empty($this->kb_product->cache_default_attribute)) {
            $msg = sprintf(
                Tools::displayError('File "%s" is missing'),
                $this->kb_product->productDownload->display_filename
            );
        } else {
            $msg = '';
        }

        $this->context->smarty->assign(array(
            'download_product_file_missing' => $msg,
            'download_dir_writable' => ProductDownload::checkWritableDir(),
            'up_filename' => (string)Tools::getValue('virtual_product_filename')
        ));

        $this->kb_product->productDownload->nb_downloadable = ($this->kb_product->productDownload->id > 0)
            ? $this->kb_product->productDownload->nb_downloadable
            : htmlentities(Tools::getValue('virtual_product_nb_downloable'), ENT_COMPAT, 'UTF-8');

        $this->kb_product->productDownload->date_expiration = ($this->kb_product->productDownload->id > 0)
            ? ((!empty($this->kb_product->productDownload->date_expiration)
            && $this->kb_product->productDownload->date_expiration != '0000-00-00 00:00:00')
                ? date('Y-m-d', strtotime($this->kb_product->productDownload->date_expiration)) : '' )
            : htmlentities(Tools::getValue('virtual_product_expiration_date'), ENT_COMPAT, 'UTF-8');

        $this->kb_product->productDownload->nb_days_accessible = ($this->kb_product->productDownload->id > 0)
            ? $this->kb_product->productDownload->nb_days_accessible
            : htmlentities(Tools::getValue('virtual_product_nb_days'), ENT_COMPAT, 'UTF-8');

        $this->kb_product->productDownload->is_shareable = $this->kb_product->productDownload->id > 0
            && $this->kb_product->productDownload->is_shareable;

        $this->context->smarty->assign(array(
            'uploadable_files' => $this->kb_product->uploadable_files,
            'text_fields' => $this->kb_product->text_fields,
            'virtual_product_filename' => $this->kb_product->productDownload->filename,
            'is_virtual' => $this->kb_product->is_virtual,
            'download_active' => $this->kb_product->productDownload->active,
            'download_id' => $this->kb_product->productDownload->id,
            'cache_default_attribute' => $this->kb_product->cache_default_attribute,
            'display_filename' => $this->kb_product->productDownload->display_filename,
            'is_file' => $this->kb_product->productDownload->checkFile(),
            'text_link' => $this->kb_product->productDownload->getTextLink(
                false,
                $this->kb_product->productDownload->getHash()
            ),
            'nb_downloadable' => $this->kb_product->productDownload->nb_downloadable,
            'date_expiration' => $this->kb_product->productDownload->date_expiration,
            'nb_days_accessible' => $this->kb_product->productDownload->nb_days_accessible
        ));

        return $this->context->smarty->fetch($this->getKbTemplateDir() . 'product/virtual.tpl');
    }

    public function initFormPack()
    {
        $this->context->smarty->assign('form_title', $this->available_tabs_lang['Pack']);

        // If pack items have been submitted, we want to display them instead of the actuel content of the pack
        // in database. In case of a submit error, the posted data is not lost and can be sent again.
        if (Tools::getValue('namePackItems')) {
            $input_pack_items = Tools::getValue('inputPackItems');
            $input_namepack_items = Tools::getValue('namePackItems');
            $pack_items = $this->getPackItems();
        } else {
            $this->kb_product->packItems = Pack::getItems($this->kb_product->id, $this->default_form_language);
            $pack_items = $this->getPackItems($this->kb_product);
            $input_namepack_items = '';
            $input_pack_items = '';
            foreach ($pack_items as $pack_item) {
                $input_pack_items .= $pack_item['pack_quantity']
                    . 'x' . $pack_item['id'] . 'x' . $pack_item['id_product_attribute'] . '-';
                $input_namepack_items .= $pack_item['pack_quantity'] . ' x ' . $pack_item['name'] . '??';
            }
        }

        $this->context->smarty->assign(array(
            'input_pack_items' => $input_pack_items,
            'input_namepack_items' => $input_namepack_items,
            'pack_items' => $pack_items
        ));

        $this->context->smarty->assign(
            'search_product_url',
            $this->context->link->getModuleLink(
                $this->kb_module_name,
                $this->controller_name,
                array('method' => 'searchedproduct', 'ajax' => true),
                (bool)Configuration::get('PS_SSL_ENABLED')
            )
        );
        return $this->context->smarty->fetch($this->getKbTemplateDir() . 'product/pack.tpl');
    }

    /**
     * Get an array of pack items for display from the product object if specified, else from POST/GET values
     *
     * @param Product $product
     * @return array of pack items
     */
    public function getPackItems($product = null)
    {
        $pack_items = array();

        if (!$product) {
            $names_input = Tools::getValue('namePackItems');
            $ids_input = Tools::getValue('inputPackItems');
            if (!$names_input || !$ids_input) {
                return array();
            }

            // ids is an array of string with format : QTYxID
            $ids = array_unique(explode('-', $ids_input));
            $names = array_unique(explode('??', $names_input));

            if (!empty($ids)) {
                $length = count($ids);
                for ($i = 0; $i < $length; $i++) {
                    if (!empty($ids[$i]) && !empty($names[$i])) {
                        list($pack_items[$i]['pack_quantity'], $pack_items[$i]['id']) = explode('x', $ids[$i]);
                        $exploded_name = explode('x', $names[$i]);
                        $pack_items[$i]['name'] = $exploded_name[1];
                    }
                }
            }
        } else {
            $i = 0;
            foreach ($this->kb_product->packItems as $pack_item) {
                $pack_items[$i]['id'] = $pack_item->id;
                $pack_items[$i]['pack_quantity'] = $pack_item->pack_quantity;
                $pack_items[$i]['name'] = $pack_item->name;
                $pack_items[$i]['reference'] = $pack_item->reference;
                $pack_items[$i]['id_product_attribute'] = isset($pack_item->id_pack_product_attribute)
                    && $pack_item->id_pack_product_attribute ? $pack_item->id_pack_product_attribute : 0;
                $cover = $pack_item->id_pack_product_attribute
                    ? Product::getCombinationImageById(
                        $pack_item->id_pack_product_attribute,
                        $this->default_form_language
                    ) : Product::getCover($pack_item->id);
                if (empty($cover)) {
                    $cover = Product::getCover($pack_item->id);
                }
                $img_tmp1 = 'home';
                $img_tmp2 = '_default';
                $img_thumb_type = $img_tmp1 . $img_tmp2;
                $pack_items[$i]['image'] = Context::getContext()->link->getImageLink(
                    $pack_item->link_rewrite,
                    $cover['id_image'],
                    $img_thumb_type
                );
                $i++;
            }
        }
        return $pack_items;
    }

    public function processAdd()
    {
        $object = new Product();
        $this->copyFromPost($object);

        $before_status = $object->active;
//        print_r($this->seller_info);die;
        if (!$this->seller_obj->isApprovedSeller() || $this->seller_obj->active == 0) {
            $object->active = 0;
        }

        if ($this->seller_info['settings']['kbmp_new_product_approval_required'] != 0) {
            $object->active = 0;
        }
//        else {
//            $object->active = 1;
//        }

        $after_status = $object->active;

        if ($object->add()) {
            if ($before_status != $after_status) {
                Db::getInstance(_PS_USE_SQL_SLAVE_)->insert(
                    'kb_mp_seller_product_tracking',
                    array(
                        'id_seller' => (int) $this->seller_info['id_seller'],
                        'id_product' => (int) $object->id,
                        'date_add' => pSQL(date('Y-m-d H:i:s'))
                    )
                );
            }
            $seller_product = new KbSellerProduct();
            $seller_product->id_seller = $this->seller_info['id_seller'];
            $seller_product->id_shop = $this->seller_info['id_shop'];
            $seller_product->id_product = $object->id;
            $seller_product->deleted = 0;
            if ($this->seller_info['settings']['kbmp_new_product_approval_required'] == 0) {
                $seller_product->approved = (string)KbGlobal::APPROVED;
            } else {
                $seller_product->approved = (string)KbGlobal::APPROVAL_WAITING;
            }

            $seller_product->save();

            $required_approval = $this->sendNotificationForProductApproval($object);

            StockAvailable::setQuantity(
                $object->id,
                0,
                (int)Tools::getValue('qty_0'),
                (int)$this->seller_info['id_shop']
            );
            StockAvailable::setProductOutOfStock(
                (int)$object->id,
                $object->out_of_stock,
                $this->seller_info['id_shop']
            );
            if (!$this->seller_obj->isApprovedSeller() || $this->seller_obj->active == 0) {
                $tmp = (int)$this->seller_obj->product_limit_wout_approval;
                $this->seller_obj->product_limit_wout_approval = $tmp + 1;
                $this->seller_obj->save(true);
            }
            if (Tools::getIsset('type_product') && Tools::getValue('type_product') == Product::PTYPE_PACK) {
                $this->updatePackItems($object);
            }

            if (Tools::getIsset('type_product') && Tools::getValue('type_product') == Product::PTYPE_VIRTUAL) {
                $object->is_virtual = 1;
                $object->save();
                $this->updateDownloadProduct($object);
            }

            if (Configuration::get('PS_FORCE_ASM_NEW_PRODUCT') && Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')) {
                $object->advanced_stock_management = 1;
                $object->save();
                StockAvailable::setProductDependsOnStock(
                    $object->id,
                    true,
                    (int)$this->seller_info['id_shop'],
                    0
                );
            }

            if (empty($this->Kberrors)) {
                //Set Specific Prices
                $this->setSpecificPrice($object->id);

                $languages = Language::getLanguages(false);
                $categories = Tools::getValue('categoryBox');
                if (!empty($categories) && !$object->updateCategories($categories)) {
                    $this->Kberrors[] = $this->module->l('An error occurred while linking the 
						product with categories.');
                }

                if (Tools::getIsset('shipping_tab')) {
                    $this->updateShipping($object);
                }

                Hook::exec(
                    'actionKbSellerProductAdd',
                    array('product' => $object, 'id_seller' => $this->seller_info['id_seller'])
                );
                
                if (!$this->updateTags($languages, $object)) {
                    $this->Kberrors[] = $this->module->l('An error occurred while adding tags.');
                } else {
                    Hook::exec('actionProductAdd', array('product' => $object));
                    if (in_array($object->visibility, array('both', 'search'))
                        && Configuration::get('PS_SEARCH_INDEXATION')) {
                        Search::indexation(false, $object->id);
                    }
                }

                if (Configuration::get('PS_DEFAULT_WAREHOUSE_NEW_PRODUCT') != 0
                    && Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')) {
                    $warehouse_location_entity = new WarehouseProductLocation();
                    $warehouse_location_entity->id_product = $object->id;
                    $warehouse_location_entity->id_product_attribute = 0;
                    $warehouse_location_entity->id_warehouse = Configuration::get('PS_DEFAULT_WAREHOUSE_NEW_PRODUCT');
                    $warehouse_location_entity->location = pSQL('');
                    $warehouse_location_entity->save();
                }

                if (empty($this->Kberrors)) {
                    if ($required_approval == KbGlobal::APPROVED) {
                        $this->Kbconfirmation[] = $this->module->l('New product has been created.');
                    } else {
                        $this->Kbconfirmation[] = $this->module->l('New product successfully created and 
							gone for admin approval.');
                    }
                } else {
                    if ($required_approval == KbGlobal::APPROVED) {
                        array_unshift(
                            $this->Kberrors,
                            $this->module->l('Product has been created, but some of 
							the parameters are not saved.')
                        );
                    } else {
                        array_unshift(
                            $this->Kberrors,
                            $this->module->l('Product has been created and gone for admin 
							approval, but some of the parameters are not saved.')
                        );
                    }
                }
            } else {
                $object->delete();
                $this->Kberrors = array();
                $this->Kberrors[] = $this->module->l(
                    'An error occured while creating new product.'
                );
            }
        } else {
            $this->Kberrors[] = $this->module->l('An error occured while creating new product.');
        }

        if (count($this->Kberrors) > 0) {
            $this->context->cookie->__set('redirect_error', implode('####', $this->Kberrors));
        } else {
            $this->context->cookie->__set('redirect_success', implode('####', $this->Kbconfirmation));
        }

        $request_param = array();
        if (Tools::isSubmit('submitType') && Tools::getValue('submitType') == 'savenstay'
            && isset($object->id) && $object->id > 0) {
            $request_param['id_product'] = (int)$object->id;
            $request_param['render_type'] = 'form';
        }
        Tools::redirect($this->context->link->getModuleLink(
            $this->kb_module_name,
            'product',
            $request_param,
            (bool)Configuration::get('PS_SSL_ENABLED')
        ));
    }

    public function processUpdate()
    {
//        print_r($this->seller_obj->isApprovedSeller());die;
        $id = (int)Tools::getValue('id_product');
        /* Update an existing product */
        if (isset($id) && !empty($id)) {
            $object = new Product((int)$id);
            $id_tax_rules_group = $object->id_tax_rules_group;
            if (Validate::isLoadedObject($object)) {
                $this->copyFromPost($object);
                $object->indexed = 0;
//                 print_r($object);die;
                
                if (!$this->seller_obj->isApprovedSeller() || $this->seller_obj->active == 0) {
                    $object->active = 0;
                } else {
                    if (!KbSellerProduct::isApprovedProduct($this->seller_obj->id, $object->id)) {
                        $object->active = 0;
                    } else {
                        $object->active = 1;
                        if (Tools::getIsset('active')) {
                            if (Tools::getValue('active')) {
                                $object->active = 1;
                            } else {
                                $object->active = 0;
                            }
                        }
                    }
                }
//                print_r(Tosols::getAllValues());die;
                $object->id_tax_rules_group = $id_tax_rules_group;
//                
//                print_r($object);die;
                if ($object->update()) {
                    $languages = Language::getLanguages(false);
                    StockAvailable::setQuantity($object->id, 0, (int)Tools::getValue('qty_0'));

                    StockAvailable::setProductOutOfStock(
                        (int)$object->id,
                        $object->out_of_stock,
                        $this->seller_info['id_shop']
                    );
                    StockAvailable::setProductDependsOnStock(
                        (int)$object->id,
                        $object->depends_on_stock,
                        $this->seller_info['id_shop']
                    );

                    if ($object->getType() == Product::PTYPE_PACK) {
                        $this->updatePackItems($object);
                    }

                    if ($object->getType() == Product::PTYPE_VIRTUAL) {
                        $this->updateDownloadProduct($object, 1);
                    }

                    $this->updateTags($languages, $object);

                    $this->updateImages($languages);

                    if (Tools::getIsset('shipping_tab')) {
                        $this->updateShipping($object);
                    }

                    $categories = Tools::getValue('categoryBox');
                    if (!empty($categories) && !$object->updateCategories($categories)) {
                        $this->Kberrors[] = $this->module->l('An error occurred while linking the 
                            product with categories.');
                    }
                    
                    // START - Code for suppliers custom change
                    // Saving the supplier value
                    if (Tools::getIsset('id_suppliers')) {
                        $id_suppliers = Tools::getValue("id_suppliers");
                        $this->updateProductSuppliers($id, $id_suppliers);
                    } else {
//                        $id_suppliers = Tools::getValue("id_suppliers");
//                        $this->updateProductSuppliers($id, '');
                    }

                    // Update default supplier value
                    if (Tools::getIsset('default_supplier')) {
                        $default_supplier    = Tools::getValue('default_supplier');
                        $object->id_supplier = (int) $default_supplier;
                        $object->update();
                    }
                    // END - Code for suppliers custom change

                    $this->processProductFeatures($object->id);

                    if (empty($this->Kberrors)) {
                        //Set Specific Prices
                        $this->setSpecificPrice($object->id);

                        if (in_array($object->visibility, array('both', 'search'))
                            && Configuration::get('PS_SEARCH_INDEXATION')) {
                            Search::indexation(false, $object->id);
                        }
                        if (Tools::getIsset('duplicateProduct') && Tools::getValue('duplicateProduct')) {
                            $this->context->cookie->__set(
                                'redirect_success',
                                $this->module->l('Product is duplicated successfully. 
                                Please change the SKU of Product ID #'. Tools::getValue('id_product') .' .')
                            );
                        } else {
                            $this->context->cookie->__set(
                                'redirect_success',
                                $this->module->l('Product has been updated successfully.')
                            );
                        }
                        if (Tools::isSubmit('submitType') && Tools::getValue('submitType') == 'savenstay') {
                            $_POST['id_product'] = (int)$id;
                        } else {
                            Tools::redirect($this->context->link->getModuleLink(
                                $this->kb_module_name,
                                'product',
                                array(),
                                (bool)Configuration::get('PS_SSL_ENABLED')
                            ));
                        }
                    }

                    Hook::exec(
                        'actionKbSellerProductUpdate',
                        array('product' => $object, 'id_seller' => $this->seller_info['id_seller'])
                    );
                    
                    
                    if (empty($this->Kberrors)) {
                        $this->Kbconfirmation[] = $this->module->l('Product has been updated.');
                    } else {
                        array_unshift(
                            $this->Kberrors,
                            $this->module->l('Product has been updated, but some 
							of the parameters are not saved.')
                        );
                    }
                } else {
                    $this->Kberrors[] = $this->module->l(
                        'An error occurred while updating product.'
                    );
                }
            } else {
                $this->Kberrors[] = $this->module->l('An error occurred while updating product.');
            }
        } else {
            $this->Kberrors[] = $this->module->l('An error occurred while updating product.');
        }

        if (count($this->Kberrors) > 0) {
            $this->context->cookie->__set('redirect_error', implode('####', $this->Kberrors));
        } else {
            $this->context->cookie->__set('redirect_success', implode('####', $this->Kbconfirmation));
        }

        $request_param = array();
        if (Tools::isSubmit('submitType') && Tools::getValue('submitType') == 'savenstay') {
            $request_param['id_product'] = (int)$id;
            $request_param['render_type'] = 'form';
        }
        Tools::redirect($this->context->link->getModuleLink(
            $this->kb_module_name,
            'product',
            $request_param,
            (bool)Configuration::get('PS_SSL_ENABLED')
        ));
    }
    
    private function updateProductSuppliers($id_product, $id_suppliers = array(), $id_product_attribute = 0)
    {
        // Delete previously saved suppliers
        Db::getInstance()->delete('product_supplier', "id_product =". (int) $id_product."");

        foreach ($id_suppliers as $id_supplier) {
            $product_supplier                       = new ProductSupplier();
            $product_supplier->id_product           = $id_product;
            $product_supplier->id_product_attribute = $id_product_attribute;
            $product_supplier->id_supplier          = $id_supplier;
            $product_supplier->save();
        }
    }

    private function copyFromPost(&$object)
    {
        foreach ($_POST as $key => $value) {
            if (array_key_exists($key, $object) && $key != 'id_product') {
                $object->{$key} = trim($value);
            }
        }

        /* Multilingual fields */
        $languages = Language::getLanguages(false);
        $class_vars = get_class_vars(get_class($object));
        $fields = array();
        if (isset($class_vars['definition']['fields'])) {
            $fields = $class_vars['definition']['fields'];
        }

        foreach ($fields as $field => $params) {
            if (array_key_exists('lang', $params) && $params['lang']) {
                foreach ($languages as $language) {
                    $value = '';
                    if (Tools::getIsset($field . '_' . (int)$language['id_lang'])) {
                        $value = trim(Tools::getValue($field . '_' . (int)$language['id_lang']));
                    } elseif (isset($object->{$field}[(int)$language['id_lang']])) {
                        $value = $object->{$field}[(int)$language['id_lang']];
                    }
                    if (Tools::getIsset('id_product') && Tools::getValue('id_product') == 0) {
                        foreach ($languages as $lang) {
                            if (Tools::getIsset($field . '_' . (int) $lang['id_lang'])
                                    && Tools::getValue($field . '_' . (int) $lang['id_lang']) != ''
                            ) {
                                $value = trim(Tools::getValue($field . '_' . (int) $lang['id_lang']));
                            }
                        }
                    }
                    if ($field == 'description_short') {
                        $short_description_limit = Configuration::get('PS_PRODUCT_SHORT_DESC_LIMIT')
                            ? Configuration::get('PS_PRODUCT_SHORT_DESC_LIMIT') : 400;
                        $object->{$field}[(int)$language['id_lang']] = strip_tags(
                            $this->clipLongText(
                                $value,
                                '',
                                $short_description_limit,
                                false
                            )
                        );
                    } else {
                        if (isset($params['required']) && $params['required']) {
                            if (empty($value) && $value !== 0) {
                                $value_temp = Tools::getValue($field . '_' . (int)$this->default_form_language, '');
                                $object->{$field}[(int)$language['id_lang']] = $value_temp;
                            } else {
                                $object->{$field}[(int)$language['id_lang']] = $value;
                            }
                        } else {
                            $object->{$field}[(int)$language['id_lang']] = $value;
                        }
                    }
                }
            }
        }

        /* Additional fields */
        foreach ($languages as $language) {
            $keywords = '';
            if (Tools::getIsset('meta_keywords_' . $language['id_lang'])) {
                $keywords = trim(Tools::getValue('meta_keywords_' . $language['id_lang']));
            } elseif (isset($object->meta_keywords[$language['id_lang']])) {
                $keywords = $object->meta_keywords[$language['id_lang']];
            }
            $keywords = $this->cleanMetaKeywords(
                Tools::strtolower($keywords)
            );
            $object->meta_keywords[$language['id_lang']] = $keywords;
        }

        $_POST['width'] = (!Tools::getIsset('width')) ? '0' : str_replace(',', '.', Tools::getValue('width'));
        $_POST['height'] = (!Tools::getIsset('height')) ? '0' : str_replace(',', '.', Tools::getValue('height'));
        $_POST['depth'] = (!Tools::getIsset('depth')) ? '0' : str_replace(',', '.', Tools::getValue('depth'));
        $_POST['weight'] = (!Tools::getIsset('weight')) ? '0' : str_replace(',', '.', Tools::getValue('weight'));

        if (Tools::getIsset('unit_price') != null) {
            $object->unit_price = str_replace(',', '.', Tools::getValue('unit_price'));
        }

        $object->available_for_order = (int)Tools::getValue('available_for_order');
        $object->show_price = $object->available_for_order ? 1 : (int)Tools::getValue('show_price');
        $object->on_sale = (int)Tools::getValue('on_sale');
        $object->online_only = (int)Tools::getValue('online_only');
        $object->id_manufacturer = (int)Tools::getValue('id_manufacturer', 0);

        $ecotaxTaxRate = Tax::getProductEcotaxRate();
        if ($ecotax = Tools::getValue('ecotax')) {
            $_POST['ecotax'] = Tools::ps_round($ecotax / (1 + $ecotaxTaxRate / 100), 6);
        }
        if (Tools::getIsset('ecotax') != null) {
            $object->ecotax = str_replace(',', '.', Tools::getValue('ecotax'));
        }
        $this->processSubmittedData($object);
    }

    private function setSpecificPrice($id_product = 0)
    {
        $props = array(
            'id_product_attribute' => 0,
            'id_shop' => $this->seller_info['id_shop'],
            'id_currency' => 0, //for all currency
            'id_country' => 0, //for all countries
            'id_group' => 0, //for all groups
            'id_customer' => 0, // for all customers
            'price' => -1,
            'from_quantity' => 1, //Min quantity 1
            'reduction_type' => 'amount'
        );

        $reduced_price = (float)Tools::getValue('sp_reduction', 0);
        if ($reduced_price > 0) {
            $props['id_product'] = $id_product;
            $actual_retail_price = (float)Tools::getValue('price', 0);
            $props['reduction'] = (float)($actual_retail_price - $reduced_price);

            $from = Tools::getValue('sp_from_date', '0000-00-00 00:00:00');
            $to = Tools::getValue('sp_to', '0000-00-00 00:00:00');

            $props['from'] = $from;
            $props['to'] = $to;

            if (SpecificPrice::deleteByProductId($id_product)) {
                $specific_price = new SpecificPrice();
                foreach ($props as $prop => $val) {
                    $specific_price->{$prop} = $val;
                }

                $specific_price->add();
            }
        }
    }

    private function processSubmittedData(&$object)
    {
        $default_params = array(
            'unity' => '1',
            'redirect_type' => '404',
            'id_tax_rules_group' => 0,
            'depends_on_stock' => false,
        );

        foreach ($default_params as $key => $value) {
            $object->{$key} = $value;
        }
    }

    /**
     * delete all items in pack, then check if type_product value is 2.
     * if yes, add the pack items from input "inputPackItems"
     *
     * @param Product $product
     * @return boolean
     */
    public function updatePackItems($product)
    {
        Pack::deleteItems($product->id);
        // lines format: QTY x ID-QTY x ID
        if (Tools::getValue('inputPackItems')) {
            $product->setDefaultAttribute(0); //reset cache_default_attribute
            $items = Tools::getValue('inputPackItems');
            $lines = array_unique(explode('-', $items));
            // lines is an array of string with format : QTYxID
            if (count($lines)) {
                foreach ($lines as $line) {
                    if (!empty($line)) {
                        list($qty, $item_id) = explode('x', $line);
                        if ($qty > 0 && isset($item_id)) {
                            if (Pack::isPack((int)$item_id)) {
                                $this->Kberrors[] = $this->module->l(
                                    'You can\'t add product packs into a pack'
                                );
                            } elseif (!Pack::addItem((int)$product->id, (int)$item_id, (int)$qty)) {
                                $this->Kberrors[] = $this->module->l('An error occurred while attempting to 
									add products to the pack.');
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Update product download
     *
     * @param object $product Product
     * @return bool
     */
    public function updateDownloadProduct($product, $edit = 0)
    {
        if ((int)Tools::getValue('is_virtual_file') == 1) {
            if (isset($_FILES['virtual_product_file_uploader'])
                && $_FILES['virtual_product_file_uploader']['size'] > 0) {
                $virtual_product_filename = ProductDownload::getNewFilename();
                $helper = new HelperUploader('virtual_product_file_uploader');
                $helper->setPostMaxSize(Tools::getOctets(ini_get('upload_max_filesize')))
                    ->setSavePath(_PS_DOWNLOAD_DIR_)
                    ->upload($_FILES['virtual_product_file_uploader'], $virtual_product_filename);
            } else {
                $virtual_product_filename = Tools::getValue(
                    'virtual_product_filename',
                    ProductDownload::getNewFilename()
                );
            }

            $product->setDefaultAttribute(0); //reset cache_default_attribute
            if (Tools::getValue('virtual_product_expiration_date')
                && !Validate::isDate(Tools::getValue('virtual_product_expiration_date'))) {
                if (!Tools::getValue('virtual_product_expiration_date')) {
                    $this->Kberrors[] = $this->module->l(
                        'The expiration-date attribute is required.'
                    );
                    return false;
                }
            }

            // Trick's
            if ($edit == 1) {
                $id_product_download = (int)ProductDownload::getIdFromIdProduct((int)$product->id);
                if (!$id_product_download) {
                    $id_product_download = (int)ProductDownload::getIdFromIdProduct((int)$product->id, false);
                    if (!$id_product_download) {
                        $id_product_download = (int)Tools::getValue('virtual_product_id');
                    }
                }
            } else {
                $id_product_download = Tools::getValue('virtual_product_id');
            }

            $is_shareable = Tools::getValue('virtual_product_is_shareable');
            $virtual_product_name = Tools::getValue('virtual_product_name');
            $virtual_product_nb_days = Tools::getValue('virtual_product_nb_days');
            $virtual_product_nb_downloable = Tools::getValue('virtual_product_nb_downloable');
            $virtual_product_expiration_date = Tools::getValue('virtual_product_expiration_date');

            $download = new ProductDownload((int)$id_product_download);
            $download->id_product = (int)$product->id;
            $download->display_filename = $virtual_product_name;
            $download->filename = $virtual_product_filename;
            $download->date_add = date('Y-m-d H:i:s');
            $download->date_expiration = $virtual_product_expiration_date
                ? $virtual_product_expiration_date . ' 23:59:59' : '';
            $download->nb_days_accessible = (int)$virtual_product_nb_days;
            $download->nb_downloadable = (int)$virtual_product_nb_downloable;
            $download->active = 1;
            $download->is_shareable = (int)$is_shareable;

            if ($download->save()) {
                return true;
            }
        } else {
            /* unactive download product if checkbox not checked */
            if ($edit == 1) {
                $id_product_download = (int)ProductDownload::getIdFromIdProduct((int)$product->id);
                if (!$id_product_download) {
                    $id_product_download = (int)Tools::getValue('virtual_product_id');
                }
            } else {
                $id_product_download = ProductDownload::getIdFromIdProduct($product->id);
            }

            if (!empty($id_product_download)) {
                $product_download = new ProductDownload((int)$id_product_download);
                $product_download->date_expiration = date('Y-m-d H:i:s', time() - 1);
                $product_download->active = 0;
                return $product_download->save();
            }
        }
        return false;
    }

    /**
     * Update product tags
     *
     * @param array Languages
     * @param object $product Product
     * @return boolean Update result
     */
    public function updateTags($languages, $product)
    {
        $tag_success = true;
        $saved_tags = Tag::getProductTags($product->id);
        if (Tag::deleteTagsForProduct((int)$product->id)) {
            /* Assign tags to this product */
            foreach ($languages as $language) {
                if (Tools::getIsset('tags_' . $language['id_lang'])) {
                    $value = Tools::getValue('tags_' . $language['id_lang']);
                } elseif (isset($saved_tags[$language['id_lang']])) {
                    $value = implode(',', $saved_tags[$language['id_lang']]);
                } else {
                    $value = '';
                }
                if (!empty($value)) {
                    $tag_success &= Tag::addTags($language['id_lang'], (int)$product->id, $value);
                }
            }
        } else {
            $tag_success = false;
        }

        if (!$tag_success) {
            $this->Kberrors[] = $this->module->l('An error occurred while adding tags.');
        }

        return $tag_success;
    }

    public function updateImages($languages)
    {
        if (Tools::getIsset('product_img')) {
            $product_imgs = Tools::getValue('product_img', array());
            if ($product_imgs && is_array($product_imgs)) {
                foreach ($product_imgs as $id_image => $img) {
                    if ($id_image == 'image_id') {
                        continue;
                    }
                    $img_obj = new Image($id_image);
                    if (Validate::isLoadedObject($img_obj)) {
                        foreach ($languages as $lang) {
                            $legend = 'legend_'.$lang['id_lang'];
                            if (isset($img[$legend]) && $img[$legend] != '') {
                                $value = $img[$legend];
                            } elseif (isset($img_obj->legend[$lang['id_lang']])
                                && $img_obj->legend[$lang['id_lang']] != '') {
                                $value = $img_obj->legend[$lang['id_lang']];
                            } else {
                                $value = '';
                            }
                            $img_obj->legend[$lang['id_lang']] = $value;
                        }
                        if (Tools::getIsset('product_img_default_cover')
                            && Tools::getValue('product_img_default_cover', null) == $id_image) {
                            $img_obj->cover = Tools::getValue('product_img_default_cover', null);
                        } else {
                            $img_obj->cover = 0;
                        }
                        
                        $img_obj->position = (int)$img['position'];
                        $img_obj->update();
                    }
                }
            }
        }
    }

    public function updateShipping($product)
    {
        $shippings = array();
        if (Tools::getIsset('selectedShipping')) {
            $shippings = Tools::getValue('selectedShipping', array());
        }
        if (empty($shippings)) {
            $shippings = array(KbSellerShipping::getDefaultShippingId($this->seller_obj->id));
        }
        $product->setCarriers($shippings);
    }

    public function processDeleteVirtual($id_product)
    {
        $error = '';
        if (!($id_product_download = ProductDownload::getIdFromIdProduct($id_product))) {
            $error = $this->module->l('Error occurred while retrieving file to delete');
        } else {
            $product_download = new ProductDownload((int)$id_product_download);

            if (!$product_download->deleteFile((int)$id_product_download)) {
                $error = $this->module->l('Error occurred while deleting file');
            }
        }

        if ($error == '') {
            $request_param = array();
            $request_param['id_product'] = (int)$id_product;
            $request_param['render_type'] = 'form';
            $this->context->cookie->__set(
                'redirect_success',
                $this->module->l('File is successfully deleted.')
            );
            $link = $this->context->link->getModuleLink(
                $this->kb_module_name,
                'product',
                $request_param,
                (bool)Configuration::get('PS_SSL_ENABLED')
            );
            $json = array(
                'status' => true,
                'msg' => $this->module->l('Successfuly deleted'),
                'redirect' => $link
            );
        } else {
            $json = array('status' => false, 'msg' => $error);
        }

        return $json;
    }

    public function processDuplicate()
    {
        //check for product limit
        if (!$this->seller_obj->isApprovedSeller() || $this->seller_obj->active == 0) {
            $added_product_count = (int)$this->seller_obj->product_limit_wout_approval;
            $product_limit = (int)KbSellerSetting::getSellerSettingByKey($this->seller_obj->id, 'kbmp_product_limit');
            $error_txt = 'Your limit of adding new products has been over as your account is not approved.';
            $error_txt .= 'To add more products, please contact to admin.';
            if ($added_product_count >= $product_limit) {
                $this->context->cookie->__set(
                    'redirect_error',
                    $this->module->l($error_txt)
                );

                Tools::redirect($this->context->link->getModuleLink(
                    $this->kb_module_name,
                    $this->controller_name,
                    array(),
                    (bool)Configuration::get('PS_SSL_ENABLED')
                ));
            }
        }
        if (Validate::isLoadedObject($product = new Product((int)Tools::getValue('id_product')))) {
            $id_product_old = $product->id;
            unset($product->id);
            unset($product->id_product);
            $product->indexed = 0;
            $product->active = 0;
            if ($product->add()) {
                $seller_product = new KbSellerProduct();
                $seller_product->id_seller = $this->seller_info['id_seller'];
                $seller_product->id_shop = $this->seller_info['id_shop'];
                $seller_product->id_product = $product->id;
                $seller_product->deleted = 0;
                if ($this->seller_info['settings']['kbmp_new_product_approval_required'] == 0) {
                    $seller_product->approved = (string)KbGlobal::APPROVED;
                } else {
                    $seller_product->approved = (string)KbGlobal::APPROVAL_WAITING;
                }

                $seller_product->save();

                $required_approval = $this->sendNotificationForProductApproval($product);

                if (Category::duplicateProductCategories($id_product_old, $product->id)
                    && ($combination_images = Product::duplicateAttributes($id_product_old, $product->id)) !== false
                    && GroupReduction::duplicateReduction($id_product_old, $product->id)
                    && Product::duplicateSpecificPrices($id_product_old, $product->id)
                    && Pack::duplicate($id_product_old, $product->id)
                    && Product::duplicateFeatures($id_product_old, $product->id)
                    && Product::duplicateTags($id_product_old, $product->id)
                    && Product::duplicateDownload($id_product_old, $product->id)) {
                    if ($product->hasAttributes()) {
                        Product::updateDefaultAttribute($product->id);
                    }

                    if (!Image::duplicateProductImages($id_product_old, $product->id, $combination_images)) {
                        $this->Kberrors[] = Tools::displayError('An error occurred while copying images.');
                    } else {
                        if (in_array($product->visibility, array('both', 'search'))
                            && Configuration::get('PS_SEARCH_INDEXATION')) {
                            Search::indexation(false, $product->id);
                        }

                        $_POST['id_product'] = (int)$product->id;

                        if ($required_approval == KbGlobal::APPROVED) {
                            $this->context->cookie->__set(
                                'redirect_success',
                                $this->module->l('Product is duplicated successfully. 
                                Please change the SKU of this product.')
                            );
                        } else {
                            $this->processUpdate();
                            $this->context->cookie->__set(
                                'redirect_success',
                                $this->module->l(
                                    'Product is duplicated successfully and waiting for admin approval. 
                                    Please change the SKU of this product.'
                                )
                            );
                        }
                    }
                }
            } else {
                $this->Kberrors[] = $this->module->l(
                    'Error occurred while creating object of duplicate product.'
                );
            }
        } else {
            $this->Kberrors[] = $this->module->l('This product is not exist in your context.');
        }
    }
    

    private function sendNotificationForProductApproval($product)
    {
        $approved = KbGlobal::APPROVAL_WAITING;
        if ($this->seller_info['settings']['kbmp_new_product_approval_required'] == 0) {
            $approved = KbGlobal::APPROVED;
        }
        //Send Email to Admin to get notify about new product
        $data = array(
            'email' => $this->seller_info['email'],
            'title' => (!empty($this->seller_info['title'])) ? $this->seller_info['title'] : 'Anonymous seller',
            'name' => $this->seller_info['seller_name'],
            'contact' => $this->seller_info['phone_number'],
            'product_name' => $product->name,
            'product_sku' => $product->reference,
            'product_price' => Tools::displayPrice(
                Tools::convertPrice($product->price),
                $this->seller_currency
            ),
        );

        $email = new KbEmail(
            KbEmail::getTemplateIdByName('mp_new_product_notification_admin'),
            $this->seller_info['id_default_lang']
        );
        $email->sendRequestForProductApproval($data);

        return $approved;
    }

    public function processDelete()
    {
        if (Validate::isLoadedObject($object = new Product((int)Tools::getValue('id_product')))) {
            /*
             * @since 1.5.0
             * It is NOT possible to delete a product if there are currently:
             * - physical stock for this product
             * - supply order(s) for this product
             */
            if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT') && $object->advanced_stock_management) {
                $stock_manager = StockManagerFactory::getManager();
                $physical_quantity = $stock_manager->getProductPhysicalQuantities($object->id, 0);
                $real_quantity = $stock_manager->getProductRealQuantities($object->id, 0);
                if ($physical_quantity > 0 || $real_quantity > $physical_quantity) {
                    $this->Kberrors[] = $this->module->l('You cannot delete this product 
						because there\'s physical stock left.');
                }
            }

            if (!count($this->Kberrors)) {
                if ($object->delete()) {
                    if (!$this->seller_obj->isApprovedSeller() || $this->seller_obj->active == 0) {
                        $tmp = (int)$this->seller_obj->product_limit_wout_approval;
                        $this->seller_obj->product_limit_wout_approval -= $tmp;
                        $this->seller_obj->save();
                    }
                    $this->context->cookie->__set(
                        'redirect_success',
                        $this->module->l('Product has been deleted successfully.')
                    );
                    Tools::redirect(
                        $this->context->link->getModuleLink(
                            $this->kb_module_name,
                            $this->controller_name,
                            array(),
                            (bool)Configuration::get('PS_SSL_ENABLED')
                        )
                    );
                } else {
                    $this->Kberrors[] = $this->module->l(
                        'An error occurred while deleting product.'
                    );
                }
            }
        } else {
            $this->Kberrors[] = $this->module->l(
                'An error occurred while deleting product.'
            );
        }
    }

    public function getAjaxProductListHtml()
    {
        $json = array();
        $query = 'Select {{COLUMN}} from ' . _DB_PREFIX_ . 'product as p 
			LEFT JOIN ' . _DB_PREFIX_ . 'product_lang as pl on (p.id_product = pl.id_product) 
			INNER JOIN ' . _DB_PREFIX_ . 'kb_mp_seller_product as p2s on (p.id_product = p2s.id_product) 
			WHERE p2s.id_seller = ' . (int)$this->seller_info['id_seller']
            . ' AND pl.id_lang = ' . (int)$this->seller_info['id_default_lang'] . ' ';

        $custom_filter = '';
        if (Tools::getIsset('reference') && Tools::getValue('reference') != '') {
            $custom_filter .= ' AND p.reference like "%' . pSQL(Tools::getValue('reference')) . '%"';
        }

        if (Tools::getIsset('id_category_default') && Tools::getValue('id_category_default') != '') {
            $custom_filter .= ' AND p.id_category_default = ' . (int)Tools::getValue('id_category_default');
        }

        if (Tools::getIsset('active') && Tools::getValue('active') != '') {
            $custom_filter .= ' AND p.active = ' . (int)Tools::getValue('active');
        }

        if (Tools::getIsset('approved') && Tools::getValue('approved') != '') {
            $custom_filter .= ' AND p2s.approved = "' . (int)Tools::getValue('approved') . '"';
        }

        if (Tools::getIsset('name') && Tools::getValue('name') != '') {
            $custom_filter .= ' AND pl.name like "%' . pSQL(Tools::getValue('name')) . '%"';
        }

        $query .= pSQL($custom_filter);

        $this->total_records = DB::getInstance(_PS_USE_SQL_SLAVE_)->getValue(
            str_replace(
                '{{COLUMN}}',
                'COUNT(p.id_product) as total',
                $query
            )
        );

        if ($this->total_records > 0) {
            if (Tools::getIsset('start') && (int)Tools::getValue('start') > 0) {
                $this->page_start = (int)Tools::getValue('start');
            }

            $query .= ' ORDER BY p.id_product DESC LIMIT '
                . (int)$this->getPageStart() . ', ' . (int)$this->tbl_row_limit;
            $results = DB::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
                str_replace(
                    '{{COLUMN}}',
                    'p.id_product, p2s.approved',
                    $query
                )
            );

            $row_html = '';
            foreach ($results as $val) {
                $product = new Product($val['id_product'], false, $this->seller_info['id_default_lang']);
                $cat = new Category($product->id_category_default, $this->seller_info['id_default_lang']);

                $edit_link = $this->context->link->getModuleLink(
                    $this->kb_module_name,
                    $this->controller_name,
                    array('render_type' => 'form', 'step' => 2, 'id_product' => $product->id),
                    (bool)Configuration::get('PS_SSL_ENABLED')
                );

                $view_link = $this->context->link->getProductLink(
                    $product,
                    null,
                    null,
                    null,
                    $this->seller_info['id_default_lang']
                );

                $yes_txt = $this->module->l('Yes');
                $row_html .= '<tr>
							<td class="kb-tcenter">
                            <div class="checker"><span>
								<input type="checkbox" class="kb_list_row_checkbox" name="row_item_id[]" 
								value="' . $product->id . '" title=""></span></div>
							</td>
							<td class="kb-tright">
								#' . $product->id . '
							</td>
							<td class=" ">
								<a href="' . $view_link . '" 
								title="' . $this->module->l('Click to view product')
                            . '" onclick="" target="_blank">'
                    . $product->name . '</a>
							</td>
							<td class=" ">' . $product->reference . '</td>
                            <td class=" ">' . $this->getProductType($product) . '</td>
							<td class=" ">' . $cat->name . '</td>
							<td class=" kb-tright">'
                            . Tools::displayPrice(Tools::convertPrice($product->price), $this->seller_currency)
                            . '</td>
							<td class=" ">'
                            . KbGlobal::getApporvalStatus($val['approved'])
                            . '</td>
							<td class=" ">'
                    . (($product->active) ? $yes_txt : $this->module->l('No'))
                    . '</td><td>
                        <a href="' . $edit_link . '" 
								title="' . $this->module->l('Click to edit product')
                            . '" class="btn btn-default kb-multiaction-link">
                            <i class="kb-material-icons kb-multiaction-icon"></i>
							</a>
                        <a href="javascript:void(0)"
								title="' . $this->module->l('Click to delete product')
                            . '" class="btn btn-default kb-multiaction-link" onclick="KbDeleteAction('
                        .$product->id.')"><i class="kb-material-icons kb-multiaction-icon"></i>
							</a>
						</tr>';
            }
            $this->table_id = 'seller_product';
            $this->list_row_callback = 'getSellerProducts';
            $json['status'] = true;
            $json['html'] = $row_html;
            $json['pagination'] = $this->generatePaginator(
                $this->page_start,
                $this->total_records,
                $this->getTotalPages(),
                $this->list_row_callback
            );
        } else {
            $json['status'] = false;
            $json['msg'] = $this->module->l('No Data Found');
        }
        return $json;
    }

    public function processMultiAction()
    {
        $all_updated = true;
        $update_count = 0;
        if (Tools::getIsset('mutiaction_type')
            && Tools::getValue('mutiaction_type') == KbGlobal::MULTI_ACTION_TYPE_STATUS) {
            if (Tools::getIsset('mutiaction_status_list')) {
                $status = Tools::getValue('mutiaction_status_list');
                $product_ids = explode(',', trim(Tools::getValue('selected_table_item_ids')));
                foreach ($product_ids as $id) {
                    if ((int)$id > 0) {
                        $product = new Product($id);
                        if (!$this->seller_obj->isApprovedSeller() || $this->seller_obj->active == 0) {
                            $product->active = 0;
                        } else {
                            if (!KbSellerProduct::isApprovedProduct($this->seller_obj->id, $product->id)) {
                                $product->active = 0;
                            } else {
                                $product->active = (int)$status;
                            }
                        }

                        if (!$product->save()) {
                            $all_updated = false;
                        } else {
                            $update_count++;
                        }
                    }
                }

                if (!$all_updated) {
                    $this->context->cookie->__set(
                        'redirect_success',
                        sprintf(
                            $this->module->l(
                                '<b>%s</b> product(s) has been updated out of <b>%s</b> product(s).'
                            ),
                            $update_count,
                            count($product_ids)
                        )
                    );
                } else {
                    $this->context->cookie->__set(
                        'redirect_success',
                        $this->module->l('Selected product(s) are updated successfully.')
                    );
                }
            } else {
                $this->context->cookie->__set(
                    'redirect_error',
                    $this->module->l('Atleast one product is required to take selected action.')
                );
            }
        } elseif (Tools::getIsset('mutiaction_type')
            && Tools::getValue('mutiaction_type') == KbGlobal::MULTI_ACTION_TYPE_DELETE) {
            $product_ids = explode(',', trim(Tools::getValue('selected_table_item_ids')));
            $cannot_delete = 0;
            foreach ($product_ids as $id) {
                if ((int)$id > 0) {
                    $enable_to_delete = true;
                    $product = new Product($id);
                    if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT') && $product->advanced_stock_management) {
                        $stock_manager = StockManagerFactory::getManager();
                        $physical_quantity = $stock_manager->getProductPhysicalQuantities($product->id, 0);
                        $real_quantity = $stock_manager->getProductRealQuantities($product->id, 0);
                        if ($physical_quantity > 0 || $real_quantity > $physical_quantity) {
                            $enable_to_delete = false;
                            $cannot_delete++;
                        }
                    }

                    if ($enable_to_delete) {
                        if (!$product->delete()) {
                            $all_updated = false;
                            if (!$this->seller_obj->isApprovedSeller() || $this->seller_obj->active == 0) {
                                $tmp = (int)$this->seller_obj->product_limit_wout_approval;
                                $this->seller_obj->product_limit_wout_approval -= $tmp;
                                $this->seller_obj->save();
                            }
                        } else {
                            $update_count++;
                        }
                    }
                }
            }
            if ($cannot_delete > 0) {
                if (!$all_updated) {
                    $this->context->cookie->__set(
                        'redirect_success',
                        sprintf(
                            $this->module->l(
                                '<b>%s</b> product(s) has been deleted out of <b>%s</b> product(s) 
								and <b>%s</b> product(s) cannot be delete due to pysical stock.'
                            ),
                            $update_count,
                            count($product_ids),
                            $cannot_delete
                        )
                    );
                } else {
                    $this->context->cookie->__set(
                        'redirect_success',
                        sprintf(
                            $this->module->l('<b>%s</b> product(s) has been deleted successfully and <b>%s</b>
                                 product(s) cannot be delete due to pysical stock.'),
                            $update_count,
                            $cannot_delete
                        )
                    );
                }
            } else {
                if (!$all_updated) {
                    $this->context->cookie->__set(
                        'redirect_success',
                        sprintf(
                            $this->module->l('<b>%s</b> product(s) has been deleted successfully out 
                            of <b>%s</b> product(s).'),
                            $update_count,
                            count($product_ids)
                        )
                    );
                } else {
                    if (count($product_ids) == 1) {
                        $this->context->cookie->__set(
                            'redirect_success',
                            $this->module->l('Product has been deleted successfully.')
                        );
                    } else {
                        $this->context->cookie->__set(
                            'redirect_success',
                            $this->module->l('Selected product(s) has been deleted successfully.')
                        );
                    }
                }
            }

            $reason_log = new KbReasonLog();
            $reason_log->reason_type = 2;
            $reason_log->id_seller = $this->seller_info['id_seller'];
            $reason_log->id_employee = null;
            $reason_log->comment = Tools::getValue('reason');
            $reason_log->save(true);
        } else {
            $this->context->cookie->__set(
                'redirect_error',
                $this->module->l('Please select valid action')
            );
        }

        Tools::redirect(
            $this->context->link->getModuleLink(
                $this->kb_module_name,
                $this->controller_name,
                array(),
                (bool)Configuration::get('PS_SSL_ENABLED')
            )
        );
    }

    public function processProductFeatures($product_id)
    {
        if (!Feature::isFeatureActive()) {
            return;
        }

        if (Validate::isLoadedObject($product = new Product((int)$product_id))) {
            // delete all objects
            $product->deleteFeatures();

            // add new objects
            foreach ($_POST as $key => $val) {
                if (preg_match('/^feature_([0-9]+)_value/i', $key, $match)) {
                    if ($val) {
                        $product->addFeaturesToDB($match[1], $val);
                    }
                }
            }
        }
    }
}
