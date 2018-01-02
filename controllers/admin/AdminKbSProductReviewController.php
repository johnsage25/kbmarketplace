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

require_once dirname(__FILE__) . '/AdminKbMarketplaceCoreController.php';

class AdminKbSProductReviewController extends AdminKbMarketplaceCoreController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'kb_mp_seller_product_review';
        $this->className = 'KbSellerProductReview';
        $this->identifier = 'id_seller_product_review';
        $this->lang = false;
        $this->display = 'list';
        $this->allow_export = true;
        $this->context = Context::getContext();
        
        parent::__construct();
        $this->toolbar_title = $this->module->l('Product Reviews');

        $this->_select = 'pc.`id_product_comment`, 
			IF(c.id_customer, CONCAT(c.`firstname`, \' \',  c.`lastname`), pc.customer_name) customer_name, 
			pc.`title`, pc.`content`, pc.`grade`, pc.`date_add`, pl.`name`, pc.`validate`';

        if (Tools::getIsset('id_seller') && Tools::getValue('id_seller') > 0) {
            $this->_join .= ' INNER JOIN ' . _DB_PREFIX_ . 'kb_mp_seller as sr ON (a.`id_seller` = sr.`id_seller` 
				AND sr.id_seller = ' . (int)Tools::getValue('id_seller') . ')';
        } else {
            $this->_select .=', CONCAT(LEFT(sn.`firstname`, 1), \'. \', sn.`lastname`) AS `seller_name`';
            $this->_join .= ' INNER JOIN ' . _DB_PREFIX_ . 'kb_mp_seller as sr on (a.id_seller = sr.id_seller)';
        }

        $this->_join .= '
			INNER JOIN ' . _DB_PREFIX_ . 'product_comment as pc on (a.id_product_comment = pc.id_product_comment) 
			INNER JOIN `' . _DB_PREFIX_ . 'customer` sn ON (sr.`id_customer` = sn.`id_customer`) 
			LEFT JOIN `' . _DB_PREFIX_ . 'customer` c ON (c.`id_customer` = pc.`id_customer`) 
			LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (pl.`id_product` = pc.`id_product` 
			AND pl.`id_lang` = ' . (int)$this->context->language->id . Shop::addSqlRestrictionOnLang('pl') . ')';

        $this->_orderBy = 'a.id_seller_product_review';
        $this->_orderWay = 'DESC';

        $ratings = array('0' => '0', '0.5' => '0.5', '1' => '1', '1.5' => '1.5', '2' => '2', '2.5' => '2.5',
            '3' => '3', '3.5' => '3.5', '4' => '4', '4.5' => '4.5', '5' => '5');

        $this->fields_list = array(
            'id_seller_product_review' => array(
                'width' => 'auto',
                'title' => $this->module->l('ID'),
                'align' => 'text-center',
                'class' => 'fixed-width-xs'
            ),
            'name' => array(
                'title' => $this->module->l('Product'),
                'havingFilter' => true,
                'filter_key' => 'pl!name',
                'order_key' => 'pl.name',
            ),
            'seller_name' => array(
                'title' => $this->module->l('Seller'),
                'havingFilter' => true,
                'filter_key' => 'seller_name',
                'order_key' => 'seller_name',
            ),
            'customer_name' => array(
                'title' => $this->module->l('Customer'),
                'havingFilter' => true,
                'filter_key' => 'customer_name',
                'order_key' => 'customer_name',
            ),
            'title' => array(
                'title' => $this->module->l('Title'),
                'havingFilter' => true,
                'filter_key' => 'pc!title',
                'order_key' => 'pc.title',
            ),
            'content' => array(
                'title' => $this->module->l('Comment'),
                'havingFilter' => false,
                'class' => 'comment_col_w',
                'maxlength' => 200
            ),
            'grade' => array(
                'title' => $this->module->l('Rating'),
                'havingFilter' => true,
                'type' => 'select',
                'list' => $ratings,
                'callback' => 'showRating',
                'filter_type' => 'float',
                'filter_key' => 'pc!grade',
                'order_key' => 'pc.grade'
            ),
            'validate' => array(
                'title' => $this->module->l('Status'),
                'havingFilter' => true,
                'type' => 'select',
                'list' => array(
                    KbGlobal::APPROVAL_WAITING => KbGlobal::getApporvalStatus(KbGlobal::APPROVAL_WAITING),
                    KbGlobal::APPROVED => KbGlobal::getApporvalStatus(KbGlobal::APPROVED)
                    ),
                'callback' => 'getReviewStatus',
                'filter_type' => 'int',
                'filter_key' => 'pc!validate',
                'order_key' => 'pc.validate'
            ),
            'date_add' => array(
                'title' => $this->module->l('Added'),
                'havingFilter' => true,
            )
        );

        $this->addRowAction('viewmodal');
        $this->addRowAction('approve');
        $this->addRowAction('deletewreason');

        if (!Module::isInstalled('productcomments')) {
            $this->_select = null;
            $this->_join = null;
            $this->_orderBy = null;
            $this->_orderWay = null;
            $this->fields_list = null;
        }
    }

    public function initProcess()
    {
        parent::initProcess();
        if (Tools::getIsset('approve' . $this->table)) {
            $this->action = 'approve';
        }
    }

    public function postProcess()
    {
        parent::postProcess();
    }

    public function setMedia()
    {
        parent::setMedia();
        $this->addJqueryPlugin('fancybox');
    }

    public function initContent()
    {
        if (Module::isInstalled('productcomments')) {
            $tpl = $this->custom_smarty->createTemplate('ajax_view_popup.tpl');

            $this->content .= $tpl->fetch();
        } else {
            $this->errors[] = $this->module->l('Product Comment module is not installed. Please install it first');
        }

        parent::initContent();
    }

    public function initToolbar()
    {
        parent::initToolbar();
        unset($this->toolbar_btn['new']);
    }

    /*
     * render rating star
     */

    public function showRating($id_row, $tr)
    {
        unset($id_row);
        $width = (float)((float)$tr['grade'] / 5) * 100;
        return '<div class="vss_seller_ratings"><div class="vss_rating_unfilled">★★★★★</div>
			<div class="vss_rating_filled" style="width:' . $width . '%">★★★★★</div></div>';
    }

    /*
     * render rating star
     */

    public function getReviewStatus($id_row, $tr)
    {
        unset($id_row);
        if ($tr['validate'] == 0) {
            return $this->approval_statuses[0];
        } else {
            return $this->approval_statuses[1];
        }
    }

    public function processKbAjaxView()
    {
        $this->render_ajax_html = true;

        $id_review = (int)Tools::getValue($this->identifier);

        $review = new $this->className($id_review);

        $sql = 'Select pl.`name`, pc.id_product_comment, pc.`title`, pc.`content`, pc.`grade`, pc.`date_add`, 
			IF(c.id_customer, CONCAT(c.`firstname`, \' \',  c.`lastname`), pc.customer_name) as customer_name 
			from ' . _DB_PREFIX_ . 'product_comment as pc 
			LEFT JOIN `' . _DB_PREFIX_ . 'customer` c ON (c.`id_customer` = pc.`id_customer`) 
			LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl 
			ON (
				pl.`id_product` = pc.`id_product` 
				AND pl.`id_lang` = ' . (int)$this->context->language->id . Shop::addSqlRestrictionOnLang('pl') . ') 
			WHERE pc.id_product_comment = ' . (int)$review->id_product_comment;

        $data = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql, true);

        if ($data && is_array($data)) {
            $data['overall_grade_percent'] = number_format(KbGlobal::convertRatingIntoPercent($data['grade']), 2);
            $data['date_add'] = Tools::displayDate($data['date_add'], null, true);
            $data['title'] = Tools::safeOutput($data['title'], true);
            $data['content'] = Tools::safeOutput($data['content'], true);
            $indivual_grades = array();
            //get Individual grade
            $sql = 'Select pcg.*, pcc.name from ' . _DB_PREFIX_ . 'product_comment_grade as pcg 
				INNER JOIN ' . _DB_PREFIX_ . 'product_comment_criterion_lang as pcc 
				ON (pcg.id_product_comment_criterion = pcc.id_product_comment_criterion) 
				WHERE pcg.id_product_comment = ' . (int)$data['id_product_comment']
                . ' AND pcc.id_lang = ' . (int)$this->context->language->id;

            $results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
            if ($results && count($results) > 1) {
                foreach ($results as &$result) {
                    $result['grade_percent'] = number_format(KbGlobal::convertRatingIntoPercent($result['grade']), 2);
                }
                $indivual_grades = $results;
            }

            $tpl = $this->custom_smarty->createTemplate('view_product_comment.tpl');

            $tpl->assign(array(
                'data' => $data,
                'individual_grades' => $indivual_grades,
                'post_on_title' => $this->module->l('Posted on'),
                'by_title' => $this->module->l('by'),
                'overall_title' => $this->module->l('Overall Rating'),
                'summary_title' => $this->module->l('Summary'),
                'comment_title' => $this->module->l('Comment')
            ));
            return $tpl->fetch();
        }

        return Tools::displayError('Data Not Found');
    }

    public function processApprove()
    {
        if (Tools::getIsset($this->identifier)) {
            $object = new $this->className(Tools::getValue($this->identifier));
            try {
                Db::getInstance(_PS_USE_SQL_SLAVE_)->update(
                    'product_comment',
                    array('validate' => 1),
                    'id_product_comment = '.(int)$object->id_product_comment
                );
                Hook::exec('actionKbMarketPlaceProductReviewApprove', array('object' => $object));
                $this->context->cookie->__set(
                    'kb_redirect_success',
                    $this->module->l('Review has been approved and ready to display on front.')
                );
            } catch (Exception $e) {
                $this->context->cookie->__set(
                    'kb_redirect_error',
                    $e->getMessage()
                );
            }
        } else {
            $this->context->cookie->__set(
                'kb_redirect_error',
                $this->module->l('Not able to create object.')
            );
        }
        Tools::redirectAdmin($this->context->link->getAdminLink('AdminKbSProductReview'));
    }

    public function processDelete()
    {
        if (Tools::getIsset($this->identifier)) {
            $object = new $this->className(Tools::getValue($this->identifier));
            try {
                Db::getInstance(_PS_USE_SQL_SLAVE_)->delete(
                    'product_comment',
                    'id_product_comment = '.(int)$object->id_product_comment
                );
                Db::getInstance(_PS_USE_SQL_SLAVE_)->delete(
                    'product_comment_grade',
                    'id_product_comment = '.(int)$object->id_product_comment
                );
                Db::getInstance(_PS_USE_SQL_SLAVE_)->delete(
                    'product_comment_report',
                    'id_product_comment = '.(int)$object->id_product_comment
                );
                Db::getInstance(_PS_USE_SQL_SLAVE_)->delete(
                    'product_comment_usefulness',
                    'id_product_comment = '.(int)$object->id_product_comment
                );
                
                $object->delete();

                Hook::exec(
                    'actionKbMarketPlaceProductCommentDelete',
                    array(
                        'id_seller_product_review' => Tools::getValue($this->identifier),
                        'comment_id' => $object->id_product_comment
                    )
                );
                $this->context->cookie->__set(
                    'kb_redirect_success',
                    $this->module->l('Review has been deleted.')
                );
            } catch (Exception $e) {
                $this->context->cookie->__set(
                    'kb_redirect_error',
                    $e->getMessage()
                );
            }
        } else {
            $this->context->cookie->__set(
                'kb_redirect_error',
                $this->module->l('Not able to create object.')
            );
        }
        Tools::redirectAdmin($this->context->link->getAdminLink('AdminKbSProductReview'));
    }
}
