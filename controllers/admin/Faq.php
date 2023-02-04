<?php

class FaqController extends ModuleAdminController {
    
    private $module_name = "module:categoryfaq"; 

    public function initContent()
    {
        parent::initContent();


        $token = Tools::getAdminTokenLite('AdminCatalog');

        $this->context->smarty->assign(array(
            'controller_url' => 'index.php?controller=Faq&token='.$token.'',
            'categories' => $this->getAvailableCategories(),
        ));

        $template_file = _PS_MODULE_DIR_. 'categoryfaq/views/templates/admin/main.tpl';

        $content = $this->context->smarty->fetch($template_file);

        $this->context->smarty->assign(array(
            'content' =>  $content,
        ));

    }


    public function postProcess()
    {

        if(Tools::getIsset('ajax')){
            
            $this->ajaxCall(); 

        } else {
            parent::postProcess();

        }

    }

    public function ajaxCall()
    {

        $action = Tools::ucfirst(Tools::getValue('action'));

        if (!empty($action) && method_exists($this, 'ajax' . $action)) {
          
            $result = $this->{'ajax' . $action}();
        } else {
            $result = (array('error' => 'Ajax parameter used, but action \'' . Tools::getValue('action') . '\' is not defined'));
        }

        die(json_encode($result));
    }

    public function ajaxTest() {
        return true;
    }

    
    public function getAvailableCategories() {
        
        $categories =  Db::getInstance()->executeS('SELECT `id_category`, `name` FROM `ps_category_lang` WHERE `id_lang` = 1 ORDER BY `id_category` ASC');

        return $categories;

    }

    public function ajaxGetCategoryFaq() {
        $id_category = Tools::getValue('id_category');
        $category_name = Tools::getValue('category_name');

        $this->context->smarty->assign(array(
            'id_category' => $id_category,
            'category_name' => $category_name,
        ));

        return $this->context->smarty->fetch($this->module_name.'/views/templates/admin/category_faq.tpl');

    }

}