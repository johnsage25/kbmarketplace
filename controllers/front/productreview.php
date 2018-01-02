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

class KbmarketplaceProductreviewModuleFrontController extends KbmarketplaceCoreModuleFrontController
{

    public $controller_name = 'productreview';

    public function __construct()
    {
        parent::__construct();
    }

    public function setMedia()
    {
        parent::setMedia();
    }

    public function postProcess()
    {
        parent::postProcess();
        if (Tools::isSubmit('ajax')) {
            $this->json = array();
            $renderhtml = false;
            if (Tools::isSubmit('method')) {
                switch (Tools::getValue('method')) {
                    case 'getSellerProductReviews':
                        $this->json = $this->getAjaxReviewsListHtml();
                        break;
                    case 'getProductReviewDetail':
                        $this->json = $this->getAjaxReviewDetail();
                        break;
                }
            }
            if (!$renderhtml) {
                echo Tools::jsonEncode($this->json);
            }
            die;
        } elseif (Tools::isSubmit('multiaction') && Tools::getValue('multiaction')) {
            $this->processMultiAction();
        }
    }

    public function initContent()
    {
        $this->renderList();
        parent::initContent();
    }

    public function getTemplateVarPage()
    {
        $page = parent::getTemplateVarPage();
        if (isset($page['meta']) && $this->seller_info) {
            $page_title = 'Product Reviews';
            $page['meta']['title'] =  $page_title;
            $page['meta']['keywords'] = $this->seller_info['meta_keyword'];
            $page['meta']['description'] = $this->seller_info['meta_description'];
        }
        return $page;
    }
    
    private function renderList()
    {
        if (Module::isInstalled('productcomments')) {
            $approve_statuses = array(
                array('value' => 0, 'label' => $this->module->l('Waiting for Approval')),
                array('value' => 1, 'label' => $this->module->l('Approved'))
            );

            $i = 0;
            $rating_array = array();
            while ($i <= KbGlobal::MAX_RATING) {
                $rating_array[] = array('value' => $i, 'label' => $i);
                $i++;
            }

            $this->filter_header = $this->module->l('Filter Your Search');
            $this->filter_id = 'seller_productreview_filter';
            $this->filters = array(
                array(
                    'type' => 'text',
                    'name' => 'start_date',
                    'class' => 'datepicker',
                    'label' => $this->module->l('From Date')
                ),
                array(
                    'type' => 'text',
                    'name' => 'to_date',
                    'class' => 'datepicker',
                    'label' => $this->module->l('To Date')
                ),
                array(
                    'type' => 'select',
                    'placeholder' => $this->module->l('Select'),
                    'name' => 'validate',
                    'label' => $this->module->l('Status'),
                    'values' => $approve_statuses
                ),
                array(
                    'type' => 'select',
                    'placeholder' => $this->module->l('Select'),
                    'name' => 'grade',
                    'label' => $this->module->l('Rating'),
                    'values' => $rating_array
                )
            );
            $this->filter_action_name = 'getSellerProductReviews';
            $this->context->smarty->assign('kbfilter', $this->renderKbListFilter());

            $this->table_id = $this->filter_id;
            $this->table_header = array(
                array(
                    'label' => $this->module->l('ID'),
                    'align' => 'right',
                    'width' => '60'
                ),
                array('label' => $this->module->l('Posted On'), 'width' => '80'),
                array('label' => $this->module->l('Product Name'), 'width' => '150'),
                array('label' => $this->module->l('Status'), 'width' => '150'),
                array('label' => $this->module->l('Comment')),
                array('label' => $this->module->l('Rating'), 'width' => '80')
            );

            $this->total_records = KbSellerProductReview::getReviewsBySeller(
                $this->seller_info['id_seller'],
                null,
                null,
                true
            );

            if ($this->total_records > 0) {
                $reviews = KbSellerProductReview::getReviewsBySeller(
                    $this->seller_info['id_seller'],
                    null,
                    false,
                    false,
                    $this->getPageStart(),
                    $this->tbl_row_limit
                );

                foreach ($reviews as $review) {
                    $read_more_link = '<a onclick="getProductReviewDetail(' . $review['id_seller_product_review']
                        . ')" href="javascript:void(0)" >' .
                        $this->module->l('Read More') . '</a>';

                    $rating_block = '<div class="vss_seller_ratings"><div class="vss_rating_unfilled">
                        ★★★★★</div><div class="vss_rating_filled" style="width:'
                        . $review['rating_percent'] . '%">★★★★★</div></div>';
                    $this->table_content[$review['id_seller_product_review']] = array(
                        array(
                            'link' => array(
                                'function' => 'getProductReviewDetail(' . $review['id_seller_product_review'] . ')',
                                'title' => $this->module->l('Click to view review')),
                            'value' => '#' . $review['id_seller_product_review'],
                            'class' => '',
                            'align' => 'kb-tright'
                        ),
                        array('value' => Tools::displayDate($review['date_add'], null, true)),
                        array('value' => $review['name']),
                        array('value' => KbGlobal::getApporvalStatus($review['validate'])),
                        array('value' => $this->clipLongText(Tools::safeOutput($review['content']), $read_more_link)),
                        array('value' => $rating_block),
                    );
                }

                $this->list_row_callback = $this->filter_action_name;

                $this->table_enable_multiaction = true;
                //Show Multi actions
                $this->kb_multiaction_params['multiaction_values'] = array(
                    array(
                    'label' => $this->module->l('Delete'),
                    'value' => KbGlobal::MULTI_ACTION_TYPE_DELETE)
                );
                $this->kb_multiaction_params['multiaction_related_to_table'] = $this->table_id;
                $this->kb_multiaction_params['submit_action'] = $this->context->link->getModuleLink(
                    $this->kb_module_name,
                    $this->controller_name,
                    array('multiaction' => true),
                    (bool)Configuration::get('PS_SSL_ENABLED')
                );
                $this->context->smarty->assign('kbmutiaction', $this->renderKbMultiAction());
            }

            $this->context->smarty->assign('kblist', $this->renderKbList());
            $this->context->smarty->assign('display_feature', true);
        } else {
            $this->context->smarty->assign('display_feature', false);
            $this->Kberrors[] = $this->module->l(
                'This feature is not active. Please contact to support.'
            );
        }

        $this->setKbTemplate('product/review/list.tpl');
    }

