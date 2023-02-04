
<div class="wrapper ">

    <button class="d-none btn btn-primary test-conn-btn" data-url-attribute={$controller_url} data-token-attribute={$token}>Test connection</button>


    <div class="category-selection-wrapper d-flex  flex-column">
        <h3>Categories List</h3>
        <label for="categories-slist" class="text-left">Choose a category to create a FAQ for:</label>
        <select class="category-select my-1 w-25" name="categories-list" id="categories-list">
            {foreach $categories item=$cat}
                <option value="{$cat.id_category}" id="option-cat-{$cat.id_category}">{$cat.name}</option>
            {/foreach}
        </select>
    </div>

    <div class="d-flex category-faq-content">
        
        
    </div>

</div>


