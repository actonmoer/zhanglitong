<template>
  <div class="roomList">
    <div class="headBox"></div>

    <header id="header" class="header-img">
      <img :src="thumb">
    </header>
    <a class="goMenu" @click='goMenu'>菜谱</a>
    <h6 style="font-size: .34rem;font-weight: bold;padding: .16rem 0 0 .26rem;">{{roomDeal.data.company}}</h6>
    <!--<starsList :stars="score"></starsList>-->
    <div class="xing">
      <i class="iconfont icon-xing" style="color: #ffd800;"></i>
      <i class="iconfont icon-xing" style="color: #ffd800;"></i>
      <i class="iconfont icon-xing" style="color: #ffd800;"></i>
      <i class="iconfont icon-xing" style="color: #ffd800;"></i>
      <i class="iconfont icon-xing"></i>
    </div>
    <!--<section class="source"><span>{{roomDeal.data.score}}</span> 分 高于100%同类型酒店</section>-->
    <div class="h-tags">
      <a v-if="url" v-bind:href="url" style="border-color: #24ce5c;color: #24ce5c;">美食视频</a>
      <a v-if="showPhotoTag" v-bind:href="urlPhoto" style="border-color: #ffa200;color: #ffa200;">环境图片</a>
    </div>

    <section class="detail" style="border-top-width: 0;">
      <ul>
        <li v-if="hotel.is_wifi == 1">
          <div class="detail-left-img"><img alt="service.attrDesc" src="../assets/images/0e59ed6fea38fea5beaf4e9719950b1f1869.png"></div>
          <div class="detail-left-desc">WIFI</div>
        </li>
        <li v-if="hotel.is_park == 1">
          <div class="detail-left-img"><img alt="service.attrDesc" src="../assets/images/aec15480c0dae9a1f7ab12aa68737c422029.png"></div>
          <div class="detail-left-desc">停车场</div>
        </li>
        <li v-if="hotel.is_swipe == 1">
          <div class="detail-left-img"><img alt="service.attrDesc" src="../assets/images/27cd20f9fc3f84a066fc3b10b21def741256.png"></div>
          <div class="detail-left-desc">刷卡消费</div>
        </li>
      </ul>
        <div class="more">
          <a href="javascript:;" @click.stop="popupOpen">详情 > </a>
        </div>
      <div style="color: #db4141;font-size: .24rem;clear: both;padding: .2rem 0;">营业时间：{{hotel.servicetime}}</div>
    </section>

    <div class="h-address">
      <div style="max-width: 75%;float: left;height: .7rem;">
        地址：<i class="iconfont icon-dingwei1" style="font-size: .24rem;"></i>{{hotel.address}}
      </div>
      <a :href="'tel:'+hotel.telephone"><i class="iconfont icon-dianhua1" style="color: #24ce5c;float: right;margin-right: .34rem;"></i></a>
    </div>

    <div style="padding: .2rem 0;background-color: #f1f1f1;">
        <group gutter="0">
          <datetime class="red" format="YYYY-MM-DD HH:mm" @on-change="changeTime" title="请先选择到店时间" v-model="now" :start-date="startDate" :end-date="endDate" ></datetime>
          <!--<cell title="地址" value="到这里去" is-link link="http://map.baidu.com/mobile/webapp/place/linesearch/foo=bar/from=place&tab=line"></cell>-->
        </group>

      </div>

   <!-- <section class="map">
      <div class="mapTitle"><h6>地图</h6></div>
       <el-amap vid="amapDemo" :zoom="zoom" :center="center">
          <el-amap-marker v-for="i in markers" :position="i.position" :key="i.position">
          </el-amap-marker>
      </el-amap>
    </section> -->

  <!--   <group gutter="0">

    </group> -->

    <div class="allRoom-d">
      <div v-bind:class="{'active':isShow==1}" v-if="desk.length != 0" v-on:click="isShow=1">房间[{{room.length}}]</div>
      <div v-bind:class="{'active':isShow==2}" v-if="desk.length != 0" v-on:click="isShow=2">大厅[{{desk.length}}]</div>
    </div>
    <div class="allRoom-d1">
      <div style="line-height: 2rem;text-align: center;" v-if="room.length == 0" v-show="isShow==1">该餐馆没有开设房间</div>
      <div style="line-height: 2rem;text-align: center;" v-if="desk.length == 0" v-show="isShow==2">该餐馆没有开设大厅</div>
      <section class="allRoom" v-if="room.length != 0" v-show="isShow==1">
        <!--<div class="roomTitle"><h6>房间[{{room.length}}]</h6><span>预定成功后100%有房</span></div>-->
        <ul class="roomBox">
          <li class="room-list" v-for="(i, index) in room" @click="openRoomDeal(i.id, i.is_use)">
            <div class="room-left">
              <h6>{{i.room_name}}</h6>
              <p class='red' v-if='i.is_use == 1 && i.endtime'>结束时间：{{i.endtime}}</p>
              <p>最低消费{{i.min_consumption}}元 | 容纳{{i.people_count}}人</p>
              <p>{{i.info}}</p>
            </div>
            <div class="room-right">
              <a href="javascript:;" class="reserve-btn btn" :class="{disabled: i.is_use == 1}">预订</a>
            </div>
          </li>
        </ul>
      </section>
      <section class="allTable" v-if="desk.length != 0" v-show="isShow==2">
        <!--<div class="tableTitle"><h6>大厅[{{desk.length}}]</h6></div>-->
        <ul class="tableBox">
          <li class="table-list" v-for="(i, index) in desk" @click.stop="openTableDetail(i.id, i.is_use, index)">
            <div class="table-left">
              <h6>{{i.desk}}</h6>
              <p class='red' v-if='i.is_use == 1 && i.endtime'>结束时间：{{i.endtime}}</p>
              <p>最低消费{{i.min_consumption}} | 可坐{{i.hold}}人</p>
              <p>{{i.info}}</p>
            </div>
            <div class="table-right">
              <a href="javascript:;" class="reserve-btn btn" :class="{disabled: i.is_use == 1}">预订</a>
            </div>
          </li>
        </ul>
      </section>
    </div>


    <transition
      enter-active-class="enterActiveClass2"
      leave-active-class="leaveActiveClass2"
      enter-class="enterClass2"
      leave-class="leaveClass2"
    >
      <section class="popup" v-if="popup.status">
        <div class="popup-box">
          <div class="popup-title">
            <h6>酒家详情介绍</h6>
            <a class="close-btn" @click.stop="popupClose">x</a>
          </div>
          <div class="popup-cont">
            <div class="popup-list">
              <h6>商家电话</h6>
              <p>{{roomDeal.data.telephone}}</p>
            </div>

            <div class="popup-list">
              <h6>酒店介绍</h6>
              <div class="hotel-msg">
                <p>加盟时间：{{roomDeal.data.validtime | formateTime}}</p>
                <p>月销量：{{roomDeal.data.month_sells}}</p>
                <p>营业时间：{{roomDeal.data.servicetime}}</p>
                <p>桌子总数：{{roomDeal.data.desk_counts}}</p>
                <p>房间总数：{{roomDeal.data.room_counts}}</p>
              </div>
            </div>
          </div>
        </div>
      </section>
    </transition>

    <transition
      enter-active-class="enterActiveClass2"
      leave-active-class="leaveActiveClass2"
      enter-class="enterClass2"
      leave-class="leaveClass2"
    >
      <section class="roomDeal-bg" v-if="roomDeal.status"></section>
    </transition>
    <transition
      enter-active-class="enterActiveClass3"
      leave-active-class="leaveActiveClass3"
      enter-class="enterClass3"
      leave-class="leaveClass3"
    >
      <section class="roomDeal" v-if="roomDeal.status">
        <div class="roomDeal-box" :class="{desk: param.rtype == 'desk'}">
          <div class="roomDeal-title">
            <h6>{{roomDeal.data.room_name}}</h6>
            <a href="javascript:;" class="close-roomDeal" @click="closeRoomDeal">x</a>
          </div>
          <div class="roomDeal-cont" ref="roomDealDoom">
            <div>
              <div class="roomDeal-img" v-if="param.rtype == 'room'">
                <swiper :list="list" v-model="demo02_index" @on-index-change="onIndexChange"></swiper>
              </div>
              <div class="room-device" v-if="param.rtype == 'room'">
                <div class="room-device-left">
                  <p v-if="hotel.is_wifi == 1">上网：WIFI</p>
                  <p>窗户：有</p>
                  <p>空调：有</p>
                </div>
                <div class="room-device-right">
                  <p>卫生间：独立</p>
                  <p>可容纳：{{roomDeal.data.people_count}}人</p>
                  <p>桌子：{{roomDeal.data.people_count}}张</p>
                </div>
              </div>
              <div class="cannel-txt">
                <p>取消规则</p>
                <p>{{roomDeal.data.refund_rule}}</p>
              </div>
              <div class="use-txt">
                <p>使用规则</p>
                <p>到店提供姓名手机号直接使用</p>
                <p>请在预约时间前到店，如需延迟到店倾咨询商家</p>
                <p>预定需要押金，实际付款金额以前台为准</p>
              </div>
              <div class="use-txt">
                <p>预定情况</p>
                <table class="table" v-if="roomDeal.data.reserve_time.length">
                  <tr>
                    <td>姓名</td>
                    <td>预定时间</td>
                    <td>结束时间</td>
                  </tr>
                  <tr v-for="item in roomDeal.data.reserve_time">
                    <td>{{item.contact_name}}</td>
                    <td>{{item.addtime}}</td>
                    <td>{{item.endtime}}</td>
                  </tr>
                </table>

              </div>
            </div>
          </div>
          <div class="roomDeal-bottom">
            <a href="javascript:;" class="reserve-money">￥{{param.reserve_price}}</a>
            <a @click="toMenu" class="reserve-btn" href="javascript:;">预定</a>
          </div>
        </div>
      </section>
    </transition>
    <loading :status="status"></loading>
  </div>
