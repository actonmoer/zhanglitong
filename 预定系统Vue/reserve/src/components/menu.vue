<template>

  <div class="menu">

    <!--<section class="menu-title">
      <h6>{{param.name || "大酒店"}}</h6>
    </section>-->
    <section class="filler">
      <div class="filler-box">

        <div class="filler-title">
          <div class="filler-list" >
            <a href="javascript:;" @click.stop="selecteekday(1)">今日优惠</a>
          </div>
          <div class="filler-list" @click.stop="selectHot(1)">
            <a href="javascript:;">店长推荐</a>
          </div>
          <div class="filler-list" @click.stop="selectLevel(1)">
            <a href="javascript:;">特色热卖</a>
          </div>
        </div>

      </div>
    </section>

    <section class="menu-cont">
      <div class="menu-1" ref="menu1">
        <ul>
          <li v-for="(i, index) in classes" @click.stop="selectClasses(i.typeid, index)" :class="[index == isActive ? 'active' : '']"><p>{{i.typename}}</p></li>
        </ul>
      </div>
      <div class="menu-2" ref="menu2">
        <ul>

          <li v-for="(i, index) in food">
            <div v-if="i.spec_attr != 0" @click.stop="showMenuSize(index)">
              <div class="menu-img">
                <img :src="i.thumb">
              </div>
              <div class="menu-txt">
                <h6>{{i.title}}</h6>
                <p>月销 {{i.month_sells}}</p>
                <div class="menu-btn">
                  <p class="money">￥{{i.price}}</p>
                  <a href="javascript:;" class="size-btn">选规格</a>
                </div>
              </div>
            </div>
            <div v-else @click.stop="joinCar(i.itemid,i.price, 0)">
              <div class="menu-img">
                <img :src="i.thumb">
              </div>
              <div class="menu-txt">
                <h6>{{i.title}}</h6>
                <p>月销 {{i.month_sells}}</p>
                <div class="menu-btn">
                  <p class="money">￥{{i.price}}</p>
                  <div class="menu-num">
                    <a href="javascript:;" class="join-btn">加入菜单</a>
                  </div>
                </div>
              </div>
            </div>
          </li>
        </ul>
      </div>
    </section>


    <section class="menu-bottom">
      <div class="menu-bottom-left">
        <div class="cart-icon" @click.stop="showBuyCar">
          <b v-if="buyCar.length != 0">{{buyCar.length}}</b>
        </div>
        <h6 class="money">￥{{total}}</h6>
        <p class="ps">押金：￥{{param.reserve_price}}</p>
      </div>
      <div class="menu-bottom-right">
        <router-link class="go-pay-btn" :to="{path: 'roomList', query: {'param': JSON.stringify(param)}}" v-if="param.type==0">去看看</router-link>
        <router-link class="go-pay-btn" :to="{path: 'reserve', query: {'param': JSON.stringify(param)}}" v-else-if="param.type==1">去结算</router-link>
        <a href="javascript:;" class="addCuisines-btn"  v-else @click="submit">确认加菜</a>

      </div>
    </section>

    <transition
      enter-active-class="enterActiveClass2"
      leave-active-class="leaveActiveClass2"
      enter-class="enterClass2"
      leave-class="leaveClass2"
    >
    <section class="menuSize" v-if="menuSize.status">
      <div class="menuSize-box">
        <div class="menuSize-title">
          <h6>{{foodDetail.title}}</h6>
          <a href="javascript:;" class="close-menuSize" @click="closeMenuSize">x</a>
        </div>
        <div class="menuSize-cont">
          <div class="menuSize-list" v-for="(i, index) in foodDetail.spec_attr">
            <h6>{{i.title}}</h6>
            <ul>
              <li :class="{on: r.status}" @click="selectType(index,index2)" v-for="(r, index2) in i.cont">{{r.name}}</li>
            </ul>
          </div>
          <div class="menuSize-bottom">
            <span class="goods-money">￥{{foodDetail.money}}</span>
            <a href="javascript:;" class="btn addCar-btn" @click.stop="joinCar(foodDetail.itemid, foodDetail.money, 1)">加入购物车</a>
          </div>
        </div>
      </div>
    </section>
    </transition>

    <section class="buyCar" v-if="buyCar.status" @click.stop="closeBuyCar">
       <div class="buyCar-box menu-3" ref="menu3">
         <div class="buyCar-cont">
           <div class="buyCar-list" v-for="(i, index) in buyCar.cont">
             <div class="buyCar-list-title">
               <h6>{{i.title}}</h6>
               <p v-if="i.spec_attr">
                 <span>规格：</span>
                 <span v-for="r in i.spec_attr">
                  <span v-for="s in r.cont">{{s.name}},</span>
                 </span>
               </p>
               <p v-else>
                 <span>规格：</span>
                 <span>无</span>
               </p>
             </div>
             <div class="buyCar-list-money">￥{{i.price}}</div>
             <div class="buyCar-list-num">{{i.num}}</div>
             <a class="buyCar-list-del" @click.stop="delBuyCar(i.placeid)">删除</a>
           </div>

         </div>
       </div>
    </section>
    <loading :status="status"></loading>
    <tips :tipsStatus="tipsStatus" :text="text"></tips>
  </div>
