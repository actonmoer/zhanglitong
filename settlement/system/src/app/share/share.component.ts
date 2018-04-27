import {Component, Input, OnInit, Output,EventEmitter} from '@angular/core';

@Component({
  selector: 'app-share',
  templateUrl: './share.component.html',
  styleUrls: ['./share.component.css']
})
export class ShareComponent implements OnInit {
  @Output()event = new EventEmitter();
  private ofs:boolean = false;
  constructor() { }
  ngOnInit() {
  }
  sharewx(){
    this.ofs = false;
    this.event.emit(this.ofs);
  }
}
