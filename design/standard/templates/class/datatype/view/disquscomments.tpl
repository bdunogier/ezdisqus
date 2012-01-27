<div class="block">
    <label>{"Comments activated by default"|i18n( "ezdisqus/datatype" )}:</label>
    <p>
        {if $class_attribute.data_int1}
            {'Yes'|i18n( "ezdisqus/datatype" )}
        {else}
            {'No'|i18n( "ezdisqus/datatype" )}
        {/if}
    </p>
</div>