</template>

<script>
import Vue from 'vue'
import {XHeader} from 'vux'
import {Util} from '@/assets/js/util.js'
import store from '@/vuex/store.js'
import BScroll from 'better-scroll'
import loading from '@/components/public/loading'
import tips from '@/components/public/tips'
import {getMenuClasses, getMenuList, joinCar, getCar, delCar, addCuisines} from '@/model/getData'

let util = new Util();
// Vue.use(Vuex);

var menu2;
export default {
  name: 'menu',
  data () {
    return {
      id : 0,
      cid: 0,
      money: 0,
      total: 0,
      status : false,
      tipsStatus: false,
      text: "",
      cache : {},
      param: {},
      type: 0,
      isActive: 0,
      menuSize : {
        status : false
      },

      buyCar: {
        status: false,
        cont: [],
        length: 0
      },

      classes: [],
      spec_attr_arr: [],


      food: [],
      foodDetail: {
        spec_attr: [],
        title: "",
        money: 0,
        itemid  : 0,
      },

    }
  },
  methods: {
    showMenuSize (index) {
      this.menuSize.status = true;
      this.foodDetail.spec_attr = this.food[index].spec_attr;
      this.foodDetail.title = this.food[index].title;
      this.foodDetail.money = this.food[index].price;
      this.foodDetail.itemid = this.food[index].itemid;

      this.money = +this.food[index].price;

      for(var i in this.foodDetail.spec_attr){
        for(var r in this.foodDetail.spec_attr[i].cont){
          if(this.foodDetail.spec_attr[i].cont[r].status){
            // this.foodDetail.money = +this.foodDetail.money + (+this.foodDetail.spec_attr[i].cont[r].money);
            this.foodDetail.money = +this.foodDetail.spec_attr[i].cont[r].money;
            this.spec_attr_arr[i] = this.foodDetail.spec_attr[i].cont[r].id;
          }
        }
      }
    },
    closeMenuSize () {
      this.menuSize.status = false;
    },

    async submit () {

      this.status = true;
      let data = await addCuisines({
        rid      : this.param.id,
        order_id : this.param.oid,
      })

      if(data.code == 200){
        this.$router.push({path: 'orderDetail', query: {param: JSON.stringify(this.param)}});
      }

    },

    async getBuyCar () {
      // if(!run){
      //   this.status = false;
      //   return;
      // }

      let data = await getCar({rid: this.param.id}),
          num = 0;
      this.status = false;
      if(data){
        this.buyCar.cont = data.cuisines;
        store.state.cuisines = data.cuisines;
        for (let i in data.cuisines) {
          num += data.cuisines[i].num;
        }
        this.total = data.detial_price;
        this.buyCar.length = num;
      }


    },

    async showBuyCar () {
      let self = this;
      // this.status = true;
      // await this.getBuyCar();
      this.buyCar.status = true;
      setTimeout(function(){
        let menu3 = new BScroll(self.$refs.menu3, {
              click: true,
              bounce:false,
            });
      }, 300);
    },

    closeBuyCar () {
      this.buyCar.status = false;
    },

    async joinCar (cid, cash, type) {
      this.status = true;
      let opt = {};
      if(type == 0){
        opt = {
          rid        : this.param.id,
          cuisine_id : cid,
          num        : 1,
          cash       : cash,
          // spec_attr  : spec_attr
          is_spec  : 0
        };
      }else{
        opt = {
          rid        : this.param.id,
          cuisine_id : cid,
          num        : 1,
          cash       : cash,
          spec_attr  : this.spec_attr_arr,
          is_spec    : 1
        };
      }
      let data = await joinCar(opt);
      this.getBuyCar();
      this.closeMenuSize();


    },

    async delBuyCar (placeid) {
      this.status = true;

      let data = await delCar({rid: this.param.id, num: 1, placeid: placeid});
      this.getBuyCar();
    },


    async selectClasses (typeId ,index) {
      this.isActive = index;
      this.status = true;
      if(!store.state.cache[typeId]){
        let data = await getMenuList({
              rid           : this.param.id,
              cuisine_catid : typeId,
            });

        this.food = data.restaurant;
        store.state.cache[typeId] = data.restaurant;
      }else{
        this.food = store.state.cache[typeId]
      }
      setTimeout(function(){
        menu2.refresh();
      }, 300);
      menu2.refresh();
      this.status = false;
    },

    async selecteekday (p) {
      this.status = true;
      if(!store.state.cache["is_weekday"]){
        let data = await getMenuList({
              rid           : this.param.id,
              is_weekday    : p
            });

        this.food = data.restaurant;
        store.state.cache["is_weekday"] =data.restaurant;
      }else{
        this.food = store.state.cache["is_weekday"];
      }
      setTimeout(function(){
        menu2.refresh();
      }, 300);
      menu2.refresh();
      this.status = false;
    },
    async selectHot (p) {
      this.status = true;

      if(!store.state.cache["is_feature_hot"]){
        let data = await getMenuList({
              rid           : this.param.id,
              is_feature_hot    : p
            });

        this.food = data.restaurant;
        store.state.cache["is_feature_hot"] =data.restaurant;
      }else{
        this.food = store.state.cache["is_feature_hot"];
      }
      setTimeout(function(){
        menu2.refresh();
      }, 300);
      menu2.refresh();
      this.status = false;
    },
    async selectLevel (p) {
      this.status = true;

      if(!store.state.cache["is_level"]){
        let data = await getMenuList({
              rid           : this.param.id,
              is_level    : p
            });

        this.food = data.restaurant;
        store.state.cache["is_level"] =data.restaurant;
      }else{
        this.food = store.state.cache["is_level"]
      }
      setTimeout(function(){
        menu2.refresh();
      }, 300);
      menu2.refresh();
      this.status = false;
    },

    selectType (index,index2) {
      this.foodDetail.money = this.money;
      for(var r in this.foodDetail.spec_attr[index].cont){
        this.foodDetail.spec_attr[index].cont[r].status = false;
      }
      this.foodDetail.spec_attr[index].cont[index2].status = true;

      for(var i in this.foodDetail.spec_attr){
        for(var r in this.foodDetail.spec_attr[i].cont){
          if(this.foodDetail.spec_attr[i].cont[r].status){
            // this.foodDetail.money = this.foodDetail.money + (+this.foodDetail.spec_attr[i].cont[r].money);
            this.foodDetail.money = +this.foodDetail.spec_attr[i].cont[r].money;
            this.spec_attr_arr[i] = this.foodDetail.spec_attr[i].cont[r].id;

          }
        }
      }
    }
  },
  activated () {
    this.isActive = 0;
    let self = this;
    this.tipsStatus = false;
    this.classes = [];
    this.food = [];
    this.param = JSON.parse(this.$route.query.param);
    // if(this.id != this.param.id){
      this.status = true;
      let p1 = new Promise((resolve, reject) => {
        this.id = this.param.id;
        let data = getMenuClasses(this.param.id);
        resolve(data)

      });

      let p2 = new Promise(async (resolve, reject) => {
        let data = getCar({rid: this.param.id});

        resolve(data)

      });


      Promise.all([p1, p2]).then(async (res) => {
        let num = 0;
        this.classes = res[0];


        if(res[0].length == 0){
          this.status = false;
          this.tipsStatus = true;
          this.text = "暂无菜单";
          return;
        }

        this.cid = res[0][0].typeid;


        if(res[1]){
          this.buyCar.cont = res[1].cuisines;
          store.state.cuisines = res[1].cuisines;
          for (let i in res[1].cuisines) {
            num += res[1].cuisines[i].num;
          }
          this.total = res[1].detial_price;
          this.buyCar.length = num;
        }

        if(!store.state.cache[this.cid]){
          let data = await getMenuList({
              rid           : this.param.id,
              cuisine_catid : this.cid,
            });

          this.food = data.restaurant;
          store.state.cache[this.cid] = data.restaurant;



        }else{
          this.food = store.state.cache[this.cid]
        }

        this.status = false;

        setTimeout(function(){
          let menu1 = new BScroll(self.$refs.menu1, {
            click: true
          });
          menu2 = new BScroll(self.$refs.menu2, {
            click: true,
            checkDOMChanges: true
          });
        }, 300);
      })

      // promise.then(async () => {

      //

      // })
    // }


  },

  components: {
    loading,
    XHeader,
    tips
  }

}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style lang="scss" scoped>
@import '../assets/scss/com.scss';


