import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { IndexsComponent } from './indexs.component';

describe('IndexsComponent', () => {
  let component: IndexsComponent;
  let fixture: ComponentFixture<IndexsComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ IndexsComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(IndexsComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
