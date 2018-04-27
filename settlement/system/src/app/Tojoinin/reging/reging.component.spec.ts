import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { RegingComponent } from './reging.component';

describe('RegingComponent', () => {
  let component: RegingComponent;
  let fixture: ComponentFixture<RegingComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ RegingComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(RegingComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
