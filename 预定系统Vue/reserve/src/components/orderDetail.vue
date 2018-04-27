<template>
  <div class="orderDetail">
    <div class="headBox"></div>
    <section class="box">
      <h6>订单详情</h6>
      <div class="order-box">
        <div class="hotel"><p>{{order.company}}</p></div>
        <div class="menu-box">
          <div class="menu-list" v-for="(i, index) in cuisines">
            <span class="menu-name">{{i.title}}<b>（{{i.add_title || "无"}}）</b></span>
            <span class="menu-mun">x{{i.num}}</span>
            <span class="menu-money">￥{{i.detial_cash}}</span>
          </div>
        </div>
        <div class="order-money">
          <p class="discount" v-if="order.is_discount == 1">折扣：{{order.discount_num}}折</p>
          <p>总价：￥{{order.price}}</p>
        </div>
      </div>
    </section>

    <section class="box">
      <h6>订单信息</h6>
      <div class="order-box">
        <div class="order-list">
          <div class="order-title">订单号</div>
          <div class="order-cont">{{order.order_id}}</div>
        </div>
        <div class="order-list">
          <div class="order-title">订单时间</div>
          <div class="order-cont">{{order.addtime}}</div>
        </div>
        <div class="order-list">
          <div class="order-title">状态</div>
          <div class="order-cont" style="color: red;">{{order.status_text}}</div>
        </div>
        <div class="order-list">
          <div class="order-title">备注</div>
          <div class="order-cont">{{order.remark}}</div>
        </div>
      </div>
    </section>

    <section class="box" v-if="param.isReserve == 1">
      <h6>用户信息</h6>
      <div class="user-box">
        <div class="user-list">
          <div class="user-title">姓名</div>
          <div class="user-cont">{{order.contact_name}}</div>
        </div>
        <div class="user-list">
          <div class="user-title">联系电话</div>
          <div class="user-cont">{{order.mobile}}</div>
        </div>
        <div class="user-list">
          <div class="user-title">人数</div>
          <div class="user-cont">{{order.persons}}</div>
        </div>
        <div class="user-list">
          <div class="user-title">预定酒店</div>
          <div class="user-cont">{{order.company}}</div>
        </div>
        <div class="user-list">
          <div class="user-title">结束时间</div>
          <div class="user-cont">{{ endtime }}</div>
        </div>
        <div class="user-list">
          <div class="user-title">预定房间</div>
          <div class="user-cont">{{room.room_name || "无"}}</div>
        </div>
        <div class="user-list">
          <div class="user-title">预定桌子</div>
          <div class="user-cont">{{desk.desk || "无"}}</div>
        </div>
      </div>
    </section>

    <!--按钮悬浮底部-->
    <div style="height: .8rem;background-color: #fff;">
      <div class="fixed-btn">
        <div :class="[orderStatus == 1 || orderStatus == 2 ? 'fixed-btn-2' : 'fixed-btn-4']" v-if="param.isReserve == 1">
          <a @click="invite" class="btn invite-btn" v-if="param.status != 6">我的邀请函</a>
        </div>
        <div class="fixed-btn-2" v-if="orderStatus == 1">
          <a class="btn invite-btn" v-bind:href='order.pay_url'>去支付</a>
        </div>

        <div class="fixed-btn-2" v-if="orderStatus == 2">
          <a href="javascript:;" class="btn refund-btn" @click="showAlert">申请退款</a>
        </div>

        <div class="fixed-btn-4" v-if="orderStatus == 3">
          <a class="btn invite-btn" @click="cWaiter">呼叫服务员</a>
        </div>
        <div class="fixed-btn-4" v-if="orderStatus == 3">
          <router-link :to="{path:'menu', query: {'param': JSON.stringify({id: order.restaurant_id, type: 2, reserve_price: 0, oid: order.order_id})}}" class="btn addCuisines-btn">增加菜式</router-link>
        </div>
        <div class="fixed-btn-4" v-if="orderStatus == 3">
          <a class="btn invite-btn" v-bind:href='order.checkout_pay_url'>结账</a>
        </div>
      </div>
    </div>

    <!--<section class="btn-box" v-if="orderStatus == 1">-->
      <!--<a class="btn invite-btn" v-bind:href='order.pay_url'>去支付</a>-->
    <!--</section>-->

    <!--<section class="btn-box" v-if="orderStatus == 3">-->
      <!--<a class="btn invite-btn" @click="cWaiter">呼叫服务员</a>-->
    <!--</section>-->

    <!--<section class="btn-box" v-if="orderStatus == 3">-->
      <!--<a class="btn invite-btn" v-bind:href='order.checkout_pay_url'>结账</a>-->
    <!--</section>-->

    <!--<section class="btn-box" v-if="orderStatus == 2">-->
      <!--<a href="javascript:;" class="btn refund-btn" @click="showAlert">申请退款</a>-->
    <!--</section>-->

    <!--<section class="btn-box" v-if="param.isReserve == 1">-->
      <!--<a @click="invite" class="btn invite-btn">我的邀请函</a>-->
    <!--</section>-->

    <!--<section class="btn-box" v-if="orderStatus == 3">-->
      <!--<router-link :to="{path:'menu', query: {'param': JSON.stringify({id: order.restaurant_id, type: 2, reserve_price: 0, oid: order.order_id})}}" class="btn addCuisines-btn">增加菜式</router-link>-->
    <!--</section>-->

    <section class="alert" v-if="alert">
      <div class="alert-box">
        <div class="alert-title">确认要申请退款？</div>
        <div class="alert-cont">
          <p>
            <span>退款理由</span>
            <input type="text" class="refund-input" v-model="refund_reason">
          </p>
        </div>
        <div class="alert-btn">
          <a href="javascript:;" class="cancel-btn" @click="CloseAlert">取消</a>
          <a href="javascript:;" class="submit-btn" @click="refund">确认</a>
        </div>
      </div>
    </section>

    <loading :status="status"></loading>
  </div>
