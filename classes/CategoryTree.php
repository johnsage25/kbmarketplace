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

class CategoryTree extends TreeCore
{
    const DEFAULT_TEMPLATE = 'tree_categories.tpl';
    const DEFAULT_NODE_FOLDER_TEMPLATE = 'tree_node_folder_radio.tpl';
    const DEFAULT_NODE_ITEM_TEMPLATE = 'tree_node_item_radio.tpl';

    private $disabled_categories;
    private $enabled_categories;
    private $input_name;
    public $lang;
    private $root_category;
    private $selected_categories;
    private $shop;
    private $use_checkbox;
    private $use_search;
    private $use_shop_restriction;

    public function __construct($id, $title = null, $root_category = null, $lang = null, $use_shop_restriction = true)
    {
        parent::__construct($id);

        if (isset($title)) {
            $this->setTitle($title);
        }

        if (isset($root_category)) {
            $this->setRootCategory($root_category);
        }

        $this->setLang($lang);
        $this->setUseShopRestriction($use_shop_restriction);
    }

    public function getData()
    {
        if (!isset($this->_data)) {
            $this->setData(
                Category::getNestedCategories(
                    $this->getRootCategory(),
                    $this->getLang(),
                    false,
                    null,
                    $this->useShopRestriction()
                )
            );
        }

        return $this->_data;
    }

    public function setDisabledCategories($value)
    {
        $this->disabled_categories = $value;
        return $this;
    }
    
    public function setEnabledCategories($value)
    {
        $this->enabled_categories = $value;
        return $this;
    }

    public function getDisabledCategories()
    {
        return $this->disabled_categories;
    }
    
    public function getEnabledCategories()
    {
        return $this->enabled_categories;
    }

    public function setInputName($value)
    {
        $this->input_name = $value;
        return $this;
    }

    public function getInputName()
    {
        if (!isset($this->input_name)) {
            $this->setInputName('categoryBox');
        }

        return $this->input_name;
    }

    public function setLang($value)
    {
        $this->lang = $value;
        return $this;
    }

    public function getLang()
    {
        if (!isset($this->lang)) {
            $this->setLang($this->getContext()->employee->id_lang);
        }

        return $this->lang;
    }

    public function getNodeFolderTemplate()
    {
        if (!isset($this->_node_folder_template)) {
            $this->setNodeFolderTemplate(self::DEFAULT_NODE_FOLDER_TEMPLATE);
        }

        return $this->_node_folder_template;
    }

    public function getNodeItemTemplate()
    {
        if (!isset($this->_node_item_template)) {
            $this->setNodeItemTemplate(self::DEFAULT_NODE_ITEM_TEMPLATE);
        }

        return $this->_node_item_template;
    }

    public function setRootCategory($value)
    {
        if (!Validate::isInt($value)) {
            throw new PrestaShopException('Root category must be an integer value');
        }

        $this->root_category = $value;
        return $this;
    }

    public function getRootCategory()
    {
        return $this->root_category;
    }

    public function setSelectedCategories($value)
    {
        if (!is_array($value)) {
            throw new PrestaShopException('Selected categories value must be an array');
        }

        $this->selected_categories = $value;
        return $this;
    }

    public function getSelectedCategories()
    {
        if (!isset($this->selected_categories)) {
            $this->selected_categories = array();
        }

        return $this->selected_categories;
    }

    public function setShop($value)
    {
        $this->shop = $value;
        return $this;
    }

    public function getShop()
    {
        if (!isset($this->shop)) {
            if (Tools::isSubmit('id_shop')) {
                $this->setShop(new Shop(Tools::getValue('id_shop')));
            } elseif ($this->getContext()->shop->id) {
                $this->setShop(new Shop($this->getContext()->shop->id));
            } elseif (!Shop::isFeatureActive()) {
                $this->setShop(new Shop(Configuration::get('PS_SHOP_DEFAULT')));
            } else {
                $this->setShop(new Shop(0));
            }
        }

        return $this->shop;
    }

    public function getTemplate()
    {
        if (!isset($this->_template)) {
            $this->setTemplate(self::DEFAULT_TEMPLATE);
        }

        return $this->_template;
    }

    public function setUseCheckBox($value)
    {
        $this->use_checkbox = (bool) $value;
        return $this;
    }

    public function setUseSearch($value)
    {
        $this->use_search = (bool) $value;
        return $this;
    }

    public function setUseShopRestriction($value)
    {
        $this->use_shop_restriction = (bool) $value;
        return $this;
    }

    public function useCheckBox()
    {
        return (isset($this->use_checkbox) && $this->use_checkbox);
    }

    public function useSearch()
    {
        return (isset($this->use_search) && $this->use_search);
    }

    public function useShopRestriction()
    {
        return (isset($this->use_shop_restriction) && $this->use_shop_restriction);
    }

