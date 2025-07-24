@if($config['general']['site_chat'] and !$agent->isMobile())
    <script type='text/javascript'>
        function jivo_onLoadCallback() { window.jivo_cstm_widget = document.createElement('div');jivo_cstm_widget.setAttribute('id', 'jivo_custom_widget');document.body.appendChild(jivo_cstm_widget);jivo_cstm_widget.onclick = function () {jivo_api.open();};if (jivo_config.chat_mode === "online") {jivo_cstm_widget.setAttribute("class", "jivo_online");}window.jivo_cstm_widget.style.display = 'block';}
        function jivo_onOpen() { if (jivo_cstm_widget) jivo_cstm_widget.style.display = 'none';}
        function jivo_onClose() { if (jivo_cstm_widget) jivo_cstm_widget.style.display = 'block';}
        (function() { var widget_id = '{!! $config['general']['site_chat'] !!}';var d = document;var w = window; function l() {var s = document.createElement('script');s.type = 'text/javascript';s.async = false;s.src = '//code.jivosite.com/script/widget/' + widget_id;var ss = document.getElementsByTagName('script')[0];ss.parentNode.insertBefore(s, ss);} if (d.readyState == 'complete') {l();} else {if (w.attachEvent) {w.attachEvent('onload', l);} else {w.addEventListener('load', l, false);}}})();
    </script>
@endif