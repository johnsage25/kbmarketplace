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

class KbmarketplaceSellerreviewModuleFrontController extends KbmarketplaceCoreModuleFrontController
{
    public $controller_name = 'sellerreview';

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
                    case 'getSellerReviews':
                        $this->json = $this->getAjaxReviewsListHtml();
                        break;
                    case 'getReviewDetail':
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
            $page_title = 'Reviews';
            $page['meta']['title'] =  $page_title;
            $page['meta']['keywords'] = $this->seller_info['meta_keyword'];
            $page['meta']['description'] = $this->seller_info['meta_description'];
        }
        return $page;
    }
    
    private function renderList()
    {
        $tmp = KbGlobal::getApporvalStatus();
        $approve_statuses = array();
        foreach ($tmp as $key => $val) {
            $approve_statuses[] = array('value' => $key, 'label' => $val);
        }

        $i = 0;
        $rating_array = array();
        while ($i <= KbGlobal::MAX_RATING) {
            $rating_array[] = array('value' => $i, 'label' => $i);
            $i++;
        }

        $this->filter_header = $this->module->l('Filter Your Search');
        $this->filter_id = 'seller_review_filter';
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
                'name' => 'approved',
                'label' => $this->module->l('Status'),
                'values' => $approve_statuses
            ),
            array(
                'type' => 'select',
                'placeholder' => $this->module->l('Select'),
                'name' => 'rating',
                'label' => $this->module->l('Rating'),
                'values' => $rating_array
            )
        );
        $this->filter_action_name = 'getSellerReviews';
        $this->context->smarty->assign('kbfilter', $this->renderKbListFilter());

        $this->table_id = $this->filter_id;
        $this->table_header = array(
            array(
                'label' => $this->module->l('ID'),
                'align' => 'right',
                'width' => '60'
            ),
            array('label' => $this->module->l('Posted On'), 'width' => '80'),
            array('label' => $this->module->l('Customer'), 'width' => '150'),
            array('label' => $this->module->l('Status'), 'width' => '150'),
            array('label' => $this->module->l('Comment')),
            array('label' => $this->module->l('Rating'), 'width' => '80')
        );

        $this->total_records = KbSellerReview::getReviewsBySellerId(
            $this->seller_info['id_seller'],
            null,
            false,
            true
        );

        if ($this->total_records > 0) {
            $reviews = KbSellerReview::getReviewsBySellerId(
                $this->seller_info['id_seller'],
                null,
                false,
                false,
                false,
                $this->getPageStart(),
                $this->tbl_row_limit
            );

            foreach ($reviews as $review) {
                $read_more_link = '<a onclick="getSellerReviewDetail(' . $review['id_seller_review']
                    . ')" href="javascript:void(0)">' . $this->module->l('Read More') . '</a>';

                $rating_percent = KbGlobal::convertRatingIntoPercent($review['rating']);
                $rating_block = '<div class="vss_seller_ratings"><div class="vss_rating_unfilled">★★★★★</div>
					<div class="vss_rating_filled" style="width:' . $rating_percent . '%">★★★★★</div></div>';

                $this->table_content[$review['id_seller_review']] = array(
                    array(
                        'link' => array(
                            'function' => 'getSellerReviewDetail(' . $review['id_seller_review'] . ')',
                            'title' => $this->module->l('Click to view review')),
                        'value' => '#' . $review['id_seller_review'],
                        'class' => '',
                        'align' => 'kb-tright'
                    ),
                    array('value' => Tools::displayDate($review['date_add'], null, true)),
                    array('value' => $review['firstname'] . ' ' . $review['lastname']),
                    array('value' => KbGlobal::getApporvalStatus($review['approved'])),
                    array('value' => $this->clipLongText(Tools::safeOutput($review['comment']), $read_more_link)),
                    array('value' => $rating_block),
                );
            }

            $this->list_row_callback = $this->filter_action_name;

            $this->table_enable_multiaction = true;
            //Show Multi actions
            $this->kb_multiaction_params['multiaction_values'] = array(
                array(
                    'label' => $this->module->l('Delete'),
                    'value' => KbGlobal::MULTI_ACTION_TYPE_DELETE
                )
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

        $this->context->smarty->assign(
            'get_review_detail_url',
            $this->context->link->getModuleLink(
                $this->kb_module_name,
                $this->controller_name,
                array('render_type' => 'view'),
                (bool)Configuration::get('PS_SSL_ENABLED')
            )
        );

        $this->context->smarty->assign('kblist', $this->renderKbList());

        $this->setKbTemplate('seller/review_list.tpl');
    }

    protected function getAjaxReviewsListHtml()
    {
        $json = array();

        $custom_filter = '';
        if (Tools::getIsset('start_date') && Tools::getValue('start_date') != '') {
            $custom_filter .= ' AND DATE(sr.date_add) >= "'
                . pSQL(date('Y-m-d', strtotime(Tools::getValue('start_date')))) . '"';
        }

        if (Tools::getIsset('to_date') && Tools::getValue('to_date') != '') {
            $custom_filter .= ' AND DATE(sr.date_add) <= "'
                . pSQL(date('Y-m-d', strtotime(Tools::getValue('to_date')))) . '"';
        }

        if (Tools::getIsset('active') && Tools::getValue('active') != '') {
            $custom_filter .= ' AND p.active = ' . (int)Tools::getValue('active');
        }

        $approved = false;
        if (Tools::getIsset('approved') && Tools::getValue('approved') != '') {
            $approved = Tools::getValue('approved');
        }

        $rating = false;
        if (Tools::getIsset('rating') && Tools::getValue('rating') != '') {
            $rating = Tools::getValue('rating');
        }

        $this->total_records = KbSellerReview::getReviewsBySellerId(
            $this->seller_info['id_seller'],
            null,
            $approved,
            true,
            $rating,
            null,
            null,
            $custom_filter
        );

        if ($this->total_records > 0) {
            if (Tools::getIsset('start') && (int)Tools::getValue('start') > 0) {
                $this->page_start = (int)Tools::getValue('start');
            }

            $this->table_id = 'seller_review_filter';

            $reviews = KbSellerReview::getReviewsBySellerId(
                $this->seller_info['id_seller'],
                null,
                $approved,
                false,
                $rating,
                $this->getPageStart(),
                $this->tbl_row_limit,
                $custom_filter
            );

            $row_html = '';
            foreach ($reviews as $review) {
                $read_more_link = '<a onclick="getSellerReviewDetail(' . $review['id_seller_review']
                    . ')" href="javascript:void(0)">' . $this->module->l('Read More') . '</a>';

                $rating_percent = KbGlobal::convertRatingIntoPercent($review['rating']);
                $rating_block = '<div class="vss_seller_ratings"><div class="vss_rating_unfilled">★★★★★</div>
					<div class="vss_rating_filled" style="width:' . $rating_percent . '%">★★★★★</div></div>';

                $row_html .= '<tr>
						<td class="kb-tcenter"><div class="checker"><span>
							<input type="checkbox" class="kb_list_row_checkbox" name="row_item_id[]" value="'
                    . $review['id_seller_review'] . '" title=""></span></div>
						</td>
						<td class=" kb-tright">
							<a href="javascript:void(0)" title="'
                    . $this->module->l('Click to view review')
                    . '" onclick="getSellerReviewDetail('
                    . $review['id_seller_review'] . ')">#' . $review['id_seller_review'] . '</a>
						</td>
						<td>' . Tools::displayDate($review['date_add'], null, true) . '</td>
						<td>' . $review['firstname'] . ' ' . $review['lastname'] . '</td>
						<td>' . KbGlobal::getApporvalStatus($review['approved']) . '</td>
						<td>' . $this->clipLongText(Tools::safeOutput($review['comment']), $read_more_link) . '</td>
						<td>' . $rating_block . '</td>
                                    </tr>';
            }

            $this->list_row_callback = 'getSellerReviews';
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
        $id_seller_review = (int)Tools::getValue('id_seller_review', 0);
        if ($id_seller_review > 0) {
            $review = new KbSellerReview($id_seller_review);

            $data = array(
                'customer_name' => $review->customer['name'],
                'posted_on' => Tools::displayDate($review->date_add, null, true),
                'rating_percent' => KbGlobal::convertRatingIntoPercent($review->rating),
                'title' => Tools::safeOutput($review->title),
                'comment' => Tools::safeOutput($review->comment)
            );

            $json['status'] = true;
            $json['data'] = $data;
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
            foreach ($comment_ids as $id) {
                if ((int)$id > 0) {
                    $rev = new KbSellerReview($id);
                    if (!$rev->delete()) {
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
                            '<b>%s</b> review(s) has been deleted out of <b>%s</b> review(s).'
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
        } else {
            $this->context->cookie->__set(
                'redirect_error',
                $this->module->l('Please select valid action')
            );
        }

        Tools::redirect($this->context->link->getModuleLink(
            $this->kb_module_name,
            $this->controller_name,
            array(),
            (bool)Configuration::get('PS_SSL_ENABLED')
        ));
    }
}
