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

class KbmarketplaceFrontCoreModuleFrontController extends ModuleFrontController
{
    protected $kb_module_name = 'kbmarketplace';
    protected $kbtemplate = 'not_found_page.tpl';
    protected $seller_image_path;
    //Show error message on same page
    protected $Kberrors;
    //Show success/confirmation message on same page
    protected $Kbconfirmation;

    public function __construct()
    {
        $this->context = Context::getContext();
        if (Configuration::get('KB_MARKETPLACE') === false
            || Configuration::get('KB_MARKETPLACE') == 0) {
            Tools::redirect(
                $this->context->link->getPageLink(
                    'index',
                    (bool)Configuration::get('PS_SSL_ENABLED')
                )
            );
        }
        parent::__construct();

        $this->context->smarty->assign(array(
            'HOOK_KBLEFT_COLUMN' => null,
            'HOOK_KBRIGHT_COLUMN' => null,
        ));

        $this->seller_image_path = KbGlobal::getBaseLink((bool)Configuration::get('PS_SSL_ENABLED'))
            . 'img/' . KbSeller::SELLER_PROFILE_IMG_PATH;

        $this->context->smarty->assign('kb_module_name', $this->kb_module_name);
        $this->context->smarty->assign(
            'kb_image_path',
            KbGlobal::getBaseLink((bool)Configuration::get('PS_SSL_ENABLED'))
            . 'modules/' . $this->kb_module_name . '/views/img/'
        );

        $this->context->smarty->assign(
            'kb_current_request',
            $this->context->link->getModuleLink(
                $this->kb_module_name,
                $this->controller_name,
                array(),
                (bool)Configuration::get('PS_SSL_ENABLED')
            )
        );
    }

    public function setMedia()
    {
        parent::setMedia();
        $this->addCSS($this->getKbModuleDir() . 'views/css/front/kblayout.css');
        $this->addJS($this->getKbModuleDir() . 'views/js/front/kb-common.js');
        $this->addJS($this->getKbModuleDir() . 'views/js/front/kb-list.js');
    }

    public function initContent()
    {
        parent::initContent();
        $this->context->smarty->assign(array(
            'HOOK_LEFT_COLUMN' => null,
            'HOOK_RIGHT_COLUMN' => null
        ));

        if ($this->kbtemplate) {
            $template = $this->fetchTemplate();
        } else {
            $template = '';
        }

        if (isset($this->context->cookie->redirect_error)) {
            $this->Kberrors[] = $this->context->cookie->redirect_error;
            unset($this->context->cookie->redirect_error);
        }

        if (isset($this->context->cookie->redirect_success)) {
            $this->Kbconfirmation[] = $this->context->cookie->redirect_success;
            unset($this->context->cookie->redirect_success);
        }

        $this->context->smarty->assign('mobile_device', $this->context->getMobileDevice());
        $this->context->smarty->assign('kb_errors', $this->Kberrors);
        $this->context->smarty->assign('kb_confirmation', $this->Kbconfirmation);
        $this->context->smarty->assign('kb_layout_dir', $this->getKbTemplateDir() . 'layouts/');
        $this->context->smarty->assign('TEMPLATE', $template);

        //ajax request and validation errors
        $this->context->smarty->assign(
            'ajax_error',
            $this->module->l('Technical Error: Contact to support')
        );
        $this->context->smarty->assign(
            'required_field_error',
            $this->module->l('Required Field')
        );
        $this->context->smarty->assign(
            'invalid_field_error',
            $this->module->l('Invalid value')
        );

        $layout = $this->getKbLayout();
        if ($layout) {
            $this->setTemplate($layout);
        } else {
            Tools::displayAsDeprecated(
                sprintf(
                    $this->module->l('Template file is missing from %s directory.'),
                    $this->getKbTemplateDir()
                )
            );
        }
    }

    public function fetchTemplate()
    {
        if (!$path = $this->getKbTemplatePath($this->kbtemplate)) {
            throw new PrestaShopException("Template ' . $this->kbtemplate . ' not found");
        }

        return $this->context->smarty->fetch($path);
    }

    protected function setKbTemplate($template)
    {
        $this->kbtemplate = $template;
    }

    public function getKbTemplatePath($template)
    {
        if (Tools::file_exists_cache($this->getKbTemplateDir() . $template)) {
            return $this->getKbTemplateDir() . $template;
        }

        return false;
    }

    public function getKbLayout()
    {
        $layout = false;

        if (!$layout && file_exists($this->getKbTemplateDir() . 'layout.tpl')) {
            $layout = 'module:'.$this->kb_module_name.'/views/templates/front/layout.tpl';
        }

        return $layout;
    }

    protected function getKbTemplateDir()
    {
        return $this->getKbModuleDir() . 'views/templates/front/';
    }

    protected function getKbModuleDir()
    {
        return _PS_MODULE_DIR_ . $this->kb_module_name . '/';
    }

    public function getCategoryList()
    {
        $categories = KbGlobal::getCategories();

        return $categories;
    }

    public function getTranslatedText($text, $class)
    {
        return Translate::getModuleTranslation('kbmarketplace', $text, $class);
    }
}