</template>

<script>
import Vue from 'vue'
import loading from '@/components/public/loading'

import BScroll from 'better-scroll'
import { Group, Datetime, Swiper, Cell, CellBox } from 'vux'
import {Util} from '@/assets/js/util.js'
import {getHotelDetail, getHotelRooms, getRoomDetail, getDeskDetail} from '@/model/getData'
// import starsList from '@/components/public/stars'


let util = new Util();
//     scrollBug = util.scrollBug;



export default {
  name: 'roomList',
  data () {
    return {
      id : 0,
      status : false,
      list: [
        // {url: 'javascript:',img: 'https://static.vux.li/demo/1.jpg',}
      ],
      startDate: '2010-01-01',
      endDate: '2020-12-01',
      demo02_index: 0,

      swiperItemIndex: 1,

      roomDeal : {
        status : false,
        data : {}
      },

      hotel: {},

      desk : [],
      isShow:1,
      room : [],
      param : {},
      thumb: '',
      // markers : [{
      //   position: [121.59996, 31.197646]
      //  }],
      //  center: [121.59996, 31.197646],
      //  zoom : 12,
      url: '',
      urlPhoto: '',
      showPhotoTag: true,
      popup: {
        status: false
      },
      now : util.formatDate(+new Date())
    }
  },
  filters: {
    formateTime: function (value) {
      let MM = "", dd = "";
      value = new Date(value*1000);
      return value.getFullYear()+"-"+(Number(value.getMonth())+1)+"-"+(Number(value.getDate()));
    }
  },
  methods: {
    toMenu () {
      this.closeRoomDeal();
      this.$router.push({path: 'menu', query: {param: JSON.stringify(this.param)}})
    },

    goMenu () {
      this.param.type = 0;
      this.$router.push({path: 'menu', query: {param: JSON.stringify(this.param)}})
    },

    popupOpen () {

      this.popup.status = true;
    },
    popupClose () {
      this.popup.status = false;
    },
    async openTableDetail (deskid, use, index) {
      if(use == 1) return;
      // let data = this.desk[index];
      // this.param.cname = data.desk;
      // this.param.commonid = deskid;
      // this.param.reserve_price = data.reserve_price || 0;
      // this.param.rtype = "desk";
      // this.$router.push({path: 'menu', query: {param: JSON.stringify(this.param)}})
      // if(use == 1) return;
      this.status = true;

      let data = await getDeskDetail(this.param.id, deskid, this.now);

      this.roomDeal.status = true;
      this.status = false;
      this.roomDeal.data = data;

      this.param.cname = data.desk_name;
      this.param.reserve_price = data.reserve_price || 0;
      this.param.commonid = deskid;
      this.param.rtype = "desk";
      for(let i in data.thumb_banner_arr){
        this.list.push({
          img: data.thumb_banner_arr[i],
          url: 'javascript:;',
        })
      }
      setTimeout(() => {
        let roomDealDoom = new BScroll(this.$refs.roomDealDoom);
      },500)
    },
    async openRoomDeal (roomid, use) {
      // console.log(use);
      if(use == 1) return;
      this.status = true;
      this.list = []

      let data = await getRoomDetail(this.param.id, roomid, this.now);


      this.roomDeal.status = true;
      this.status = false;
      this.roomDeal.data = data;


      this.param.cname = data.room_name;
      this.param.reserve_price = data.reserve_price;
      this.param.commonid = roomid;
      this.param.rtype = "room";

      for(let i in data.thumb_banner_arr){
        this.list.push({
          img: data.thumb_banner_arr[i],
          url: 'javascript:;',
        })
      }
      setTimeout(() => {
        console.log(1)
        let roomDealDoom = new BScroll(this.$refs.roomDealDoom);
      },500)


    },
    closeRoomDeal () {
      this.roomDeal.status = false;
    },
    onIndexChange (index) {
      this.index = index
    },
    async getRoomDeskList () {
      this.param = JSON.parse(this.$route.query.param);
      var self = this;

      this.status = true;
      let p1 = new Promise((resolve, reject) => {
        let data = getHotelDetail(this.param.id);
        resolve(data);
      });


      let p2 = new Promise((resolve, reject) => {
        let data = getHotelRooms(this.param.id);
          resolve(data);
      })

      Promise.all([p1, p2]).then((res) => {
        let endTime = res[0].reserve_days;
        if(endTime == 0) {
          endTime = 15000;
        }
        // this.endDate = ''
        this.startDate = util.formatDate2(+new Date());
        this.endDate = util.formatDate2(+new Date() + endTime * 86400000);

        this.roomDeal.data = res[0];

        this.hotel = res[0];

        this.thumb = res[0].thumb;

        this.param.name = document.title = res[0].company;
        this.param.type = 1;
        this.param.rtype = "room";
        this.param.time = this.now;

        this.desk = res[1].desk;
        this.room = res[1].room;
        this.status = false;

        if(res[0].video_id){
          this.url = '//www.zhanglitong.com/mobile/Video/videoDetail.html?moduleid=14&itemid=' + res[0].video_id;
        }else{
          this.url = '//www.zhanglitong.com/mobile/Video/video.html';
        }

        if(res[0].photo_id){
          this.showPhotoTag = true;
          this.urlPhoto = '//www.zhanglitong.com/mobile/index.php?moduleid=12&itemid=' + res[0].photo_id;
        }else{
          this.showPhotoTag = false;
        }
      })
    },

    async changeTime () {
      this.status = true;
      let data = await getHotelRooms(this.param.id, this.now);
      this.desk = data.desk;
      this.room = data.room;
      this.param.time = this.now;
      this.status = false;

    }


  },


  activated () {
    this.getRoomDeskList();
    this.now = util.formatDate(+new Date());
  },

  deactivated () {
    this.closeRoomDeal();
  },
  // watch : {
  //   $route : 'getRoomDeskList'
  // },
  components: {
    loading,
    Group,
    Datetime,
    Swiper,
    Cell,
    CellBox
  }

}


