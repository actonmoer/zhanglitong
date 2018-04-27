
import { BrowserModule } from '@angular/platform-browser';
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import { NgModule } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { AppRoutingModule } from './app-routing.module';
import { HttpClientModule } from '@angular/common/http';
import { NgZorroAntdModule } from 'ng-zorro-antd';
import { AppComponent } from './app.component';
import { DirectorComponent } from './Tojoinin/director/director.component';
import { HeadComponent } from './head/head.component';
import { BottomsComponent } from './bottoms/bottoms.component';
import { ChairmanComponent } from './Tojoinin/chairman/chairman.component';
import { ExecutiveComponent } from './Tojoinin/executive/executive.component';
import { PartnerComponent } from './Tojoinin/partner/partner.component';
import { IndexsComponent } from './Tojoinin/indexs/indexs.component';
import { EarningsComponent } from './Tojoinin/earnings/earnings.component';
import { TeamComponent } from './Tojoinin/team/team.component';
import { TitlesComponent } from './titles/titles.component';
import { LayerComponent } from './layer/layer.component';
import { InvitationComponent } from './Tojoinin/invitation/invitation.component';
import {HashLocationStrategy, LocationStrategy} from '@angular/common';
import {ShareComponent} from "./share/share.component";
import {CookieService} from "./servie/cookie.servie";
import {HttpClientService} from "./servie/httpclient.servie";
import { RegingComponent } from './Tojoinin/reging/reging.component';
import { WarningComponent } from './warning/warning.component';
import {PreviewimgService} from "./servie/Previewimg.service";

@NgModule({
  declarations: [
    AppComponent,
    DirectorComponent,
    HeadComponent,
    BottomsComponent,
    ChairmanComponent,
    ExecutiveComponent,
    PartnerComponent,
    IndexsComponent,
    EarningsComponent,
    TeamComponent,
    TitlesComponent,
    LayerComponent,
    InvitationComponent,
    ShareComponent,
    RegingComponent,
    WarningComponent
  ],
  imports: [
    AppRoutingModule,
    BrowserModule,
    FormsModule,
    HttpClientModule,
    BrowserAnimationsModule,
    NgZorroAntdModule.forRoot({ extraFontName: 'anticon', extraFontUrl: './assets/fonts/iconfont' })
  ],
  providers: [
    CookieService,
    WarningComponent,
    PreviewimgService,
    HttpClientService,
     {provide: LocationStrategy, useClass: HashLocationStrategy}

  ],
  bootstrap: [AppComponent]
})
export class AppModule { }
