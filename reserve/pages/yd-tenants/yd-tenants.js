Page({
  //  页面的初始数据
  data: {
    businessname: '',    //商家名称
    hours: '',     //营业时间
    unsername: '',  //店主姓名
    id: '',     //身份证号
    storeurl :'../../images/upload.png',
    businessurl:'../../images/upload.png',
    licenseurl: '../../images/upload.png',
  },
  formSubmit: function (e) {
    Object.assign( this.data, e.detail.value);    
    
  },
  // 生命周期函数--监听页面加载
  onLoad: function (options) {
    wx.hideLoading();
    //设置当前页头
    wx.setNavigationBarTitle({
      title: '商家入驻'
    })
  },
  storeimg: function (ev){
    wx.chooseImage({   //调用相册与摄像头
      count:1,
      success:(res)=>{
        switch (ev.currentTarget.dataset.type) {
          case "storeimg":
            this.setData({   //店铺
              storeurl: res.tempFilePaths[0]
            })
            break;
          case "business":   //营业执照
            this.setData({
              businessurl: res.tempFilePaths[0]
            })
            break;
          default:    //许可证
            this.setData({
              licenseurl: res.tempFilePaths[0]
            })
        }    
      },
      fail:function(){
        console.log('授权失败');
      }
    })


  },
  // 用户点击右上角分享
  onShareAppMessage:function (res){
    

  }
})