import { Component, OnInit } from '@angular/core';
import {Router} from "@angular/router";
import { Title } from '@angular/platform-browser';
@Component({
  selector: 'app-partner',
  templateUrl: './partner.component.html',
  styleUrls: ['./partner.component.css']
})
export class PartnerComponent implements OnInit {

  public titles:any={};
  constructor(
    private  router:Router,
    private title: Title
  ) { }

  ngOnInit() {
    this.title.setTitle('成为合伙人');
    this.titles = {title:"成为合伙人",of:false};
  }

  agent(num){
    if(num === 5){
      this.router.navigate(['./director',num]); //
    }else if(num === 4){
      this.router.navigate(['./executive',num]); //
    }else {
      this.router.navigate(['./chairman',num]); //
    }

  }



}


