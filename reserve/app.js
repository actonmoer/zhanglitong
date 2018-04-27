//app.js
App({
  onLaunch: function () {
    wx.setStorage({
      key: 'vedio',
      data:[
        { id: '360', url: 'https://www.zhanglitong.com/mobile/videoSource/zysz.mp4'},
        { id: '358', url: 'https://www.zhanglitong.com/mobile/videoSource/ymzz.mp4' },
        { id: '340', url: 'https://www.zhanglitong.com/mobile/videoSource/changan.mp4' },
        { id: '342', url: 'https://www.zhanglitong.com/mobile/videoSource/wztn.mp4' }
      ]
    });
    let s = this,
    _session = '',
    isCheck = '';
    s.globalData.checkSession(function(d){
       isCheck = d;
    });
    wx.getStorage({
      key: '3r_session',
      success: function (res) {
        if (!isCheck){
          s.globalData.login();
        }
      },
      fail: function(){
        s.globalData.login();
      }
    });
    //加载页面
    wx.showLoading({
      title: '加载中',
   });
  },
  globalData: {
    checkSession: function(callBack){
      let isFlag = false;
      wx.checkSession({
        success: function(){
          isFlag = true;
          callBack(isFlag);
        },
        fail: function(){
          isFlag = false;
          callBack(isFlag);
        }
      });
    },
    login: function(callBack){
      wx.login({
        success: function(loginData){
          if (loginData.code){
            wx.getUserInfo({
              complete: function (userInfo){
                if (userInfo.errMsg == 'getUserInfo:ok'){
                  userInfo.userInfo.code = loginData.code;
                  wx.request({
                    url: 'https://www.zhanglitong.com/wechatapp/wechatapp_login.php',
                    method: 'POST',
                    data: userInfo.userInfo,
                    success: function (data) {
                      wx.setStorage({
                        key: '3r_session',
                        data: data.data
                      });
                      wx.setStorage({
                        key: 'userInfo',
                        data: {
                          avatarUrl: userInfo.userInfo.avatarUrl,
                          nickName: userInfo.userInfo.nickName,
                          code: userInfo.userInfo.code
                        },
                        success: function (d) {
                          if (typeof (callBack) === 'function') {
                            callBack();
                          }
                        }
                      });
                    }
                  });
                }else{
                  if (typeof (callBack) === 'String'){
                    callBack('notLogin');
                  }
                }
              }
            });
          }
        }
      });
    },
    userInfo: null,
    globalStorage: '',
    //获取用户地理位置
    getLocation: function(callBack){
      wx.getLocation({
        complete: function(d){
          typeof callBack === 'function' && callBack(d);
          // if (typeof callBack === 'function'){
          //   callBack(d)
          // }
          //callBack === 'function' && callBack(d);
        }
      });
    }
  }
})