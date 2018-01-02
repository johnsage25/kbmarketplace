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

class KbmarketplaceTransactionModuleFrontController extends KbmarketplaceCoreModuleFrontController
{
    public $controller_name = 'transaction';

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
            if (Tools::isSubmit('method')) {
                switch (Tools::getValue('method')) {
                    case 'getTransactionsList':
                        $this->json = $this->getAjaxTransactionsListHtml();
                        break;
                    case 'getTransactionDetail':
                        $this->json = $this->getAjaxTransactionDetail();
                        break;
                }
            }
            echo Tools::jsonEncode($this->json);
            die;
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
            $page_title = 'Transaction History';
            $page['meta']['title'] =  $page_title;
            $page['meta']['keywords'] = $this->seller_info['meta_keyword'];
            $page['meta']['description'] = $this->seller_info['meta_description'];
        }
        return $page;
    }
    
    private function renderList()
    {
        $total_earning = KbSellerEarning::getTotalSellerEarning($this->seller_info['id_seller']);
        $this->context->smarty->assign('total_earning', $total_earning);

        $total_paid_amount = KbSellerTransaction::getSellerTotalPaidAmount($this->seller_info['id_seller']);
        $this->context->smarty->assign('total_paid_amount', $total_paid_amount);

        $total_bal_amount = KbSellerTransaction::getSellerTotalBalanceAmount($this->seller_info['id_seller']);
        $this->context->smarty->assign('total_bal_amount', $total_bal_amount);


        $tmp = $this->getTransactionTypes();
        $transaction_types = array();
        foreach ($tmp as $type => $label) {
            $transaction_types[] = array('value' => $type, 'label' => $label);
        }

        $this->filter_header = $this->module->l('Filter Your Search');
        $this->filter_id = 'seller_transaction_filter';

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
                'name' => 'transaction_type',
                'label' => $this->module->l('Transaction Type'),
                'values' => $transaction_types
            )
        );

        $this->filter_action_name = 'getTransactionsList';
        $this->context->smarty->assign('kbfilter', $this->renderKbListFilter());

        $this->table_id = $this->filter_id;
        $this->table_header = array(
            array('label' => $this->module->l('Date')),
            array('label' => $this->module->l('Transaction Id')),
            array('label' => $this->module->l('Comment')),
            array('label' => $this->module->l('Type')),
            array('label' => $this->module->l('Amount'))
        );

        $this->total_records = KbSellerTransaction::getTransactionsBySellerId($this->seller_info['id_seller'], true);

        if ($this->total_records > 0) {
            $transactions = KbSellerTransaction::getTransactionsBySellerId(
                $this->seller_info['id_seller'],
                false,
                $this->getPageStart(),
                $this->tbl_row_limit
            );

            foreach ($transactions as $transaction) {
                $read_more_link = '<a onclick="getTransactionDetail(' . $transaction['id_seller_transaction']
                    . ')" href="javascript:void(0)" >' . $this->module->l('Read More') . '</a>';

                $this->table_content[] = array(
                    array('value' => Tools::displayDate($transaction['date_add'], null, true)),
                    array(
                        'link' => array(
                            'function' => 'getTransactionDetail(' . $transaction['id_seller_transaction'] . ')',
                            'title' => $this->module->l('Click to view detail')),
                        'value' => $transaction['transaction_number'],
                        'class' => '',
                    ),
                    array('value' => $this->clipLongText(
                        Tools::safeOutput($transaction['comment'], false),
                        $read_more_link
                    )
                    ),
                    array('value' => $this->getTransactionTypes($transaction['transaction_type'])),
                    array('value' => Tools::displayPrice($transaction['amount'], $this->seller_currency))
                );
            }

            $this->list_row_callback = $this->filter_action_name;
        }

        $this->context->smarty->assign(
            'transaction_detail_url',
            $this->context->link->getModuleLink(
                $this->kb_module_name,
                $this->controller_name,
                array('render_type' => 'view'),
                (bool)Configuration::get('PS_SSL_ENABLED')
            )
        );

        $this->context->smarty->assign('kblist', $this->renderKbList());

        $this->setKbTemplate('seller/transaction.tpl');
    }

    protected function getTransactionTypes($key = '')
    {
        $types = array(
            KbSellerTransaction::KB_TRANSACTION_CREDIT_TYPE => $this->module->l('Credit'),
            KbSellerTransaction::KB_TRANSACTION_DEBIT_TYPE => $this->module->l('Debit')
        );

        if ($key != '') {
            if (isset($types[$key])) {
                return $types[$key];
            } else {
                return '';
            }
        }

        return $types;
    }

    protected function getAjaxTransactionsListHtml()
    {
        $json = array();

        $custom_filter = '';
        if (Tools::getIsset('start_date') && Tools::getValue('start_date') != '') {
            $custom_filter .= ' AND DATE(date_add) >= "'
                . pSQL(date('Y-m-d', strtotime(Tools::getValue('start_date')))) . '"';
        }

        if (Tools::getIsset('to_date') && Tools::getValue('to_date') != '') {
            $custom_filter .= ' AND DATE(date_add) <= "'
                . pSQL(date('Y-m-d', strtotime(Tools::getValue('to_date')))) . '"';
        }

        if (Tools::getIsset('transaction_type') && Tools::getValue('transaction_type') != '') {
            $custom_filter .= ' AND transaction_type = "'
                . pSQL(Tools::getValue('transaction_type')) . '"';
        }

        $this->total_records = KbSellerTransaction::getTransactionsBySellerId(
            $this->seller_info['id_seller'],
            true,
            null,
            null,
            $custom_filter
        );
        if ($this->total_records > 0) {
            if (Tools::getIsset('start') && (int)Tools::getValue('start') > 0) {
                $this->page_start = (int)Tools::getValue('start');
            }

            $this->table_id = 'seller_transaction_filter';

            $transactions = KbSellerTransaction::getTransactionsBySellerId(
                $this->seller_info['id_seller'],
                false,
                $this->getPageStart(),
                $this->tbl_row_limit,
                $custom_filter
            );

            $row_html = '';
            foreach ($transactions as $transaction) {
                $read_more_link = '<a onclick="getTransactionDetail(' . $transaction['id_seller_transaction']
                    . ')" href="javascript:void(0)" >' . $this->module->l('Read More') . '</a>';

                $row_html .= '<tr>
						<td>' . Tools::displayDate($transaction['date_add'], null, true) . '</td>
						<td class=" kb-tright">
							<a href="javascript:void(0)" title="' . $this->module->l('Click to view detail')
                    . '" onclick="getTransactionDetail(' . $transaction['id_seller_transaction'] . ')">'
                    . $transaction['transaction_number'] . '</a>
                    </td>
                    <td>' . $this->clipLongText(Tools::safeOutput($transaction['comment'], $read_more_link)) . '</td>
                    <td>' . $this->getTransactionTypes($transaction['transaction_type']) . '</td>
                    <td class=" kb-tright">' . Tools::displayPrice($transaction['amount'], $this->seller_currency)
                        . '</td>
                    </tr>';
            }

            $this->list_row_callback = 'getTransactionsList';
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

    protected function getAjaxTransactionDetail()
    {
        $json = array();
        $id_seller_transaction = (int)Tools::getValue('id_seller_transaction', 0);
        if ($id_seller_transaction > 0) {
            $transaction = new KbSellerTransaction($id_seller_transaction);

            $data = array(
                'posted_on' => Tools::displayDate($transaction->date_add, null, true),
                'amount' => Tools::displayPrice($transaction->amount, $this->seller_currency),
                'type' => $transaction->transaction_type,
                'transaction_number' => $transaction->transaction_number,
                'comment' => Tools::safeOutput($transaction->comment, false)
            );

            $json['status'] = true;
            $json['data'] = $data;
        } else {
            $json['status'] = false;
            $json['msg'] = $this->module->l('No Data Found');
        }
        return $json;
    }
}
