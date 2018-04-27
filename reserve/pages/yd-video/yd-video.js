Page({
  data: {
    url: ''
  },

  // 监听页面加载
  onLoad: function (options) {
    // this.loadVideo();
    console.log(options.url);
    this.setData({
      url: options.url
    });
  },

  //加载视频
  loadVideo: function (event) {
    wx.request({
      url: 'https://www.zhanglitong.com/mobile/show_video.php',
      data: {
        itemid: 155
      },
      method: "POST",
      header: {
        'content-type': 'application/x-www-form-urlencoded'
      },
      success: function (res) {
        console.log(res.data);
      },
      fail: function (res) {
        console.log(res.data)
      }
    });
  },

  // 用户点击右上角分享
  onShareAppMessage: function (res) {
    
  }
})