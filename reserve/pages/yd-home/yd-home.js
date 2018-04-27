let appInstance = getApp();

Page({
  data: {
    userInfo: '',
    balance: '',
    isBind: ''
  },

  //监听页面加载
  onLoad: function (options) {
    wx.hideLoading();
    this.setData({
      userInfo: wx.getStorageSync('userInfo')
    });

    this.bindPhone();
    if (this.data.isBind) {
      this.balance();
    }
  },
  clear: function () {
    let s = this;
    if (s.data.userInfo) {
      wx.removeStorageSync('3r_session');
      wx.removeStorageSync('userInfo');
      wx.showToast({
        title: '退出成功',
        icon: 'success'
      });
      s.setData({
        userInfo: ''
      });
    } else {
      wx.showToast({
        title: '正在登录中',
        icon: 'success'
      });
      wx.openSetting({
        success: function (d) {
          console.log(d);
          wx.showToast({
            title: '登录成功',
            icon: 'success'
          });
          appInstance.globalData.login(function (d) {

            wx.getStorage({
              key: 'userInfo',
              success: function (res) {
                s.setData({
                  userInfo: res.data
                });
              }
            });
            if (d == 'notLogin') {
              appInstance.globalData.login();
            }
          });
        },
        fail: function () {
          wx.showToast({
            title: '登录失败',
            icon: 'success'
          });
        }
      });

    }
  },

  //加载余额
  balance: function () {
    let that = this,
      session = wx.getStorageSync('3r_session');
    wx.request({
      url: 'https://www.zhanglitong.com/wechatapp/app_wallet.php',
      data: {
        "action": "show_money",
        "3r_session": session
      },
      method: "POST",
      header: {
        'content-type': 'application/x-www-form-urlencoded'
      },
      success: function (res) {
        if (res.data.code == 200) {
          that.setData({
            balance: res.data.data
          })
          console.log(res.data.data)
        } else {
          wx.showModal({
            title: '提示',
            content: '获取金额失败',
            showCancel: false,
            success: function (res) {
              if (res.confirm) {
                console.log('用户点击确定')
              }
            }
          })
        }
      },
      fail: function (res) {
        console.log(res.data)
      }
    });
  },

  //判断是否已经绑定手机号
  bindPhone: function () {
    let that = this,
      session = wx.getStorageSync('3r_session');
    wx.request({
      url: 'https://www.zhanglitong.com/wechatapp/app_wallet.php',
      data: {
        "action": "show_phone",
        "3r_session": session
      },
      method: "POST",
      header: {
        'content-type': 'application/x-www-form-urlencoded'
      },
      success: function (res) {
        console.log(res.data)
        if (res.data.data) {
          that.setData({
            isBind: true
          })
        } else {
          that.setData({
            isBind: false
          })
        }
      },
      fail: function (res) {
        console.log(res.data)
        wx.showModal({
          title: '提示',
          content: res.data,
          success: function (res) {
            if (res.confirm) {
              console.log('用户点击确定')
            } else if (res.cancel) {
              console.log('用户点击取消')
            }
          }
        })
      }
    })
  },

  //拨打电话
  call: function (event) {
    wx.makePhoneCall({
      phoneNumber: '020-36910000'
    })
  },

  //余额充值
  recharge: function (event) {
    wx.switchTab({
      url: '../yd-discountcard/yd-discountcard',
    })
  },

  //获取手机号码
  getPhoneNumber: function (e) {
    let that = this,
      iv = e.detail.iv,
      encryptedData = e.detail.encryptedData,
      session = wx.getStorageSync('3r_session');
    wx.checkSession({
      success: function () {
        wx.request({
          url: 'https://www.zhanglitong.com/wechatapp/get_phone.php',
          data: {
            iv: iv,
            encryptedData: encryptedData,
            "3r_session": session
          },
          method: 'POST',
          header: {
            'content-type': 'application/josn'
          },
          success: function (res) {
            console.log(res.data);
            that.setData({
              isBind: true
            })
            that.balance()
          },
          fail: function (res) {
            console.log("网络错误，请稍后再试！")
          }
        })
      },
      fail: function () {
        console.log("失败");
      }
    })
  },

  // 用户点击右上角分享
  onShareAppMessage: function (res) {

  }
})