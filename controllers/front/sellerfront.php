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

require_once 'KbFrontCore.php';

class KbmarketplaceSellerfrontModuleFrontController extends KbmarketplaceFrontCoreModuleFrontController
{
    public $controller_name = 'sellerfront';
    private $page_limit = 12;

    public function __construct()
    {
        parent::__construct();
        $this->context->smarty->assign('kb_is_customer_logged', $this->context->customer->logged);
    }

    public function setMedia()
    {
        parent::setMedia();
        $this->addCSS(_THEME_CSS_DIR_ . 'product.css');
        if (Tools::getIsset('render_type')
            && (Tools::getValue('render_type') == 'sellerview'
            || Tools::getValue('render_type') == 'sellerproducts')) {
            $this->addCSS(_THEME_CSS_DIR_ . 'product_list.css');
        }
        $this->addJS(_THEME_JS_DIR_ . 'category.js');
    }

    public function postProcess()
    {
        if (Tools::isSubmit('ajax')) {
            $this->json = array();
            $renderhtml = false;
            if (Tools::isSubmit('method')) {
                switch (Tools::getValue('method')) {
                    case 'getSellerList':
                        $this->json['content'] = $this->getAjaxSellerListHtml();
                        break;
                }
            }
            if (!$renderhtml) {
                echo Tools::jsonEncode($this->json);
            }
            die;
        }
    }

    public function initContent()
    {

        $config = Tools::unSerialize(Configuration::get('KB_MARKETPLACE_CONFIG'));
        if (Tools::getIsset('render_type')) {
            if (Tools::getValue('render_type') == 'sellerview' && $config['kbmp_show_seller_on_front']) {
                $this->renderViewToCustomer();
            } elseif (Tools::getValue('render_type') == 'sellerreviews' && $config['kbmp_show_seller_on_front']) {
                $this->renderReviewToCustomer();
            } elseif (Tools::getValue('render_type') == 'sellerproducts' && $config['kbmp_show_seller_on_front']) {
                $this->renderSellersProducts();
            } else {
                $this->context->cookie->__set(
                    'redirect_error',
                    $this->module->l(
                        'Currently, You are not authorized to view sellers.'
                    )
                );
            }
        } else {
            if ($config['kbmp_show_seller_on_front']) {
                $this->renderSellerList();
            } else {
                $this->context->cookie->__set(
                    'redirect_error',
                    $this->module->l(
                        'Currently, You are not authorized to view sellers.'
                    )
                );
            }
        }

        parent::initContent();
    }

