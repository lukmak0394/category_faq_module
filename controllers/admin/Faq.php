<?php

class FaqController extends ModuleAdminController {
    
    private $module_name = "module:categoryfaq"; 

    public function initContent()
    {
        parent::initContent();

        $this->context->smarty->assign(array(
            'controller_url' => $this->getControllerUrl(),
            'categories' => $this->getAvailableCategories(),
            'languages' => $this->context->controller->getLanguages(),
        ));

        // main.tpl is the main template - at first there's only select with categories and the rest of the content is fetched in specific methods
        $template_file = _PS_MODULE_DIR_. 'categoryfaq/views/templates/admin/main.tpl';

        $content = $this->context->smarty->fetch($template_file);

        $this->context->smarty->assign(array(
            'content' =>  $content,
        ));

    }

    // Controller URL will be used in different methods so let's create a separate method for it
    public function getControllerUrl() {
        $token = Tools::getAdminTokenLite('AdminCatalog');
        return 'index.php?controller=Faq&token='.$token.'';
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

    // This is just to test a connection with the controller
    public function ajaxTest() {
        return true;
    }


    // Retreive whole FAQ to be displayed
    public function ajaxGetCategoryFaq() {

        $id_category = (int) Tools::getValue('id_category');
        $category_name = Tools::getValue('category_name');
        $id_lang = (int) Tools::getValue('id_lang');

        $this->context->smarty->assign(array(
            'controller_url' => $this->getControllerUrl(),
            'id_category' => $id_category,
            'category_name' => $category_name,
            'languages' => $this->context->controller->getLanguages(),
            'current_lang' => $this->getLanguageName($id_lang),
            'faq' => $this->getCategoryFaq($id_category,$id_lang),
        ));

        // In category_faq.tpl I put the form and the FAQ list. The template is fetched after selecting a category
        return $this->context->smarty->fetch($this->module_name.'/views/templates/admin/category_faq.tpl');

    }

    // In this function I get posted values and create a new element in a DB
    // Get values and create element
    public function ajaxGetQaValues() {
        $question = Tools::getValue('question');
        $answer = Tools::getValue('answer');
        $id_lang = (int) Tools::getValue('language_id');
        $id_category = (int) Tools::getValue('id_category');

        $this->createFaqElement($id_category,$id_lang,$question,$answer);

        
        $this->context->smarty->assign(array(
            'controller_url' => $this->getControllerUrl(),
            'id_lang' => $id_lang,
            'languages' => $this->context->controller->getLanguages(),
            'faq' => $this->getAddedElement($id_category,$id_lang),
        ));

        // faq_element.tpl represents one element (div with quesiton and answer)
        return $this->context->smarty->fetch($this->module_name.'/views/templates/admin/faq_element.tpl');

    }

    // Delete element
    public function ajaxDeleteFaqElement() {
        $id_faq = (int) Tools::getValue('id_faq');

        return $this->deleteElement($id_faq);
    }

    public function ajaxUpdateFaqElement() {
        $id_faq = (int) Tools::getValue('id_faq');
        $question = Tools::getValue('question');
        $answer = Tools::getValue('answer');

        $this->updateElement($id_faq,$question,$answer);
    }

    // Here I get list of available categories
    public function getAvailableCategories() {
        $categories =  Db::getInstance()->executeS('SELECT `id_category`, `name` FROM `ps_category_lang` WHERE `id_lang` = '.Configuration::get('PS_LANG_DEFAULT').' ORDER BY `id_category` ASC');
        return $categories;
    }

    // Function that creates a new FAQ element in DB
    public function createFaqElement($id_category,$id_lang,$question,$answer) {
        return Db::getInstance()->execute('INSERT INTO `lm_category_faq` (`id_category`, `id_lang`, `question`, `answer`) VALUES ('.$id_category.','.$id_lang.',"'.$question.'","'.$answer.'")');
    }

    // Get a whole FAQ related to selected category
    public function getCategoryFaq($id_category,$id_lang) {
        return Db::getInstance()->executeS('SELECT `id_faq`, `question`,`answer` FROM `lm_category_faq` WHERE `id_category` = '.$id_category.' AND `id_lang` = '.$id_lang.' ORDER BY `id_faq` DESC');
    }

    // Function that retreive newly added element - will be shown at first place after using button 
    public function getAddedElement($id_category,$id_lang) {
        return Db::getInstance()->executeS('SELECT `id_faq`, `question`,`answer` FROM `lm_category_faq` WHERE `id_category` = '.$id_category.' AND `id_lang` = '.$id_lang.' ORDER BY `id_faq` DESC LIMIT 1');
    }

    
    public function deleteElement($id_faq) {
        return Db::getInstance()->execute('DELETE FROM `lm_category_faq` WHERE `id_faq` = '.$id_faq.'');
    }

    public function updateElement($id_faq,$question,$answer) {
        return Db::getInstance()->execute('UPDATE `lm_category_faq` SET `question` = "'.$question.'", `answer` = "'.$answer.'" WHERE `id_faq` = '.$id_faq.'');
    }

    public function getLanguageName($id_lang) {
        return Db::getInstance()->getValue('SELECT `name` FROM `ps_lang` WHERE `id_lang` = '.$id_lang.'');
    }


}