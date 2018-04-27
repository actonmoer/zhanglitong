<template>
  <div id="app">
    <XHeader></XHeader>
    <transition 
      :name="transitionName"
    > 
        
      <keep-alive>
        <router-view class="child-view"></router-view>
      </keep-alive>
    </transition> 

    <order></order>
  </div>

</template>

<script>
// import {Util} from './assets/js/util.js'
import {XHeader} from 'vux'
import order from '@/components/public/order'
// var util = new Util();

export default {
  name: 'app',
  data () {
    return {
      transitionName: "slide-right",
      isBack: false
    }
  },
  components: {
    XHeader,
    order
    
  },
  methods: {
    swiped () {
      // 如果isBack为true时，证明是用户点击了回退，执行slide-right动画
      
      if (this.isBack) {
          this.transitionName = 'slide-right'
      } else {
          this.transitionName = 'slide-left'
      }


    },
  },

  beforeCreate () {
    let self  = this;
    // //根元素的字体缩放函数  
    function setRootFontSize () {
        let ww = window.innerWidth,
            hh = window.innerHeight,
            $html = document.getElementsByTagName('html')[0];
            
        const MIN = 320,
              MAX = 750;
            

        if(ww < MIN) ww = MIN;
        if(ww > MAX) ww = MAX;

       
        $html.style.dispaly = "block";
        $html.style.fontSize = (ww * 100 / MAX) + "px";
        document.addEventListener('mousedown', (e) => {
            var target = e.target;
            while(target.tagName !== 'A'){
              target = target.parentNode;
              if(target === document){
                target = null
                break;
              }
            }
            if(target && target.getAttribute("href")) {
              self.isBack = true;
              setTimeout(function(){
                self.isBack = false;
              },1000)
            }
            self.swiped();
        })
        // util.setLock()
    };
   

    window.onresize = setRootFontSize;

    setRootFontSize();

  }
}
</script>

<style lang="scss">
@import 'assets/scss/com.scss';
/*背景色*/
html,body {
  background:#fff;
}
html{
  font-size: 50px;
}

/*css 重置*/
body,div,dl,dt,dd,ul,ol,li,h1,h2,h3,h4,h5,h6,form,fieldset,input,button,textarea,p,th,td {
  padding:0;
  margin:0;
  font-weight: normal;
}
fieldset,img {
  border:0;
}
em,th,optgroup {
  font-style:normal;
  font-weight:normal;
}
h1,h2,h3,h4,h5,h6 {
  font-size:100%;
}
input,button,textarea,select,optgroup,option {
  font-family:inherit;
  font-size:inherit;
  font-style:inherit;
  font-weight:inherit;
}
ol,ul,li {
  list-style:none;
}
table {
  border-collapse:collapse;
  border-spacing:0;
}
body {
  font-size:16px;
  font-family:"Hiragino Sans GB",Helvetica,"Microsoft YaHei",Simsun,"Droid Sans Fallback",sans-serif;
  -webkit-tap-highlight-color:rgba(0,0,0,0);
}
[hidden] {
  display:none;
}
/*响应式图片*/
img {
  width:100%;
}
input, textarea {
  outline: none;
  -webkit-appearance:none;
}
/*j_wrapper*/
#app {
  position:relative;
  width:100%;
  min-width:320px;
  max-width:750px;
  margin:0 auto .4rem auto;
  overflow: hidden;
  .amap-copyright, .amap-logo{
    z-index: 1;
  }
  .weui-cells{
    font-size: $title;
  }
  input{
    font-size: $title;
  }
}
#app img {
  width:100%;
  vertical-align:top;
}
#app a {
  text-decoration: none;
}
#app .hide {
  display: none
}


.btn{
  color: #fff;
  background: $green;
  padding: .1rem $padding;
  border: 0;
  text-align: center;
  border-radius: 5px;
  font-size: $fontsize;
  &.disabled{
    background: #ccc;
  }
}

.leaveActiveClass,
.enterActiveClass{
  position: fixed;
  width: 7.5rem;
  top: 0;
  transition: transform .5s cubic-bezier(.42,.4,.14,1.24);
}

.enterActiveClass{
  transform: translate(0, 0);
}

.enterClass{
  transform: translate(100%, 0);
}

.leaveClass{
  transform: translate(0, 0);
}


.leaveActiveClass{
  transform: translate(-100%, 0);
}


.leaveActiveClass2,
.enterActiveClass2{
  transition: opacity .5s cubic-bezier(0,.21,0,1.04);
}

.enterActiveClass2{
  opacity: 1;
}
.leaveActiveClass2{
  opacity: 0;
}

.enterClass2{
  opacity: 0;
}

.leaveClass2{
  opacity: 1;
}

.leaveActiveClass3,
.enterActiveClass3{
  position: fixed;
  width: 7.5rem;
  top: 0;
  transition: transform .5s cubic-bezier(.42,.4,.14,1.24);
}

.enterActiveClass3{
  transform: translate(0, 0);
}

.enterClass3{
  transform: translate(0, 100%);
}

.leaveClass3{
  transform: translate(0, 0);
}


.leaveActiveClass3{
  transform: translate(0, 100%);
}


.orange{
  color: $orange;
}
#app .vux-header{
  position: fixed;
  width: 100%;
  left: 0;
  z-index: 40;
  top: 0;
  background: $green;

  .vux-header-left a{
    color: #fff;
  }
  .left-arrow:before{
    
    border-top: 1px solid #fff;
    border-left: 1px solid #fff;
  }
}

.headBox{
  position: relative;
  height: 46px;
  width: 100%;
}

.child-view {
  position: relative;
  width:100%;
  transition: all 0.65s cubic-bezier(0,1,.4,1);
}
#app .slide-left-enter, #app .slide-right-leave-active {
  position: fixed;
  -webkit-transform: translate(-100%, 0);
  transform: translate(-100%, 0);
  opacity: 0;
  .vux-header{
    position: absolute;
    top: -46px;
  }

}
#app .slide-left-leave-active, #app .slide-right-enter {
  position: fixed;
  -webkit-transform: translate(100%, 0);
  transform: translate(100%, 0);
  opacity: 0;
  .vux-header{
      position: absolute;
      top: -46px;
  }

}

</style>