.menu{
  position: fixed;
  top: 46px;
  left: 50%;
  margin: 0 0 0 -3.75rem;
  width: 7.5rem;
  bottom: 0;

  .menu-title{
    line-height: 1rem;
    background: $green;
    text-align: center;
    color: #fff;
    font-size: $h6;
  }

  .menu-bottom{
    position: absolute;
    bottom: 0;
    width: 100%;
    height: 1rem;
    background: #fffefd;
    border-top: 1px solid #cacaca;
    z-index: 97;
    .menu-bottom-left{
      float: left;
      width: 68%;
      height: 100%;
      .money{
        font-size: .4rem;
        color: $orange;
        font-weight: normal;
        margin: 0 0 0 1.5rem;
      }
      .ps{
        font-size: $p;
        margin: 0 0 0 1.5rem;
        color: $color1;
      }
    }
    .menu-bottom-right{
      float: left;
      width: 32%;
      height: 100%;
      .go-pay-btn,
      .addCuisines-btn{
        display: block;
        width: 100%;
        height: 100%;
        line-height: 1rem;
        text-align: center;
        background: $green;
        color: #fff;
        font-size: $fontsize;
      }
    }
  }

  .menu-cont{
    position: absolute;
    top: 36px;
    bottom: 1rem;
    width: 100%;
    .menu-1{
      width: 25%;
      height: 100%;
      overflow-y: hidden;
      overflow-x: hidden;
      -webkit-overflow-scrolling: touch;
      background: #efefef;
      float: left;
      ul{
        li{
          height: 1.2rem;
          border-bottom: 1px solid $border;
          p{
            font-size: $fontsize;
            color: $color1;
            text-align: center;
            position: relative;
            top: 50%;
            -webkit-transform: translate(0,-50%);
          }
        }
        .active{
          background-color: #4fc08d;
          p{
            color: #fff;
          }
        }
      }
    }
    .menu-2{
      width: 75%;
      height: 100%;
      overflow-y: hidden;
      overflow-x: hidden;
      -webkit-overflow-scrolling: touch;
      background: #fff;
      float: left;
      ul{
        overflow: hidden;
        padding: 0 $padding;
        li{
          padding: .15rem 0;
          overflow: hidden;
          border-bottom: 1px solid $border;
          .menu-img{
            width: 1.5rem;
            overflow: hidden;
            float: left;
          }

          .menu-txt{
            margin: 0 0 0 .2rem;
            float: left;
            h6{
              font-size: $fontsize;
            }
            p{
              font-size: $p;
              color: $color2;
            }

            .size-btn,
            .join-btn{
              background: green;
              display: block;
              float: right;
              font-size: .24rem;
              border-radius: 5px;
              padding: .05rem .1rem;
              color: #fff;
            }
            .menu-btn{
              overflow: hidden;
              width: 3.2rem;
              .money{
                font-size: $title;
                color: $orange;
                float: left;
              }
              .menu-num{

                float: right;
                .minus-btn{
                  width: .5rem;
                  height: .5rem;
                  background: #fff;
                  border-radius: 50%;
                  line-height: .5rem;
                  text-align: center;
                  display: block;
                  float: left;
                  font-size: $fontsize;
                  color: $color2;
                  font-weight: bold;
                  border: 1px solid $border;
                }
                span{
                  float: left;
                  line-height: .5rem;
                  display: block;
                  font-size: $fontsize;
                  margin: 0 .2rem;
                }
                .plus-btn{
                  width: .5rem;
                  height: .5rem;
                  background: $green;
                  border-radius: 50%;
                  line-height: .5rem;
                  text-align: center;
                  display: block;
                  float: left;
                  font-size: $fontsize;
                  color: #fff;
                  font-weight: bold;
                  border: 1px solid $border;
                }
              }
            }
          }
        }
      }
    }
  }

  .cart-icon{
    z-index: 2;
    position: absolute;
    top: -.25rem;
    left: .25rem;
    width: 1.1rem;
    height: 1.1rem;
    background: url(../assets/images/cart_72px_1132260_easyicon.net.png);
    background-size: 100% 100%;

    b{
      border-radius: 50%;
      width: .4rem;
      height: .4rem;
      background: #f00;
      color: #fff;
      text-align: center;
      line-height: .4rem;
      display: block;
      font-size: $s;
    }
  }

}

