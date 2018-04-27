;(function(minWidth, maxWidth){  //minWidth最小的宽度，maxWidth最大宽度
    var doc = document,
        win = window,
        docEl = doc.documentElement,
        tid;

    function setRootFontSize(){
        var width = docEl.getBoundingClientRect().width;
        if(minWidth >= width) width = minWidth;
        if(maxWidth < width) width = maxWidth;
        document.getElementsByTagName('html')[0].style.fontSize = (width * 100 / maxWidth) + 'px';
    }

    win.addEventListener("resize", function() {
        clearTimeout(tid);
        tid = setTimeout(setRootFontSize, 300);
    }, false);

    win.addEventListener("pageshow", function() {
        clearTimeout(tid);
        tid = setTimeout(setRootFontSize, 300);
    }, false);

    setRootFontSize();
})(320,640);