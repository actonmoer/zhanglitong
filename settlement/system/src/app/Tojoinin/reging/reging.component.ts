import {Component, EventEmitter, Input, OnInit, Output} from '@angular/core';
import { Title } from '@angular/platform-browser';
import {HttpClient,HttpHeaders} from "@angular/common/http";
import {ActivatedRoute, Params, Router} from "@angular/router";
import {WarningComponent} from "../../warning/warning.component";
import {CookieService} from "../../servie/cookie.servie";

import {PreviewimgService} from "../../servie/Previewimg.service";

@Component({
  selector: 'app-reging',
  templateUrl: './reging.component.html',
  styleUrls: ['./reging.component.css']
})
export class RegingComponent implements OnInit {
  public titles:any={};
  public data:any={};
  public state:boolean = false;
  public Trying:boolean;
  public num;
  public shuax:boolean;


  public bankid:string;  //银行卡
  public bankimg:string;  //银行卡LOGO
  public previewImgSrcs:any = [];
  public img:any;
  public imgs:any;
  public imgtext:boolean = false;
  public Cost:string;


  public Fullname:string;
  public Fullid:string;
  public phone:string;
  public Password:string;
  public ConfirmPassword :string;
  public states:any = {msg:'信息不能为空'};

  @Input()
  previewImgFile:any = [];
  @Output()
  previewImgFileChange: EventEmitter<string> = new EventEmitter();

  constructor(
    private title: Title,
    private  router:Router,
    private http:HttpClient,
    private tx:WarningComponent,
    private cookie:CookieService,
    private routerInfo:ActivatedRoute,
    private previewimgService: PreviewimgService,
  ) { }

  ngOnInit() {
    this.title.setTitle('成为合伙人');
    this.titles = {title:"成为合伙人",of:false};
    this.shuax = false;
    this.routerInfo.params.subscribe((params:Params)=>{
      console.log(params);
      this.num = params['id'];
      if(this.num == 5){
        this.Cost = '300.00';
      }else if(this.num == 4){
        this.Cost = '10000.00';
      }else {
        this.Cost = '30000.00';
      }
    });
  }


  //验证
  Verification(){
    if(!this.Fullname){
      this.tx.createMessage("error",'姓名不能为空');
      this.states = {msg:'姓名不能为空',code:-200};
      return false
    }
    if(!this.Fullid){
      this.tx.createMessage("error",'身份证不能为空');
      this.states = {msg:'身份证不能为空',code:-200};
      return false
    }
    if(!this.phone){
      this.tx.createMessage("error",'手机不能为空');
      this.states = {msg:'手机不能为空',code:-200};
      return false
    }
    if(!this.Password){
      this.tx.createMessage("error",'密码不能为空');
      this.states = {msg:'密码不能为空',code:-200};
      return false
    }
    if(this.Password != this.ConfirmPassword){
      this.tx.createMessage("error",'2次密码输入不一致');
      this.states = {msg:'2次密码输入不一致',code:-200};
      return false
    }


    this.states = {msg:' ',code:200};

  }





  //提交
  submit(){
    if(!this.state){
      this.tx.createMessage("error",'请阅读并同意申请协议');
      return false
    }

    if(!this.imgtext){
      this.tx.createMessage("error",'请上传付款凭证图片');
      return false
    }

    if(this.states.code != 200){
      this.tx.createMessage("error",this.states.msg);
      return false
    }

    // if(this.imgs){
    //   this.tx.createMessage("error",'请上传汇款凭证');
    //   return false
    // }

    this.http.post('https://tp.zhanglitong.com/admin/long_distance/toExamine',{
      real_name:this.Fullname,   //姓名
      id_number:this.Fullid,  //身份证
      mobile:this.phone,  //手机
      role:this.num, //申请等级
      password:this.Password,
      pay_pic:this.imgs,  //图片
    },{
      headers: new HttpHeaders().set('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8')
    }).subscribe(
      (res) => {
        console.log(res);
        this.data = res;
        this.tx.success(this.data.msg, ()=> {
        });
        if(this.data.code = -202){
          //this.tx.success('您还没有实名认证', ()=> {
            //window.location.href='https://www.zhanglitong.com/mobile/login.php';
         //});
        }
        if(this.data.code = 200){
          this.router.navigate(['']); //
          this.tx.createMessage("suucer",'提交成功,等待审核');
        }
        if(this.data.code = -200){
          this.router.navigate(['']); //
          this.tx.createMessage("suucer",'您已提交审核');
        }
      },
      err => {
        console.log(err);
      }
    );
    //this.router.navigate(['']); //
    this.tx.createMessage("suucer",'提交成功,等待审核');

  }
//选择图片
  previewPic(event) {
    if(!event.target.files[0]) {
      return;
    }
    this.previewimgService.readAsDataUrl(event.target.files[0]).then((result)=> {
      this.img = result; //缩略图base64
      let file = event.target.files[0];

      console.log('图片文件');
      console.log(file);
      //console.log(result)
      // this.previewImgFile.push(file)3
      // this.previewImgFileChange.emit(this.previewImgFile);
      this.upload(file)
    })
  }

//上传凭证
  upload(urs){
    let formData = new FormData();
    formData.append('file',urs);
    this.http.post('https://tp.zhanglitong.com/admin/upload_id_pic/upload',formData,{
      // headers: new HttpHeaders().set('Content-Type', 'application/json;charset=utf-8')
    }).subscribe(
      res => {
        let dat;
        dat = res;
       if(res){
         this.tx.success('上传成功',()=> {
            this.imgtext = true;
            this.imgs = dat.filename
         });
       }else {
         this.imgtext = false;
         this.tx.createMessage('error','上传失败，请检查网络');
         this.imgs = dat.filename
       }

       if(dat.code == 200){
         this.imgtext = true;
         this.imgs = dat.filename;
         console.log(this.imgs);
       }else {
         this.imgtext = false;
         this.imgs = dat.filename
       }
      },
      err => {
        this.imgtext = false;
        this.imgs = '';
       // this.tx.createMessage('success','上传成功');
        console.log(err)
        // this.tx.createMessage('error','上传失败，请检查网络');
        // this.img = '../../../assets/img/pz.png';
        // console.log(err);
      }
    );
  }

  //选择其他银行
  explains(ev){   //刷选
    console.log(ev.bank);
    this.bankid = ev.bank.id;
    this.bankimg = '../../../assets/img/'+ ev.bank.logo  +  '.png'


  }

//是否同意协议
  switch(num){
    if(num == 1){
      this.state = true;
      console.log(this.state)
    }else {
      this.state = false;
      console.log(this.state)
    }
  }

//协议详情
  reading(num){

    //我已阅读本协议
    if(num == 1){
      this.title.setTitle('电子协议');
      this.titles = {title:"电子协议",of:false};
      this.Trying = true
    }else {
      this.title.setTitle('成为合伙人');
      this.titles = {title:"成为合伙人",of:false};
      this.Trying = false
    }

  }

}