.buyCar{
  position: fixed;
  bottom: 0;
  background: rgba(0,0,0,.5);
  z-index: 80;
  width: 100%;
  height: 100%;

  .buyCar-box{
    width: 7.5rem;
    bottom: 1rem;
    left: 50%;
    margin: 0 0 0 -3.75rem;
    max-height: 6rem;

    position: absolute;
    overflow: hidden;
    -webkit-overflow-scrolling: touch;
    .buyCar-cont{
      position: relative;
      background: #fff;
      -webkit-overflow-scrolling: touch;
      .buyCar-list{
        position: relative;
        overflow: hidden;
        padding: .2rem;
        border-bottom: 1px solid $border;
        .buyCar-list-title{
          float: left;
          width: 3.5rem;
          h6{
            font-size: $fontsize2;

          }
          p{
            font-size: $p;
            color: $color3;
          }

        }
        .buyCar-list-money{
          float: left;
          line-height: .8rem;
          margin: 0 0 0 .2rem;
          font-size: $p;
          color: $orange;
          width: 1rem;
        }

        .buyCar-list-num{
          float: left;
          line-height: .8rem;
          margin: 0 0 0 .4rem;
          font-size: $p;
          width: .5rem;
        }

        .buyCar-list-del{
          float: right;
          line-height: .8rem;
          margin: 0 .4rem 0 0;
          font-size: $p;
        }
      }
    }
  }
}