</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->

<style lang="scss" scoped>
@import '../assets/scss/com.scss';
.allRoom-d{
  display: flex;
  display: -webkit-flex;
  width: 100%;
  position: relative;
  height: .72rem;
  div{
    width:50%;
    height: .68rem;
    text-align: center;
    line-height: .68rem;
    font-size: .3rem;
    border-bottom: 2px solid #f1f1f1;
  }
  .active{
    color: #2ad060;
    border-bottom: 2px solid #2ad060;
  }
}
.allRoom-d:before{
  content: "";
  width: 1px;
  height: .5rem;
  background-color: #e4e4e4;
  left: 50%;
  top: .1rem;
  position: absolute;
}

.red{
  color: #c42121;
}

.xing{
  padding-left: .2rem;
  i{
    color: #eee;
  }
}
.h-tags{
  padding-left: .2rem;
  height: .52rem;
  margin-top: .28rem;
  a{
    display: inline-block;
    width: 1.48rem;
    height: .48rem;
    font-size: .26rem;
    border: 1px solid;
    text-align: center;
    line-height: .48rem;
    border-radius: .08rem;
    float: left;
    margin-right: .6rem;
  }
}
.h-address{
  border-top: 1px solid #f1f1f1;
  padding: 0 .2rem;
  font-size: .24rem;
  color: #666;
  height: .7rem;
  line-height: .7rem;


}

