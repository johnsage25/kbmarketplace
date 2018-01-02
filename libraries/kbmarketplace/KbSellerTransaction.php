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

class KbSellerTransaction extends ObjectModel
{
    public $id;
    public $id_seller;
    public $id_shop;
    public $id_employee;
    public $transaction_number;
    public $amount;
    public $transaction_type;
    public $comment;
    public $date_add;
    public $date_upd;

    const KB_TRANSACTION_CREDIT_TYPE = 0;
    const KB_TRANSACTION_DEBIT_TYPE = 1;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'kb_mp_seller_transaction',
        'primary' => 'id_seller_transaction',
        'fields' => array(
            'id_seller' => array('type' => self::TYPE_INT, 'validate' => 'isNullOrUnsignedId', 'copy_post' => false),
            'id_shop' => array('type' => self::TYPE_INT, 'validate' => 'isNullOrUnsignedId', 'copy_post' => false),
            'id_employee' => array('type' => self::TYPE_INT, 'validate' => 'isNullOrUnsignedId', 'copy_post' => false),
            'transaction_number' => array('type' => self::TYPE_STRING, 'validate' => 'isTrackingNumber',
                'required' => true),
            'amount' => array('type' => self::TYPE_FLOAT, 'validate' => 'isNegativePrice', 'required' => true),
            'transaction_type' => array('type' => self::TYPE_STRING, 'values' => array('0', '1'), 'default' => '0'),
            'comment' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate')
        )
    );

    protected $webserviceParameters = array(
        'objectsNodeName' => 'kbsellertransactions',
        'objectNodeName' => 'kbsellertransaction',
        'fields' => array(
            'id_seller' => array('xlink_resource' => 'kbsellers'),
            'id_shop' => array('xlink_resource' => 'shops')
        )
    );

    public function __construct($id = null)
    {
        parent::__construct($id);
    }

    public static function getTransactionsBySellerId(
        $id_seller,
        $only_count = false,
        $start = null,
        $limit = null,
        $filter_string = null
    ) {
        $sql = 'Select {{COLUMN}} from ' . _DB_PREFIX_ . 'kb_mp_seller_transaction 
			Where id_seller = ' . (int)$id_seller;

        if (!empty($filter_string)) {
            $sql .= pSQL($filter_string);
        }

        if ($only_count) {
            $sql = str_replace('{{COLUMN}}', 'COUNT(id_seller_transaction) as total', $sql);
            return DB::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
        } else {
            $sql = str_replace('{{COLUMN}}', '*', $sql);

            $sql .= ' ORDER BY date_add DESC';

            if ((int)$start >= 0 && (int)$limit > 0) {
                $sql .= ' LIMIT ' . (int)$start . ', ' . (int)$limit;
            } elseif ((int)$limit > 0) {
                $sql .= ' LIMIT ' . (int)$limit;
            }

            return DB::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        }
    }

    public static function getSellerTotalPaidAmount($id_seller)
    {
        $sql = 'Select IF(SUM(amount) IS NULL,0,SUM(amount)) as total from ' . _DB_PREFIX_ . 'kb_mp_seller_transaction 
			where id_seller = ' . (int)$id_seller;

        return DB::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
    }

    public static function getSellerTotalBalanceAmount($id_seller)
    {
        $paid_amount = self::getSellerTotalPaidAmount($id_seller);

        $total_amount = KbSellerEarning::getTotalSellerEarning($id_seller);

        return (float)($total_amount - $paid_amount);
    }
}
