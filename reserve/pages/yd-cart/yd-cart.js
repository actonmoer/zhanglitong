let commonjs = require("../../utils/util.js");
Page({
  data: {
  
  },

  
  // 监听页面加载
  onLoad: function (options) {
    wx.hideLoading();
  },

  // 用户点击右上角分享
  onShareAppMessage: function (res) {
    return commonjs.onShareAppMessage(res)
  }
})