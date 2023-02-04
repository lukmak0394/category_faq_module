<h3>FAQ for category: <span class="cat-title">{$id_category} - {$category_name} - language: {$current_lang}</span></h3>

<div class="d-flex flex-column w-50">
    <input type="text" id="question" name="question" class="mb-1 d-block" placeholder="Question">
    <textarea id="answer" name="answer"  placeholder="Type an answer here"></textarea>
    <button class="btn btn-primary submit-qa mt-1">Add</button>
</div> 

<div class="faq-elements-wrapper">
    {if $faq}
        {foreach $faq item=$element}
            <div class="d-flex flex-column w-50 my-1 mx-1">
                <label class="d-flex align-center w-100">
                    <span class="d-block">Question: </span>
                    <input type="text" id="existing-question" name="existing-question" class="w-100 mx-1 d-block" value="{$element.question}">
                </label>
                <label class="d-flex align-center w-100">
                    <span class="d-block">Answer: </span>
                    <input type="text" id="existing-answer" name="existing-answer" class=" w-100 mx-1 d-block" value="{$element.answer}">
                </label>
                <div class="action-buttons d-flex w-100 mt-1">
                    <button class="btn btn-primary w-50 update-faq-btn" data-id-faq="{$element.id_faq}">Edit</button>
                    <button class="btn btn-secondary w-50 delete-faq-btn" data-id-faq="{$element.id_faq}">Delete</button>
                </div>

            </div>
        {/foreach}
    {/if}

</div>
