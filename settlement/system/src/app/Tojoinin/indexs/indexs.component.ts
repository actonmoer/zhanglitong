import { Component, OnInit } from '@angular/core';
import { Title } from '@angular/platform-browser';
import {CookieService} from "../../servie/cookie.servie";
import {HttpClient,HttpHeaders} from "@angular/common/http";
import {HttpClientService} from "../../servie/httpclient.servie";
import {WarningComponent} from "../../warning/warning.component";
import {Router} from "@angular/router";

declare var soshm:any;
declare var setShareInfo:any;
@Component({
  selector: 'app-indexs',
  templateUrl: './indexs.component.html',
  styleUrls: ['./indexs.component.css']
})
export class IndexsComponent implements OnInit {
    public titles:any={};
    public shareshow:boolean;
    public username:string;
    public data:any={};
    public Logged:boolean = true;
    public Loggeds:boolean = true;

    currentPic = 0;
  constructor(
    private title: Title,
    private cookie:CookieService,
    private  router:Router,
    private http:HttpClient,
    private tx:WarningComponent,
    private https:HttpClientService
  ) {
    this.title.setTitle('掌里通');
    this.titles = {title:"掌里通",of:false,ofs:true};

    setInterval(() => {
      let id = (this.currentPic + 1) % 3;
      this.currentPic = id;
    },3000)
  }

  changebanner(id) {
    console.log(id);
    this.currentPic = id;
  }



  getpermissions(token){//获取合伙人等级
    this.http.get('https://tp.zhanglitong.com/admin/long_distance/checkUser?token='+token).subscribe(
      res => {
        console.log(res);
        this.data = res;
        console.log(res);
          if(res){
            this.cookie.set("stoken",this.data.token);
            console.log(res);
          }
          this.titles = {title:"合伙人等级："+ this.data.msg,of:false,ofs:true};

      },
      err => {
        this.Loggeds = false;
        this.Logged = false;
        console.log('请求失败 失败信息：');
        console.log(err);
      }
    );
  }
  gettoken(token){   //获取合伙人的登陆状态
    this.http.get('https://tp.zhanglitong.com/admin/long_distance/checkLogin?code='+ token).subscribe(
      res => {
        console.log(res);
        this.data = res;
        if(this.data.code == 20000){
          console.log(res);
          //已经登陆
          this.Loggeds = true;
          this.cookie.set("token",this.data.token);
          this.getpermissions(this.data.token)
        }else {  //合伙人没登陆或不存在
          this.Loggeds = false;
        }
      },
      err => {
        this.Logged = false;
        this.Loggeds = false;
        console.log('请求失败 失败信息：');
        console.log(err);
      }
    );
  }

  ngOnInit() { //获取登陆状态
    this.http.post('https://www.zhanglitong.com/mobile/usercenter.php?check_login=true',{
      check_login:true
    },{
      headers: new HttpHeaders().set('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8')
    }).subscribe(
      (res) => {
          console.log(res);
          this.data = res;
          if(this.data.code == 200){
            this.Logged = true;  //已登陆
            this.tx.success(this.data.msg, ()=> {
              this.cookie.set("token",this.data.token);
              this.gettoken(this.data.token)
            });
          }else {
            this.tx.error(this.data.msg, ()=> {
              this.Logged = false;  //没有登陆

            });
          }
        },
        err => {
          this.Logged = false;
          this.Loggeds = false;
          console.log('请求失败 失败信息：');
          console.log(err);
        }
      );

    this.cookie.set("cpk_userid",'1');
    this.cookie.set("cpk_username",'destoon');

    console.log(this.username);
    //this.username = URLEncoder.encode("cpk_username", "utf-8");
    if(this.data.token){
      setShareInfo({
        title:          '掌里通·中国，智慧城市', // 分享标题
        summary:        '抢地盘，做地主', // 分享内容
        pic:            'https://www.zhanglitong.com/assets/img/32923690705102752.png', // 分享图片
        url:            'https://www.zhanglitong.com/jsxt/#/invitation?token='+this.data.token, // 分享链接
      });
    }
  }

  getdata(event){
    this.shareshow = event;
  }

  shares(num){    //列表点击
    if(!this.Logged){   //如果没登陆
      window.location.href='https://www.zhanglitong.com/mobile/login.php';
      return false
    }
    if(num != 3){
      if(!this.Loggeds){   //如果没登陆
        //你还不是合伙人
        this.tx.error('你还不是合伙人', ()=> {
        });
        return false
      }
    }

    if(num === 1){
      window.location.href='https://www.zhanglitong.com/mobile/share/discountCard.html'
    }else if(num === 2){
      var ua = navigator.userAgent.toLowerCase();
      if (ua.indexOf('micromessenger') != -1) {
        //微信打开
        new soshm.weixinSharetip()
      }else if(ua.match(/\sQQ/i) !== null) {
        //qq打开
        this.shareshow = true
      }else {
        //游览器打开
        new soshm.popIn({
          // 分享的链接，默认使用location.href
          url: 'https://www.zhanglitong.com/jsxt/#/invitation?token='+this.data.token,
          // 分享的标题，默认使用document.title
          title: '掌里通·中国，智慧城市',
          // 分享的摘要，默认使用<meta name="description" content="">content的值
          digest: '抢地盘，做地主',
          // 分享的图片，默认获取本页面第一个img元素的src
          pic: 'https://www.zhanglitong.com/assets/img/32923690705102752.png',
          // 默认显示的网站为以下六个个,支持设置的网站有
          sites: ['weixin','weixintimeline','weibo','qzone','qq']
        });
      }
    }else if(num === 3){    //合伙人

      if(this.Loggeds){   //已经登陆 还不是合伙人
        this.tx.error('您已经是合伙人了', ()=> {
        });
      }else {
        this.router.navigate(['./partner']); //
      }


    }else if(num === 4){    //推荐收益
      this.router.navigate(['./earnings']); //
    }else if(num === 5){    //我的团队
      this.router.navigate(['./team']); //

    }
  }
}

