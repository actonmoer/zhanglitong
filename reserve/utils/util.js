let YMD = new Date();
const formatTime = date => {
  const year = date.getFullYear()
  const month = date.getMonth() + 1
  const day = date.getDate()
  const hour = date.getHours()
  const minute = date.getMinutes()
  const second = date.getSeconds()

  return [year, month, day].map(formatNumber).join('-') + ' ' + [hour, minute].map(formatNumber).join(':')
}

const formatNumber = n => {
  n = n.toString()
  return n[1] ? n : '0' + n
}
//计算距离m与km的换算
const tofixedNum = n => {
  n = n*1;
  return n > 1000 ? (n/1000).toFixed(3) : n;
}

//日期 2018-10-03
const dateYMD = date => {
  const year = date.getFullYear();
  const month = date.getMonth() + 1;
  const day = date.getDate();

  return [year, month, day].map(formatNumber).join('-');
}
//年
const dateY = function(){
  const year = YMD.getFullYear();
  return year;
}
//月
const dateM = function (){
  const month = YMD.getMonth() + 1;
  return month;
}
//日
const dateD = function () {
  const day = YMD.getDate();
  return day;
}

const dateH = function () {
  const house = YMD.getHours();
  return house;
}

const datems = function () {
  const minute = YMD.getMinutes();
  return minute;
}

//微信支付
//获取MD5签名算法，取到的值给支付用
const getAutograph = function (session, order) {
  let that = this;
  wx.request({
    url: 'https://www.zhanglitong.com/wechatapp/pay/payfee.php',
    header: {
      'Content-Type': 'application/json'
    },
    data: {
      order: order,
      session: session
    },
    method: 'POST',
    success: function (res) {
      //调用支付函数
      console.log(res);
      that.pay(res.data, order);
    },
    fail: function (res) {
      console.log(res.data)
    }
  });
}

//调起小程序支付
const pay = function (param, order) {
  let that = this;
  wx.requestPayment({
    'timeStamp': param.timeStamp,
    'nonceStr': param.nonceStr,
    'package': param.package,
    'signType': 'MD5',
    'paySign': param.paySign,
    'success': function (res) {
      if (res.errMsg == "requestPayment:ok") {
        wx.navigateTo({
          url: '../yd-order/yd-orderDetail/yd-orderDetail?&order_id=' + order
        })
        wx.removeStorageSync('cartData');
      }
    },
    'fail': function (res) {
      wx.showModal({
        title: '提示',
        content: '支付失败',
        showCancel: false,
        success: function (res) {
          if (res.confirm) {
            console.log('确定')
          }
        }
      })
    }
  });
}
const onShareAppMessage  =  (res)=> {
  var promoter = wx.getStorageSync("3r_session")
  if (res.from === 'button') {
    // 来自页面内转发按钮
    console.log(res.target)
  }
  console.log(promoter)
  
  if (promoter){
    return {
      title: '掌里通美食城',
      path: 'pages/index/index?3r_session=' + promoter,
      success: (res) => {
        // 转发成功
        console.log('转发成功')
        console.log('pages/index/index?promoter=' + promoter)
      },
      fail: (res) => {
        // 转发失败
        console.log('转发失败')
      }
    } 
  }
  
}
   

module.exports = {
  formatTime: formatTime,
  formatNumber: formatNumber,
  tofixedNum: tofixedNum,
  dateYMD: dateYMD,
  dateY: dateY,
  dateM: dateM,
  dateD: dateD,
  dateH: dateH,
  datems: datems,
  getAutograph: getAutograph,
  pay : pay,
  onShareAppMessage: onShareAppMessage
  
}

