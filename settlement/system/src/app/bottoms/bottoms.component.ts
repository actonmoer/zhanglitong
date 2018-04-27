import { Component, OnInit } from '@angular/core';
import {ActivatedRoute, Params} from "@angular/router";
import {Router} from "@angular/router";

@Component({
  selector: 'app-bottoms',
  templateUrl: './bottoms.component.html',
  styleUrls: ['./bottoms.component.css']
})
export class BottomsComponent implements OnInit {
  public num:number;
  constructor(
    private routerInfo:ActivatedRoute,
    private router:Router
  ) { }

  ngOnInit() {
    this.routerInfo.params.subscribe((params:Params)=>{
      console.log(params);
      this.num = params['id'];
    });
  }

  become(){
    this.router.navigate(['./reging',this.num]);
  }
}
