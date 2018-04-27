<template>
  <div class="index">
    <div class="search">
      <div class="search-box">
        <div class="dz-icon"></div>
        <p class="my-address">{{myAddress || "无法定位"}}</p>
        <form class="form" action="javascirpt:return;">
          <input type="search" name="search" class="input-search" placeholder="搜索商家、商品" @keyup.enter="search" v-model="keyword" ref="search">
        </form>
      </div>
    </div>
    <fillerBar :nav="nav" @on-loading="change" @setList="getHotel"></fillerBar>
    <hotelList :list="list"></hotelList>
    <loading :status="status"></loading>

  </div>
</template>

<script>
import geolocation from '@/assets/js/geolocation.js'
import fillerBar from '@/components/public/nav'
import hotelList from '@/components/public/list'
import loading from '@/components/public/loading'
import {getList, getArea, invite, giveFood} from '@/model/getData'

export default {
  name: 'index',
  data () {
    return {
      status : false,
      myAddress : "无法定位",
      catid : "",
      keyword: "",
      lat : 0,
      lng : 0,
      distance_sort : 0,
      sell_sort : 0,
      list : [],
      nav  : [],
      give : "",
    }
  },
  methods: {
    change (status) {
      this.status = status;
    },
    async getHotel (opt) {

      opt = opt || {};
      this.status = true;
      if (opt.sort == "distance_sort") {
        this.distance_sort = 1;
        this.sell_sort     = 0;
      }else if(opt.sort == "sell_sort"){
        this.distance_sort = 0;
        this.sell_sort     = 1;
      }

      this.catid  = opt.catid;
      this.areaid = opt.areaid;
      this.list = await getList({
        lng           : this.lng,
        lat           : this.lat,
        catid         : this.catid,
        areaid        : this.areaid,
        distance_sort : this.distance_sort,
        sell_sort     : this.sell_sort,
        keyword       : this.keyword
      });
      this.status = false;

      if(JSON.stringify(opt) != '{}' && !isNaN(opt) ){
        this._chooseCuisine(opt)
      }
    },

    //选择菜式
    _chooseCuisine(catid) {
      let that = this,
        listArr = [],
        param = new URLSearchParams()
      param.append('catid', catid)
      that.$http.post('https://www.zhanglitong.com/mobile/reserve_category.php', param, {
        headers: {
          'Access-Control-Allow-Origin': '*'
        }
      })
        .then(function (res) {
          if(res.data.code == 200){
            for(let i=0,iLength=that.list.length;i<iLength;i++){
              for(let j=0,jLength=res.data.data.length;j<jLength;j++){
                if(that.list[i].username == res.data.data[j]){
                  listArr.push(that.list[i])
                }
              }
            }
          }
          that.list = listArr
        })
        .catch(function (error) {
          console.log(error);
        });
    },

    search () {
      this.$refs.search.blur();
      this.getHotel();
    }
  },
  components: {
    fillerBar,
    hotelList,
    loading,
  },
  created () {
    // getArea({
    //   areaid : 0
    // })
    // invite({
    //   order_id: "2017062962541"
    // });
    let geolocation = new qq.maps.Geolocation("T7WBZ-PJF3O-GHUWN-S6KOG-UUPGQ-PFBSZ","myapp"),
        self = this;
    this.status = true;
    let promise = new Promise((resolve, reject) => {
      geolocation.getLocation((res) => {
        resolve(res);
        self.lng = res.lng;
        self.lat = res.lat;

        self.myAddress = res.addr;
      }, (res) => {
        resolve();
        self.lng = 0;
        self.lat = 0;
        // alert("请允许定位服务");
      });

    });
    promise.then((res) => {
      this.getHotel();
    });
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style lang="scss" scoped>
@import '../assets/scss/com.scss';
.index{
  z-index: 90;
  background: #fff;
}
.search{
  width: 100%;
  background: $green;
  position: relative;
  z-index: 80;

  .search-box{
    position: relative;
    overflow: hidden;
    .input-search{
      display: block;
      margin: .2rem 0;
      width: 6rem;
      height: .7rem;
      text-align: center;
      border-radius: .96rem;
      color: $color1;
      font-size: $fontsize;
      border: 1px solid $border;
      margin: .1rem auto .3rem auto;
      outline: 0;
    }
  }

  .dz-icon{
    width: .36rem;
    height: .36rem;
    background: url(../assets/images/dz.png) no-repeat;
    background-size: 100% 100%;
    margin: .14rem 0 0 .6rem;
    float: left;
  }

  .my-address{
    text-align: left;
    font-size: 0.28rem;
    color: #fff;
    text-indent: .1rem;
    margin: .1rem 0 0 0;
  }
}

.foodentry{
  position: relative;
  margin: .1rem 0;
  .foodentry-box{
    overflow: hidden;
    .foodentry-list{
      float: left;
      width: 25%;
      margin: .1rem 0 0 0;
      .foodentry-img{
        width: .8rem;
        margin: 0 auto;
      }
      p{
        margin: .2rem auto;
        text-align: center;
        font-size: $p;
        color: $color2;
      }
    }
  }
}
</style>
