{*
    Disqus template for comments count.
    Available variables :
      - $disqus_shortname (defaults to disqus.ini/[Base].DisqusShortname)

    You can pass those variables via the include parameters :
    {include uri="design:standard/disqus_count.tpl" disqus_shortname="my_shortname"}

    Then, don't forget to update your comments count links according to Disqus doc
    http://docs.disqus.com/developers/universal/
*}
<script type="text/javascript">
    var disqus_shortname = '{cond( is_set( $disqus_shortname ), $disqus_shortname, ezini( 'Base', 'DisqusShortname', 'disqus.ini' ) )}';

{literal}
    (function () {
        var s = document.createElement('script'); s.async = true;
        s.type = 'text/javascript';
        s.src = 'http://' + disqus_shortname + '.disqus.com/count.js';
        (document.getElementsByTagName('HEAD')[0] || document.getElementsByTagName('BODY')[0]).appendChild(s);
    }());
{/literal}

</script>
