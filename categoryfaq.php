<?php
/**
* 2007-2023 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2023 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class CategoryFaq extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'categoryfaq';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'Łukasz Makowski - supportal';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('EH FAQ for custom category');
        $this->description = $this->l('Module that will allow to FAQ for custom categories');

        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        Configuration::updateValue('CATEGORYFAQ_LIVE_MODE', false);

        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = 'Faq';       
        $tab->position = 3;
		$tab->name = array();
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = 'Create FAQ';
        }
        $tab->id_parent = (int) Tab::getIdFromClassName('AdminCatalog');
        $tab->module = $this->name;
        $tab->add();
        $tab->save();


        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('displayBackOfficeHeader') &&
            $this->registerHook('displayFaq') &&
            $this->installMainTable();
    }

    public function uninstall()
    {

        if (!parent::uninstall() 
        || !$this->uninstallMainTable()
        || !Configuration::deleteByName('categoryfaq')
        ) {
            return false;
        }

        return true;

        return parent::uninstall();
    }

    public function installMainTable() {

        return Db::getInstance()->execute(
            'CREATE TABLE `lm_category_faq` (
                `id_faq` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `id_category` int(11) NOT NULL, 
                `id_lang` int(11) NOT NULL,
                `question` varchar(255) NULL, 
                `answer` varchar(255) NULL)
            '
        );

    }

    public function uninstallMainTable() {
        return Db::getInstance()->execute(
            "DROP TABLE lm_category_faq"
        );
    }


    /**
     * Load the configuration form
     */
    public function getContent()
    {
        /**
         * If values have been submitted in the form, process.
         */
        if (((bool)Tools::isSubmit('submitCategoryFaqModule')) == true) {
            $this->postProcess();
        }

        $this->context->smarty->assign('module_dir', $this->_path);

        $output = $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');

        return $output.$this->renderForm();
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitCategoryFaqModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Settings'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Live mode'),
                        'name' => 'CATEGORYFAQ_LIVE_MODE',
                        'is_bool' => true,
                        'desc' => $this->l('Use this module in live mode'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'prefix' => '<i class="icon icon-envelope"></i>',
                        'desc' => $this->l('Enter a valid email address'),
                        'name' => 'CATEGORYFAQ_ACCOUNT_EMAIL',
                        'label' => $this->l('Email'),
                    ),
                    array(
                        'type' => 'password',
                        'name' => 'CATEGORYFAQ_ACCOUNT_PASSWORD',
                        'label' => $this->l('Password'),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        return array(
            'CATEGORYFAQ_LIVE_MODE' => Configuration::get('CATEGORYFAQ_LIVE_MODE', true),
            'CATEGORYFAQ_ACCOUNT_EMAIL' => Configuration::get('CATEGORYFAQ_ACCOUNT_EMAIL', 'contact@prestashop.com'),
            'CATEGORYFAQ_ACCOUNT_PASSWORD' => Configuration::get('CATEGORYFAQ_ACCOUNT_PASSWORD', null),
        );
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();

        foreach (array_keys($form_values) as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }
    }

    /**
    * Add the CSS & JavaScript files you want to be loaded in the BO.
    */
    public function hookDisplayBackOfficeHeader()
    {
        $this->context->controller->addJS($this->_path.'views/js/back.js');
        $this->context->controller->addCSS($this->_path.'views/css/back.css');
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        $this->context->controller->addJS($this->_path.'/views/js/front.js');
        $this->context->controller->addCSS($this->_path.'/views/css/front.css');
    }

    public function hookDisplayFaq($params) {
        $id_category = (int) Tools::getValue('id_category');
        $id_lang = (int) $this->context->language->id;

        $faq = $this->getFaqToDisplay($id_category,$id_lang);

        $this->context->smarty->assign(array(
            'faq' => $faq,
        ));

        return $this->display(__FILE__, 'faq-front.tpl');
    }

    public function getFaqToDisplay($id_category,$id_lang) {
        return Db::getInstance()->executeS('SELECT `question`,`answer` FROM `lm_category_faq` WHERE `id_category` = '.$id_category.' AND `id_lang` = '.$id_lang.' ORDER BY `id_faq` DESC');
    }
}
