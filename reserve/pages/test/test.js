// pages/test/test.js
let commonjs = require("../../utils/util.js");
Page({

  /**
   * 页面的初始数据
   */
  data: {
    arrIndex: [
      { id: 1, name: '辣1' },
      { id: 2, name: '辣2' },
      { id: 3, name: '辣3' },
      { id: 4, name: '辣4' }
    ]
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    wx.hideLoading();
  },
  select: function(){
    wx.chooseAddress({
      success:function(res){
        console.log(res);
      }
    });
  },
  /**
   * 生命周期函数--监听页面初次渲染完成
   */
  onReady: function () {
  
  },

  /**
   * 生命周期函数--监听页面显示
   */
  onShow: function () {
  
  },

  /**
   * 生命周期函数--监听页面隐藏
   */
  onHide: function () {
  
  },

  /**
   * 生命周期函数--监听页面卸载
   */
  onUnload: function () {
  
  },

  /**
   * 页面相关事件处理函数--监听用户下拉动作
   */
  onPullDownRefresh: function () {
  
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
    return commonjs.onShareAppMessage(res)
  }
})