    public function render($data = null)
    {
        if (!isset($data)) {
            $data = $this->getData();
        }

        if (isset($this->disabled_categories) && !empty($this->disabled_categories)) {
            $this->disableCategories($data, $this->getDisabledCategories());
        }

        if (isset($this->selected_categories) && !empty($this->selected_categories)) {
            $this->getSelectedChildNumbers($data, $this->getSelectedCategories());
        }

        //Default bootstrap style of search is push-right, so we add this button first
        if ($this->useSearch()) {
            $this->addAction(
                new TreeToolbarSearchCategories('Find a category:', $this->getId() . '-categories-search')
            );
            $this->setAttribute('use_search', $this->useSearch());
        }

        $collapse_all_str = '$(\'#' . $this->getId() . '\').tree(\'collapseAll\');
            $(\'#collapse-all-' . $this->getId() . '\').hide();
            $(\'#expand-all-' . $this->getId() . '\').show(); return false;';
        $collapse_all = new TreeToolbarLink(
            'Collapse All',
            '#',
            $collapse_all_str,
            'icon-collapse-alt'
        );
        $collapse_all->setAttribute('id', 'collapse-all-' . $this->getId());

        $expand_all_str = '$(\'#' . $this->getId() . '\').tree(\'expandAll\');
            $(\'#collapse-all-' . $this->getId() . '\').show();
            $(\'#expand-all-' . $this->getId() . '\').hide(); return false;';
        $expand_all = new TreeToolbarLink(
            'Expand All',
            '#',
            $expand_all_str,
            'icon-expand-alt'
        );
        $expand_all->setAttribute('id', 'expand-all-' . $this->getId());
        $this->addAction($collapse_all);
        $this->addAction($expand_all);

        if ($this->useCheckBox()) {
            $check_all = new TreeToolbarLink(
                'Check All',
                '#',
                'checkAllAssociatedCategories($(\'#' . $this->getId() . '\')); return false;',
                'icon-check-sign'
            );
            $check_all->setAttribute('id', 'check-all-' . $this->getId());
            $uncheck_all = new TreeToolbarLink(
                'Uncheck All',
                '#',
                'uncheckAllAssociatedCategories($(\'#' . $this->getId() . '\')); return false;',
                'icon-check-empty'
            );
            $uncheck_all->setAttribute('id', 'uncheck-all-' . $this->getId());
            $this->addAction($check_all);
            $this->addAction($uncheck_all);
            $this->setNodeFolderTemplate('tree_node_folder_checkbox.tpl');
            $this->setNodeItemTemplate('tree_node_item_checkbox.tpl');
            $this->setAttribute('use_checkbox', $this->useCheckBox());
        }

        $this->setAttribute('selected_categories', $this->getSelectedCategories());
        $this->getContext()->smarty->assign('root_category', Configuration::get('PS_ROOT_CATEGORY'));

        //Adding tree.js
        $js_path = __PS_BASE_URI__ . 'modules/kbmarketplace/views/js/front/kb_category_tree.js';
        if ($this->getContext()->controller->ajax) {
            $html = '<script type="text/javascript" src="' . $js_path . '"></script>';
        } else {
            $this->getContext()->controller->addJs($js_path);
        }

        //Create Tree Template
        $template = $this->getContext()->smarty->createTemplate(
            $this->getTemplateFile($this->getTemplate()),
            $this->getContext()->smarty
        );

        //Assign Tree nodes
        $template->assign($this->getAttributes())->assign(
            array(
                'id' => $this->getId(),
                'nodes' => $this->renderNodes($data)
            )
        );

        return (isset($html) ? $html : '') . $template->fetch();
    }

    //Override
    public function renderNodes($data = null)
    {
        if (!isset($data)) {
            $data = $this->getData();
        }

        if (!is_array($data) && !$data instanceof Traversable) {
            throw new PrestaShopException('Data value must be an traversable array');
        }

        $html = '';
        foreach ($data as $item) {
            if (array_key_exists('children', $item) && !empty($item['children'])) {
                $html .= $this->getContext()->smarty->createTemplate(
                    $this->getTemplateFile($this->getNodeFolderTemplate()),
                    $this->getContext()->smarty
                )->assign(
                    array(
                        'input_name' => $this->getInputName(),
                        'children' => $this->renderNodes($item['children']),
                        'node' => $item
                    )
                )->fetch();
            } else {
                $html .= $this->getContext()->smarty->createTemplate(
                    $this->getTemplateFile($this->getNodeItemTemplate()),
                    $this->getContext()->smarty
                )->assign(
                    array(
                        'input_name' => $this->getInputName(),
                        'node' => $item
                    )
                )->fetch();
            }
        }

        return $html;
    }

    private function disableCategories(&$categories, $disabled_categories = null)
    {
        foreach ($categories as &$category) {
            if (!isset($disabled_categories) || in_array($category['id_category'], $disabled_categories)) {
                if (!in_array($category['id_category'], $this->getEnabledCategories())) {
                    $category['disabled'] = true;
                }
                if (array_key_exists('children', $category) && is_array($category['children'])) {
                    self::disableCategories($category['children']);
                }
            } elseif (array_key_exists('children', $category) && is_array($category['children'])) {
                self::disableCategories($category['children'], $disabled_categories);
            }
        }
    }

    private function getSelectedChildNumbers(&$categories, $selected, &$parent = null)
    {
        $selected_childs = 0;

        foreach ($categories as &$category) {
            if (isset($parent) && in_array($category['id_category'], $selected)) {
                $selected_childs++;
            }

            if (isset($category['children']) && !empty($category['children'])) {
                $selected_childs += $this->getSelectedChildNumbers($category['children'], $selected, $category);
            }
        }

        if (!isset($parent['selected_childs'])) {
            $parent['selected_childs'] = 0;
        }

        $parent['selected_childs'] = $selected_childs;
        return $selected_childs;
    }
}
