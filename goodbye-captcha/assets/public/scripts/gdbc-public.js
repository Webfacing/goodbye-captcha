(function(g,d,b,c){b = g.createElement('script');c=g.scripts[0];b.async=1;b.src=Gdbc.clientUrl+'-'+(new Date()).getTime();c.parentNode.insertBefore(b,c);})(document);

jQuery(document).ready(function($) {"use strict";
    var w=window,d=document,e=0,f=0;e|=w.ActiveXObject?1:0;e|=w.opera?2:0;e|=w.chrome?4:0;
    e|='getBoxObjectFor' in d || 'mozInnerScreenX' in w?8:0;e|=('WebKitCSSMatrix' in w||'WebKitPoint' in w||'webkitStorageInfo' in w||'webkitURL' in w)?16:0;
    e|=(e&16&&({}.toString).toString().indexOf("\n")===-1)?32:0;f|='sandbox' in d.createElement('iframe')?1:0;f|='WebSocket' in w?2:0;
    f|=w.Worker?4:0;f|=w.applicationCache?8:0;f|=w.history && history.pushState?16:0;f|=d.documentElement.webkitRequestFullScreen?32:0;f|='FileReader' in w?64:0;

    var ua = navigator.userAgent.toLowerCase();
    var regex = /compatible; ([\w.+]+)[ \/]([\w.+]*)|([\w .+]+)[: \/]([\w.+]+)|([\w.+]+)/g;
    var match = regex.exec(ua);
    Gdbc.browserInfo = {screenWidth:screen.width,screenHeight:screen.height,engine:e,features:f};
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
        prop.type = $.trim(prop.type).replace(".","").replace(" ","_");
        var value = prop.version ? prop.version : true;
        if (Gdbc.browserInfo[prop.type]) {
            !$.isArray(Gdbc.browserInfo[prop.type])?Gdbc.browserInfo[prop.type]=new Array(Gdbc.browserInfo[prop.type]):'';
            Gdbc.browserInfo[prop.type].push(value);
        }
        else Gdbc.browserInfo[prop.type] = value;
        match = regex.exec(ua);
    }
});
