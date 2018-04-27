<template>
  <div class="order">
    <router-link class='order-box' :to="{path:'/orderList'}"></router-link>
  </div>

</template>

<script>
export default {
  name: 'order',
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

.order{
  position: fixed;

  right: .3rem;
  top: .2rem;
  width: 0.8rem;
  height: 0.8rem;
  z-index: 90;
  background: url(../../assets/images/order-icon.png) no-repeat #fff center;
  background-size: 60% 60%;
  border-radius: 50%;
  border: 1px solid $border;
  .order-box{
      width: 100%;
      height: 100%;
      display: block;
  }
}


</style>
