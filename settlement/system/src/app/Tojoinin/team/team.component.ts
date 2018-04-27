import { Component, OnInit } from '@angular/core';
import { Title } from '@angular/platform-browser';
import {CookieService} from "../../servie/cookie.servie";
import {HttpClient} from "@angular/common/http";
@Component({
  selector: 'app-team',
  templateUrl: './team.component.html',
  styleUrls: ['./team.component.css']
})
export class TeamComponent implements OnInit {
  public titles:any={};
  public cooken:string;
  public data:any={};
  constructor(
    private title: Title,
    private cookie:CookieService,
    private http:HttpClient,
  ) { }

  ngOnInit() {
    this.data.Chairman=[];  //主席
    this.data.directors=[];   //主任
    this.data.dictor=[];     //主管
    this.title.setTitle('我的团队');
    this.titles = {title:"我的团队",of:false};
    this.cooken = this.cookie.get("token"); //获取数据
    if(this.cooken){
      this.http.get('https://tp.zhanglitong.com/admin/long_distance//getMyPartners?token='+this.cooken).subscribe(
        res => {
          this.displaydata(res)
          console.log(res)
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
      data.partners.forEach((key)=>{
        if(key.role_id == 3){  //主席
          this.data.Chairman.push(key);
        }
        if(key.role_id == 4){   //主任
          this.data.directors.push(key);
        }
        if(key.role_id == 5){   //主管
          this.data.dictor.push(key);
        }

      });
      this.Calculateearnings()
    }
  }

//参数1当前角色
  Calculateearnings(){   //收益计算
    this.data.dictor.money = 0;
    this.data.directors.money = 0;
    this.data.Chairman.money = 0;
    this.data.dictor.forEach((key)=>{ //主管
      this.data.dictor.money = parseFloat(this.data.dictor.money + key.role_money).toFixed(2);
    });

    this.data.directors.forEach((key)=>{ //主任
      this.data.directors.money = parseFloat(this.data.directors.money + key.role_money).toFixed(2);
    });

    this.data.Chairman.forEach((key)=>{  //主席
      this.data.Chairman.money = parseFloat(this.data.Chairman.money+ key.role_money).toFixed(2)
    })
  }






}
