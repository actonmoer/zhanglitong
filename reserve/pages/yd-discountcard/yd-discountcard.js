let u = require('../../utils/util.js');

Page({

  /**
   * 页面的初始数据
   */
  data: {
    UserId:'',
    refereeUserId: '',
    isShow: false
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    this.setData({
      refereeUserId: options.userid
    });
    wx.hideLoading();
    //表示允许转发时是否携带 shareTicket
    wx.showShareMenu({
      withShareTicket: true
    });
  },

  /**
   * 页面上拉触底事件的处理函数
   */
  onReachBottom: function () {
  
  },

  /**
   * 用户点击右上角分享
   */
  onShareAppMessage: function (res) {
    
  },
  shareCard: function(e){
    let m = e.currentTarget.dataset.money,
    s = this;
    wx.request({
      url: 'https://www.zhanglitong.com/wechatapp/pay_charge/payfee.php',
      method: 'POST',
      data: { 
        session: wx.getStorageSync('3r_session'),
        referee: s.data.refereeUserId,
        money: m
      },
      success: function(res){
        console.log(res.data);
        if(res.data.code == 200){
          wx.requestPayment({
            timeStamp: res.data.timeStamp,
            nonceStr: res.data.nonceStr,
            package: res.data.package,
            signType: res.data.signType,
            paySign: res.data.paySign,
            complete: function (d) {
              if (d.errMsg == 'requestPayment:ok') {
                wx.showToast({
                  title: '成功购买' + m + '元打折卡',
                  icon: 'success',
                  duration: 2000
                });
              }
            }
          });
        } else if (res.data.code == -102){
          s._model(res.data.msg)
        } else if (res.data.code == -103){
          s._model(res.data.msg)
        }
        
      }
    })
    
  },

  //模态框
  _model: function(msg){
    wx.showModal({
      title: '提示',
      content: msg,
      showCancel: false,
    })
  },

  ydclose: function(){
    this.setData({
      isShow: false
    });
  },
  openTip: function(){
    this.setData({
      isShow: true
    });
  }
})