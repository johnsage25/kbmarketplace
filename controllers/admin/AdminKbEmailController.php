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

class AdminKbEmailController extends AdminKbMarketplaceCoreController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'kb_mp_email_template';
        $this->className = 'KbEmail';
        $this->identifier = 'id_email_template';
        $this->lang = true;
        $this->allow_export = true;
        $this->context = Context::getContext();
        parent::__construct();
        $this->toolbar_title = $this->module->l('Market Place Email Templates');

        $this->fields_list = array(
            'id_email_template' => array(
                'title' => $this->module->l('ID'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
                'type' => 'int'
            ),
            'subject' => array(
                'title' => $this->module->l('Subject'),
                'havingFilter' => true,
            ),
            'description' => array(
                'title' => $this->module->l('Description'),
                'havingFilter' => false,
                'maxlength' => 200,
                'search' => false,
            )
        );

        $this->orderBy = 'id_email_template';
        $this->addRowAction('edit');
        $this->page_header_toolbar_title = $this->toolbar_title;
    }

    public function postProcess()
    {
        parent::postProcess();
    }

    public function setMedia()
    {
        parent::setMedia();
    }

    public function initContent()
    {
        parent::initContent();
    }

    public function initToolbar()
    {
        parent::initToolbar();
        unset($this->toolbar_btn['new']);
    }

    public function getList(
        $id_lang,
        $orderBy = null,
        $orderWay = null,
        $start = 0,
        $limit = null,
        $id_lang_shop = null
    ) {
        parent::getList($id_lang, $orderBy, $orderWay, $start, $limit, $id_lang_shop);
    }

    public function initPageHeaderToolbar()
    {
        parent::initPageHeaderToolbar();
    }

    public function renderForm()
    {
        if (!($obj = $this->loadObject(true))) {
            return;
        }

        $id_lang = $this->context->language->id;

        $content_warning = $this->module->l('Keywords like {{sample}} will be replace by dynamic content 
			at the time of execution. Please do not remove these type of words from template, otherwise 
			proper information will not be send in email to seller as well you. You can only change the 
			position of these keywords in the template.');

        $this->fields_form = array(
            'legend' => array(
                'title' => $this->module->l('Template Editor'),
                'icon' => 'icon-envelope'
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->module->l('Subject'),
                    'name' => 'subject',
                    'id' => 'kb_mp_email_subject',
                    'lang' => true,
                    'required' => true,
                    'col' => '9',
                    'class' => 'col-lg-9',
                    'hint' => sprintf($this->module->l('Invalid characters: %s'), '0-9!&lt;&gt;,;?=+()@#"°{}_$%:')
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->module->l('Email Content'),
                    'name' => 'body',
                    'lang' => true,
                    'required' => true,
                    'col' => '9',
                    'class' => 'col-lg-9',
                    'desc' => $content_warning,
                    'autoload_rte' => true
                )
            ),
            'buttons' => array(
                array(
                    'title' => $this->module->l('Save'),
                    'type' => 'submit',
                    'icon' => 'process-icon-save',
                    'class' => 'pull-right',
                    'name' => 'submitAdd' . $this->table,
                ),
                array(
                    'title' => $this->module->l('Save And Stay'),
                    'type' => 'submit',
                    'icon' => 'process-icon-save',
                    'class' => 'pull-right',
                    'name' => 'submitAdd' . $this->table . 'AndStay',
                )
            )
        );

        $this->fields_value = array(
            'subject' => $obj->subject,
            'body' => $obj->body
        );

        $this->show_form_cancel_button = true;

        // autoload rich text editor (tiny mce)
        $this->tpl_form_vars['tinymce'] = true;
        $iso = Language::getIsoById($id_lang);
        $this->tpl_form_vars['iso'] = file_exists(_PS_CORE_DIR_ . '/js/tiny_mce/langs/' . $iso . '.js') ? $iso : 'en';
        $this->tpl_form_vars['path_css'] = _THEME_CSS_DIR_;
        $this->tpl_form_vars['ad'] = __PS_BASE_URI__ . basename(_PS_ADMIN_DIR_);

        return parent::renderForm();
    }

    public function processAdd()
    {
        $this->redirect_after = self::$currentIndex . '&token=' . $this->token;
    }

    public function processUpdate()
    {
        $this->copyFromPost($this->object, $this->table);
//        print_r($this->object);die;
//        $language = Language::getLanguages(true);
        $pass = 1;
        $pass1 = 1;
        foreach ($this->object->subject as $value) {
            if (empty(trim($value))) {
                $pass = 0;
                $this->context->cookie->__set(
                    'kb_redirect_error',
                    $this->module->l('Subject can not be blank for all available langauges.')
                );
                $this->redirect_after = self::$currentIndex . '&' .
                    $this->identifier . '=' . (int)$this->object->id
                    . '&update' . $this->table . '&token=' . $this->token;
            }
        }
        foreach ($this->object->body as $value) {
            if (empty(trim($value))) {
                $pass1 = 0;
                $this->context->cookie->__set(
                    'kb_redirect_error',
                    $this->module->l('Email Content can not be blank for all available langauges.')
                );
                $this->redirect_after = self::$currentIndex . '&'
                    . $this->identifier . '=' . (int)$this->object->id
                    . '&update' . $this->table . '&token=' . $this->token;
            }
        }
        if ($pass == 1 && $pass1 == 1) {
            if ($this->object->save()) {
                $this->context->cookie->__set(
                    'kb_redirect_success',
                    $this->module->l('Template successfully updated.')
                );
                if ($this->display == 'edit') {
                    $this->redirect_after = self::$currentIndex . '&' . $this->identifier . '=' . (int)$this->object->id
                        . '&update' . $this->table . '&token=' . $this->token;
                } else {
                    $this->redirect_after = self::$currentIndex . '&token=' . $this->token;
                }
            } else {
                $this->errors[] = Tools::displayError($this->module->l('Error occurred while updating the template.'));
            }
        }
    }
    
    public function initProcess()
    {
//        print_r(Tools::getAllValues());die;
        parent::initProcess();
    }
}
