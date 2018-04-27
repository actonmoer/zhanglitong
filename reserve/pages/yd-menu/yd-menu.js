let url = 'https://www.zhanglitong.com/mobile/ajax_reserve.php';
Page({
  data: {
    leftCategory: '',
    rightCategory: '',
    cartData: '',
    leftClass: '',
    goodsNum: 0,
    goodsPrice: 0,
    hasGoods: false,
    cartDatas: '',
    cartDataStorage: ' ',
    getUrlData: '',
    firstCatid: '',
    rid: '',
    tip: '',
    isTip: false,
    tipPrice: '',
    tipTitle: '',
    isShow: false,
    nowImg: '',
    nowTtitle: '',
    goodsInfo: ''
  },

  //监听页面加载
  onLoad: function (options) {
    wx.hideLoading();
    this.ergodicCart();
    this.setData({
      getUrlData: options,
      rid: options.rid,

    })

    this.tip(this.data.rid);

    //设置当前页面的title
    wx.setNavigationBarTitle({
      title: options.name
    })

    //请求列表数据
    this.getLeftMenu(url + "?for_ajax=cuisinecate&wechat=true", 'GET', this.data.getUrlData.rid);
  },
  fanhui: function () {
    wx.navigateBack({

    })
  },

  //商品详情
  openGoodsDetail: function (event) {
    let that = this
    that.setData({
      isShow: true,
      nowImg: event.currentTarget.dataset.thumb,
      nowTtitle: event.currentTarget.dataset.title
    })
    wx.request({
      url: 'https://www.zhanglitong.com/mobile/ajax_reserve.php?for_ajax=goods_detail&itemid=' + event.currentTarget.dataset.itemid,
      method: 'GET',
      header: {
        'content-type': 'application/json'
      },
      success: function (res) {
        console.log(res.data);
        if (res.data.code == 200) {
          let goodsInfo = (res.data.introduce ? res.data.introduce : '这个商家很懒，什么介绍都没编写！')
          that.setData({
            goodsInfo: goodsInfo
          })
        }
      },
      fail: function (res) {
        console.log("网络错误，请稍后再试！")
      }
    })
  },
  closeGoodsDetail: function (evet) {
    this.setData({
      isShow: false
    })
  },

  //点击购物车图标
  cartList: function (event) {
    var switchCart = !this.data.hasGoods;
    this.setData({
      hasGoods: switchCart
    })
  },

  // 点击左边列表
  leftNav: function (event) {
    let that = this;
    let catid = event.currentTarget.dataset.typeid;
    that.getRightMenu(url + "?for_ajax=cuisine", 'POST', that.data.getUrlData.rid, catid);
    that.setData({
      leftClass: event.currentTarget.dataset.typeid
    })
  },

  //确认下单
  confirmOrder: function (event) {
    let param = this.data.getUrlData;
    if (this.data.getUrlData.type == 0) {
      wx.navigateTo({
        url: '../yd-roomList/yd-roomList?id=' + this.data.getUrlData.rid + '&reserve_price=' + this.data.getUrlData.reserve_price
      })
    } else if (this.data.getUrlData.type == 1) {
      wx.navigateTo({
        url: '../pay/pay?name=' + param.name + '&rtype=' + param.rtype + '&cname=' + param.cname + '&reserve_price=' + param.reserve_price + '&time=' + param.time + '&rid=' + this.data.getUrlData.rid + '&commonid=' + this.data.getUrlData.commonid
      })
    } else {
      this.addMenu(param.oid);
    }
  },

  //加菜
  addMenu: function (orderid) {
    let that = this,
      session = wx.getStorageSync('3r_session'),
      rid = that.data.rid,
      cuisines = wx.getStorageSync('cartData');
    if (cuisines == '') {
      let desc = '你没有添加新菜单！';
      that.addGoodModel(orderid, desc)
    } else {
      wx.request({
        url: 'https://www.zhanglitong.com/mobile/new_cuisines.php?for_ajax=add_cuisines&3r_session=' + session,
        data: {
          rid: rid,
          order_id: orderid,
          cuisines: cuisines[1]
        },
        method: 'POST',
        header: {
          'content-type': 'application/json'
        },
        success: function (res) {
          if (res.data.code == 200) {
            let desc = '加菜成功！';
            that.addGoodModel(orderid, desc)
          }
        },
        fail: function (res) {
          console.log("网络错误，请稍后再试！")
        }
      })
    }

  },

  //加菜成功模态框
  addGoodModel: function (orderid, desc) {
    wx.showModal({
      title: '提示',
      content: desc,
      showCancel: false,
      success: function (res) {
        if (res.confirm) {
          if (desc == '加菜成功！') {
            wx.navigateTo({
              url: '../yd-order/yd-orderDetail/yd-orderDetail?order_id=' + orderid,
            })
          }
          else {
            return
          }
        }
      }
    })
  },

  //清空购物车
  clear: function () {
    wx.removeStorageSync('cartData')
    this.ergodicCart();
  },

  //取得组件storage改变后的值
  cartStorage: function (event) {
    this.ergodicCart();
  },

  //遍历storage中的数据
  ergodicCart: function () {
    let goodsItemNum = 0;
    let goodsItemPrice = 0

    //获取storage
    this.setData({
      cartDataStorage: wx.getStorageSync('cartData')
    });

    //遍历购物车中商品数量
    let cartDataStorage = this.data.cartDataStorage;
    // addTip ;  //添加赠送商品到storage的判断
    if (cartDataStorage) {
      for (let i = 0; i < cartDataStorage[1].length; i++) {
        goodsItemNum += cartDataStorage[1][i].num;
        goodsItemPrice += (+cartDataStorage[1][i].total_price);
      }
    }
    this.setData({
      goodsNum: goodsItemNum,
      goodsPrice: goodsItemPrice
    })
  },

  // 请求预定列表数据
  getLeftMenu: function (url, method, rid) {
    let that = this;
    wx.request({
      url: url,
      data: {
        rid: rid,
      },
      method: method,
      header: {
        'content-type': 'application/json'
      },
      success: function (res) {
        if (res.data.code === 200) {
          that.setData({
            leftCategory: res.data.data,
            firstCatid: res.data.data[0].typeid,
            leftClass: res.data.data[0].typeid
          })
          that.getData();
        }
      },
      fail: function (res) {
        console.log("网络错误，请稍后再试！")
      }
    })
  },
  getRightMenu: function (url, method, rid, catid) {
    let that = this;
    wx.request({
      url: url,
      data: {
        rid: rid,
        cuisine_catid: catid
      },
      method: method,
      header: {
        'content-type': 'application/json'
      },
      success: function (res) {
        if (res.data.code === 200) {
          that.setData({
            rightCategory: res.data.data.restaurant
          })
        }
      },
      fail: function (res) {
        console.log("网络错误，请稍后再试！")
      }
    })
  },
  getData: function () {
    this.getRightMenu(url + "?for_ajax=cuisine&wechat=true", 'POST', this.data.getUrlData.rid, this.data.firstCatid);
  },

  //赠送菜提示
  tip: function (rid) {
    let that = this;
    wx.request({
      url: 'https://www.zhanglitong.com/mobile/ajax_reserve.php?for_ajax=present&rid=' + rid,
      method: 'GET',
      header: {
        'content-type': 'application/json'
      },
      success: function (res) {
        let tip
        if (res.data.code == 200){
          let data = res.data
          if (res.data.data.title) {
            tip = '成功预定本店后，送' + data.data.title;
            that.setData({
              tip: tip,
              isTip: true,
              tipPrice: data.data.amount,
              tipTitle: data.data.title
            })
          } else {
            that.setData({
              isTip: false
            })
          }
        }
      },
      fail: function (res) {
        console.log("网络错误，请稍后再试！")
      }
    })
  },

  // 用户点击右上角分享
  onShareAppMessage: function (res) {

  }
})


