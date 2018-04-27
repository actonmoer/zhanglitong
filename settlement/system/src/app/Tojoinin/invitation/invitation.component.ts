import { Component, OnInit } from '@angular/core';
import {ActivatedRoute, Router} from "@angular/router";
import {WarningComponent} from "../../warning/warning.component";
import {HttpClient,HttpHeaders} from "@angular/common/http";
import {CookieService} from "../../servie/cookie.servie";

@Component({
  selector: 'app-invitation',
  templateUrl: './invitation.component.html',
  styleUrls: ['./invitation.component.css']
})
export class InvitationComponent implements OnInit {
  public data:any;
  public dats:any;
  public phone:string;  //手机号
  public code:string;  //验证码
  //public password:string;  //密码
  public recommended:string;  //推荐码
  public num:string;  //倒计时
  public isdisabled:boolean;

  constructor(
    private activatedRoute:ActivatedRoute,
    private tx:WarningComponent,
    private http:HttpClient,
    private cookie:CookieService,
    private  router:Router,
  ) { }

  ngOnInit() {

    this.activatedRoute.queryParams.subscribe(res => {
      console.log('获取url和参数');
      console.log(res);
      this.data = res;
      //this.retrievedata();
    });

    if(this.data.token){
      this.recommended = this.data.token;
      console.log('推荐码存在')
    }
  }

  checkPhone(){
    let phone = this.phone;
    if(!(/^1[34578]\d{9}$/.test(phone))){
      // util.showToast("手机号码有误，请重填");
      this.tx.error("手机号码有误，请重新输入",function () {
        return false;
      });
    }
  }
  Djs() {  //短信发送
    this.isdisabled = true;
    let num = 60;
    this.num = num + '秒';
    let cont = setInterval( ()=> {
      num--;
      this.num = num + '秒';
      if (num == 0) {
        this.num = '重新获取';
        clearInterval(cont);
        this.isdisabled = false;
      }
    }, 1000)
  }

  accept(){
    if(!this.phone){
      this.tx.createMessage('error','请输入手机号码');
      return false
    }

    // if(!this.password){
    //   this.tx.createMessage('error','请设置密码');
    //   return false
    // }

    if(this.recommended == ''){
      this.tx.createMessage('error','推荐码异常');
      return false
    }

    this.http.post('https://tp.zhanglitong.com/admin/long_distance/register',{
      mobile:this.phone, //手机号
      // password:this.password,//密码
      referralCode:this.recommended   //推荐
    },{
      headers: new HttpHeaders().set('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8')
    }).subscribe(
      (res) => {
        console.log(res);
        this.dats = res;
        if(this.dats.code == -201){
          this.tx.error(this.dats.msg, ()=> {
            this.router.navigate(['']); //跳转首页
          });
        }else {
          //接受邀请
          this.tx.success(this.dats.msg, ()=> {
            console.log(this.dats.code);
            this.cookie.set("code",this.dats.code)
            this.router.navigate(['']); //跳转首页

          });
        }
      },
      err => {
        console.log('请求失败 失败信息：');
        console.log(err);
      }
    );
  }

}