.goMenu{
  display: block;
  font-size: .26rem;
  color: #fff;
  background: #4fc08d;
  position: absolute;
  top: 6.5rem;
  right: .4rem;
  border-radius: 50%;
  width: .8rem;
  height: .8rem;
  text-align: center;
  line-height: .8rem;
}
/*#amapDemo{
  height: 3rem;
}*/
.weui-cells.vux-no-group-title{
  margin-top: 0;
}

.map{
  padding: .2rem $padding;
  /*border-top:  1px solid $border;*/
  .mapTitle{
    font-size: .34rem;
    overflow: hidden;
  }
}

.el-vue-amap-container{
  height:  3rem;
}
.header-img{
  width: 100%;
  overflow: hidden;
  height: 5rem;
  position: relative;

  h6{
    font-size: $h6;
    font-weight: normal;
    color: #fff;
    position: absolute;
    bottom: .22rem;
    left: .3rem;
  }
}

.source{
  padding: .2rem $padding;
  color: $orange;
  font-size: .24rem;
  span{
    font-size: .46rem;
  }
}

.detail{
  border-top:  1px solid $border;
  padding: .2rem $padding;
  overflow: hidden;
  width: 100%;
  ul{
    width: 6rem;
    float: left;
    li{
      float: left;
      margin-right: .3rem;

      .detail-left-img{
        width: .5rem;
        margin: 0 auto;
      }
      .detail-left-desc{
        font-size: $p;
        color: $color3;
        text-align: center;
      }
    }
  }

  .more{
    float: right;
    margin: .3rem .5rem 0 0;
    a{
      font-size: $fontsize;
      color: $color3;
    }

  }
}