    public function renderSellerList()
    {
        //coupon- mayank kumar
        if (Tools::getValue('searchcoupon') && Tools::getIsset('searchcoupon')) {
            if (!empty(Tools::getValue('coupon')) && Tools::getIsset('coupon')) {
                $coupon = Tools::getValue('coupon');
                $free_shipping = Db::getInstance()->getRow(
                    'SELECT * FROM '._DB_PREFIX_.'cart_rule where code="'.pSQL($coupon).'"'
                );
                if ($free_shipping['free_shipping'] == '1') {
                    echo 'free';
                    die;
                } elseif ($free_shipping['free_shipping'] == '0') {
                    echo 'paid';
                    die;
                } else {
                    echo 'no';
                    die;
                }
            }
        }
        
        if (Tools::isSubmit('new_review_submit') && Tools::getValue('new_review_submit') == 1) {
            $id_seller = Tools::getValue('id_seller', 0);
            $seller = new KbSeller($id_seller);
            if ($seller->isSeller()) {
                $this->saveNewReview($id_seller);

                $redirect_link = $this->context->link->getModuleLink(
                    $this->kb_module_name,
                    $this->controller_name,
                    array(),
                    (bool)Configuration::get('PS_SSL_ENABLED')
                );
                Tools::redirect($redirect_link);
            }
        }
        $start = 1;
        if (Tools::getIsset('kb_page_start') && (int)Tools::getValue('kb_page_start') > 0) {
            $start = Tools::getValue('kb_page_start');
        }

        $total = KbSeller::getSellers(true, true, null, null, null, null, true, true);

        if ($total > 0) {
            $paging = KbGlobal::getPaging($total, $start, $this->page_limit, false, 'getSellerList');

            $orderby = null;
            if (Tools::getIsset('orderby') && Tools::getValue('orderby') != '') {
                $orderby = Tools::getValue('orderby');
            }

            $orderway = null;
            if (Tools::getIsset('orderway') && Tools::getValue('orderway') != '') {
                $orderway = Tools::getValue('orderway');
            }

            $sellers = KbSeller::getSellers(
                false,
                true,
                $paging['page_position'],
                $this->page_limit,
                $orderby,
                $orderway,
                true,
                true
            );

            $base_link = KbGlobal::getBaseLink((bool)Configuration::get('PS_SSL_ENABLED'));
            $profile_default_image_path = $base_link . 'modules/' . $this->module->name . '/' . 'views/img/';
            foreach ($sellers as $key => $val) {
                $seller_image_path = _PS_IMG_DIR_ . KbSeller::SELLER_PROFILE_IMG_PATH . $val['id_seller'] . '/';
                if (empty($val['logo'])
                    || !Tools::file_exists_no_cache($seller_image_path . $val['logo'])) {
                    $sellers[$key]['logo'] = $profile_default_image_path . KbGlobal::SELLER_DEFAULT_LOGO;
                } else {
                    $sellers[$key]['logo'] = $this->seller_image_path . $val['id_seller'] . '/' . $val['logo'];
                }

                if ($val['title'] == '' || empty($val['title'])) {
                    $sellers[$key]['title'] = $this->module->l('Not Mentioned');
                }

                $sellers[$key]['href'] = KbGlobal::getSellerLink($val['id_seller']);

                $review_setting = KbSellerSetting::getSellerSettingByKey(
                    $val['id_seller'],
                    'kbmp_enable_seller_review'
                );

                if ($review_setting == 1) {
                    $sellers[$key]['display_write_review'] = true;
                } else {
                    $sellers[$key]['display_write_review'] = false;
                }

                $sellers[$key]['view_review_href'] = $this->context->link->getModuleLink(
                    $this->kb_module_name,
                    $this->controller_name,
                    array('render_type' => 'sellerreviews', 'id_seller' => $val['id_seller'])
                );

                if ((int)$val['total_review'] > 0) {
                    $tmp = (int)$val['total_review'];
                    $sellers[$key]['rating_percent'] = (float)((($val['rating'] / $tmp) / 5) * 100);
                } else {
                    $sellers[$key]['rating_percent'] = 0;
                }
            }

            $this->context->smarty->assign('sellers', $sellers);

            $pagination_string = sprintf(
                $this->module->l('Showing %d - %d of %d items'),
                $paging['paging_summary']['record_start'],
                $paging['paging_summary']['record_end'],
                $total
            );
            $this->context->smarty->assign('pagination_string', $pagination_string);
            $this->context->smarty->assign('kb_pagination', $paging);
            $this->context->smarty->assign(
                'seller_reviews',
                $this->context->link->getModuleLink(
                    $this->kb_module_name,
                    $this->controller_name,
                    array('render_type' => 'sellerreviews'),
                    (bool)Configuration::get('PS_SSL_ENABLED')
                )
            );

            $sorting_types = array(
                array('value' => 'sl.title:asc', 'label' => $this->module->l('Name: A to Z')),
                array('value' => 'sl.title:desc', 'label' => $this->module->l('Name: Z to A')),
                array(
                    'value' => 'rating:asc',
                    'label' => $this->module->l('Rating: Low to High')
                ),
                array(
                    'value' => 'rating:desc',
                    'label' => $this->module->l('Rating: High to Low')
                ),
                array(
                    'value' => 'total_review:asc',
                    'label' => $this->module->l('Review: Lowest')
                ),
                array(
                    'value' => 'total_review:desc',
                    'label' => $this->module->l('Review: Highest')
                )
            );

            $this->context->smarty->assign('sorting_types', $sorting_types);
            $this->context->smarty->assign('selected_sort', $orderby . ':' . $orderway);
        } else {
            $this->context->smarty->assign(
                'empty_list',
                $this->module->l('No Seller found')
            );
        }

        $this->setKbTemplate('seller/list_to_customers.tpl');
    }

