<h2>{'Disqus comments'|i18n( 'ezdisqus/admin' )}</h2>
{def $disqus_shortname = ezini( 'Base', 'DisqusShortname', 'disqus.ini' )
     $default_tab = ezini( 'DashboardBlock_disquscombination', 'DefaultTab', 'dashboard.ini' )
     $colorTheme = ezini( 'DashboardBlock_disquscombination', 'Color', 'dashboard.ini' )}
<script type="text/javascript"
        src="http://{$disqus_shortname}.disqus.com/combination_widget.js?num_items={$block.number_of_items}&hide_mods=0&color={$colorTheme}&default_tab={$default_tab}&excerpt_length=200"></script>
