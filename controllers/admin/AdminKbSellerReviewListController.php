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

class AdminKbSellerReviewListController extends AdminKbMarketplaceCoreController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'kb_mp_seller_review';
        $this->className = 'KbSellerReview';
        $this->identifier = 'id_seller_review';
        $this->lang = false;
        $this->display = 'list';
        $this->allow_export = true;
        $this->context = Context::getContext();
        
        parent::__construct();

        $this->toolbar_title = $this->module->l('Seller Reviews List');
        $this->_select = 'CONCAT(c.`firstname`, \' \',  c.`lastname`) as customer_name';

        $this->_select .=', CONCAT(LEFT(sn.`firstname`, 1), \'. \', sn.`lastname`) AS `seller_name`';
        $this->_join .= ' INNER JOIN ' . _DB_PREFIX_ . 'kb_mp_seller as sr on (a.id_seller = sr.id_seller)';

        $this->_join .= '
			INNER JOIN `' . _DB_PREFIX_ . 'customer` sn ON (sr.`id_customer` = sn.`id_customer`) 
			LEFT JOIN `' . _DB_PREFIX_ . 'customer` c ON (c.`id_customer` = a.`id_customer`)';

        $this->_where .= 'AND a.approved = "' . (int) KbGlobal::APPROVED . '"';

        $this->_orderBy = 'a.id_seller_review';
        $this->_orderWay = 'DESC';

        $ratings = array('0' => '0', '1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5');

        $this->fields_list = array(
            'id_seller_review' => array(
                'title' => $this->module->l('ID'),
                'align' => 'text-center',
                'class' => 'fixed-width-xs'
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
                'filter_key' => 'a!title',
                'order_key' => 'a.title',
            ),
            'comment' => array(
                'title' => $this->module->l('Comment'),
                'havingFilter' => false,
                'class' => 'comment_col_w',
                'maxlength' => 100
            ),
            'rating' => array(
                'title' => $this->module->l('Rating'),
                'havingFilter' => true,
                'type' => 'select',
                'list' => $ratings,
                'callback' => 'showRating',
                'filter_type' => 'float',
                'filter_key' => 'a!rating',
                'order_key' => 'a.rating'
            ),
            'date_add' => array(
                'title' => $this->module->l('Added'),
                'havingFilter' => true,
            )
        );

        $this->addRowAction('viewmodal');
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
        $tpl = $this->custom_smarty->createTemplate('ajax_view_popup.tpl');

        $this->content .= $tpl->fetch();

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
        $width = (float)((float)$tr['rating'] / 5) * 100;
        return '<div class="vss_seller_ratings"><div class="vss_rating_unfilled">★★★★★</div>
			<div class="vss_rating_filled" style="width:' . $width . '%">★★★★★</div></div>';
    }

    public function processKbAjaxView()
    {
        $this->render_ajax_html = true;
        $id_seller_review = (int)Tools::getValue('id_seller_review');

        $review = new KbSellerReview($id_seller_review);

        $tpl = $this->custom_smarty->createTemplate('view_seller_comment.tpl');

        $rating = KbGlobal::convertRatingIntoPercent((int)$review->rating);

        $tpl->assign(array(
            'posted_on' => Tools::displayDate($review->date_add, null, true),
            'seller_name' => $review->seller['seller_name'],
            'seller_title' => $review->seller['title'],
            'review_title' => $review->title,
            'review_comment' => Tools::safeOutput($review->comment, true),
            'rating_percent' => number_format($rating, 2),
            'customer_name' => $review->customer['name'],
            'customer_email' => $review->customer['email'],
            'post_on_title' => $this->module->l('Posted on'),
            'by_title' => $this->module->l('by'),
            'rating_title' => $this->module->l('Rating'),
            'summary_title' => $this->module->l('Summary'),
            'comment_title' => $this->module->l('Comment')
        ));

        return $tpl->fetch();
    }
}
