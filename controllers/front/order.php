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

class KbmarketplaceOrderModuleFrontController extends KbmarketplaceCoreModuleFrontController
{

    public $controller_name = 'order';

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
                    case 'getSellerOrders':
                        $this->json = $this->getAjaxOrderListHtml();
                        break;
                }
            }
            if (!$renderhtml) {
                echo Tools::jsonEncode($this->json);
            }
            die;
        } else {
            $seller_order_handling = Configuration::get('KB_MP_SELLER_ORDER_HANDLING');

            if ($seller_order_handling == 1) {
                if (Tools::getIsset('submitState') && $id_order = Tools::getValue('id_order', 0)) {
                    $this->updateState($id_order);
                } elseif (Tools::getIsset('submitMessage')) {
                    $this->sendMessageToCustomer();
                } elseif (Tools::getIsset('generateInvoicePdf')) {
                    $this->generateInvoicePDFByIdOrder();
                } elseif (Tools::getIsset('generateDeliverySlipPDF')) {
                    $this->generateDeliverySlipPDFByIdOrder();
                } elseif (Tools::getIsset('submitShippingNumber')) {
                    $this->updateShippingNumber();
                }
            } else {
                if (Tools::getIsset('submitState')
                    || Tools::getIsset('submitShippingNumber')
                    || Tools::getIsset('generateDeliverySlipPDF')
                    || Tools::getIsset('submitMessage')
                ) {
                        $this->context->cookie->__set(
                            'redirect_error',
                            $this->module->l('You do not have permissions. Please contact admin.')
                        );
                }
            }
        }
    }

    public function initContent()
    {
        if (Tools::getIsset('render_type') && Tools::getValue('render_type') == 'view') {
            $this->registerAdminSmartyPlugins();
            $this->renderOrderView();
        } elseif (Tools::getIsset('render_type') && Tools::getValue('render_type') == 'print_order') {
            $this->context->smarty->assign('kb_print_order', true);
            $this->renderOrderView();
        } else {
            $this->renderList();
        }

        parent::initContent();
    }
    
    public function getTemplateVarPage()
    {
        $page = parent::getTemplateVarPage();
        if (isset($page['meta']) && $this->seller_info) {
            $page_title = 'Orders';
            $page['meta']['title'] =  $page_title;
            $page['meta']['keywords'] = $this->seller_info['meta_keyword'];
            $page['meta']['description'] = $this->seller_info['meta_description'];
        }
        return $page;
    }

    private function renderOrderView()
    {
//        print_r(unserialize(Configuration::get('KB_MARKETPLACE_CONFIG')));
//        die;
        
        $id_order = (int)Tools::getValue('id_order', 0);
        $id_seller = $this->seller_info['id_seller'];
        if (KbSellerEarning::isSellerOrder($id_seller, $id_order)) {
            $order = new Order($id_order);
            if (Validate::isLoadedObject($order)) {
                $order_product_detail = $order->getProducts();
                $currency = new Currency((int)$order->id_currency);
                $this->context->smarty->assign('currency', $currency);
                $seller_products = array();
                if ($order_product_detail && is_array($order_product_detail) && count($order_product_detail) > 0) {
                    /* DEPRECATED: customizedDatas @since 1.5 */
                    $customizedDatas = Product::getAllCustomizedDatas((int)($order->id_cart));
                    Product::addCustomizationPrice($order_product_detail, $customizedDatas);
                    foreach ($order_product_detail as &$detail) {
                        if (KbSellerProduct::isSellerProduct($id_seller, $detail['product_id'])) {
                            if ($detail['image'] != null) {
                                $name = 'product_mini_' . (int)$detail['product_id']
                                    . (isset($detail['product_attribute_id'])
                                    ? '_' . (int)$detail['product_attribute_id'] : '') . '.jpg';

                                // generate image cache, only for back office
                                $detail['image_tag'] = ImageManager::thumbnail(
                                    _PS_IMG_DIR_ . 'p/' . $detail['image']->getExistingImgPath()
                                    . '.jpg',
                                    $name,
                                    60,
                                    'jpg'
                                );

                                if (file_exists(_PS_TMP_IMG_DIR_ . $name)) {
                                    $detail['image_size'] = getimagesize(_PS_TMP_IMG_DIR_ . $name);
                                } else {
                                    $detail['image_size'] = false;
                                }
                            }

                            $resume = OrderSlip::getProductSlipResume($detail['id_order_detail']);
                            $detail['quantity_refundable'] = $detail['product_quantity'] - $resume['product_quantity'];
                            $detail['amount_refundable'] = (
                                $detail['total_price_tax_incl'] - $resume['amount_tax_incl']);
                            $detail['amount_refund'] = Tools::displayPrice($resume['amount_tax_incl'], $currency);
                            $detail['refund_history'] = OrderSlip::getProductSlipDetail($detail['id_order_detail']);
                            $detail['return_history'] = OrderReturn::getProductReturnDetail(
                                $detail['id_order_detail']
                            );

                            $seller_products[] = $detail;
                        }
                    }
                    /* DEPRECATED: customizedDatas @since 1.5 */
                    $this->context->smarty->assign('customizedDatas', $customizedDatas);
                }

                $seller_earning = KbSellerEarning::getEarningBySellerAndOrder($this->seller_obj->id, $order->id);
                
                $carrier_name_replace_str = ' - ' . $this->seller_info['seller_name'];
                $carrier = new Carrier((int)($order->id_carrier), (int)($order->id_lang));
                $carrier->name = str_replace($carrier_name_replace_str, '', $carrier->name);
                $order_state = KbSellerEarning::getOrderState($order->getCurrentState(), $order->id_lang);
                $customer = $order->getCustomer();
//print_r($customer->email);die;
                $addressInvoice = new Address((int)($order->id_address_invoice));
                $inv_formatted_addr = AddressFormat::generateAddress($addressInvoice, array(), '<br>');
                $inv_adr_fields = AddressFormat::getOrderedAddressFields($addressInvoice->id_country);
                $invoiceAddressFormatedValues = AddressFormat::getFormattedAddressFieldsValues(
                    $addressInvoice,
                    $inv_adr_fields
                );
                $invoice_customer_name = $invoiceAddressFormatedValues['firstname']
                    . ' ' . $invoiceAddressFormatedValues['lastname'];
                $invoice_address_txt = array(
                    'name' => $invoice_customer_name,
                    'address' => str_replace($invoice_customer_name . '<br>', '', $inv_formatted_addr)
                );

                $history = $order->getHistory($this->seller_obj->id_default_lang);
                foreach ($history as &$order_his) {
                    $order_his['text-color'] = Tools::getBrightness($order_his['color']) < 128 ? 'white' : 'black';
                }
                $setting_mark = Tools::unserialize(Configuration::get('KB_MARKETPLACE_CONFIG'));
                $message= CustomerMessage::getMessagesByOrderId((int)($order->id), true);
                $update_msg = array();
                foreach ($message as $msg) {
                    if ($msg['id_employee'] !== 0) {
                        $cus = new Customer($msg['id_employee']);
                        $msg['efirstname'] = $cus->firstname;
                        $msg['elastname'] = $cus->lastname;
                    } else {
                        $msg['efirstname'] = '';
                        $msg['elastname'] = '';
                    }
                    $update_msg[] = $msg;
                }
                $this->context->smarty->assign(array(
                    'seller_order_handling' => $setting_mark['kbmp_enable_seller_order_handling'],
                    'products' => $seller_products,
                    'shop_name' => (string)Configuration::get('PS_SHOP_NAME'),
                    'order' => $order,
                    'order_currency' => $currency,
                    'seller_earning' => $seller_earning,
                    'can_handle_order' => KbSellerEarning::isSellerCanHandleOrder($this->seller_obj->id, $id_order),
                    'items_ordered' => count($seller_products),
                    'history' => $history,
                    'order_state' => $order_state,
                    'currentState' => $order->getCurrentOrderState(),
                    'states' => OrderState::getOrderStates($this->seller_obj->id_default_lang),
                    'carrier' => $carrier,
                    'carrier_replace_str' => $carrier_name_replace_str,
//                    'messages' => CustomerMessage::getMessagesByOrderId((int)($order->id), true),
                    'messages' => $update_msg,
                    'customer_email' => $customer->email,
                    'invoice_address_txt' => $invoice_address_txt
                ));

                if (!$order->isVirtual()) {
                    $addressDelivery = new Address((int)($order->id_address_delivery));
                    $delv_formatted_addr = AddressFormat::generateAddress($addressDelivery, array(), '<br>');
                    $dlv_adr_fields = AddressFormat::getOrderedAddressFields($addressDelivery->id_country);
                    $deliveryAddressFormatedValues = AddressFormat::getFormattedAddressFieldsValues(
                        $addressDelivery,
                        $dlv_adr_fields
                    );
                    $delv_customer_name = $deliveryAddressFormatedValues['firstname']
                        . ' ' . $deliveryAddressFormatedValues['lastname'];
                    $delv_address_txt = array(
                        'name' => $delv_customer_name,
                        'address' => str_replace($delv_customer_name . '<br>', '', $delv_formatted_addr)
                    );
                    $this->context->smarty->assign('delv_address_txt', $delv_address_txt);
                }

                $this->setKbTemplate('order/view.tpl');
            }
        }
    }

    private function renderList()
    {
        $statuses = array();
        $tmp = OrderState::getOrderStates((int)$this->context->language->id);
        foreach ($tmp as $val) {
            $statuses[$val['id_order_state']] = array('value' => $val['id_order_state'], 'label' => $val['name']);
        }

        $this->filter_header = $this->module->l('Filter Your Search');
        $this->filter_id = 'seller_order_filter';
        $this->filters = array(
            array(
                'type' => 'text',
                'name' => 'reference',
                'label' => $this->module->l('Reference')
            ),
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
                'type' => 'text',
                'name' => 'customer_name',
                'label' => $this->module->l('Customer')
            ),
            array(
                'type' => 'select',
                'placeholder' => $this->module->l('Select'),
                'name' => 'current_state',
                'label' => $this->module->l('Status'),
                'values' => $statuses
            )
        );

        $this->filter_action_name = 'getSellerOrders';
        $this->context->smarty->assign('kbfilter', $this->renderKbListFilter());

        $this->table_id = $this->filter_id;
        $this->table_header = array(
            array(
                'label' => $this->module->l('Reference'),
                'align' => 'right',
                'width' => '100'
            ),
            array(
                'label' => $this->module->l('Order Date'),
                'align' => 'left',
            ),
            array(
                'label' => $this->module->l('Customer Name'),
                'align' => 'left',
            ),
            array(
                'label' => $this->module->l('Customer Email'),
                'align' => 'left',
            ),
            array(
                'label' => $this->module->l('Qty'),
                'align' => 'right',
                'width' => '50'
            ),
            array(
                'label' => $this->module->l('Status'),
                'align' => 'left',
            ),
            array(
                'label' => $this->module->l('Order Total'),
                'align' => 'right',
                'width' => '100',
            )
        );

        $this->total_records = KbSellerEarning::getOrdersBySellerId($this->seller_info['id_seller'], true);

        if ($this->total_records > 0) {
            $seller_orders = KbSellerEarning::getOrdersBySellerId(
                $this->seller_info['id_seller'],
                false,
                $this->getPageStart(),
                $this->tbl_row_limit
            );

            foreach ($seller_orders as $so) {
                $order = new Order($so['id_order']);
                $customer = $order->getCustomer();
                $currency = new Currency($order->id_currency);
                $view_link = $this->context->link->getModuleLink(
                    $this->kb_module_name,
                    $this->controller_name,
                    array('render_type' => 'view', 'id_order' => $order->id),
                    (bool)Configuration::get('PS_SSL_ENABLED')
                );
                $this->table_content[] = array(
                    array(
                        'link' => array(
                            'href' => $view_link,
                            'function' => '',
                            'title' => $this->module->l('Click to view order detail'),
                            'target' => '_blank'
                        ),
                        'value' => Tools::strtoupper($order->getUniqReference()),
                    ),
                    array('value' => Tools::displayDate($order->date_add, null, false)),
                    array('value' => $customer->firstname . ' ' . $customer->lastname),
                    array('value' => $customer->email),
                    array('value' => $so['product_count'], 'align' => 'kb-tright'),
                    array('value' => $statuses[$order->current_state]['label']),
                    array('value' => Tools::displayPrice($so['total_earning'], $currency), 'align' => 'kb-tright')
                );
            }

            $this->list_row_callback = $this->filter_action_name;
        }

        $this->context->smarty->assign('kblist', $this->renderKbList());

        $total_revenue = Tools::displayPrice(
            KbSellerEarning::getTotalEarningInSellerOrders($this->seller_info['id_seller']),
            $this->seller_currency
        );
        $this->context->smarty->assign('total_revenue', $total_revenue);

        $total_sold_products = KbSellerEarning::getTotalSellerSoldProduct($this->seller_info['id_seller']);
        $this->context->smarty->assign('total_sold_products', $total_sold_products);

        $total_pending_orders = KbSellerEarning::getSellerPendingOrders($this->seller_info['id_seller']);
        $this->context->smarty->assign('total_pending_orders', $total_pending_orders);

        $this->setKbTemplate('order/list.tpl');
    }

    protected function getAjaxOrderListHtml()
    {
        $json = array();

        $custom_filter = '';
        if (Tools::getIsset('start_date') && Tools::getValue('start_date') != '') {
            $custom_filter .= ' AND DATE(o.date_add) >= "'
                . pSQL(date('Y-m-d', strtotime(Tools::getValue('start_date')))) . '"';
        }

        if (Tools::getIsset('to_date') && Tools::getValue('to_date') != '') {
            $custom_filter .= ' AND DATE(o.date_add) <= "'
                . pSQL(date('Y-m-d', strtotime(Tools::getValue('to_date')))) . '"';
        }

        if (Tools::getIsset('reference') && Tools::getValue('reference') != '') {
            $custom_filter .= ' AND o.reference like "%'
                . pSQL(Tools::getValue('reference')) . '%"';
        }

        if (Tools::getIsset('customer_name') && Tools::getValue('customer_name') != '') {
            $custom_filter .= ' AND (c.firstname like "%' . pSQL(Tools::getValue('customer_name'))
                . '%" OR c.lastname like "%' . pSQL(Tools::getValue('customer_name')) . '%")';
        }

        if (Tools::getIsset('current_state') && Tools::getValue('current_state') != '') {
            $custom_filter .= ' AND o.current_state = "' . pSQL(Tools::getValue('current_state')) . '"';
        }

        $this->total_records = KbSellerEarning::getOrdersBySellerId(
            $this->seller_info['id_seller'],
            true,
            null,
            null,
            $custom_filter
        );

        if ($this->total_records > 0) {
            $statuses = array();
            $tmp = OrderState::getOrderStates((int)$this->context->language->id);
            foreach ($tmp as $val) {
                $statuses[$val['id_order_state']] = $val['name'];
            }

            if (Tools::getIsset('start') && (int)Tools::getValue('start') > 0) {
                $this->page_start = (int)Tools::getValue('start');
            }

            $this->table_id = 'seller_order_filter';

            $seller_orders = KbSellerEarning::getOrdersBySellerId(
                $this->seller_info['id_seller'],
                false,
                $this->getPageStart(),
                $this->tbl_row_limit,
                $custom_filter
            );

            $row_html = '';
            foreach ($seller_orders as $so) {
                $order = new Order($so['id_order']);
                $customer = $order->getCustomer();
                $currency = new Currency($order->id_currency);
                $view_link = $this->context->link->getModuleLink(
                    $this->kb_module_name,
                    $this->controller_name,
                    array('render_type' => 'view', 'id_order' => $order->id),
                    (bool)Configuration::get('PS_SSL_ENABLED')
                );

                $row_html .= '<tr>
                    <td><a href="' . $view_link . '" title="'
                    . $this->module->l('Click to view order detail') . '" onclick="" target="_blank">'
                    . Tools::strtoupper($order->getUniqReference()) . '</a></td>
                    <td>' . Tools::displayDate($order->date_add, null, false) . '</td>
                    <td>' . $customer->firstname . ' ' . $customer->lastname . '</td>
                    <td>' . $customer->email . '</td>
                    <td class="kb-tright">' . $so['product_count'] . '</td>
                    <td>' . $statuses[$order->current_state] . '</td>
                    <td class="kb-tright">' . Tools::displayPrice($so['total_earning'], $currency) . '</td>
                    </tr>';
            }

            $this->list_row_callback = 'getSellerOrders';
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

    public function updateState($id_order)
    {
        if (KbSellerEarning::isSellerOrder($this->seller_obj->id, $id_order)) {
            $order = new Order($id_order);
            $order_state = new OrderState(Tools::getValue('id_order_state'));

            if (!Validate::isLoadedObject($order_state)) {
                $this->context->cookie->__set(
                    'redirect_error',
                    $this->module->l('The new order status is invalid.')
                );
            } else {
                $current_order_state = $order->getCurrentOrderState();
                if ($current_order_state->id != $order_state->id) {
                    $employees = Employee::getEmployeesByProfile(_PS_ADMIN_PROFILE_, true);
                    $employee = null;
                    foreach ($employees as $em) {
                        $employee = new Employee($em['id_employee']);
                        if ($employee->isSuperAdmin()) {
                            break;
                        }
                    }

                    if (!Validate::isLoadedObject($employee)) {
                        $this->context->cookie->__set(
                            'redirect_error',
                            $this->module->l(
                                'An error occurred while changing order status due 
                                to missing employee, contact to support.'
                            )
                        );
                    } else {
                        $this->context->employee = $employee;
                        // Create new OrderHistory
                        $history = new OrderHistory();
                        $history->id_order = $order->id;
                        $history->id_employee = (int)$this->context->employee->id;

                        $use_existings_payment = false;
                        if (!$order->hasInvoice()) {
                            $use_existings_payment = true;
                        }

                        try {
                            $history->changeIdOrderState((int)$order_state->id, $order, $use_existings_payment);

                            $carrier = new Carrier($order->id_carrier, $order->id_lang);
                            $templateVars = array();
                            if ($history->id_order_state == Configuration::get('PS_OS_SHIPPING')
                                && $order->shipping_number
                            ) {
                                $templateVars = array(
                                    '{followup}' => str_replace('@', $order->shipping_number, $carrier->url)
                                );
                            }

                            // Save all changes
                            if ($history->addWithemail(true, $templateVars)) {
                                // synchronizes quantities if needed..
                                if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')) {
                                    foreach ($order->getProducts() as $product) {
                                        if (StockAvailable::dependsOnStock($product['product_id'])) {
                                            StockAvailable::synchronize(
                                                $product['product_id'],
                                                (int)$product['id_shop']
                                            );
                                        }
                                    }
                                }
                                $this->context->cookie->__set(
                                    'redirect_success',
                                    $this->module->l('Order status changed.')
                                );
                            } else {
                                $this->context->cookie->__set(
                                    'redirect_success',
                                    $this->module->l(
                                        'Order status changed, but unable to 
                                        send an email to the customer.'
                                    )
                                );
                            }
                        } catch (Exception $e) {
                            $this->context->cookie->__set(
                                'redirect_error',
                                $this->module->l('An error occurred while changing order status.')
                            );
                        }
                    }
                } else {
                    $this->context->cookie->__set(
                        'redirect_error',
                        $this->module->l('The order has already been assigned this status.')
                    );
                }
            }
        } else {
            $this->context->cookie->__set(
                'redirect_error',
                $this->module->l('You do not have permission for this order.')
            );
        }
        $this->context->employee = null;
        $redirect_link = $this->context->link->getModuleLink(
            $this->kb_module_name,
            $this->controller_name,
            array('render_type' => 'view', 'id_order' => $id_order),
            (bool)Configuration::get('PS_SSL_ENABLED')
        );
        Tools::redirect($redirect_link);
    }

    public function updateShippingNumber()
    {
        $id_order = Tools::getValue('id_order', 0);
        if (KbSellerEarning::isSellerOrder($this->seller_obj->id, $id_order)) {
            $order = new Order($id_order);
            $order_carrier = new OrderCarrier((int)Tools::getValue('id_order_carrier'));

            if (!Validate::isLoadedObject($order_carrier)) {
                $this->context->cookie->__set(
                    'redirect_error',
                    $this->module->l('The request shipping ID is invalid.')
                );
            } elseif (!Validate::isTrackingNumber(Tools::getValue('tracking_number', 0))) {
                $this->context->cookie->__set(
                    'redirect_error',
                    $this->module->l('The tracking number is incorrect.')
                );
            } else {
                $order->shipping_number = Tools::getValue('tracking_number');
                DB::getInstance()->update(
                    'orders',
                    array('shipping_number' => pSQL(Tools::getValue('tracking_number'))),
                    'id_order = ' . (int)$id_order
                );
                // Update order_carrier
                $order_carrier->tracking_number = pSQL(Tools::getValue('tracking_number'));
                if ($order_carrier->update()) {
                    // Send mail to customer
                    $customer = new Customer((int)$order->id_customer);
                    $carrier = new Carrier((int)$order->id_carrier, $order->id_lang);
                    $is_all_loaded = true;
                    if (!Validate::isLoadedObject($customer)) {
                        $is_all_loaded = false;
                    }
                    if (!Validate::isLoadedObject($carrier)) {
                        $is_all_loaded = false;
                    }
                    if ($is_all_loaded) {
                        $templateVars = array(
                            '{followup}' => str_replace('@', $order->shipping_number, $carrier->url),
                            '{firstname}' => $customer->firstname,
                            '{lastname}' => $customer->lastname,
                            '{id_order}' => $order->id,
                            '{shipping_number}' => $order->shipping_number,
                            '{order_name}' => $order->getUniqReference()
                        );
                        if (@Mail::Send(
                            (int)$order->id_lang,
                            'in_transit',
                            Mail::l('Package in transit', (int)$order->id_lang),
                            $templateVars,
                            $customer->email,
                            $customer->firstname . ' ' . $customer->lastname,
                            null,
                            null,
                            null,
                            null,
                            _PS_MAIL_DIR_,
                            true,
                            (int)$order->id_shop
                        )
                        ) {
                            Hook::exec(
                                'actionAdminOrdersTrackingNumberUpdate',
                                array('order' => $order, 'customer' => $customer, 'carrier' => $carrier),
                                null,
                                false,
                                true,
                                false,
                                $order->id_shop
                            );
                            $msg = array('Order and shipping has been updated with tracking number.');
                        } else {
                            $msg = array(
                                'Order and shipping has been updated with tracking number',
                                'But, error occurred while sending notification to customer.'
                            );
                        }
                    } else {
                        $msg = array(
                            'Order and shipping has been updated with tracking number',
                            'But, error occurred while sending notification to customer.'
                        );
                    }
                    $this->context->cookie->__set(
                        'redirect_success',
                        implode('####', $msg)
                    );
                } else {
                    $this->context->cookie->__set(
                        'redirect_success',
                        $this->module->l(
                            'Order is updated, but order shipping is not update with passed tracking number.'
                        )
                    );
                }
            }
        } else {
            $this->context->cookie->__set(
                'redirect_error',
                $this->module->l('You do not have permission for this order.')
            );
        }
        $redirect_link = $this->context->link->getModuleLink(
            $this->kb_module_name,
            $this->controller_name,
            array('render_type' => 'view', 'id_order' => $id_order),
            (bool)Configuration::get('PS_SSL_ENABLED')
        );
        Tools::redirect($redirect_link);
    }

    public function sendMessageToCustomer()
    {
        $id_order = Tools::getValue('id_order', 0);
        if (KbSellerEarning::isSellerOrder($this->seller_obj->id, $id_order)) {
            $order = new Order($id_order);
            
            $idSeller = KbSellerOrderDetail::getDetailByOrderId($id_order);
            
//            print_r($idSeller['id_seller']);die;
            if ($idSeller) {
                $id_emp = new KbSeller($idSeller[0]['id_seller']);
            }
            $customer = new Customer($id_emp->id_customer);
//            print_r($id_emp);die;
            if (!Validate::isLoadedObject($customer)) {
                $this->context->cookie->__set(
                    'redirect_error',
                    $this->module->l('The customer is invalid.')
                );
            } else {
                $employees = Employee::getEmployeesByProfile(_PS_ADMIN_PROFILE_, true);
                $employee = null;
                $errors = array();
                foreach ($employees as $em) {
                    $employee = new Employee($em['id_employee']);
                    if ($employee->isSuperAdmin()) {
                        break;
                    }
                }
                if (!Validate::isLoadedObject($employee)) {
                    $errors[] = $this->module->l('Not able to send message due to missing employee.');
                }

                $this->context->employee = $employee;
//print_r($this->context->employee);die;
                /* Get message rules and and check fields validity */
                $rules = call_user_func(array('Message', 'getValidationRules'), 'Message');
                foreach ($rules['required'] as $field) {
                    if (($value = Tools::getValue($field)) == false && (string)$value != '0') {
                        if (!Tools::getValue('id_' . $this->table) || $field != 'passwd') {
                            $errors[] = sprintf($this->module->l('Field %s is required.'), $field);
                        }
                    }
                }
                foreach ($rules['size'] as $field => $maxLength) {
                    if (Tools::getValue($field) && Tools::strlen(Tools::getValue($field)) > $maxLength) {
                        $errors[] = sprintf(
                            $this->module->l('Field %1$s is too long (%2$d chars max).'),
                            $field,
                            $maxLength
                        );
                    }
                }
                foreach ($rules['validate'] as $field => $function) {
                    $tmp = $function;
                    unset($tmp);
                    if (Tools::getValue($field)) {
                        if (!Validate::$function(htmlentities(Tools::getValue($field), ENT_COMPAT, 'UTF-8'))) {
                            $errors[] = sprintf($this->module->l('Field %s is invalid.'), $field);
                        }
                    }
                }

                if (!count($errors)) {
                    //check if a thread already exist
                    $id_customer_thread = CustomerThread::getIdCustomerThreadByEmailAndIdOrder(
                        $customer->email,
                        $order->id
                    );
                    if (!$id_customer_thread) {
                        $customer_thread = new CustomerThread();
                        $customer_thread->id_contact = 0;
                        $customer_thread->id_customer = (int)$id_emp->id_customer;
                        $customer_thread->id_shop = (int)$this->context->shop->id;
                        $customer_thread->id_order = (int)$order->id;
                        $customer_thread->id_lang = (int)$this->context->language->id;
                        $customer_thread->email = $customer->email;
                        $customer_thread->status = 'open';
                        $customer_thread->token = Tools::passwdGen(12);
                        $customer_thread->add();
                    } else {
                        $customer_thread = new CustomerThread((int)$id_customer_thread);
                    }

                    $customer_message = new CustomerMessage();
                    $customer_message->id_customer_thread = $customer_thread->id;
                    $customer_message->id_employee = (int)$id_emp->id_customer;
                    $customer_message->message = Tools::getValue('message');
                    $customer_message->private = Tools::getValue('visibility');

                    if (!$customer_message->add()) {
                        $this->context->cookie->__set(
                            'redirect_error',
                            $this->module->l('An error occurred while saving the message.')
                        );
                    } else {
                        if (!$customer_message->private) {
                            $message = $customer_message->message;
                            if (Configuration::get('PS_MAIL_TYPE', null, null, $order->id_shop) != Mail::TYPE_TEXT) {
                                $message = Tools::nl2br($customer_message->message);
                            }

                            $varsTpl = array(
                                '{lastname}' => $customer->lastname,
                                '{firstname}' => $customer->firstname,
                                '{id_order}' => $order->id,
                                '{order_name}' => $order->getUniqReference(),
                                '{message}' => $message
                            );
                            if (@Mail::Send(
                                (int)$order->id_lang,
                                'order_merchant_comment',
                                Mail::l('New message regarding your order', (int)$order->id_lang),
                                $varsTpl,
                                $customer->email,
                                $customer->firstname . ' ' . $customer->lastname,
                                null,
                                null,
                                null,
                                null,
                                _PS_MAIL_DIR_,
                                true,
                                (int)$order->id_shop
                            )) {
                                $this->context->cookie->__set(
                                    'redirect_success',
                                    $this->module->l('Message has been saved.')
                                );
                            } else {
                                $this->context->cookie->__set(
                                    'redirect_success',
                                    $this->module->l(
                                        'Message has been saved, but error occurred 
                                        while sending an email to the customer.'
                                    )
                                );
                            }
                        } else {
                            $this->context->cookie->__set(
                                'redirect_success',
                                $this->module->l('Message has been saved.')
                            );
                        }
                    }
                } else {
                    $this->context->cookie->__set('redirect_error', implode('####', $errors));
                }
            }
        } else {
            $this->context->cookie->__set(
                'redirect_error',
                $this->module->l('You do not have permission for this order.')
            );
        }
        $this->context->employee = null;
        $redirect_link = $this->context->link->getModuleLink(
            $this->kb_module_name,
            $this->controller_name,
            array('render_type' => 'view', 'id_order' => $id_order),
            (bool)Configuration::get('PS_SSL_ENABLED')
        );
        Tools::redirect($redirect_link);
    }

    public function generateInvoicePDFByIdOrder()
    {
        $id_order = Tools::getValue('id_order', 0);
        if (KbSellerEarning::isSellerOrder($this->seller_obj->id, $id_order)) {
            $order = new Order((int)$id_order);
            if (Validate::isLoadedObject($order)) {
                $this->registerAdminSmartyPlugins();
                $order_invoice_list = $order->getInvoicesCollection();
                Hook::exec('actionPDFInvoiceRender', array('order_invoice_list' => $order_invoice_list));
                $this->generatePDF($order_invoice_list, PDF::TEMPLATE_INVOICE);
            } else {
                $this->context->cookie->__set(
                    'redirect_error',
                    $this->module->l('Error occurred while getting order detail.')
                );
            }
        } else {
            $this->context->cookie->__set(
                'redirect_error',
                $this->module->l('You do not have permission for this order.')
            );
        }

        $redirect_link = $this->context->link->getModuleLink(
            $this->kb_module_name,
            $this->controller_name,
            array('render_type' => 'view', 'id_order' => $id_order),
            (bool)Configuration::get('PS_SSL_ENABLED')
        );
        Tools::redirect($redirect_link);
    }

    public function generateDeliverySlipPDFByIdOrder()
    {
        $id_order = Tools::getValue('id_order', 0);
        if (KbSellerEarning::isSellerOrder($this->seller_obj->id, $id_order)) {
            $order = new Order((int)$id_order);
            if (Validate::isLoadedObject($order)) {
                $this->registerAdminSmartyPlugins();
                $order_invoice_collection = $order->getInvoicesCollection();
                $this->generatePDF($order_invoice_collection, PDF::TEMPLATE_DELIVERY_SLIP);
            } else {
                $this->context->cookie->__set(
                    'redirect_error',
                    $this->module->l('Error occurred while getting order detail.')
                );
            }
        } else {
            $this->context->cookie->__set(
                'redirect_error',
                $this->module->l('You do not have permission for this order.')
            );
        }

        $redirect_link = $this->context->link->getModuleLink(
            $this->kb_module_name,
            $this->controller_name,
            array('render_type' => 'view', 'id_order' => $id_order),
            (bool)Configuration::get('PS_SSL_ENABLED')
        );
        Tools::redirect($redirect_link);
    }
}
