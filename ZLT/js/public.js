var pubilc = {
    getCookie: function(name){      //获取cookie值
        var cname = name + "=";
        var cookieArr = document.cookie.split(";");
        for(var i=0;i<cookieArr.length;i++){
            var c = cookieArr[i].trim();
            if(c.indexOf(cname) == 0){
                return c.substring(cname.length, c.length);
            }
        }
        return "";
    },
    setCookie: function(name, value, time){     //设置cookie
        var d = new Date(), expires = "", ctime = time || 0;
        if(ctime != 0){
            d.setTime(d.getTime()+(timer*24*60*60*1000));
            expires = "expires="+d.toGMTString();
        }
        document.cookie = cName + "=" + cValue + ";" + expires+";path=/";
    },
    delCookie: function(name){      //删除cookie
        var Path = cPath || "/";
        setCookie(name, getCookie(name), -1, Path);
    },
    strUrl: function(){
        var reg=/[<'/"]/g;
        var Url =window.location.search.slice(1).replace(reg,"%ABC").split("&"), obj = {};
        for(var i=0;i<Url.length;i++){
            var arrurl=arrurl[i].split("=");
            obj[arrurl[0]]=(arrurl[1]);
        }
    },
    isWXBrowser: function(){
        var wx=navigator.userAgent.toLowerCase();
        return wx.match(/microMessenger/i) == "micromessenger" ? true : false;
    }
};