    protected function getAjaxReviewsListHtml()
    {
        $json = array();

        $custom_filter = '';
        if (Tools::getIsset('start_date') && Tools::getValue('start_date') != '') {
            $custom_filter .= ' AND DATE(spr.date_add) >= "'
                . pSQL(date('Y-m-d', strtotime(Tools::getValue('start_date')))) . '"';
        }

        if (Tools::getIsset('to_date') && Tools::getValue('to_date') != '') {
            $custom_filter .= ' AND DATE(spr.date_add) <= "'
                . pSQL(date('Y-m-d', strtotime(Tools::getValue('to_date')))) . '"';
        }

        if (Tools::getIsset('validate') && Tools::getValue('validate') != '') {
            $custom_filter .= ' AND pc.validate = "' . pSQL(Tools::getValue('validate')) . '"';
        }

        if (Tools::getIsset('grade') && Tools::getValue('grade') != '') {
            $custom_filter .= ' AND pc.grade = "' . pSQL(Tools::getValue('grade')) . '"';
        }

        $this->total_records = KbSellerProductReview::getReviewsBySeller(
            $this->seller_info['id_seller'],
            null,
            $custom_filter,
            true
        );
        if ($this->total_records > 0) {
            if (Tools::getIsset('start') && (int)Tools::getValue('start') > 0) {
                $this->page_start = (int)Tools::getValue('start');
            }

            $reviews = KbSellerProductReview::getReviewsBySeller(
                $this->seller_info['id_seller'],
                null,
                $custom_filter,
                false,
                $this->getPageStart(),
                $this->tbl_row_limit,
                $custom_filter
            );

            $row_html = '';
            foreach ($reviews as $review) {
                $read_more_link = '<a onclick="getProductReviewDetail(' . $review['id_seller_product_review']
                    . ')" href="javascript:void(0)" >'
                    . $this->module->l('Read More') . '</a>';

                $rating_block = '<div class="vss_seller_ratings"><div class="vss_rating_unfilled">★★★★★</div>
					<div class="vss_rating_filled" style="width:' . $review['rating_percent'] . '%">★★★★★</div></div>';
                $row_html .= '<tr>
						<td class="kb-tcenter">
                        <div class="checker"><span>
							<input type="checkbox" class="kb_list_row_checkbox" name="row_item_id[]" value="'
                    . $review['id_seller_product_review'] . '" title=""></span></div>
						</td>
						<td class=" kb-tright">
							<a href="javascript:void(0)" title="' .
                        $this->module->l('Click to view review')
                    . '" onclick="getProductReviewDetail(' . $review['id_seller_product_review'] . ')">#'
                    . $review['id_seller_product_review'] . '</a>
						</td>
						<td>' . Tools::displayDate($review['date_add'], null, true) . '</td>
						<td>' . $review['name'] . '</td>
						<td>' . KbGlobal::getApporvalStatus($review['validate']) . '</td>
						<td>' . $this->clipLongText(Tools::safeOutput($review['content']), $read_more_link) . '</td>
						<td>' . $rating_block . '</td>
                                    </tr>';
            }
            $this->table_id = 'seller_productreview_filter';
            $this->list_row_callback = 'getSellerProductReviews';
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

    protected function getAjaxReviewDetail()
    {
        $json = array();
        $id_review = (int)Tools::getValue('id_seller_product_review', 0);
        if ($id_review > 0) {
            $review = new KbSellerProductReview($id_review);

            $sql = 'Select pl.`name`, pc.id_product_comment, pc.`title`, pc.`content`, pc.`grade`, pc.`date_add`, 
				IF(c.id_customer, CONCAT(c.`firstname`, \' \',  c.`lastname`), pc.customer_name) as customer_name 
				from ' . _DB_PREFIX_ . 'product_comment as pc 
				LEFT JOIN `' . _DB_PREFIX_ . 'customer` c ON (c.`id_customer` = pc.`id_customer`) 
				LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl 
				ON (
					pl.`id_product` = pc.`id_product` 
					AND pl.`id_lang` = ' . (int)$review->id_lang . Shop::addSqlRestrictionOnLang('pl') .
                ') 
				WHERE pc.id_product_comment = ' . (int)$review->id_product_comment;

            $data = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql, true);

            if ($data && is_array($data)) {
                $data['overall_grade_percent'] = KbGlobal::convertRatingIntoPercent($data['grade']);
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
                        $result['grade_percent'] = KbGlobal::convertRatingIntoPercent($result['grade']);
                    }
                    $indivual_grades = $results;
                }

                $json['status'] = true;
                $json['data'] = $data;
                $json['individual_grades'] = $indivual_grades;
            } else {
                $json['status'] = false;
                $json['msg'] = $this->module->l('No Data Found');
            }
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
            && Tools::getValue('mutiaction_type') == KbGlobal::MULTI_ACTION_TYPE_DELETE) {
            $comment_ids = explode(',', trim(Tools::getValue('selected_table_item_ids')));
            try {
                foreach ($comment_ids as $id) {
                    if ((int)$id > 0) {
                        $rev = new KbSellerProductReview($id);

                        require_once(_PS_MODULE_DIR_ . 'productcomments/ProductComment.php');
                        if (class_exists('ProductComment', false)) {
                            $comment = new ProductComment($rev->id_product_comment);
                            if ($comment->delete()) {
                                if (!$rev->delete()) {
                                    $all_updated = false;
                                } else {
                                    $update_count++;
                                }
                            } else {
                                $all_updated = false;
                            }
                        } else {
                            if (!$rev->delete()) {
                                $all_updated = false;
                            } else {
                                $update_count++;
                            }
                        }
                    }
                }
                if (!$all_updated) {
                    $this->context->cookie->__set(
                        'redirect_success',
                        sprintf(
                            $this->module->l(
                                '%s review(s) has been deleted out of %s review(s).'
                            ),
                            $update_count,
                            count($comment_ids)
                        )
                    );
                } else {
                    $this->context->cookie->__set(
                        'redirect_success',
                        $this->module->l('Selected review(s) has been deleted successfully.')
                    );
                }
            } catch (Exception $e) {
                $this->context->cookie->__set(
                    'redirect_error',
                    $this->module->l('Technical error occurred while deleting comment.')
                );
            }
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
}
