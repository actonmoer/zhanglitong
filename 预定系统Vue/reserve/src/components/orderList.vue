<template>
  <div class="orderList">
    <div class="headBox"></div>
    <tab>
      <tab-item selected @on-item-click="get">全部订单</tab-item>
      <tab-item @on-item-click="get(2)">支付成功</tab-item>
      <tab-item @on-item-click="get(3)">进行中</tab-item>
      <tab-item @on-item-click="get(6)">已完成</tab-item>
      <tab-item @on-item-click="get(8)">退款成功</tab-item>
    </tab>
    <section class="box">
      <div class="list" v-for="(i, index) in order">
        <router-link :to="{path:'/orderDetail', query: {'param': JSON.stringify({oid: i.order_id, isReserve: i.is_reserve, status: i.status})}}">
          <div class="list-img">
            <img :src="i.thumb">
          </div>
          <div class="list-msg">
            <p>订单号：{{i.order_id}}</p>
            <p>下单时间：{{i.addtime}}</p>
            <p>订单总价：{{i.price}}元</p>
            <p>联系人：{{i.company}} - {{i.telephone}}</p>
          </div>  
          <div class="list-status">
            <p>{{i.status_text}}</p>
          </div>
        </router-link>
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
import {getOrder} from '@/model/getData'


let util = new Util();
//     scrollBug = util.scrollBug;



export default {
  name: 'orderList',
  data () {
    return {
      status : false,
      order : [],

    }
  },
  methods: {
    onItemClick (index) {
      console.log('on item click:', index)
    },

    async get(status){
      this.status = true;
      let opt = status ? {status : status} : "",
          data = await getOrder(opt);
      this.order = data.data;
      this.status = false;
    }
  },

  async activated () {
    this.get();

  },

  components: {
    loading, Tab, TabItem
  }

}


</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->

<style lang="scss" scoped>
@import '../assets/scss/com.scss';

.box{
  overflow: hidden;

  .list{
    overflow: hidden;
    border-bottom: 1px solid #ddd;
    padding: .3rem .2rem;
    a{
      display: block;
      width: 100%;
      height: 100%;
      overflow: hidden;
    }
    .list-img{
      float: left;
      width: 1.4rem;
      height: 1.4rem;
    }
    .list-msg{
      font-size: $fontsize;
      color: $color2;
      float: left;
      margin:  0 0 0 .2rem;
      width: 4.3rem;
      p{
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
      }
    }
    .list-status{
      float: right;
      font-size: $fontsize;
      margin: .5rem 0 0 0;
      color: $red;
      width: 1.2rem;
    }
  }
}

</style>