</template>

<script>
import Vue from 'vue'
import loading from '@/components/public/loading'
import {Tab, TabItem} from 'vux'
import {Util} from '@/assets/js/util.js'
import {getOrderDetal, refunds, callWaiter} from '@/model/getData'


let util = new Util();
//     scrollBug = util.scrollBug;



export default {
  name: 'orderDetail',
  data () {
    return {
      status : false,
      order : {},
      room  : {},
      desk  : {},
      cuisines:{},
      param : {},
      alert : false,
      refund_reason: "无",
      orderStatus : 0,
      endtime: ""
    }
  },
  methods: {
    onItemClick (index) {
      console.log('on item click:', index)
    },
    async refund () {
      this.status = true;
      let data = await refunds({
        order_id: this.param.oid,
        refund_reason: this.refund_reason
      });
      if(data.code == 200){
        this.$router.push({path: 'orderList'});
      }
      this.status = false;
    },
    async cWaiter () {
      let self = this;
      let data1 = await callWaiter({
        rid: self.order.restaurant_id,
        commonid: self.order.commonid,
        reserve_type: self.order.resver_type
      });
    },

    showAlert () {
      this.alert = true;
    },

    CloseAlert () {
      this.alert = false;
    },

    invite () {
      window.location.href = "invite/?oid=" + this.order.order_id;
    },

//    callWaiter () {
//      Vue.http.get('/mobile/call_small_two.php', {
//        rid:this.order.restaurant_id,
//        commonid:this.order.commonid,
//        reserve_type:this.order.reserve_type
//      }).then(function(d){
//        console.log(d);
//        alert("成功呼叫服务员,请稍等");
//      });
//    }

  },

  async activated () {
    this.param = JSON.parse(this.$route.query.param);
    this.status = true;
      // let opt = status ? {status : status} : "",
    let data = await getOrderDetal({
      order_id: this.param.oid
    });
    // this.id = data.restaurant_id;
    console.log(data);
    this.order = data.data;
    this.orderStatus = data.data.status;
    this.room = data.data.room.length == 0 ? {} : data.data.room[0];
    this.desk = data.data.desk.length == 0 ? {} : data.data.desk[0];
    this.cuisines = data.data.cuisines || [];
    this.endtime = data.data.endtime;
    this.status = false;
  },

  components: {
    loading, Tab, TabItem
  }

}