.time{
  padding: .4rem $padding;
  border-top:  1px solid $border;
  span{
    color: $color3;
    font-size: $fontsize;
  }

  .time-input{
    border: 1px solid $color3;
    color: $color3;
    width: 3.6rem;
    text-indent: .1rem;
    font-size: $fontsize;
    height: .6rem;
    line-height: .6rem;
  }
}


.allRoom{
  padding: .2rem $padding;
  border-top:  1px solid $border;
  .roomTitle{
    font-size: .34rem;
    overflow: hidden;
    border-bottom: 1px solid $border;
    padding-bottom: .2rem;
    h6{
      font-weight: normal;
      float: left;
    }
    span{
      float: right;
      font-size: .22rem;
      color: $orange;
      margin: .1rem 0 0 0;
    }
  }


  .room-list{
    padding: .2rem 0;
    position: relative;
    border-bottom: 1px solid $border;
    overflow: hidden;

    &:last-child{
      border-bottom: 0;
    }
    .room-left{
      float: left;
      h6{
        font-size: $h6;
        color: $color1;
      }

      p{
        color: $color3;
        font-size: $p;
        margin: .04rem 0 0 0;
      }
      .red{
        color: #c42121;
      }
    }
    .room-right{
      float: right;
      .reserve-btn{
        position: absolute;
        top: 50%;
        right: .2rem;
        transform: translateY(-50%);
      }
    }

  }

}


