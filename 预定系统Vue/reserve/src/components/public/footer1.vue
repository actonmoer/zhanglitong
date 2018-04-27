<template>
  <footer>
    <div>
      <a href="/" class="f-a">
        <i class="iconfont icon-n-foot1"></i>
        <p>首页</p>
      </a>
    </div>
    <div>
      <a href="/member/alert.php" class="f-a">
        <i class="iconfont icon-n-foot2" style="margin-top: .25rem;"></i>
        <p>匹配</p>
      </a>
    </div>
    <div>
      <a href="/mobile/chat.php" class="f-a">
        <i class="iconfont icon-n-foot3" style="margin-top: .25rem;"></i>
        <p>沟通</p>
      </a>
    </div>
    <div>
      <a href="/mobile/my.php" id="userCenter" class="f-a">
        <i class="iconfont icon-n-foot4" style="margin-top: .25rem;"></i>
        <p>我的</p>
      </a>
    </div>
    <div>
      <a href="/mobile/more.php" class="f-a">
        <i class="iconfont icon-n-foot5" style="margin-top: .25rem;"></i>
        <p>更多</p>
      </a>
    </div>
  </footer>
</template>

<script>
export default {
  name: 'footer1',
  data () {
    return {
    }
  },
  methods: {

  }

}
</script>

<style lang="scss">
  @import '../../assets/scss/com.scss';
  footer{
    position: fixed;
    bottom: 0;
    display: flex;
    display: -webkit-flex;
    width: 100%;
    background-color: #fdfdfd;
    height: 1rem;
    border-top: 1px solid #d3d3d3;
    z-index: 2;
  }
  footer div{
    flex: 1;
    -ms-flex: 1;
    -webkit-flex: 1;
    text-align: center;
  }
  footer>div>a img{
    margin-top: .375rem;
    margin-bottom: .125rem;
  }
  .f-a{
    width: 100%;
    display: block;
    color: #666;
    height: 1rem;
    position: relative;
  }
  .f-a p{
    font-size: .24rem;
  }
  footer>div>a i.tips{
    background-color: red;
    position: absolute;
    width: .5rem;
    height: .5rem;
    border-radius: 100%;
    top: .3125rem;
    left: 50%;
    margin-left: .25rem;
  }
  footer>div>a i.iconfont{
    font-size: .44rem;
  }

</style>
