var u = require('../../utils/util.js');
var d = require('yd-date.js');
let arrValue = [0, 0, 0, 0, 0];
let arrimg= [];
Page({
  data: {
    time: '',
    date: '',
    roomMsg: {},
    dateArrY: [d.year, d.checkMM(u.dateY()), d.checkDD(u.dateM()), d.checkHH(u.dateD()), d.checkmms(u.dateH())],
    dateYindex: [0, 0, 0, 0, 0],
    isShow: 0,
    rid: '',
    address: '',
    roomList: {},
    roomDesk: {},
    imgUrls: [
      'http://img02.tooopen.com/images/20150928/tooopen_sy_143912755726.jpg',
      'http://img06.tooopen.com/images/20160818/tooopen_sy_175866434296.jpg',
      'http://img06.tooopen.com/images/20160818/tooopen_sy_175833047715.jpg'
    ],
    isShowBox: false,
    roomType: '',
    reserveprice: '',
    deskid: '',
    deskname: '',
    cartStorages : '',
    phoneNumber:'',
    address: '',
    company: '',
    lat: '',
    lng: '',
    noPicture: false
  },
  bindIndexChange: function (e) {
    let s = this;
    this.setData({
      dateYindex: e.detail.value
    });
    this.setData({
      time: parseInt(d.arrTime[0][s.data.dateYindex[0]]) + '-' + u.formatNumber(parseInt(d.arrTime[1][s.data.dateYindex[1]])) + '-' + u.formatNumber(parseInt(d.arrTime[2][s.data.dateYindex[2]])) + ' ' + u.formatNumber(parseInt(d.arrTime[3][s.data.dateYindex[3]])) + ':' + u.formatNumber(parseInt(d.arrTime[4][s.data.dateYindex[4]]))
    });
    //存到店的日期
    wx.request({
      url: 'https://www.zhanglitong.com/mobile/ajax_reserve.php?for_ajax=room-desk',
      method: "POST",
      data: {
        rid: s.data.rid,
        reserve_time : s.data.time
      },
      success: function (res) {
        if(res.data.code == 200){
          s.setData({
            roomDesk: res.data.data
          })
        }
      }
    });

  },
  bindYearChange: function (e) {
    var self = this;
    if (e.detail.column == 0) {
      arrValue[0] = e.detail.value;
      self.setData({
        dateArrY: d.endCheckYYYY(parseInt(this.data.dateArrY[0][arrValue[0]]))
      });
    } else if (e.detail.column == 1) {
      arrValue[1] = e.detail.value;
      self.setData({
        dateArrY: d.endCheckMM(parseInt(this.data.dateArrY[1][arrValue[1]]), parseInt(this.data.dateArrY[0][arrValue[0]]))
      });
    } else if (e.detail.column == 2) {
      arrValue[2] = e.detail.value;
      self.setData({
        dateArrY: d.endCheckDD(parseInt(this.data.dateArrY[2][arrValue[2]]), parseInt(this.data.dateArrY[1][arrValue[1]]), parseInt(this.data.dateArrY[0][arrValue[0]]))
      });
    } else if (e.detail.column == 3) {
      arrValue[3] = e.detail.value;

      self.setData({
        dateArrY: d.endCheckHH(parseInt(this.data.dateArrY[3][arrValue[3]]), parseInt(this.data.dateArrY[2][arrValue[2]]), parseInt(this.data.dateArrY[1][arrValue[1]]), parseInt(this.data.dateArrY[0][arrValue[0]]))
      });


    } else if (e.detail.column == 4) {
      arrValue[4] = e.detail.value;

      self.setData({
        dateArrY: d.endCheckmms(parseInt(this.data.dateArrY[4][arrValue[4]]), parseInt(this.data.dateArrY[3][arrValue[3]]), parseInt(this.data.dateArrY[2][arrValue[2]]), parseInt(this.data.dateArrY[1][arrValue[1]]), parseInt(this.data.dateArrY[0][arrValue[0]]))
      });
    }
  },
  setDateTime: function () {

  },

  showType: function (e) {
    this.setData({
      isShow: e.currentTarget.dataset.num
    });
  },
  showBox: function (e) {

    let self = this, name = e.currentTarget.dataset.name;
    let id = e.currentTarget.dataset.id;
    let isuse = e.currentTarget.dataset.isuse;
    let t = this.data.time;
    let r = this.data.rid;
    let obj = {};
    let url = '';
    obj.rid = r;
    obj.reserve_time = t;

    if (e.currentTarget.dataset.type == 'roomid') {
      obj.roomid = id;
      url = 'https://www.zhanglitong.com/mobile/ajax_reserve.php?for_ajax=roominfo';
    } else {

      obj.deskid = id;
      url = 'https://www.zhanglitong.com/mobile/ajax_reserve.php?for_ajax=deskinfo'
    }

    self.setData({
      roomType: e.currentTarget.dataset.type,
      deskid: id,
      deskname: name
    });
    if (isuse == 1) {
      self.setData({
        isShowBox: false
      });
    } else {
      wx.request({
        url: url,
        method: "POST",
        data: obj,
        success: function (res) {
          self.setData({
            roomMsg: res.data.data,
          });
          self.setData({
            isShowBox: true
          });
        }
      });
    }
  },
  Environmental: function(){
    if (arrimg.length>0){
      wx.previewImage({  //预览店铺环境图片
        current: arrimg[0],
        urls: arrimg,
        success: function () {
          console.log('预览店铺环境图片')
        }
      }) 
    }else{
      wx.showToast({
        title: '商家暂时没有图片',
        icon: 'none',
        mask: true
      
      })
      setTimeout(function () {
        wx.hideLoading()
      }, 2000)
    }



  
  },
  openMenu: function () {    
    wx.navigateTo({
      url: '../yd-menu/yd-menu?rid=' + this.data.rid + '&type=0&rtype=room&reserve_price=' + this.data.reserveprice + '&name=' + this.data.roomList.company + '&time=' + this.data.time + '&Toview='+ false
    })
  },
  myevent: function (e) {
    this.setData({
      isShowBox: e.detail.isShowBox
    });
  },
  
  // 生命周期函数--监听页面加载
  onLoad: function (options) {
    var self = this;
    self.setData({
      rid: options.id
    });
    wx.hideLoading();
    this.setData({
      date: u.dateYMD(new Date()),
      time: u.dateY() + '-' + u.dateM() + '-' + u.dateD() + ' ' + u.dateH() + ':' + u.datems(),
      reserveprice: options.reserve_price
    });

    //获取storage
    self.setData({
      cartStorages: wx.getStorageSync('cartData')
    });
    if (self.data.cartStorages && self.data.cartStorages[0] != self.data.rid){
      wx.removeStorageSync('cartData')
    }
    wx.request({    //请求店铺环境图片接口
      url: 'https://www.zhanglitong.com/wechatapp/r_carousel.php',
      data: {
        r_id: this.data.rid
      },
      header: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      method: 'POST',
      success: function (res) {
        if (res.data.data) {
          arrimg = [];
          for (let i = 0; i < res.data.data.length; i++) {
            let imgReplace = 'https://' + res.data.data[i].thumb.slice(7),
                img = imgReplace.replace(/.thumb.jpg/, "");
            arrimg.push(img)
          }
        } else {
          console.log('商家暂时没有图片')
          self.setData({
            noPicture: true
          })
        }

      }

    }),
    //请求数据
    wx.request({
      url: 'https://www.zhanglitong.com/mobile/ajax_reserve.php?for_ajax=restaurantinfo',
      method: "POST",
      data: {
        rid: self.data.rid
      },
      success: function (res) {
        console.log('返回数据')
        console.log(res)
        self.setData({
          address: res.data.data.address,
          roomList: res.data.data,
          phoneNumber: res.data.data.telephone,
          address: res.data.data.address,
          company: res.data.data.company,
          lat: res.data.data.lat,
          lng: res.data.data.lng
        });
        //设置当前页面的title
        wx.setNavigationBarTitle({
          title: self.data.roomList.company
        })
      }
    });
    //请求该餐馆的房间和大厅的数据
    wx.request({
      url: 'https://www.zhanglitong.com/mobile/ajax_reserve.php?for_ajax=room-desk',
      method: "POST",
      data: {
        rid: self.data.rid
      },
      success: function (res) {
        if (res.data.code == 200) {
          self.setData({
            roomDesk: res.data.data
          });
        }
      }
    })
  },
  
  //跳转视频
  linkVideo:function(event){
    let s = this;
    wx.getStorage({
      key: 'vedio',
      success: function(d){
        console.log(d);
        d.data.forEach(function(v){
          console.log(v);
          if(s.data.rid == v.id){
            wx.navigateTo({
              url: '../yd-video/yd-video?id=' + v.id+'&url=' + v.url
            })
          } else{
            wx.showToast({
              title: '商家暂时没有视频',
              icon: 'none',
              mask: true

            })
            setTimeout(function () {
              wx.hideLoading()
            }, 2000)
          }
        });
      }
    });
    // wx.navigateTo({
    //   url: '../yd-video/yd-video',
    // })
  },

  //打商家电话
  callShop: function(event){
    wx.makePhoneCall({
      phoneNumber: this.data.phoneNumber
    })
  },

  //商家导航
  navigation: function(){
    let that = this;
    wx.getLocation({
      type: 'gcj02', //返回可以用于wx.openLocation的经纬度
      success: function (res) {
        var latitude = parseFloat(that.data.lat);
        var longitude = parseFloat(that.data.lng);
        console.log(res)
        wx.openLocation({
          latitude: latitude,
          longitude: longitude,
          scale: 28,
          name: that.data.company,
          address: that.data.address
        })
      }
    })
  },

  //导航栏
  toHome: function (event) {
    wx.switchTab({
      url: '../index/index',
    })
  },
  toRecharge: function (event) {
    wx.switchTab({
      url: '../yd-discountcard/yd-discountcard',
    })
  },
  toOrder: function (event) {
    wx.switchTab({
      url: '../yd-order/yd-order',
    })
  },
  toMy: function (event) {
    wx.switchTab({
      url: '../yd-home/yd-home',
    })
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
    
  }
})