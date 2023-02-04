{if $faq}
    {foreach $faq item=$element}
        {$element.question}
        {$element.answer}
    {/foreach}
{/if}