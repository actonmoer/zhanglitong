<template>
  <div class="reserve">
    <div class="headBox"></div>
    <group label-width="5em" label-margin-right="2em" label-align="left">
      <divider>酒店信息</divider>
      <cell title="酒店" :value="param.name"></cell>
      <cell title="房间" :value="param.cname" v-if="param.rtype=='room'"></cell>
      <cell title="大厅" :value="param.cname" v-else></cell>

      <cell
      title="菜单"
      is-link
      :border-intent="false"
      :arrow-direction="showMenu ? 'up' : 'down'"
      @click.native="showMenu = !showMenu"></cell>

      <template v-if="showMenu">
        <cell-form-preview :border-intent="false" :list="list"></cell-form-preview>
      </template>

      <cell title="订金" :value="user.reserve_price"></cell>
      <cell title="预定时间" :value="param.time"></cell>
      <cell title="用餐时间" :value="slot.slot"></cell>
      <cell title="温馨提示">
        <span class='importText'>{{ importText }}</span>
      </cell>
    </group>

    <group label-width="5em" label-margin-right="2em" label-align="left" class="reserve-msg">
      <divider>预订信息</divider>
      <x-input title="姓名" v-model="user.truename" placeholder="请输入你的姓名"></x-input>
      <x-input title="联系方式" v-model="user.mobile" placeholder="请输入你的手机号码"></x-input>
      <x-number title="到店人数" align="left" v-model="num" button-style="round" :min="0" :max="100"></x-number>
      <!-- <datetime format="YYYY-MM-DD HH:mm" title="到店时间" :min-hour="minHour" :max-hour="maxHour" v-model="time" :start-date="startTime" :end-date="endTime" value-text-align="left"></datetime> -->
    </group>

    <group label-width="5em" label-margin-right="2em" label-align="left" class="share-msg">
      <divider>邀请函信息</divider>
      <x-input title="邀请函标题" v-model="title" placeholder="标题"></x-input>
      <x-switch title="是否显示菜单" v-model="ifShow"></x-switch>
      <x-textarea title="邀请函内容" v-model="cont" placeholder="请填写详细信息" :rows="3"></x-textarea>
    </group>
    </group>
    <section class="bottom">
      <a href="javascript:;" class="money">￥{{ user.reserve_price }}</a>
      <a href="javascript:;" class="payment-btn" @click.stop="gopay">确认支付</a>
    </section>
    <loading :status="status"></loading>
    <tips :tipsStatus="tipsStatus" :text="text"></tips>
  </div>
</template>



<script>
import loading from '@/components/public/loading'
import tips from '@/components/public/tips'

import {Group, Cell, XInput, Datetime, XNumber, XTextarea, XSwitch, CellFormPreview, Divider} from 'vux'
import {gopay, getCar, getUserInfo, getSlot} from '@/model/getData'
import {Util} from '@/assets/js/util.js'

let util = new Util();
export default {
  name: 'reserve',
  data () {
    return {
      status: false,
      tipsStatus: false,
      text: "",
      name : "",
      tel  : "",
      num  : 0,
      time : "",
      title:"",
      cont :"",
      param: {},
      showMenu: false,
      list: [],
      user: {},
      ifShow: false,
      // startTime: '',
      // endTime: '',
      slot: {},
      // minHour: 10,
      // maxHour: 10,
      importText: '到店后必须在指定用餐时间内完成。超出时间预定餐房(桌)无效'
    }
  },
  async activated () {
    this.status = true;
    this.param = JSON.parse(this.$route.query.param);
    this.time = this.param.time;
    // this.startTime = this.param.time.split(' ')[0];
    // this.endTime = this.param.time.split(' ')[0];
    // this.minHour = +this.param.time.split(' ')[1].split(':')[0];
    // this.maxHour = +this.param.time.split(' ')[1].split(':')[0];
    let data = await getCar({rid: this.param.id});
    this.user = await getUserInfo({
      rid: this.param.id
    });
    this.slot = await getSlot({
      rid: this.param.id
    });
    // console.log(this.param.time.split(' ')[1].split(':')[0]);
    
    if(data){
      this.list = [];
      for(let i in data.cuisines){
        this.list.push({
          label: data.cuisines[i].title + "，" + data.cuisines[i].num + "份",
          value: data.cuisines[i].total_price
        })
      }
    }

    this.status = false;

  },

  
  methods: {
    async gopay () {
      if(!this.user.truename){
          this.showTips("请输入联系人姓名");
          return;
      }
      if(!this.user.mobile){
          this.showTips("请输入联系人电话");
          return;
      }
      if(!this.num){
          this.showTips("请输入到店人数");
          return;
      }
      if(!this.time){
          this.showTips("请输入到店时间");
          return;
      }
      if(!this.title){
          this.showTips("请输入分享标题");
          return;
      }
      if(!this.cont){
          this.showTips("请输入分享内容");
          return;
      }
      this.status = true;
      let data = await gopay({
        rid                : this.param.id,
        persons            : this.num,
        mobile             : this.user.mobile,
        contact_name       : this.user.truename,
        arrive_time        : this.time,
        reserve_type       : this.param.rtype,
        commonid           : this.param.commonid,
        invitation_title   : this.title,
        invitation_content : this.cont,
        is_menu            : this.ifShow,
        addtime            : this.param.time
      })
      this.status = false;
     
      this.showTips(data.msg);
      setTimeout(function(){
        window.location.href = data.url;
      },2000)
      
    },
    
    showTips (txt) {
      let self = this;
      this.tipsStatus = true;
      this.text = txt;
      setTimeout(function(){
        self.tipsStatus = false;
      },2000)
    },
  },
  components: {
    Group,
    Cell,
    XInput,
    Datetime,
    XNumber,
    XTextarea,
    XSwitch,
    CellFormPreview,
    Divider,
    loading,
    tips
  }
}
</script>

<style lang="scss" scoped>
@import '../assets/scss/com.scss';
.reserve{
  padding: 0 0 .8rem 0;
  background: #f7f7fa;
  .reserve-msg{
    margin: .5rem 0 0 0;
  }
  .share-msg{
    margin: .5rem 0 0 0;
  }
}
.bottom{
  position: fixed;
  bottom: 0;
  width: 7.5rem;
  left: 50%;
  margin-left: -3.75rem;
  height: 1rem;
  border: 1px solid $border;
  .money{
    display: block;
    float: left;
    width: 50%;
    line-height: 1rem;
    text-align: center;
    font-size: $title;
    color: $green;
    background: #fff

  }

  .payment-btn{
    display: block;
    float: left;
    width: 50%;
    line-height: 1rem;
    text-align: center;
    font-size: $title;
    color: #fff;
    background: $green;
  }
}

.importText{
    text-align: left;
    color: #cf1919;
}

</style>
