import {Component, EventEmitter, OnInit, Output} from '@angular/core';
import { Title } from '@angular/platform-browser';
import {CookieService} from "../../servie/cookie.servie";
import {HttpClient,HttpHeaders} from "@angular/common/http";
@Component({
  selector: 'app-earnings',
  templateUrl: './earnings.component.html',
  styleUrls: ['./earnings.component.css']
})
export class EarningsComponent implements OnInit {
  public titles:any={};
  public data:any={};
  public cooken:any;
  public amount:any = "0.00";  //总金额
  public explain:string = "可提";  //总金额
  public datc:any;
  constructor(
    private title: Title,
    private cookie:CookieService,
    private http:HttpClient,
  ) {
  }

  explains(ev){   //刷选
    console.log(ev.str);
    this.explain = ev.str;
    this.amount = ev.amo;
  }
  ngOnInit() {
    this.data.Chairman=[];
    this.data.directors=[];
    this.data.same=[];
    this.data.dictor=[];
    this.data.merchants=[];
    this.title.setTitle('我的收益');
    this.titles = {title:"我的收益",of:false};
    this.cooken = this.cookie.get("token"); //获取数据
    if(this.cooken){
      this.withdrawal(this.cooken);
      this.http.get('https://tp.zhanglitong.com/admin/long_distance//getMyPartners?token='+this.cooken).subscribe(
        res => {
          this.displaydata(res)
        },
        err => {
          console.log('请求失败 失败信息：');
          console.log(err);
        }
      );
    }
  }

  displaydata(data){
    if(data.partners){
      data.partners.forEach((key,i)=>{
        if(key.role_id == 3){  //主席
          this.data.Chairman.push(key);
        }
        if(key.role_id == 4){   //主任
          this.data.directors.push(key);
        }
        if(key.role_id == 5){   //主管
          this.data.dictor.push(key);
          // this.dictor.money = 100;
        }

      })
      this.Calculateearnings(data.role_id)
    }
    console.log(data.role_id) //当前用户状态

  }



  //参数1当前角色
  Calculateearnings(role_id){   //收益计算
    console.log(role_id)
    let len = this.data.dictor.length; //获取当前主管推荐人数
    let len1 = this.data.directors.length; //获取当前主任推荐人数
    let len2 = this.data.Chairman.length; //获取当前主席推荐人数

    let money = 2000;    //主管加盟金额
    let money1 = 10000; //主任加盟金额
    let money2 = 30000; //主席加盟金额

    if(role_id == 5){  //当前角色为主管
      console.log('当前角色为主管')
      this.data.dictor.money = (len * (money * 0.5)).toFixed(2);
      this.data.directors.money = (len1 * (money1 * 0.4)).toFixed(2);
      this.data.Chairman.money = (len2 * (money2 * 0.3)).toFixed(2);
    }
    if(role_id == 4){  //当前角色为主任
      console.log('当前角色为主任')
      this.data.dictor.money = (len * (money * 0.5)).toFixed(2);
      this.data.directors.money = (len1 * (money1 * 0.5)).toFixed(2);
      this.data.Chairman.money = (len2 * (money2 * 0.4)).toFixed(2);
    }
    if(role_id == 3){  //当前角色为主席
      console.log('当前角色为主席')
      this.data.dictor.money = (len * (money * 0.5)).toFixed(2);
      this.data.directors.money = (len1 * (money1 * 0.5)).toFixed(2);
      this.data.Chairman.money = (len2 * (money2 * 0.5)).toFixed(2);
    }
  }

  withdrawal(token){ //可提现金额
    this.http.get('https://tp.zhanglitong.com/admin/long_distance/my_wallet?token='+token).subscribe(
      res => {
        console.log('提现接口');
        console.log(res);
        this.datc = res;
        if(res){
          let money = this.datc[this.datc.length-1].money;//总金额
          let moneys=0;
          if(this.datc.length > 0){
            this.datc.forEach((key)=>{
              if(key.resonance == 3){   //已提
                console.log(key);
                moneys = moneys + key.money;
              }
            });
          }
          this.amount = money - moneys;
        }


      },
      err => {
        console.log('请求失败 失败信息：');
        console.log(err);
      }
    );
  }
}