.allTable{
  padding: .2rem $padding;
  border-top:  1px solid $border;
  .tableTitle{
    font-size: .34rem;
    overflow: hidden;
    border-bottom:  1px solid $border;
    padding-bottom: .2rem;
    h6{
      font-weight: normal;
      float: left;
    }
    span{
      float: right;
      font-size: .22rem;
      color: $orange;
      margin: .1rem 0 0 0;
    }
  }


  .table-list{
    padding: .2rem 0;
    position: relative;
    border-bottom:  1px solid $border;
    overflow: hidden;
    .table-left{
      position: relative;
      float: left;
      h6{
        font-size: $h6;
      }

      p{
        color: $color3;
        font-size: $p;
        margin: .04rem 0 0 0;
      }
      .red{
        color: #c42121;
      }
    }
    .table-right{
      float: right;
      .reserve-btn{
        position: absolute;
        top: 50%;
        right: .2rem;
        transform: translateY(-50%);
      }
    }


  }

}


.popup{

  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  width: 100%;
  height: 100%;
  background: rgba(0,0,0,.7);
  z-index: 9;
  .popup-box{
    width: 6rem;
    max-height: 7rem;
    position: absolute;
    padding: 0 0 .2rem 0;
    top: 50%;
    left: 50%;
    margin: 0 0 0 -3rem;
    background: #fff;
    -o-transform: translateY(-50%);
    -moz-transform: translateY(-50%);
    -webkit-transform: translateY(-50%);
    transform: translateY(-50%);

    .popup-title{
      text-align: center;
      height: .8rem;
      line-height: .8rem;
      font-size: $fontsize;
      color: $color1;
      position: relative;
      font-weight: normal;
      h6{
        font-size: $title;
      }
      .close-btn{
        position: absolute;
        top: -.08rem;
        right: .2rem;
        font-size: .38rem;
      }
    }

    .popup-cont{
      h6{
        background: #f4f4f4;
        border: 1px solid #e5e5e5;
        font-size: $fontsize;
        font-weight: normal;
        padding: .02rem 0 .02rem $padding;
        color: $color3;
      }

      p{
        padding: .2rem $padding;
        color: $color2;
        font-size: $fontsize;
      }

      .hotel-msg{
        p{
          padding: .04rem $padding 0 $padding;
        }
      }
    }

  }

}

.roomDeal-bg{
  background: url(../assets/images/bg.png) repeat;
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 99;
}

.roomDeal{
  position: fixed;
  top: 0;
  width:100%;
  height: 100%;
  left: 0;
  z-index: 100;

  .table{
    margin: .2rem 0 0 0;
    td{
      border: 1px solid #959595;
      padding: .1rem .2rem;
    }
  }

  .roomDeal-box{
    margin: 0 0 0 -3.75rem;
    left: 50%;
    position: absolute;
    top: 2.4rem;
    bottom: 0;
    width: 7.5rem;
    background: #fff;

    &.desk{
      top: 6.4rem;
    }

    .roomDeal-title{
      background: #fff;
      color: $color1;
      font-size: $fontsize2;
      text-align: center;
      line-height: .8rem;
      position: relative;
      .close-roomDeal{
        position: absolute;
        top: -.05rem;
        right: .3rem;
        font-size: .4rem;
        color: $color1;
      }
    }

    .roomDeal-cont{
      width: 100%;
      position: absolute;
      bottom: 1rem;
      top: .8rem;
      overflow: hidden;
      -webkit-overflow-scrolling: touch;
      .room-device{
        overflow: hidden;
        font-size: $fontsize;
        color: $color1;
        padding: .2rem $padding;

        .room-device-left{
          width: 50%;
          float: left;
        }
        .room-device-right{
          width: 50%;
          float: left;
        }
      }

      .cannel-txt,
      .use-txt{
        overflow: hidden;
        font-size: $fontsize2;
        color: $color1;
        padding: .2rem $padding;
        border-top: 1px solid $border;
      }

    }

    .roomDeal-bottom{
      position: absolute;
      width: 100%;
      height: 1rem;
      bottom: 0;
      .reserve-money{
        float: left;
        width: 50%;
        height: 100%;
        display: block;
        text-align: center;
        line-height: 1rem;
        color: $green;
        background: #fafafa;
        font-size: $title;
      }
      .reserve-btn{
        float: left;
        width: 50%;
        height: 100%;
        display: block;
        text-align: center;
        line-height: 1rem;
        color: #fff;
        background: $green;
        font-size: $title;

      }


    }
  }
}


.allRoom-d1{
  min-height: 3rem;
}


</style>
