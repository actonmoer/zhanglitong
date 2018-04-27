import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import {DirectorComponent} from "./Tojoinin/director/director.component";   //主管加盟
import {ChairmanComponent} from "./Tojoinin/chairman/chairman.component";  //主席加盟
import {ExecutiveComponent} from "./Tojoinin/executive/executive.component"; // 主任加盟
import {PartnerComponent} from "./Tojoinin/partner/partner.component";    //成为合伙人首页
import {IndexsComponent} from "./Tojoinin/indexs/indexs.component";    //首页
import {EarningsComponent} from "./Tojoinin/earnings/earnings.component"; //我的收益
import {TeamComponent} from "./Tojoinin/team/team.component";   //我的团队
import {InvitationComponent} from "./Tojoinin/invitation/invitation.component";//被邀请人页面
import {RegingComponent} from "./Tojoinin/reging/reging.component";

const routes: Routes = [
  {path: '',component: IndexsComponent},
  {path: 'earnings',component: EarningsComponent},
  {path: 'reging/:id',component: RegingComponent},
  {path: 'invitation',component: InvitationComponent},
  {path: 'team',component: TeamComponent},
  {path: 'partner',component: PartnerComponent},
  {path:'director/:id',component: DirectorComponent},
  {path: 'chairman/:id',component: ChairmanComponent},
  {path: 'executive/:id',component: ExecutiveComponent},

];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }
