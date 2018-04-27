<template>
  <div class="filler">
    <div class="fillerNaN j-fillerNaN"></div>

    <div class="filler-box" @click.stop="resetfiller" v-on:touchmove.prevent="stop"
         :class="[{on: filler.status },{fixed: filler.fixed }]">

      <div class="filler-title">
        <div class="filler-list" :class="{on: food.status }" @click.stop="openFood">
          <a href="javascript:;">{{food.title}}</a>
        </div>
        <div class="filler-list" :class="{on: sort.status }" @click.stop="openSort">
          <a href="javascript:;">{{sort.title}}</a>
        </div>
        <div class="filler-list" :class="{on: area.status }" @click.stop="openArea">
          <a href="javascript:;">{{area.title}}</a>
        </div>
      </div>

      <transition name="fade">
        <div class="filler-cont" v-if="food.status">
          <div class="filler-level-2" ref="level1">
            <div class="filler-hidden">
              <div class="filler-level-2-list" v-for="(i, index) in food.cont">
                <a href="javascript:;" @click.stop="selectFood(i.catid)">{{ i.catname }}</a>
              </div>
            </div>
          </div>
        </div>
      </transition>

      <transition name="fade">
        <div class="filler-cont-2 filler-cont" v-if="sort.status">
          <div class="filler-level-2">
            <div class="filler-hidden">
              <div class="filler-level-2-list" v-for="(i, index) in sort.cont">
                <a href="javascript:;" @click.stop="selectSort(index)">{{ i.name }}</a>
              </div>
            </div>
          </div>
        </div>
      </transition>

      <transition name="fade">
        <div class="filler-cont" v-if="area.status">
          <div class="filler-level-1" ref="level2">
            <div class="filler-hidden">
              <div class="filler-level-1-list" v-for="(i, index) in area.cont">
                <a href="javascript:;" @click.stop="selectArea(i.areaid)">{{ i.areaname }}</a>
              </div>
            </div>
          </div>

          <div class="filler-level-3" ref="level3">
            <div class="filler-hidden">
              <div class="filler-level-2-list" v-for="i in cont.level2">
                <a href="javascript:;" @click.stop="selectLevel(i.areaid)">{{ i.areaname }}</a>
              </div>
            </div>
          </div>
        </div>
      </transition>
    </div>

  </div>
</template>


<script>
  import BScroll from 'better-scroll'
  import {getNav, getArea} from '@/model/getData'

  export default {
    name: 'filler',
    props: {
      // nav: Array
      distance_sort: String,
      sell_sort: String
    },
    data() {
      return {
        nav: [],
        areaid: 0,

        filler: {
          status: false,
          fixed: false
        },
        food: {
          status: false,
          title: "分类",
          select: 0,
          cont: []

        },
        sort: {
          status: false,
          title: "排序",
          cont: [{
            name: "按距离排序",
            val: 1
          },
            {
              name: "按销量排序",
              val: 1
            },
            {
              name: "按价格排序",
              val: 1
            },
            {
              name: "按好评排序",
              val: 1
            }]
        },
        area: {
          status: false,
          title: "地区",
          cont: []
        },

        cont: {
          status: false,
          level1: [],
          level2: []
        },

        data: []
      }
    },
    beforeCreate() {
      let self = this;

      function fillerFixed() {
        var winTop = document.body.scrollTop || document.documentElement.scrollTop,
          e = document.getElementsByClassName('j-fillerNaN')[0],
          fillerTop = e ? e.offsetTop : 0;
        if (e) {
          while (e = e.offsetParent) {
            fillerTop += e.offsetTop;
          }
          if (winTop >= fillerTop) {
            self.filler.fixed = true;
          } else {
            self.filler.fixed = false;
          }
        }
      };


      window.onscroll = fillerFixed;


    },
    methods: {
      async openFood() {
        let self = this;
        if (!this.food.status) {
          this.$emit('on-loading', true);
          this.nav = await getNav();
          this.resetfiller();
          for (let i in this.nav) {
            this.food.cont.push(this.nav[i]);
          }

          this.food.status = true;
          // this.cont.status = true;
          this.openfiller();
          setTimeout(function () {
            new BScroll(self.$refs.level1, {
              click: true
            });
          }, 300);
          this.$emit('on-loading', false);
        } else {
          this.resetfiller();
        }
      },

      openSort() {
        if (!this.sort.status) {
          this.resetfiller();
          this.sort.status = true;
          this.cont.status = true;
          this.openfiller();
        } else {
          this.resetfiller();
        }
      },
      async openArea() {
        let self = this;
        if (!this.area.status) {
          this.$emit('on-loading', true);
          this.area.cont = await getArea(this.areaid);
          this.resetfiller();
          this.area.status = true;
          this.cont.status = true;
          this.openfiller();
          setTimeout(function () {
            new BScroll(self.$refs.level2, {
              click: true
            });
          }, 300);
          this.$emit('on-loading', false);
        } else {
          this.resetfiller();
        }

      },
      fillerStatus() {
        this.cont.status = this.cont.status ? false : true;
        this.cont.fixed = this.cont.fixed ? false : true;
        // this.filler.status = this.filler.status ? false : true;
      },
      resetfiller() {
        this.cont.level1 = [];
        this.cont.level2 = [];
        this.food.status = false;
        this.sort.status = false;
        this.area.status = false;
        this.filler.status = false;
        this.cont.status = false;

        var winTop = document.body.scrollTop,
          e = document.getElementsByClassName('j-fillerNaN')[0],
          fillerTop = e.offsetTop;

        if (!e) return;

        while (e = e.offsetParent) {
          fillerTop += e.offsetTop;
        }

        if (winTop >= fillerTop) {
          this.filler.fixed = true;
        } else {
          this.filler.fixed = false;
        }
      },

      openfiller() {
        this.filler.status = true;
        this.filler.fixed = true;
        this.cont.status = true;
      },

      stop() {

      },

      async selectLevel(catid) {
        this.resetfiller();
      },
      selectFood(catid) {
        this.$emit('setList', catid);
        this.resetfiller();
        this.$emit('on-loading', true);
      },

      selectSort(index) {
        let self = this;
        if (index == 0) {
          this.$emit('setList', 'distance_sort');

        } else {
          this.$emit('setList', 'sell_sort');
        }
        this.resetfiller();
        setTimeout(function () {
          new BScroll(self.$refs.level2, {
            click: true
          });
        }, 300);
        this.$emit('on-loading', true);
      },
      async selectArea(areaid) {
        let self = this;
        this.$emit('on-loading', true);
        this.cont.level2 = await getArea(areaid);

        this.$emit('on-loading', false);
        setTimeout(function () {
          new BScroll(self.$refs.level3, {
            click: true
          });
        }, 300);
      }
    }
  }
</script>

<style lang="scss" scoped>
  @import '../../assets/scss/com.scss';

  .filler {
    position: relative;

    .filler-box {
      position: absolute;
      top: 0;
      width: 7.5rem;
      z-index: 99;
      top: 0;
      &.on {
        bottom: 0;
        background: rgba(0, 0, 0, .5);
      }
      &.fixed {
        position: fixed;
      }
      .filler-hidden {
        overflow: hidden;
      }
    }

    .fillerNaN {
      position: relative;
      height: .8rem;
      width: 100%;
    }
    .filler-title {
      height: .8rem;
      width: 100%;
      overflow: hidden;
      position: relative;
      border-top: 1px solid $border;
      border-bottom: 1px solid $border;
      background: #fff;
      .filler-list {
        float: left;
        width: 33.3333%;
        height: 100%;
        position: relative;
        a {
          display: block;
          text-align: center;
          line-height: .8rem;
          font-size: $fontsize;
          color: $color1;
        }

        &.on {
          a {
            color: $green;
          }
        }
      }

    }

    .filler-list:nth-of-type(1),
    .filler-list:nth-of-type(2) {
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

    .filler-cont {
      background: #f2f2f2;
      overflow: hidden;
      height: 6rem;
      width: 100%;
      &.filler-cont-2 {
        height: 3.6rem;
      }
      .filler-level-1 {
        width: 50%;
        height: 100%;
        float: left;
        overflow: hidden;
        .filler-level-1-list {
          box-sizing: inherit;
          -webkit-tap-highlight-color: transparent;
          -webkit-overflow-scrolling: touch;
          width: 100%;
          float: left;

          line-height: .9rem;
          a {
            color: $color2;
            font-size: $fontsize;
            display: block;
            padding: 0 .13rem 0 .26rem;
            &.select {
              background: #fff;
            }
          }

        }
      }

      .filler-level-2 {
        /*overflow-y: scroll;*/
        overflow: hidden;
        box-sizing: inherit;
        -webkit-tap-highlight-color: transparent;
        -webkit-overflow-scrolling: touch;
        width: 100%;
        height: 100%;
        float: left;
        background: #fff;
        .filler-level-2-list {
          width: 100%;
          float: left;
          padding: 0 .13rem 0 .26rem;
          line-height: .9rem;

          a {
            color: $color2;
            font-size: $fontsize;
            display: block;
            width: 100%;
            height: 100%;
          }

        }
      }

      .filler-level-3 {
        overflow-y: scroll;
        overflow-x: hidden;
        box-sizing: inherit;
        -webkit-tap-highlight-color: transparent;
        -webkit-overflow-scrolling: touch;
        width: 50%;
        height: 100%;
        float: left;
        background: #fff;
        .filler-level-2-list {
          width: 100%;
          float: left;
          padding: 0 .13rem 0 .26rem;
          line-height: .9rem;

          a {
            color: $color2;
            font-size: $fontsize;
            display: block;
            width: 100%;
            height: 100%;
          }

        }
      }
    }
  }

  .fade-enter {
    opacity: 0;
  }

  .fade-enter-active {
    transition: opacity .3s;
  }

</style>
