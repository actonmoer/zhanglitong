let distance1 = 0;
let distance2 = 0;
let latitude = '';
let longitude = '';
Page({
  data: {
    invitData: {},
    markers: [{
      iconPath: "/resources/others.png",
      id: 0,
      latitude: latitude,
      longitude: longitude,
      width: 50,
      height: 50
    }],
    polyline: [{
      points: [{
        longitude: 113.3245211,
        latitude: 23.10229
      }, {
        longitude: 113.324520,
        latitude: 23.21229
      }],
      color: "#FF00DD",
      width: 2,
      dottedLine: true
    }],

    
    distance: [0,0],
    num: 1,
    rid: '',
    reservePrice: ''
  },
  tel:function(){
    console.log();
    if (this.data.invitData.mobile){
      wx.makePhoneCall({
        phoneNumber: this.data.invitData.mobile //仅为示例，并非真实的电话号码
      })
    }
   
  }, 
  tels: function () {
    console.log();
    if (this.data.invitData.company_telephone) {
      wx.makePhoneCall({
        phoneNumber: this.data.invitData.company_telephone //仅为示例，并非真实的电话号码
      })
    }

  },
  clickMap: function(){
    let that = this;
    wx.getLocation({
      type: 'gcj02', //返回可以用于wx.openLocation的经纬度
      success: function (res) {
        var latitude = parseFloat(that.data.invitData.lat);
        var longitude = parseFloat(that.data.invitData.lng);
        console.log(latitude)
        console.log(longitude)
        console.log(res)
        wx.openLocation({
          latitude: latitude,
          longitude: longitude,
          scale: 28,
          name: that.data.invitData.contact_name,
          address: that.data.invitData.company_address
        })
      }
    })
  },
  movestart: function(e){
    let s = this;
    distance1 = e.touches[0].clientY
    s.setData({
      distance: [distance1, 0]
    });
  },
  moveend: function(e){
    let s = this;
    distance2 = e.changedTouches[0].clientY
    s.setData({
      distance: [distance1, distance2]
    });
    let distanceXY = s.data.distance[1] - s.data.distance[0]
    if (distanceXY < 0){
      if (s.data.num == 1){
        s.setData({
          num: 2
        });
      } else if (s.data.num == 2){
        s.setData({
          num: 3
        });
      }
    } else if (distanceXY > 0){
      if (s.data.num == 3) {
        s.setData({
          num: 2
        });
      } else if (s.data.num == 2) {
        s.setData({
          num: 1
        });
      }
    }
  },

  // 监听页面加载
  onLoad: function (options) {
    console.log(options);
    let s = this;
    s.setData({
      rid: options.rid,
      reservePrice: options.reserve_price
    })
    wx.hideLoading();
    options.order_id
    wx.request({
      url: 'https://www.zhanglitong.com/mobile/ajax_reserve.php?for_ajax=get_invitation',
      method: 'POST',
      data: { order_id: options.order_id},
      success: function(data){
        console.log("邀请函")
        
        if(data.data.code == 200){
          s.setData({
            invitData: data.data.data,
          });
          s.setData({
            markers: [{
              iconPath: "/resources/others.png",
              id: 0,
              latitude: s.data.invitData.lat,
              longitude: s.data.invitData.lng,
              width: 50,
              height: 50
            }],
          });

          console.log(s.data.markers)
          
        }
      },
      fail: function(){
        console.log("获取不到信息");
      }
    })
  },

  //查看商家
  lookB : function(event){
    let that = this,
        data = that.data;
    wx.navigateTo({
      url: '../yd-roomList/yd-roomList?id=' + data.rid + '&reserve_price=' + data.reservePrice + '&invitation=1'
    })
  },

  // 用户点击右上角分享
  onShareAppMessage: function (res) {
    
  }
})