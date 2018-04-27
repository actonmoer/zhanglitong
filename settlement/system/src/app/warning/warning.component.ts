import { Component, OnInit } from '@angular/core';
import {NzMessageService} from 'ng-zorro-antd';
@Component({
  selector: 'app-warning',
  templateUrl: './warning.component.html',
  styleUrls: ['./warning.component.css']
})
export class WarningComponent implements OnInit {

  constructor(private _message: NzMessageService) { }

  ngOnInit() {
  }

  createMessage = (type, text) => {
    this._message.create(type, `${text}`);

  };
  success = (text,se)=>{
    this._message.success(`${text}`,se());
  }
  error = (text,se)=>{
    this._message.error(`${text}`,se());
  }

}