</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->

<style lang="scss" scoped>
@import '../assets/scss/com.scss';

.orderDetail{
  overflow: hidden;
  background: #eee;
  font-size: $fontsize2;

  h6{
    padding: .2rem;
    font-size: $h6;
  }

  .order-box,
  .user-box{
    background: #fff;
  }

  .hotel{
    font-size: $h6;
    border-bottom: 1px solid #ddd;

    p{
      padding: .1rem .2rem;
    }
  }

  .menu-box{
    padding: 0 .2rem;
    font-size: $fontsize2;
    border-bottom: 1px solid #ddd;
    .menu-list{
      margin: .2rem 0;
      overflow: hidden;
      .menu-name{
        display: block;
        float: left;
        width: 4rem;

        b{
          font-size: $p;
          color: $color2;
          font-weight: normal;
        }
      }
      .menu-mun{
        display: block;
        float: left;
        width: .55rem;
      }
      .menu-money{
        display: block;
        float: right;
      }

    }

  }
  .order-money{
    text-align: right;
    padding: .2rem;
    font-size: $title;
    color: $orange;
    .discount{
      color: $color3;
      font-size: $p;
    }
  }

  .user-box,
  .order-box{
    overflow: hidden;
    padding: 0 .2rem;
    .user-list,
    .order-list{
      overflow: hidden;
      font-size: $fontsize2;
      border-bottom: 1px solid #ddd;
      padding: .2rem 0;
      .user-title,
      .order-title{
        float: left;
        width: 1.5rem;
      }
      .user-cont,
      .order-cont{
        float: left;
      }
    }
  }
  .btn-box{
    position: relative;
    width: 100%;
    background: #fff;
    overflow: hidden;
  }
  .refund-btn,
  .invite-btn,
  .addCuisines-btn{
    width: 90%;
    height: .6rem;
    margin: .4rem auto 0 auto;
    display: block;
    line-height: .6rem;
  }

  .alert{
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,.5);

    .alert-box{
      width: 5rem;
      margin: 0 auto;
      background: #fff;
      border-radius: 5px;
      position: absolute;
      top: 50%;
      left: 50%;
      margin: 0 0 0 -2.5rem;
      transform: translateY(-50%);
      -webkit-transform: translateY(-50%);
      .alert-title{
        font-size: $title;
        text-align: center;
        padding: .2rem;
      }

      .alert-cont{
        font-size: $p;
        padding: .2rem .2rem .4rem .2rem;
        text-align: center;
        .refund-input{
          border: 1px solid $border;
          font-size: $p;
          color: $color2;
          padding: .1rem;
          width: 2.6rem;
        }


      }
      .alert-btn{
        width: 100%;
        overflow: hidden;
        height: .8rem;
        border-top: 1px solid $border;
        background: url(../assets/images/border.png) top repeat-y;
        a{
          display: block;
          float: left;
          width: 50%;
          line-height: .8rem;
          text-align: center;
        }
        .cancel-btn{
          color: $red;
        }
        .submit-btn{
          color: $green;
        }
      }
    }
  }

}

.fixed-btn{
  position: fixed;
  width: 100%;
  bottom: -4px;
  height: .8rem;
  display: -webkit-box;
  display: -moz-box;
  display: -webkit-flex;
  display: -moz-flex;
  display: -ms-flexbox;
  display: flex;
  div a{
    -webkit-box-sizing: border-box;
    -moz-box-sizing: border-box;
    box-sizing: border-box;
    padding: 0;
    margin: 0 auto!important;
  }
  div.fixed-btn-2{
    width: 50%;
  }
  div.fixed-btn-4{
    width: 25%;
  }
}

</style>
