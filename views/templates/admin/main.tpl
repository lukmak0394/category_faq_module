
<div class="wrapper ">

    <button class="d-none btn btn-primary test-conn-btn" data-url-attribute={$controller_url} data-token-attribute={$token}>Test connection</button>


    <div class="category-selection-wrapper d-flex  flex-column">
        <h3>Categories List</h3>
        <label for="categories-slist" class="text-left">Choose a category to get/create a FAQ for:</label>
        <select class="category-select my-1 w-50" name="categories-list" id="categories-list">
            {foreach $categories item=$cat}
                <option value="{$cat.id_category}" id="option-cat-{$cat.id_category}">{$cat.name}</option>
            {/foreach}
        </select>
        <label for="languages-list" class="text-left">Choose a language of FAQ:</label>
        <select class="category-select my-1  w-50" name="languages-list" id="languages-list">
            {foreach $languages item=$lang}
                <option value="{$lang.id_lang}" id="option-lang-{$lang.id_lang}">{$lang.iso_code}</option>
            {/foreach}
        </select>
    </div>
    

    <div class="d-flex flex-column category-faq-content">
      
     
    </div>

</div>


