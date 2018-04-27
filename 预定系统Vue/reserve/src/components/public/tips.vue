<template>
  <div id="tips">
    <section class="tips" v-if="tipsStatus">
      <div class="tips-cont">{{ text }}</div>
    </section>
  </div>

</template>

<script>
export default {
  name: 'tips',
  props: {
    tipsStatus : {
      type : Boolean,
      default : false
    },
    text : {
      type : String
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

.tips{
  position: fixed;
  top: 50%;
  left: 50%;
  z-index: 100;

  .tips-cont{
    background: rgba(0,0,0,.7);
    border-radius: 5px;
    padding: .2rem .4rem;
    font-size: $p;
    color: #fff;
    transform: translate(-50%, -50%);
    -moz-transform: translate(-50%, -50%);
    -o-transform: translate(-50%, -50%);
    -webkit-transform: translate(-50%, -50%);


    
  }
}


</style>
