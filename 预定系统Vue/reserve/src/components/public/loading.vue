<template>
  <div id="loading">
    <section class="loading" v-if="status">
      <div class="loading-cont"></div>
    </section>
  </div>

</template>

<script>
export default {
  name: 'loading',
  props: {
    status : {
      type : Boolean,
      default : false
    }
  },
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

.loading{
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  width: 100%;
  height: 100%;
  background: rgba(255,255,255,.5);
  z-index: 100;

  .loading-cont{
    width: .4rem;
    height: .4rem;
    background: url(../../assets/images/load.gif) no-repeat;
    background-size: 100% 100%;
    position: absolute;
    top: 50%;
    left: 50%;
    margin: -.2rem 0 0 -.2rem;
  }
}


</style>
