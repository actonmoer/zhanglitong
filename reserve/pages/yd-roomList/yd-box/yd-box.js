Component({
  properties: {
    // 这里定义了innerText属性，属性值可以在组件使用时指定
    setData: {
      type: Object,
      value: '',
      observer: function (newVal, oldVal){
        let s = this, roomtype = '';
        if (s.properties.setType == 'roomid'){
          roomtype = 'room';
          
        }else{
          roomtype = 'desk';
        }
        s.setData({
          cname: s.properties.setName,
          id: s.properties.setRid,
          time: s.properties.setTime,
          rtype: roomtype,
          name: s.properties.setCompany,
          reserve_price: s.properties.setData.reserve_price,
          type1: 1,
          commonid: s.properties.setCommonid
        });
      }
    },
    setType: {
      type: String,
      value: ''
    },
    setTime: {
      type: String,
      value: ''
    },
    setName: {
      type: String,
      value: ''
    },
    setRid: {
      type: String,
      value: ''
    },
    setCommonid:{
      type: String,
      value:''
    },
    setCompany: {
      type: String,
      value:''
    }
  },
  data: {
    isShowBox: false,
    id: '',
    type1: '',
    reserve_price: '',
    name: '',
    rtype: '',
    time: '',
    cname: '',
    commonid: ''
  },
  attached: function(){
    let o = {};
  },
  methods: {
    closeBox: function(e){
      let obj = {};
      obj.isShowBox = false;
      this.triggerEvent('myevent', obj);
    },
    ydroom: function(e){
      let s = this;
      wx.showToast({
        title: '预定成功后，如行程有变，请立即联系商家',
        icon:'none',
        duration:3000

      })

      setTimeout(function () {
        wx.hideLoading()
        wx.navigateTo({
          url: '../yd-menu/yd-menu?rid=' + s.data.id + '&type=' + s.data.type1 + '&reserve_price=' + s.data.reserve_price + '&time=' + s.data.time + '&name=' + s.data.name + '&rtype=' + s.data.rtype + '&cname=' + s.data.cname + '&commonid=' + s.data.commonid
        });
      }, 2000)
      
    }
  }
})