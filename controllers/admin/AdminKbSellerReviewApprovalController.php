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

class AdminKbSellerReviewApprovalController extends AdminKbMarketplaceCoreController
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
        $this->toolbar_title = 'Seller Reviews Approval List';

        parent::__construct();
        $this->_select = 'CONCAT(c.`firstname`, \' \',  c.`lastname`) as customer_name';

        $this->_select .=', CONCAT(LEFT(sn.`firstname`, 1), \'. \', sn.`lastname`) AS `seller_name`';
        $this->_join .= ' INNER JOIN ' . _DB_PREFIX_ . 'kb_mp_seller as sr on (a.id_seller = sr.id_seller)';

        $this->_join .= '
			INNER JOIN `' . _DB_PREFIX_ . 'customer` sn ON (sr.`id_customer` = sn.`id_customer`) 
			LEFT JOIN `' . _DB_PREFIX_ . 'customer` c ON (c.`id_customer` = a.`id_customer`)';

        $this->_where .= ' AND a.approved IN ( "' .
            (int) KbGlobal::APPROVAL_WAITING . '","' . (int) KbGlobal::DISSAPPROVED . '")';

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
                'class' => 'kb_mp_html_decode'
            ),
            'comment' => array(
                'title' => $this->module->l('Comment'),
                'havingFilter' => false,
                'type'=>'text',
                'class' => 'comment_col_w kb_mp_html_decode',
                'maxlength' => 100,
//                'callback' => 'showReviewComment',
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
            'approved' => array(
                'title' => $this->module->l('Status'),
//                'havingFilter' => true,
                'type' => 'select',
                'list' => $this->approval_statuses,
                'callback' => 'showApprovedStatus',
                'filter_type' => 'text',
                'filter_key' => 'a!approved',
                'order_key' => 'a.approved'
            ),
            'date_add' => array(
                'title' => $this->module->l('Added'),
                'havingFilter' => true,
            )
        );

        $this->addRowAction('viewmodal');
        $this->addRowAction('approve');
        $this->addRowAction('disapprovesellerreview');
        $this->addRowAction('delete');
    }
    
