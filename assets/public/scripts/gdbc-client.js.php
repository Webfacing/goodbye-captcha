<?php

defined( 'ABSPATH' ) || exit;

add_filter('nocache_headers', function($arrHeaders){
	$arrHeaders['Cache-Control'] = 'no-store, no-cache, must-revalidate, max-age=0';
	$arrHeaders['Content-Type']  = 'application/javascript; charset=utf-8';
	
	return $arrHeaders;
}, 1);

nocache_headers();

if(null === ($settingsModuleInstance = GdbcModulesController::getPublicModuleInstance(GdbcModulesController::MODULE_SETTINGS))){
	exit;
}

$arrPlaceHolders = array(
    '__INPUT_NAME__'  => $settingsModuleInstance->getOption(GdbcSettingsAdminModule::OPTION_HIDDEN_INPUT_NAME),
    '__AJAX_URL__'    => MchGdbcWpUtils::getAjaxUrl(),
    '__AJAX_NONCE__'  => GdbcAjaxController::getAjaxNonce(),
    '__AJAX_ACTION__' => GdbcAjaxController::ACTION_RETRIEVE_TOKEN,
    
);

$scriptOutput = <<<Output
(function() {'use strict';
    if (!Array.isArray){Array.isArray = function(arg){return Object.prototype.toString.call(arg) === '[object Array]';};}
    if (!String.prototype.trim){String.prototype.trim = function () {return this.replace(/^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g, '');};}

    var WPBruiserClient = function(){
        var browserInfo = new Array();
        function init(){
            var w=window,d=document,e=0,f=0;e|=w.ActiveXObject?1:0;e|=w.opera?2:0;e|=w.chrome?4:0;
            e|='getBoxObjectFor' in d || 'mozInnerScreenX' in w?8:0;e|=('WebKitCSSMatrix' in w||'WebKitPoint' in w||'webkitStorageInfo' in w||'webkitURL' in w)?16:0;
            e|=(e&16&&({}.toString).toString().indexOf("\\n")===-1)?32:0;f|='sandbox' in d.createElement('iframe')?1:0;f|='WebSocket' in w?2:0;
            f|=w.Worker?4:0;f|=w.applicationCache?8:0;f|=w.history && history.pushState?16:0;f|=d.documentElement.webkitRequestFullScreen?32:0;f|='FileReader' in w?64:0;

            var ua = navigator.userAgent.toLowerCase();
            var regex = /compatible; ([\w.+]+)[ \/]([\w.+]*)|([\w .+]+)[: \/]([\w.+]+)|([\w.+]+)/g;
            var match = regex.exec(ua);
            browserInfo = {screenWidth:screen.width,screenHeight:screen.height,engine:e,features:f};
            while (match !== null) {
                var prop = {};
                if (match[1]) {
                    prop.type = match[1];
                    prop.version = match[2];
                } else if (match[3]) {
                    prop.type = match[3];
                    prop.version = match[4];
                } else {
                    prop.type = match[5];
                }
                prop.type = (prop.type).trim().replace('.','').replace(' ','_');
                var value = prop.version ? prop.version : true;
                if (browserInfo[prop.type]) {
                    !Array.isArray(browserInfo[prop.type])?browserInfo[prop.type]=new Array(browserInfo[prop.type]):'';
                    browserInfo[prop.type].push(value);
                }
                else browserInfo[prop.type] = value;
                match = regex.exec(ua);
            }
        };


        var requestTokens = function(){for(var i = 0; i < document.forms.length; ++i){retrieveToken(document.forms[i]);}};

        function retrieveToken(formElement){

            var requestObj = (window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP"));

            var formFieldElm = formElement.querySelector('input[name="__INPUT_NAME__"]');
            if(!requestObj || !formFieldElm) return;
            var ajaxData = [];

            ajaxData['__INPUT_NAME__'] = '__AJAX_NONCE__';
            ajaxData['action']      = '__AJAX_ACTION__';
            ajaxData['requestTime'] = (new Date()).getTime();
            ajaxData['browserInfo'] = JSON.stringify(browserInfo);

            requestObj.open('POST', '__AJAX_URL__', true);
            requestObj.setRequestHeader("Content-type", "application/x-www-form-urlencoded; charset=UTF-8");
            requestObj.setRequestHeader("X-Requested-With", "XMLHttpRequest");
            requestObj.setRequestHeader('Accept',"application/json, text/javascript, */*; q=0.01");
            requestObj.send(serializeObject(ajaxData));

            requestObj.onreadystatechange = function () {
                if (4 === requestObj.readyState && 200 === requestObj.status)
                {
                    try
                    {
                        var rs = JSON.parse(requestObj.responseText);
                        if(rs.data === 'undefined')
                            return;

                        var tokens = {};

                        for(var p in rs.data)
                        {
                            if(p=='token')
                            {
                                formFieldElm.value = rs.data[p];
                                tokens[formFieldElm.name] = null;
                            }
                            else
                            {
                                var value = '', arrValues = rs.data[p].split('|');
                                for (var i = 0; i < arrValues.length; ++i) {
                                    if (browserInfo.hasOwnProperty(arrValues[i]))
                                        value += browserInfo[arrValues[i]];
                                }

                                var elm = document.createElement("input");elm.name = p;elm.value=value;elm.type='hidden';formElement.appendChild(elm);
                                tokens[elm.name] = null;

                                if((' ' + formElement.className + ' ').indexOf(' mailpoet_form ') > -1){
                                    elm.name = 'data[' + p + ']';formFieldElm.name = 'data[' + formFieldElm.name + ']';
                                }
                            }
                        }

                        window.jQuery && jQuery.ajaxPrefilter(function( options, originalOptions, jqXHR ) {

                            if( ! ('action' in originalOptions.data)  || originalOptions.data.action !== 'nf_ajax_submit')
                                return;

                            for(var token in tokens){
                                tokens[token] = formElement.querySelector('input[name="'+token+'"]');
                                tokens[token] && (tokens[token] = tokens[token].value );
                            }

                            options.data = jQuery.param(jQuery.extend(originalOptions.data||{}, tokens));

                        });



                    }
                    catch(e){console.log(e.message);}
                }
            }
        }

        init();

        function serializeObject(obj) {
            var str = [];
            for(var p in obj)
                if (obj.hasOwnProperty(p)) {
                    str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
                }
            return str.join("&");
        }
        return {requestTokens : requestTokens};
    }

    window.WPBruiserClient = new WPBruiserClient();window.WPBruiserClient.requestTokens();

})();
Output;


/**
 * compressed javascript https://jscompress.com/
 */
//$scriptOutput = <<<Output
//!function(){"use strict";Array.isArray||(Array.isArray=function(e){return"[object Array]"===Object.prototype.toString.call(e)}),String.prototype.trim||(String.prototype.trim=function(){return this.replace(/^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g,"")});window.WPBruiserClient=new function(){var p=new Array;function t(s){var c=window.XMLHttpRequest?new XMLHttpRequest:new ActiveXObject("Microsoft.XMLHTTP"),u=s.querySelector('input[name="__INPUT_NAME__"]');if(c&&u){var e=[];e['__INPUT_NAME__']="__AJAX_NONCE__",e.action="__AJAX_ACTION__",e.requestTime=(new Date).getTime(),e.browserInfo=JSON.stringify(p),c.open("POST","__AJAX_URL__",!0),c.setRequestHeader("Content-type","application/x-www-form-urlencoded; charset=UTF-8"),c.setRequestHeader("X-Requested-With","XMLHttpRequest"),c.setRequestHeader("Accept","application/json, text/javascript, */*; q=0.01"),c.send(function(e){var t=[];for(var n in e)e.hasOwnProperty(n)&&t.push(encodeURIComponent(n)+"="+encodeURIComponent(e[n]));return t.join("&")}(e)),c.onreadystatechange=function(){if(4===c.readyState&&200===c.status)try{var e=JSON.parse(c.responseText);if("undefined"===e.data)return;var a={};for(var t in e.data)if("token"==t)u.value=e.data[t],a[u.name]=null;else{for(var n="",r=e.data[t].split("|"),i=0;i<r.length;++i)p.hasOwnProperty(r[i])&&(n+=p[r[i]]);var o=document.createElement("input");o.name=t,o.value=n,o.type="hidden",s.appendChild(o),a[o.name]=null,-1<(" "+s.className+" ").indexOf(" mailpoet_form ")&&(o.name="data["+t+"]",u.name="data["+u.name+"]")}window.jQuery&&jQuery.ajaxPrefilter(function(e,t,n){if("action"in t.data&&"nf_ajax_submit"===t.data.action){for(var r in a)a[r]=s.querySelector('input[name="'+r+'"]'),a[r]&&(a[r]=a[r].value);e.data=jQuery.param(jQuery.extend(t.data||{},a))}})}catch(e){console.log(e.message)}}}}return function(){var e=window,t=document,n=0,r=0;n|=e.ActiveXObject?1:0,n|=e.opera?2:0,n|=e.chrome?4:0,n|="getBoxObjectFor"in t||"mozInnerScreenX"in e?8:0,n|="WebKitCSSMatrix"in e||"WebKitPoint"in e||"webkitStorageInfo"in e||"webkitURL"in e?16:0,n|=16&n&&-1==={}.toString.toString().indexOf("\\n")?32:0,r|="sandbox"in t.createElement("iframe")?1:0,r|="WebSocket"in e?2:0,r|=e.Worker?4:0,r|=e.applicationCache?8:0,r|=e.history&&history.pushState?16:0,r|=t.documentElement.webkitRequestFullScreen?32:0,r|="FileReader"in e?64:0;var a=navigator.userAgent.toLowerCase(),i=/compatible; ([\w.+]+)[ \/]([\w.+]*)|([\w .+]+)[: \/]([\w.+]+)|([\w.+]+)/g,o=i.exec(a);for(p={screenWidth:screen.width,screenHeight:screen.height,engine:n,features:r};null!==o;){var s={};o[1]?(s.type=o[1],s.version=o[2]):o[3]?(s.type=o[3],s.version=o[4]):s.type=o[5],s.type=s.type.trim().replace(".","").replace(" ","_");var c=!s.version||s.version;p[s.type]?(Array.isArray(p[s.type])||(p[s.type]=new Array(p[s.type])),p[s.type].push(c)):p[s.type]=c,o=i.exec(a)}}(),{requestTokens:function(){for(var e=0;e<document.forms.length;++e)t(document.forms[e])}}},window.WPBruiserClient.requestTokens()}();
//Output;

echo str_replace(array_keys($arrPlaceHolders), $arrPlaceHolders, $scriptOutput);


