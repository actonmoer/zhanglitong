import { Injectable } from '@angular/core';
import {HttpClient} from "@angular/common/http";



@Injectable()
export class HttpClientService {
  public state:boolean = false;
  constructor(private http:HttpClient ) { }







  // getHS(){
  //   let header = new Headers();
  //   header.append('Content-Type', 'application/json');
  //   console.log('请求头,ktoken');
  //   //header.append('Content-Type', 'application/x-www-form-urlencoded');
  //   // application/json
  //   // application/x-www-form-urlencoded
  //   return header
  // }
  //
  // Myget(parameter:any):Promise<any>{
  //   let Url = `${this.heroesUrl}${this.privateurl}`;
  //   let headers:Headers = this.getHS();
  //   for(let key in parameter){
  //     Url =  Url+'&'+ key +'='+ parameter[key];
  //   }
  //   if(Url.indexOf('?') == -1){
  //     Url = Url.replace('&','?')
  //   }
  //   return this.http.get(Url,)
  //     .toPromise()
  //     .then(res=>res.json())
  //     .catch(err=>{
  //       this.toat.createMessage('info',err.json().error[0]);
  //     })
  // }
  //
  // Mypost(parameter:any):Promise<any>{
  //   let Url = `${this.heroesUrl}${this.privateurl}`;
  //   let headers:Headers = this.getHS();
  //   return this.http.post(Url,parameter,)
  //     .toPromise()
  //     .then(res=>res.json())
  //     .catch(err=>{
  //       console.log(err);
  //       console.log(headers);
  //       // console.log(err.json().error[0]);
  //       if(err.json().error){
  //         if(Array.isArray(err.json().error)){
  //           this.toat.createMessage('info',err.json().error[0]);
  //         }else {
  //           this.toat.createMessage('info',err.json().error);
  //         }
  //
  //       }
  //       //this.toat.createMessage('info',err.json().error[0]);
  //     })
  // }

}