//    public function showReviewComment($echo)
//    {
////        unset($id_row);
////        return Tools::substr($echo, 0, 100);
//    }

    public function initProcess()
    {
        parent::initProcess();
        if (Tools::getIsset('approve' . $this->table)) {
            $this->action = 'approveReview';
        } elseif (Tools::getIsset('dissapprove' . $this->table)) {
            $this->action = 'dissapproveReview';
        } elseif (Tools::getIsset('delete' . $this->table)) {
            $this->action = 'delete';
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
        $tpl = $this->custom_smarty->createTemplate('ajax_view_popup.tpl');

        $this->content .= $tpl->fetch();
        $this->content .= $this->getReasonPopUpHtml();
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

        $review          = new KbSellerReview($id_seller_review);
        $review->title   = Tools::htmlentitiesDecodeUTF8($review->title);
        $review->comment = Tools::htmlentitiesDecodeUTF8($review->comment);

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

    public function processApproveReview()
    {
        if (Tools::getIsset($this->identifier)) {
            $object = new $this->className(Tools::getValue($this->identifier));
            $object->approved = (string)KbGlobal::APPROVED;

            if ($object->save()) {
                //send email to customer
                $template_vars = array(
                    '{{customer_name}}' => $object->customer['name'],
                    '{{store_name}}' => Configuration::get('PS_SHOP_NAME'),
                    '{{comment}}' => $object->comment
                );
                $email = new KbEmail(
                    KbEmail::getTemplateIdByName('mp_seller_review_approved_to_customer'),
                    $object->customer['id_lang']
                );
                $email->send($object->customer['email'], $object->customer['name'], null, $template_vars);

                //send email to Seller
                $template_vars = array(
                    '{{seller_name}}' => $object->seller['seller_name'],
                    '{{store_name}}' => Configuration::get('PS_SHOP_NAME'),
                    '{{comment}}' => $object->comment
                );
                $email = new KbEmail(
                    KbEmail::getTemplateIdByName('mp_seller_review_approved_to_seller'),
                    $object->seller['id_default_lang']
                );
                $sell_obj = new KbSeller($object->id_seller);
                $notification_emails = $sell_obj->getEmailIdForNotification();
                foreach ($notification_emails as $em) {
                    $email->send($em['email'], $em['title'], null, $template_vars);
                }

                $this->context->cookie->__set(
                    'kb_redirect_success',
                    $this->module->l('Review has been successfully approved.')
                );
            } else {
                $this->context->cookie->__set(
                    'kb_redirect_error',
                    $this->module->l('Error occurred while approving review')
                );
            }
        }
        Tools::redirectAdmin($this->context->link->getAdminLink('AdminKbSellerReviewApproval'));
    }

    public function processDissapproveReview()
    {
        if (Tools::getIsset($this->identifier)) {
            $object = new $this->className(Tools::getValue($this->identifier));
            $object->approved = (string)KbGlobal::DISSAPPROVED;

            if ($object->save()) {
                if (Tools::getValue('marketplace_reason_comment', '') !=
                    strip_tags(Tools::getValue('marketplace_reason_comment', ''))) {
                    $reason_comment = strip_tags(Tools::getValue('marketplace_reason_comment', ''));
                } else {
                    $reason_comment = Tools::getValue('marketplace_reason_comment', '');
                }
                //send email to customer
                $template_vars = array(
                    '{{customer_name}}' => $object->customer['name'],
                    '{{comment}}' => $object->comment,
                    '{{reason}}' => $reason_comment,
                    '{{store_name}}' => Configuration::get('PS_SHOP_NAME'),
                    '{shop_name}' => $object->seller['title']
                );
                $email = new KbEmail(
                    KbEmail::getTemplateIdByName('mp_seller_review_disspproved_to_customer'),
                    $object->customer['id_lang']
                );
                $email->send($object->customer['email'], $object->customer['name'], null, $template_vars);

                //send email to Seller
                $template_vars = array(
                    '{{seller_name}}' => $object->seller['seller_name'],
                    '{{seller_email}}' => $object->seller['email'],
                    '{{comment}}' => $object->comment,
                    '{{reason}}' => $reason_comment,
                    '{{store_name}}' => Configuration::get('PS_SHOP_NAME'),
                    '{shop_name}' => $object->seller['title']
                );
                $email = new KbEmail(
                    KbEmail::getTemplateIdByName('mp_seller_review_disspproved_to_seller'),
                    $object->seller['id_default_lang']
                );
                $sell_obj = new KbSeller($object->id_seller);
                $notification_emails = $sell_obj->getEmailIdForNotification();
                foreach ($notification_emails as $em) {
                    $email->send($em['email'], $em['title'], null, $template_vars);
                }

                $reason_log = new KbReasonLog();
                $reason_log->reason_type = 4;
                $reason_log->id_seller = $object->id_seller;
                $reason_log->id_seller_review = $object->id;
                $reason_log->id_employee = $this->context->employee->id;
                $reason_log->comment = $reason_comment;
                $reason_log->save(true);

                $this->context->cookie->__set(
                    'kb_redirect_success',
                    $this->module->l('Review has been disapproved successfully.')
                );
            } else {
                $this->context->cookie->__set(
                    'kb_redirect_error',
                    $this->module->l('Error occurred while disapproving review')
                );
            }
        }
        Tools::redirectAdmin($this->context->link->getAdminLink('AdminKbSellerReviewApproval'));
    }

    public function processDelete()
    {
        if (Tools::getIsset($this->identifier)) {
            //send email to customer
            $object = $del_object = new $this->className(Tools::getValue($this->identifier));
            $error = '';
            if ($del_object->delete()) {
                if (Tools::getValue('marketplace_reason_comment', '') !=
                    strip_tags(Tools::getValue('marketplace_reason_comment', ''))) {
                    $reason_comment = strip_tags(Tools::getValue('marketplace_reason_comment', ''));
                } else {
                    $reason_comment = Tools::getValue('marketplace_reason_comment', '');
                }
                $template_vars = array(
                    '{{seller_name}}' => $object->seller['seller_name'],
                    '{{customer_name}}' => $object->customer['name'],
                    '{{store_name}}' => Configuration::get('PS_SHOP_NAME'),
                    '{shop_name}' => $object->seller['title'],
                    '{{comment}}' => $object->comment,
                    '{{reason}}' => $reason_comment
                );
                $email = new KbEmail(
                    KbEmail::getTemplateIdByName('mp_seller_review_delete_to_customer'),
                    $object->customer['id_lang']
                );
                $email->send($object->customer['email'], $object->customer['name'], null, $template_vars);

                //send email to Seller
                $template_vars = array(
                    '{{seller_name}}' => $object->seller['seller_name'],
                    '{{seller_email}}' => $object->seller['email'],
                    '{{store_name}}' => Configuration::get('PS_SHOP_NAME'),
                    '{shop_name}' => $object->seller['title'],
                    '{{comment}}' => $object->comment,
                    '{{reason}}' => $reason_comment,
                );
                $email = new KbEmail(
                    KbEmail::getTemplateIdByName('mp_seller_review_delete_to_seller'),
                    $object->seller['id_default_lang']
                );
                $sell_obj = new KbSeller($object->id_seller);
                $notification_emails = $sell_obj->getEmailIdForNotification();
                foreach ($notification_emails as $em) {
                    $email->send($em['email'], $em['title'], null, $template_vars);
                }
            } else {
                $error = $this->module->l('Not able to delete review.');
            }

            if ($error == '') {
                $this->context->cookie->__set(
                    'kb_redirect_success',
                    $this->module->l('Review has been deleted from store.')
                );
            } else {
                $this->context->cookie->__set('kb_redirect_error', $error);
            }
        }
        Tools::redirectAdmin($this->context->link->getAdminLink('AdminKbSellerReviewApproval'));
    }
}