    public function renderViewToCustomer()
    {
        $id_seller = Tools::getValue('id_seller', 0);
        if ((int)$id_seller > 0) {
            $seller = new KbSeller($id_seller);
            if ($seller->isSeller()) {
                if (Tools::isSubmit('new_review_submit') && Tools::getValue('new_review_submit') == 1) {
                    $this->saveNewReview($id_seller);

                    $redirect_link = KbGlobal::getSellerLink($id_seller);

                    Tools::redirect($redirect_link);
                }

                $seller_info = $seller->getSellerInfo();
                $base_link = KbGlobal::getBaseLink((bool)Configuration::get('PS_SSL_ENABLED'));
                $profile_default_image_path = $base_link . 'modules/' . $this->module->name . '/' . 'views/img/';
                $seller_image_path = _PS_IMG_DIR_ . KbSeller::SELLER_PROFILE_IMG_PATH . $id_seller . '/';
                if (empty($seller_info['logo'])
                    || !Tools::file_exists_no_cache($seller_image_path . $seller_info['logo'])) {
                    $seller_info['logo'] = $profile_default_image_path . KbGlobal::SELLER_DEFAULT_LOGO;
                } else {
                    $seller_info['logo'] = $this->seller_image_path . $id_seller . '/' . $seller_info['logo'];
                }

                if (empty($seller_info['banner'])
                    || !Tools::file_exists_no_cache($seller_image_path . $seller_info['banner'])) {
                    $seller_info['banner'] = $profile_default_image_path . KbGlobal::SELLER_DEFAULT_BANNER;
                } else {
                    $seller_info['banner'] = $this->seller_image_path . $id_seller . '/' . $seller_info['banner'];
                }

                $review_count = KbSellerReview::getReviewsBySellerId(
                    $id_seller,
                    $this->context->language->id,
                    KbGlobal::APPROVED,
                    true
                );

                $seller_info['seller_review_count'] = sprintf(
                    $this->module->l('Total %s review(s)'),
                    $review_count
                );

                $rating = KbGlobal::convertRatingIntoPercent(KbSellerReview::getSellerRating($id_seller));
                $rating = number_format($rating, 2);
                $seller_info['seller_rating'] = $rating;

                $state_name = '';
                if (!empty($seller_info['state'])) {
                    $state_name = $seller_info['state'];
                }

                $country_name = '';
                if (!empty($seller_info['id_country'])) {
                    $country_name = Country::getNameById($this->context->language->id, $seller_info['id_country']);
                }

                $seller_info['state'] = $state_name;
                $seller_info['country'] = $country_name;
                $this->context->smarty->assign('seller', $seller_info);

                $id_category = Tools::getValue('s_filter_category', '');
                $filters = array();

                if ((int)$id_category > 0) {
                    $filters['id_category'] = (int)$id_category;
                }

                $this->context->smarty->assign('selected_category', $id_category);

                $total_records = KbSellerProduct::getProductsWithDetails(
                    $id_seller,
                    $this->context->language->id,
                    $filters,
                    true
                );

                $sort_by = array('by' => 'pl.name', 'way' => 'ASC');
                $seleted_sort = '';
                if (Tools::getIsset('s_filter_sortby') && Tools::getValue('s_filter_sortby')) {
                    $seleted_sort = Tools::getValue('s_filter_sortby');
                    $explode = explode(':', Tools::getValue('s_filter_sortby'));
                    $sort_by['by'] = $explode[0];
                    $sort_by['way'] = $explode[1];
                }

                $this->context->smarty->assign('selected_sort', $seleted_sort);

                $start = 1;
                if ((int)Tools::getValue('page_number', 0) > 0) {
                    $start = (int)Tools::getValue('page_number', 0);
                }

                $this->context->smarty->assign('seller_product_current_page', $start);

                $paging = KbGlobal::getPaging($total_records, $start, $this->page_limit, false, 'getSProduct2User');

                $products = KbSellerProduct::getProductsWithDetails(
                    $id_seller,
                    $this->context->language->id,
                    $filters,
                    false,
                    $paging['page_position'],
                    $this->page_limit,
                    $sort_by['by'],
                    $sort_by['way']
                );

                $products = Product::getProductsProperties((int)$this->context->language->id, $products);
                
                $products = array_map(array($this, 'prepareProductForTemplate'), $products);

                $this->context->smarty->assign('products', $products);

                if ($products && count($products) > 0) {
                    $pagination_string = sprintf(
                        $this->module->l('Showing %d - %d of %d items'),
                        $paging['paging_summary']['record_start'],
                        $paging['paging_summary']['record_end'],
                        $total_records
                    );

                    $this->context->smarty->assign('pagination_string', $pagination_string);
                }

                $this->context->smarty->assign('kb_pagination', $paging);

                $this->context->smarty->assign(
                    'filter_form_action',
                    KbGlobal::getSellerLink($id_seller)
                );

                $review_setting = KbSellerSetting::getSellerSettingByKey($id_seller, 'kbmp_enable_seller_review');
                if ($review_setting == 1) {
                    $this->context->smarty->assign('display_new_review', true);
                    $this->context->smarty->assign('display_review_popup', true);
                } else {
                    $this->context->smarty->assign('display_new_review', false);
                }

                $this->context->smarty->assign('category_list', $this->getCategoryList());
                $this->setKbTemplate('seller/seller_view_to_customer.tpl');
            }
        }
    }

