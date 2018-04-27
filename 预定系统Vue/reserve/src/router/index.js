import Vue from 'vue'
import Router from 'vue-router'
import index from '@/components/index'
import roomList from '@/components/roomList'
import hotelMenu from '@/components/menu'
import reserve from '@/components/reserve'
import orderList from '@/components/orderList'
import orderDetail from '@/components/orderDetail'


Vue.use(Router)

export default new Router({
  routes: [
    {
      path: '/',
      name: 'index',
      component: index
    },
    {
      path: '/roomList/',
      name: 'roomList',
      component: roomList
    },
    {
      path: '/menu/',
      name: 'hotelMenu',
      component: hotelMenu
    },
    {
      path: '/reserve/',
      name: 'reserve',
      component: reserve
    },
    {
      path: '/orderList/',
      name: 'orderList',
      component: orderList
    },
    {
      path: '/orderDetail/',
      name: 'orderDetail',
      component: orderDetail
    }
  ]
})