.filler{
  position: relative;

  .filler-box{
    position: absolute;
    top: 0;
    width: 7.5rem;
    z-index: 70;
    top: 0;
  }
  .filler-title{
    height: .8rem;
    width: 100%;
    overflow: hidden;
    position: relative;
    border-top: 1px solid $border;
    border-bottom: 1px solid $border;
    background: #fff;
    .filler-list{
      float: left;
      width: 33.3333%;
      height: 100%;
      position: relative;
      a{
        display: block;
        text-align: center;
        line-height: .8rem;
        font-size: $fontsize;
        color: $color1;
      }

      &.on{
        a{
          color: $green;
        }
      }
    }


  }

  .filler-list:nth-of-type(1),
  .filler-list:nth-of-type(2){
    &:after {
      content: "";
      background: $border;
      width: 1px;
      height: .56rem;
      position: absolute;
      top: 50%;
      right: 0;
      -webkit-transform: translateY(-50%);
      transform: translateY(-50%);
    }
  }


  .filler-cont{
    background: #f2f2f2;
    overflow: hidden;
    height: 6rem;
    width: 100%;

    .filler-level-1{
      width: 50%;
      height: 100%;
      float: left;
      .filler-level-1-list{
        overflow-y: scroll;
        overflow-x: hidden;
        box-sizing: inherit;
        -webkit-tap-highlight-color: transparent;
        -webkit-overflow-scrolling: touch;
        width: 100%;
        float: left;

        line-height: .9rem;
        a{
          color: $color2;
          font-size: $fontsize;
          display: block;
          padding: 0 .13rem 0 .26rem;
          &.select{
            background: #fff;
          }
        }

      }
    }


  }
}

.menuSize{
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0,0,0,.7);
  z-index: 100;
  .menuSize-box{
    width: 6rem;
    margin: 40% auto;
    background: #fff;
    border-radius: 5px;

    .menuSize-title{
      text-align: center;
      font-size: $title;
      line-height: .8rem;
      position: relative;
      .close-menuSize{
        position: absolute;
        top: -.1rem;
        right: .2rem;
        font-size: $h6;
        color: $color1;
      }

    }
    .menuSize-cont{
      padding: 0 $padding $padding $padding;
    }

    .menuSize-list{
      position: relative;
      overflow: hidden;

      h6{
        font-size: $p;
      }

      ul{
        overflow: hidden;
        font-size: $p;
        margin-bottom: .2rem;
        li{
          float: left;
          padding: .05rem .4rem;
          border: 1px solid $border;
          border-radius: 10px;
          margin: .1rem .2rem .1rem 0;
          &.on{
            border: 1px solid $green;
            color: $green;
          }
        }

      }
    }
    .menuSize-bottom{
      overflow: hidden;
      .goods-money{
        color: $red;
        font-size: $h6;
        line-height: .6rem;
      }
      .addCar-btn{
        padding: .1rem .2rem;
        border-radius: 15px;
        float: right;
      }
    }
  }
}

</style>
