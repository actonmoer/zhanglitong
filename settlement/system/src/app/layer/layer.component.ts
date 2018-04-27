import {Component, EventEmitter, Input, OnInit, Output} from '@angular/core';
import {CookieService} from "../servie/cookie.servie";
import {HttpClient,HttpHeaders} from "@angular/common/http";
import {ActivatedRoute} from "@angular/router";
declare var MultiPicker:any;
@Component({
  selector: 'app-layer',
  templateUrl: './layer.component.html',
  styleUrls: ['./layer.component.css']
})

export class LayerComponent implements OnInit {
  stockCode: string = "";
  str: string = '可提';
  amo:any = 0;
  public cooken:string;
  public amount:number;  //总金额
  public money:number =0;   //已经提现
  public mon:number;  //可提
  public a:string;
  public data:any;


  public bank:string;   //银行名称


  @Input() xianshi:boolean = true;

  @Output('str')
  lastPrice: EventEmitter<PriceQuote> = new EventEmitter();

  @Output('dse')
  backs: EventEmitter<BankQuote> = new EventEmitter();

  constructor( private cookie:CookieService, private http:HttpClient,
               private activatedRoute:ActivatedRoute,
               ) { }
  withdrawal(token){ //可提现金额
    this.http.get('https://tp.zhanglitong.com/admin/long_distance//my_wallet?token='+token).subscribe(
      res => {
        console.log('提现接口');
        console.log(res);
        this.data = res;
        if(this.data.length > 0 ){
          this.amount = this.data[this.data.length - 1].money;//总金额
          this.data.forEach((key)=>{
            if(key.resonance == 3){   //已提
              this.money = parseFloat(this.money + key.money);
            }
          });
          this.mon = this.amount - this.money;  //可提
        }
      },
      err => {
        console.log('请求失败 失败信息：');
        console.log(err);
      }
    );
  }
  ngOnInit() {
    this.activatedRoute.queryParams.subscribe(res => {
      console.log('获取url和参数');
      console.log(res);
      // this.data = res;
      //this.retrievedata();
      if(res){
        console.log('获取url');
        console.log(res);
        if(res.id){
          this.xianshi = false;
        }
      }

    });







    this.cooken = this.cookie.get("token"); //获取数据
    this.withdrawal(this.cooken);
    new MultiPicker({
      input: 'multiPickerInput2',//点击触发插件的input框的id
      container: 'targetContainer2',//插件插入的容器id
      jsonData: [
        {id:1,value: '已提现额度',},
        {id:2,value: '可提现额度',},
        {id:3,value: '总收入额度',}
      ],
      success: (arr) =>{
        this.withdrawal(this.cooken);
        if(arr[0].index == 0){
          this.str = '已提';
          this.amo = this.money;
        }else if(arr[0].index == 1){
          this.str = '可提';
          this.amo = this.mon;
        }else {
          this.str = '总收入';
          this.amo = this.amount;
        }
        let priceQuote: PriceQuote = new PriceQuote(this.str,parseFloat(this.amo).toFixed(2));

        this.stockCode = priceQuote.str;
        this.lastPrice.emit(priceQuote);
      }//回调






    });

    new MultiPicker({
      input: 'multiPickerInput3',//点击触发插件的input框的id
      container: 'targetContainer3',//插件插入的容器id
      jsonData: [
        {
          id:"6222 0236 0202 5624 636",
          value: '工商银行',
          logo:'gsyh',
        },
        {id:'6223 6358 0800 1177 907',value: '华润银行',logo:'hryh'},
        // {id:2,value: '2',logo:'psxe'},
        // {id:2,value: '3',logo:'psxe'},
        // {id:2,value: '4',logo:'psxe'},
        // {id:2,value: '5',logo:'psxe'},
        // {id:3,value: '6',logo:'psxe'}
      ],
      success: (arr) =>{
        //银行
        this.bank = arr[0]  ;
        let bankQuote:BankQuote = new BankQuote(this.bank);
        this.backs.emit(bankQuote);
      }//回调
    });

  }

}
export class PriceQuote {
  constructor(
    public  str: string,
    public amo:any
  ) {
  }
}
export class BankQuote {
  constructor(
    public bank:string,  //银行名称

  ) {
  }
}