    public function renderReviewToCustomer()
    {
        $id_seller = Tools::getValue('id_seller', 0);
        if ((int)$id_seller > 0) {
            $seller = new KbSeller($id_seller);
            if ($seller->isSeller()) {
                if (Tools::isSubmit('new_review_submit') && Tools::getValue('new_review_submit') == 1) {
                    $this->saveNewReview($id_seller);

                    $redirect_link = $this->context->link->getModuleLink(
                        $this->kb_module_name,
                        $this->controller_name,
                        array('render_type' => 'sellerreviews', 'id_seller' => $id_seller),
                        (bool)Configuration::get('PS_SSL_ENABLED')
                    );

                    Tools::redirect($redirect_link);
                }

                $this->page_limit = 20;
                $seller_info = $seller->getSellerInfo();
                $base_link = KbGlobal::getBaseLink((bool)Configuration::get('PS_SSL_ENABLED'));
                $profile_default_image_path = $base_link . 'modules/' . $this->module->name . '/' . 'views/img/';
                $seller_image_path = _PS_IMG_DIR_ . KbSeller::SELLER_PROFILE_IMG_PATH . $id_seller . '/';
                if (empty($seller_info['logo'])
                    || !Tools::file_exists_no_cache($seller_image_path . $seller_info['logo'])) {
                    $seller_info['logo'] = $profile_default_image_path . KbGlobal::SELLER_DEFAULT_LOGO;
                } else {
                    $seller_info['logo'] = $this->seller_image_path . $id_seller . '/' . $seller_info['logo'];
                }

                if (empty($seller_info['banner'])
                    || !Tools::file_exists_no_cache($seller_image_path . $seller_info['banner'])) {
                    $seller_info['banner'] = $profile_default_image_path . KbGlobal::SELLER_DEFAULT_BANNER;
                } else {
                    $seller_info['banner'] = $this->seller_image_path . $id_seller . '/' . $seller_info['banner'];
                }

                $review_count = KbSellerReview::getReviewsBySellerId(
                    $id_seller,
                    $this->context->language->id,
                    KbGlobal::APPROVED,
                    true
                );

                $seller_info['seller_review_count'] = sprintf(
                    $this->module->l('Total %s review(s)'),
                    $review_count
                );

                $rating = KbGlobal::convertRatingIntoPercent(KbSellerReview::getSellerRating($id_seller));
                $rating = number_format($rating, 2);
                $seller_info['seller_rating'] = $rating;

                $seller_info['is_review_page'] = true;
                $this->context->smarty->assign('seller', $seller_info);

                if ($review_count > 0) {
                    $start = 1;
                    if ((int)Tools::getValue('page_number', 0) > 0) {
                        $start = (int)Tools::getValue('page_number', 0);
                    }

                    $paging = KbGlobal::getPaging($review_count, $start, $this->page_limit, false, 'getSReview2User');

                    $reviews = KbSellerReview::getReviewsBySellerId(
                        $id_seller,
                        $this->context->language->id,
                        KbGlobal::APPROVED,
                        false,
                        false,
                        $paging['page_position'],
                        $this->page_limit
                    );

                    $this->context->smarty->assign('reviews', $reviews);
                    $this->context->smarty->assign('kb_pagination', $paging);
                }

                $review_setting = KbSellerSetting::getSellerSettingByKey($id_seller, 'kbmp_enable_seller_review');
                if ($review_setting == 1) {
                    $this->context->smarty->assign('display_new_review', true);
                    $this->context->smarty->assign('display_review_popup', false);
                } else {
                    $this->context->smarty->assign('display_new_review', false);
                }

                $state_name = '';
                if (!empty($seller_info['state'])) {
                    $state_name = $seller_info['state'];
                }

                $country_name = '';
                if (!empty($seller_info['id_country'])) {
                    $country_name = Country::getNameById($this->context->language->id, $seller_info['id_country']);
                }

                $seller_info['state'] = $state_name;
                $seller_info['country'] = $country_name;

                $this->context->smarty->assign('seller', $seller_info);
                $this->setKbTemplate('seller/seller_reviews_to_customer.tpl');
            }
        }
    }

