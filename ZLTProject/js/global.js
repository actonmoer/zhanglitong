/**
 * Created by xiaocai on 2017/6/5 0005.
 */

//返回上一级
function HistoryURL(){
    if(document.referrer == ""){
        window.location.href="/ZLTProject";
    }else{
        window.location.href=document.referrer;
    }
}
