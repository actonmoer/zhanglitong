//获取应用实例
var u = require('../../utils/util.js');
var xc = require('../../utils/xiaocai.js');
const url = require('../../utils/api.js');
const app = getApp();
var flag = true;
var abusinessArr = [];
var page = 1;
Page({
  data: {
    fixStatic: true,
    flag: true,
    abusiness: [],
    menuStay: 0, //状态值，0全部餐饮，1附近餐饮，2优惠餐饮
    abusinessCopy: [],
    loading: false,
    isActive: [1, 0, 0],
    region: ['广东省', '广州市', '花都区'],
    customItem: '全部',
    allType: ['智能排序', '附近餐饮', '优惠餐饮'],
    allTypeIndex: 0,
    dishes: [],
    dishesIndex: 0,
    isEntity: true,
    isGetLocation: false,
    arrType: [],  //记录餐馆类型
    lat: '',
    lng: ''
  },
  onLoad: function () {
    let s = this,
      obj = {},
      getLoca = false;
    xc.getLocation(function (d) {
      if (d.errMsg == "getLocation:ok") {
        obj.lat = d.latitude;
        obj.long = d.longitude;
        getLoca = true;

        s.setData({
          lat: d.latitude,
          lng: d.longitude
        })

        wx.request({
          url: 'https://www.zhanglitong.com/mobile/ajax_reserve2.php?for_ajax=malllist',
          data: {
            page: page,
            lng: obj.long,
            lat: obj.lat,
            distance_sort: 0,
            sell_sort: 0,
            keyword: "",
            areaid: 2
          },
          method: 'POST',
          success: function (data) {
            if (data.data.code == 200) {
              page++
              getLoca = true;
              data.data.data.forEach(function (v) {
                if (v.distance > 1000) {
                  v.distance1 = u.tofixedNum(v.distance) + "km";
                } else {
                  v.distance1 = v.distance + "m";
                }
              });

              for (let i = 0, iLength = data.data.data.length; i < iLength; i++) {
                abusinessArr.push(data.data.data[i])
              }

              wx.setStorage({
                key: 'resver',
                data: abusinessArr
              });
              s.setData({
                abusiness: abusinessArr,
                abusinessCopy: abusinessArr
              });
            }
          }
        });

        s.setData({
          isGetLocation: getLoca
        });
      } else {
        xc.wakeupSetting(function (d) {
          if (d == 'Y') {
            getLoca = true;
          }
        });
      }
    });

    wx.hideLoading();
    wx.request({
      url: 'https://www.zhanglitong.com/mobile/ajax_reserve.php?for_ajax=category&catid=3927',
      method: 'POST',
      success: function (d) {
        if (d.data.code == 200) {
          d.data.data.unshift({ catid: '3927', catname: '分类' })
          s.setData({
            dishes: d.data.data
          });
        }
      }
    });
  },
  navclick: function (event) {
    flag = !flag;
    this.setData({
      fixStatic: flag
    })
  },
  showurl: function (event) {
    wx.navigateTo({
      url: '../yuding/yuding?a=123',
    });
  },
  businessDetail: function (event) {
    wx.navigateTo({
      url: '../yd-roomList/yd-roomList?id=' + event.currentTarget.dataset.id + '&reserve_price=' + event.currentTarget.dataset.reserveprice
    });
  },
  clickActive: function (e) {
    let i = e.currentTarget.dataset.index,
      s = this,
      arrData = [],
      arrDataCopy = s.data.abusiness,
      arr1 = 0,
      arr2 = 0,
      arr3 = 0;
    if (i == 0) {
      arr1 = 1;
      arr2 = arr3 = 0;
    } else if (i == 1) {
      arr2 = 1;
      arr1 = arr3 = 0;
    } else if (i == 2) {
      arr3 = 1;
      arr2 = arr1 = 0;
    }
    s.setData({
      isActive: [arr1, arr2, arr3]
    });
  },
  //菜单1选择
  bindChangeAllType: function (e) {
    let index = e.detail.value,
      s = this,
      data = wx.getStorageSync('resver'),
      dataCopy = [],
      dataCopys = [],
      isTrue = false;

    if (s.data.region[2] == '花都区') {
      isTrue = true;
    }
    if (isTrue) {
      if (index == 0) {
        dataCopy = wx.getStorageSync('resver');
      } else if (index == 1) {
        for (let i = 0; i < data.length - 1; i++) {
          for (let j = 0; j < data.length - i - 1; j++) {
            if (data[j].distance > data[j + 1].distance) {
              var t = data[j];
              data[j] = data[j + 1];
              data[j + 1] = t;
            }
          }
        }
        dataCopy = data;
      } else if (index == 2) {
        for (let i = 0; i < data.length; i++) {
          if (data[i].is_present == 1) {
            dataCopy.push(data[i]);
          }
        }
      }
    } else {
      dataCopy = [];
    }

    dataCopy.forEach(function (v) {
      s.data.arrType.forEach(function (v1) {
        if (v.username == v1) {
          dataCopys.push(v);
        }
      });
    });

    if (s.data.arrType.length > 0) {
      dataCopy = dataCopys;
    }

    s.setData({
      allTypeIndex: index,
      abusiness: dataCopy,
      menuStay: index
    });

  },
  //地区选择餐饮
  bindRegionChange: function (e) {
    let s = this,
      getData = wx.getStorageSync('resver'),
      setData = [],
      area = new RegExp(e.detail.value[2]);

    for (let i = 0; i < getData.length; i++) {
      if (area.test(getData[i].address)) {
        if (s.data.menuStay == 0) {
          setData.push(getData[i]);
        } else if (s.data.menuStay == 1) {
          for (let j = 0; j < getData.length - i - 1; j++) {
            if (getData[j].distance > getData[j + 1].distance) {
              var t = getData[j];
              getData[j] = getData[j + 1];
              getData[j + 1] = t;
            }
          }
          setData = getData;
        } else if (s.data.menuStay == 2) {
          if (getData[i].is_present == 1) {
            setData.push(getData[i]);
          }
        }
      }
    }
    this.setData({
      region: e.detail.value,
      abusiness: setData
    });
  },
  //菜式分类
  bindDishes: function (e) {
    let name = this.data.dishes[e.detail.value].catname,
      catid = this.data.dishes[e.detail.value].catid,
      s = this,
      arrType = [],
      arrResver = [],
      arrdata = s.data.abusiness;

    // s.bindChangeAllType();
    if (s.data.allTypeIndex == 0) {
      arrdata = wx.getStorageSync('resver');
    } else if (s.data.allTypeIndex == 1) {
      arrdata = wx.getStorageSync('resver');

      for (let i = 0; i < arrdata.length - 1; i++) {
        for (let j = 0; j < arrdata.length - i - 1; j++) {
          if (arrdata[j].distance > arrdata[j + 1].distance) {
            var t = arrdata[j];
            arrdata[j] = arrdata[j + 1];
            arrdata[j + 1] = t;
          }
        }
      }
    } else {
      let arrd = [];
      arrdata = wx.getStorageSync('resver');
      arrdata.forEach(function (v) {
        if (v.is_present == 1) {
          arrd.push(v);
        }
      });
      arrdata = arrd;
    }
    wx.request({
      url: url.classify,
      data: { catid: catid },
      method: 'POST',
      header: { 'Content-Type': 'application/x-www-form-urlencoded' },
      success: function (res) {
        if (res.data.code == 200) {
          arrType = res.data.data;
          arrdata.forEach(function (v) {
            arrType.forEach(function (v1) {
              if (v.username == v1) {
                arrResver.push(v);
              }
            });
          });
          s.setData({
            dishesIndex: e.detail.value,
            abusiness: arrResver,
            arrType: arrType
          });
        } else if (res.data.code == -200) {

          s.setData({
            dishesIndex: e.detail.value,
            abusiness: [],
            arrType: arrType
          });
        }
      },
      fail: function (res) { }
    });
    arrType
  },

  //跳转搜索页面
  linkSearch: function (event) {
    wx.navigateTo({
      url: '../yd-search/yd-search'
    })
  },

  //页面滚动
  onPageScroll: function (res) {
    let scrollTop = res.scrollTop;
    if (scrollTop > 170) {
      this.setData({
        isEntity: false
      })
    } else {
      this.setData({
        isEntity: true
      })
    }
  },

  //没有商家列表回到首页
  backIndex: function (event) {
    wx.reLaunch({
      url: "./index"
    });
  },

  // 用户点击右上角分享
  onShareAppMessage: function (res) {

  },

  onHide: function () {
    let s = this;
    if (!s.data.isGetLocation) {
      xc.wakeupSetting(function (d) {
        if (d == 'Y') {
          s.setData({
            isGetLocation: true
          });
        }
      });
    }
  },

  /**
   * 页面上拉触底事件的处理函数
   */
  onReachBottom: function () {
    // wx.showLoading({
    //   title: '加载中',
    // })

    // let that = this
    // wx.request({  //跟上面代码一样  待优化
    //   url: 'https://www.zhanglitong.com/mobile/ajax_reserve2.php?for_ajax=malllist',
    //   data: {
    //     page: page,
    //     lng: that.data.lng,
    //     lat: that.data.lat,
    //     distance_sort: 0,
    //     sell_sort: 0,
    //     keyword: "",
    //     areaid: 2
    //   },
    //   method: 'POST',
    //   success: function (data) {
    //     if (data.data.code == 200) {
    //       page++

    //       data.data.data.forEach(function (v) {
    //         if (v.distance > 1000) {
    //           v.distance1 = u.tofixedNum(v.distance) + "km";
    //         } else {
    //           v.distance1 = v.distance + "m";
    //         }
    //       });

    //       for (let i = 0, iLength = data.data.data.length; i < iLength; i++) {
    //         abusinessArr.push(data.data.data[i])
    //       }

    //       wx.setStorage({
    //         key: 'resver',
    //         data: abusinessArr
    //       });
    //       that.setData({
    //         abusiness: abusinessArr
    //       });

    //       setTimeout(function () {
    //         wx.hideLoading()
    //       }, 300)

    //       if (data.data.data == '') {
    //         wx.showModal({
    //           title: '提示',
    //           content: '没有更多数据了！',
    //           showCancel: false,
    //         })
    //       }
    //     }
    //   }
    // });
  },
});