import {Component, Input, OnInit} from '@angular/core';
import {until} from "selenium-webdriver";
import titleIs = until.titleIs;

@Component({
  selector: 'app-titles',
  templateUrl: './titles.component.html',
  styleUrls: ['./titles.component.css']
})

export class TitlesComponent implements OnInit {
  @Input() data:any = {};
  constructor() { }

  ngOnInit() {

  }
  previous(){
    //返回上一页
    window.history.go(-1);
  }
}