    protected function saveNewReview($id_seller)
    {
        if (Configuration::get('KB_MP_ENABLE_SELLER_REVIEW') != 1) {
            $this->context->cookie->__set(
                'redirect_error',
                $this->module->l(
                    'Review can not be submitted as this feature is disabled by Admin.'
                )
            );
            return;
        }
        if (Tools::getValue('review_title') != strip_tags(Tools::getValue('review_title'))) {
            $title = strip_tags(Tools::getValue('review_title'));
        } else {
            $title = Tools::getValue('review_title');
        }
        if (Tools::getValue('review_content') != strip_tags(Tools::getValue('review_content'))) {
            $comment = strip_tags(Tools::getValue('review_content'));
        } else {
            $comment = Tools::getValue('review_content');
        }
        $rating = (int)Tools::getValue('review_rating');
        $new_review = new KbSellerReview();
        $new_review->title = $title;
        $new_review->comment = $comment;
        $new_review->rating = $rating;
        $new_review->id_seller = $id_seller;
        $new_review->id_customer = (int)$this->context->customer->id;
        $new_review->id_shop = $this->context->shop->id;
        $new_review->id_lang = $this->context->language->id;
        $approved = KbSellerSetting::getSellerSettingByKey($id_seller, 'kbmp_seller_review_approval_required');
        if ($approved == 1) {
            $new_review->approved = (string)KbGlobal::APPROVAL_WAITING;
        } else {
            $new_review->approved = (string)KbGlobal::APPROVED;
        }

        $this->sendNewReviewMail($id_seller, $approved);

        if ($new_review->save()) {
            if ($approved == 1) {
                $this->context->cookie->__set(
                    'redirect_success',
                    $this->module->l(
                        'Your review has been submitted successfully. It will be shown after approval.'
                    )
                );
            } else {
                $this->context->cookie->__set(
                    'redirect_success',
                    $this->module->l('Your review has been submitted successfully.')
                );
            }
        } else {
            $this->context->cookie->__set(
                'redirect_error',
                $this->module->l(
                    'At this time, system not able to save new review. Please try again later'
                )
            );
        }
    }

    protected function sendNewReviewMail($id_seller, $approved)
    {
        $seller = new KbSeller($id_seller);
        $seller_info = $seller->getSellerInfo();
        if (Tools::getValue('review_title') != strip_tags(Tools::getValue('review_title'))) {
            $review_title = strip_tags(Tools::getValue('review_title'));
        } else {
            $review_title = Tools::getValue('review_title');
        }
        if (Tools::getValue('review_content') != strip_tags(Tools::getValue('review_content'))) {
            $review_content = strip_tags(Tools::getValue('review_content'));
        } else {
            $review_content = Tools::getValue('review_content');
        }
        if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
            $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
            $custom_ssl_var = 1;
        }
        if ((bool) Configuration::get('PS_SSL_ENABLED') && $custom_ssl_var == 1) {
            $uri_path = _PS_BASE_URL_SSL_ . __PS_BASE_URI__;
        } else {
            $uri_path = _PS_BASE_URL_ . __PS_BASE_URI__;
        }

