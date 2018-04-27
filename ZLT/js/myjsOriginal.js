//设置Cookie
function setCookie(cName, cValue, exdays, cPath){
    var d = new Date();
    var timer = "" || exdays;
    d.setTime(d.getTime()+(timer*24*60*60*1000));
    var expires = "expires="+d.toGMTString();
    if(cPath){
        if(timer){
            document.cookie = cName + "=" + cValue + ";" + expires+";path=" + cPath;
        }else{
            document.cookie = cName + "=" + cValue + ";path=" + cPath;
        }
    }else{
        document.cookie = cName + "=" + cValue + ";" + expires;
    }
}
//获取Cookie
function getCookie(cName){
    var name = cName + "=";
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++){
        var c = ca[i].trim();
        if(c.indexOf(name) == 0) return c.substring(name.length,c.length);
    }
    return "";
}
//检测Cookie
function checkCookie(cookieName){
    var username=getCookie(cookieName);
    if(username){
        return true;
    }else{
        return false;
    }
}
//删除Cookie
function delCookie(cName, cPath){
    cPath = cPath || "/";
    setCookie(cName, getCookie(cName), -1, cPath);
}

//清除所有cookies
function clearCookie(){
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++){
        var c = ca[i].trim().split('=');
        setCookie(c[0], c[1], -1, '/');
    }
}

//url字符串切割
function strUrl(url){
    var url=window.location.search;
    var obj=new Object();
    url=url.slice(1);
    //url = encodeURIComponent(url);
    var arrurl=url.split("&");
    for(var i=0;i<arrurl.length;i++){
        var n_arrurl=arrurl[i].split("=");
        obj[n_arrurl[0]]=(n_arrurl[1]);
    }
    return obj;
}
//时间转换
function mtime(timer){
    var d=timer ? new Date(timer*1000) : new Date();
    return d.getFullYear()+"-"+Num(d.getMonth()+1)+"-"+Num(d.getDate())+" "+Num(d.getHours())+":"+Num(d.getMinutes());
}
//时间转换1
function mtime1(timer){
    var d=timer ? new Date(timer*1000) : new Date();
    return d.getFullYear()+"-"+Num(d.getMonth()+1)+"-"+Num(d.getDate());
}
//小于10前面补0
function Num(n){
    return parseInt(n) < 10 ? "0"+n : n;
}
//格式化数字 例如123456 》输出》 123,456.00
function formateMoney(n,len){
    var t="",result;
    len = len > 0 && len <= 20 ? len : 2;
    n=parseFloat((n+"").replace(/[^\d\.-]/g,"")).toFixed(len)+"";   //保留小数点
    var l=n.split(".")[0].split("").reverse();      //切割数字获得小数点左边的数字，再切割
    var r=n.split(".")[1];                          //切割数字获得小数点右边边的数字
    for(var i=0;i<l.length;i++){
        t += l[i]+((i+1)%3 == 0 && (i+1) != l.length ? "," : "");
    }
    result=t.split("").reverse().join("")+"."+r;
    return result;
}
//添加菜单
function addMenu(arrMenu,callback){
    var reg=/[<%'>.;:*-+\/]/g;
    var count=0;
    arrMenu=arrMenu.reverse();
    var str='<div id="bg-menu"><div class="bg0" onclick="closeMenu(\'bg-menu\')"></div><div class="bg-menu"><ul class="bg-menu-ul">'+
            '<li><a href="javascript:closeMenu();">取消</a></li></ul></div></div>';
    if($("#bg-menu").length <= 0){
        $("body").append(str);
        for(var i=0;i<arrMenu.length;i++){
            $(".bg-menu-ul").prepend('<li><a>'+arrMenu[i].replace(reg,"%ABC")+'</a></li>');
        }
        if(typeof callback == "function"){
            callback(arrMenu);
        }
        var h=$(".bg-menu").height();
        $(".bg-menu").css({potistion:"absolute",bottom:-h+"px"});
        $(".bg-menu").css("bottom","");
    }
}

function closeMenu(id){
    $("#bg-menu").remove();
}
//返回上一级
function HistoryURL(){
    if(document.referrer == ""){
        window.location.href="/";
    }else{
        window.history.go(-1);
    }
}
//检测是否是微信浏览器
function isWeiXin(){
    var wx=navigator.userAgent.toLowerCase();
    if(wx.match(/microMessenger/i)=="micromessenger"){
        return true;
    }else{
        return false;
    }
}
//检测是否登录
function isLogin(){
    if(!checkCookie("cpk_auth")){
        window.location.href="/mobile/login.php?Callback="+encodeURIComponent(window.location.href);
    }else{
        return true;
    }
}
//退出登录
function loginOut(){
    var tips = confirm("是否确定退出登录");
    if(tips){
        setCookie("cpk_auth",getCookie("cpk_auth"),-1,"/");
        if(!checkCookie("cpk_auth")){
            window.location.href="/";
        }
    }
}
//底部导航的状态
function footState(i){
    var footerD = $("footer div"),
        addI = i + 1;
    footerD.eq(i).find("i").css("color","#3d95e5").siblings("p").css("color","#3d95e5");
    footerD.eq(i).find("i").removeClass("icon-n-foot"+addI).addClass("icon-a-foot"+addI);
}

//加载中
function loading(id){
    var str='<div id="loading">' +
        '<div style="position: fixed;top: 0;left: 0;bottom: 0;right: 0;width: 100%;height: 100%;z-index:99;background-color: rgba(0,0,0,.4);"></div>' +
        '<div style="position: fixed;top: 50%;left: 50%;width: 100%;height: 100%;z-index:100;margin: -24px 0 0 -24px;">' +
        '<img src="/mobile/static/img/my_loading.gif" alt="加载中">' +
        '</div>' +
        '</div>';
    if(typeof id === "string"){
        $("#"+id).remove();
    }else{
        $("body").append(str);
    }
}
//渲染底部导航栏
function footNav(){
    var foot = "<footer>\n" +
        "<div>\n" +
        "<a href=\"/mobile\" class=\"f-a\">\n" +
        "<i class=\"iconfont icon-n-foot1\" style=\"margin-top: .25rem;\"></i>\n" +
        "<p>首页</p >\n" +
        "</a>\n" +
        "</div>\n" +
        "<div>\n" +
        "<a href=\"/member/alert.php\" class=\"f-a\">\n" +
        "<i class=\"iconfont icon-n-foot2\" style=\"margin-top: .25rem;\"></i>\n" +
        "<p>匹配</p >\n" +
        "</a>\n" +
        "</div>\n" +
        "<div>\n" +
        "<a href=\"/mobile/Mail/service.html\" class=\"f-a\">\n" +
        "<i class=\"iconfont icon-n-foot3\" style=\"margin-top: .25rem;\"></i>\n" +
        "<p>通通</p >\n" +
        "</a>\n" +
        "</div>\n" +
        "<div>\n" +
        "<a href=\"/mobile/User/usercenter.html\" id=\"userCenter\" class=\"f-a\">\n" +
        "<i class=\"iconfont icon-n-foot4\" style=\"margin-top: .25rem;\"></i>\n" +
        "<p>我的</p >\n" +
        "</a>\n" +
        "</div>\n" +
        "<div>\n" +
        "<a href=\"/mobile/more.php\" class=\"f-a\">\n" +
        "<i class=\"iconfont icon-n-foot5\" style=\"margin-top: .25rem;\"></i>\n" +
        "<p>更多</p >\n" +
        "</a>\n" +
        "</div>\n" +
        "</footer>";
    return foot;
}

