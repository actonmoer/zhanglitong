<template>
  <div class="hotel">
    <section class="hotel-box">
      <div class="hotel-list" v-for="(i, index) in list">
        <router-link
          :to="{path:'roomList', query: {'param': JSON.stringify({id: i.id, type: 0, reserve_price: i.reserve_price})}}"
          class="room-link">
          <!-- <router-link :to="{path:'menu', query: {'param': '{id: i.id, type: 0}'}}" class="room-link"> -->
          <!-- <router-link :to="'menu/'+i.id+'|0'" class="room-link"> -->
          <div class="hotel-img">
            <img :src="i.thumb">
          </div>
          <div class="hotel-msg">
            <h6>{{i.company}}</h6>
            <!--<p>月订单{{i.month_sells}}单</p>-->
            <p><span
              class="month-sells">月订单{{i.month_sells}}单</span><span>{{i.rooms_now == 1 ? '有' : '无'}}空房 /{{i.desks_now == 1 ? '有' : '无'}}空桌</span>
            </p>
            <p class="time">营业时间：{{i.servicetime}}</p>
            <!--<p>{{i.rooms_now == 1 ? '有' : '无'}}空房 /{{i.desks_now == 1 ? '有' : '无'}}空桌</p>-->
            <p v-if="i.is_present == 1 && i.title != null">预定成功，送{{i.title}}</p>
            <p>综合评分：<span class="score">{{i.score}}</span></p>
          </div>
          <div class="hotel-other">
            <span>{{i.distance > 100000 ? ">100km" : i.distance > 1000 ? Math.floor(i.distance / 1000) + "km" : i.distance + "m"}}</span>
            <span class="hotel-money">订金：￥{{i.reserve_price}}</span>
          </div>
        </router-link>
      </div>
      <div class="listEmpty" v-if="list.length == 0">
        <span>暂时没有此分类</span>
      </div>
    </section>
  </div>

</template>
<script>
  import {giveFood} from '@/model/getData'

  export default {
    name: 'hotel',
    props: {
      list: Array
    },
    data() {
      return {}
    },
    created() {

    },
    methods: {}
  }
</script>
<style lang="scss" scoped>
  @import '../../assets/scss/com.scss';

  .room-link {
    color: $color1;
    display: block;
    width: 100%;
    height: 100%;
    overflow: hidden;
  }

  .hotel-list {
    padding: .2rem $padding;
    border-bottom: 1px solid $border;
    overflow: hidden;
  }

  .hotel-img {
    float: left;
    width: 1.8rem;
    height: 1.8rem;
  }

  .hotel-msg {
    float: left;
    margin: 0 0 0 .2rem;
    width: 3rem;
    h6 {
      font-size: $h6;
      margin-bottom: .08em;
      font-weight: normal;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }
    .score {
      color: $orange;
    }
    p {
      color: $color2;
      font-size: $p;
      line-height: .34rem;
      .month-sells {
        display: inline-block;
        margin-right: 6px;
      }
    }
    .time {
      color: $orange;
    }
  }

  .hotel-other {
    float: right;
    text-align: right;
    span {
      display: block;
      font-size: $p;
      color: $color2;
      margin: .2rem 0 0 0;
    }
    .hotel-money {
      display: block;
      font-size: $p;
      color: $green;
      margin: 1rem 0 0 0;
    }
  }

  .test {
    /*border: 1px solid $border;*/
    color: $green;
    font-size: $s;
    /*line-height: .35rem;*/
    /*text-indent: .2rem;*/
  }

  .listEmpty {
    margin-top: 3rem;
    width: 100%;
    text-align: center;
    font-size: .3rem;
    color: #666;
  }
</style>