        $template_vars = array(
            '{{seller_name}}' => $seller_info['seller_name'],
            '{{seller_email}}' => $seller_info['email'],
            '{{shop_title}}' => $seller_info['title'],
            '{{seller_contact}}' => $seller_info['phone_number'],
            '{{review_title}}' => $review_title,
            '{{review_comment}}' => $review_content,
            '{shop_url}' => $uri_path
        );
//        print_r($template_vars);die;
        if ($approved == KbGlobal::APPROVAL_WAITING) {
            //send email to admin for approval
            $email = new KbEmail(
                KbEmail::getTemplateIdByName('mp_seller_review_approval_request_admin'),
                $seller_info['id_default_lang']
            );
            $email->send(
                Configuration::get('PS_SHOP_EMAIL'),
                Configuration::get('PS_SHOP_NAME'),
                null,
                $template_vars
            );
        }

        //send email to Seller
        $email = new KbEmail(
            KbEmail::getTemplateIdByName('mp_seller_review_notification'),
            $seller_info['id_default_lang']
        );
        $notification_emails = $seller->getEmailIdForNotification();
        foreach ($notification_emails as $em) {
            $email->send(($em['email']), ($em['title']), null, $template_vars);
        }
    }

    public function renderSellersProducts()
    {
        $id_seller = Tools::getValue('id_seller', 0);
        if ((int)$id_seller > 0) {
            $seller = new KbSeller($id_seller);
            if ($seller->isSeller()) {
                $seller_info = $seller->getSellerInfo();
                $title = sprintf($this->module->l('Seller Shop - %s'), $seller_info['title']);
                $this->context->smarty->assign('kb_page_title', $title);
                $id_category = Tools::getValue('s_filter_category', '');
                $filters = array();

                if ((int)$id_category > 0) {
                    $filters['id_category'] = (int)$id_category;
                }

                $this->context->smarty->assign('selected_category', $id_category);

                $total_records = KbSellerProduct::getProductsWithDetails(
                    $id_seller,
                    $this->context->language->id,
                    $filters,
                    true
                );

                $sort_by = array('by' => 'pl.name', 'way' => 'ASC');
                $seleted_sort = '';
                if (Tools::getIsset('s_filter_sortby') && Tools::getValue('s_filter_sortby')) {
                    $seleted_sort = Tools::getValue('s_filter_sortby');
                    $explode = explode(':', Tools::getValue('s_filter_sortby'));
                    $sort_by['by'] = $explode[0];
                    $sort_by['way'] = $explode[1];
                }

                $this->context->smarty->assign('selected_sort', $seleted_sort);

                $start = 1;
                if ((int)Tools::getValue('page_number', 0) > 0) {
                    $start = (int)Tools::getValue('page_number', 0);
                }

                $this->context->smarty->assign('seller_product_current_page', $start);

                $paging = KbGlobal::getPaging($total_records, $start, $this->page_limit, false, 'getSProduct2User');

                $products = KbSellerProduct::getProductsWithDetails(
                    $id_seller,
                    $this->context->language->id,
                    $filters,
                    false,
                    $paging['page_position'],
                    $this->page_limit,
                    $sort_by['by'],
                    $sort_by['way']
                );

                $products = Product::getProductsProperties((int)$this->context->language->id, $products);
                $products = array_map(array($this, 'prepareProductForTemplate'), $products);

                $this->context->smarty->assign('products', $products);

                if ($products && count($products) > 0) {
                    $pagination_string = sprintf(
                        $this->module->l('Showing %d - %d of %d items'),
                        $paging['paging_summary']['record_start'],
                        $paging['paging_summary']['record_end'],
                        $total_records
                    );

                    $this->context->smarty->assign('pagination_string', $pagination_string);
                }

                $this->context->smarty->assign('kb_pagination', $paging);

                $this->context->smarty->assign(
                    'filter_form_action',
                    $this->context->link->getModuleLink(
                        $this->kb_module_name,
                        $this->controller_name,
                        array('render_type' => 'sellerproducts', 'id_seller' => $id_seller),
                        (bool)Configuration::get('PS_SSL_ENABLED')
                    )
                );

                $this->context->smarty->assign('category_list', $this->getCategoryList());
                $this->setKbTemplate('seller/products_to_customer.tpl');
            }
        }
    }

    public function getAjaxSellerListHtml()
    {
        $start = 1;
        if (Tools::getIsset('start') && (int)Tools::getValue('start') > 0) {
            $start = Tools::getValue('start');
        }

        $total = KbSeller::getSellers(true, true, null, null, null, null, true, true);

        if ($total > 0) {
            $paging = KbGlobal::getPaging($total, $start, $this->page_limit, false, 'getSellerList');

            $orderby = null;
            if (Tools::getIsset('orderby') && Tools::getValue('orderby') != '') {
                $orderby = Tools::getValue('orderby');
            }

            $orderway = null;
            if (Tools::getIsset('orderway') && Tools::getValue('orderway') != '') {
                $orderway = Tools::getValue('orderway');
            }

            $sellers = KbSeller::getSellers(
                false,
                true,
                $paging['page_position'],
                $this->page_limit,
                $orderby,
                $orderway,
                true,
                true
            );

            foreach ($sellers as $key => $val) {
                $base_link = KbGlobal::getBaseLink((bool)Configuration::get('PS_SSL_ENABLED'));
                $profile_default_image_path = $base_link . 'modules/' . $this->module->name . '/' . 'views/img/';
                $seller_image_path = _PS_IMG_DIR_ . KbSeller::SELLER_PROFILE_IMG_PATH . $val['id_seller'] . '/';
                if (empty($val['logo'])
                    || !Tools::file_exists_no_cache($seller_image_path . $val['logo'])) {
                    $sellers[$key]['logo'] = $profile_default_image_path . KbGlobal::SELLER_DEFAULT_LOGO;
                } else {
                    $sellers[$key]['logo'] = $this->seller_image_path . $val['id_seller'] . '/' . $val['logo'];
                }

                if ($val['title'] == '' || empty($val['title'])) {
                    $sellers[$key]['title'] = $this->module->l('Not Mentioned');
                }

                $sellers[$key]['href'] = KbGlobal::getSellerLink($val['id_seller']);

                $review_setting = KbSellerSetting::getSellerSettingByKey(
                    $val['id_seller'],
                    'kbmp_enable_seller_review'
                );

                if ($review_setting == 1) {
                    $sellers[$key]['display_write_review'] = true;
                } else {
                    $sellers[$key]['display_write_review'] = false;
                }

                $sellers[$key]['view_review_href'] = $this->context->link->getModuleLink(
                    $this->kb_module_name,
                    $this->controller_name,
                    array('render_type' => 'sellerreviews', 'id_seller' => $val['id_seller'])
                );

                if ((int)$val['total_review'] > 0) {
                    $tmp = (int)$val['total_review'];
                    $sellers[$key]['rating_percent'] = (float)((($val['rating'] / $tmp) / 5) * 100);
                } else {
                    $sellers[$key]['rating_percent'] = 0;
                }
            }

            $this->context->smarty->assign('sellers', $sellers);
            $pagination_string = sprintf(
                $this->module->l('Showing %d - %d of %d items'),
                $paging['paging_summary']['record_start'],
                $paging['paging_summary']['record_end'],
                $total
            );
            $this->json['pagination_string'] = $pagination_string;
            $this->json['kb_pagination'] = $paging;
            $this->setKbTemplate('seller/seller_list.tpl');
            
            return $this->fetchTemplate();
        }
    }
    
    private function prepareProductForTemplate(array $rawProduct)
    {
        $pro_assembler = new ProductAssembler($this->context);
        $product = $pro_assembler->assembleProduct($rawProduct);

        $factory = new ProductPresenterFactory($this->context, new TaxConfiguration());
        $presenter = $factory->getPresenter();
        $settings = $factory->getPresentationSettings();

        return $presenter->present(
            $settings,
            $product,
            $this->context->language
        );
    }
}
