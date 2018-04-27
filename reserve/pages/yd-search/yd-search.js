Page({
  data: {
    inputValue: '',
    shopList: '',
    businessList: '',
    isEmpty : false
  },

  onLoad: function (options) {
    wx.hideLoading();
    let business = wx.getStorageSync('resver'),
      businessArr = []
    for (let i = 0; i < 10;i++){
      businessArr[i] = business[i]
    }
    this.setData({
      businessList: businessArr
    })
  },

  changeInput: function (e) {
    wx.showLoading({
      title: '搜索中',
    })

    this.search(e.detail.value);
    
  },

  //搜索商家或商品
  search: function (keyword) {
    let that = this;
    //搜索请求
    wx.request({
      url: 'https://www.zhanglitong.com/mobile/reserve_search.php',
      data: {
        keyword: keyword
      },
      method: "POST",
      header: {
        'content-type': 'application/x-www-form-urlencoded'
      },
      success: function (res) {
       
        if (res.data.code == 200) {
          that.setData({
            shopList: res.data.data,
            isEmpty: false
          })
          wx.hideLoading()
          console.log(res.data.data)
        }
        else if (res.data.code == -200){
          that.setData({
            shopList: '',
            isEmpty : true
          })
        }
      },
      fail: function (res) {
        wx.hideLoading()
        console.log(res.data)
      }
    });
  },
  businessDetail: function (event) {
    wx.navigateTo({
      url: '../yd-roomList/yd-roomList?id=' + event.currentTarget.dataset.id + '&reserve_price=' + event.currentTarget.dataset.reserveprice
    });
  },

  // 用户点击右上角分享
  onShareAppMessage: function (res) {
    
  }
})