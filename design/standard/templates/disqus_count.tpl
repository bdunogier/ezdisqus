{*literal}
<script type="text/javascript">
    {if is_set( $disqus_shortname )}
        var disqus_shortname = '{$disqus_shortname}';
    {/if}

    (function () {
        var s = document.createElement('script'); s.async = true;
        s.type = 'text/javascript';
        s.src = 'http://' + disqus_shortname + '.disqus.com/count.js';
        (document.getElementsByTagName('HEAD')[0] || document.getElementsByTagName('BODY')[0]).appendChild(s);
    }());
</script>
{/literal*}
