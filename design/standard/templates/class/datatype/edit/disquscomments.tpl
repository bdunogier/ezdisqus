<div class="block">
    <label for="ContentClass_disquscomments_enabled_{$class_attribute.id}">
        {"Comments activated by default"|i18n( "ezdisqus/datatype" )}:
    </label>
    <input type="checkbox"
            name="ContentClass_disquscomments_enabled_{$class_attribute.id}"
            id="ContentClass_disquscomments_enabled_{$class_attribute.id}"{if $class_attribute.data_int1} checked="checked"{/if} />
</div>
