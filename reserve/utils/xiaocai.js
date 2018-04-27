const getLocation = (callBack) => {
  wx.getLocation({
    type: 'gcj02',
    complete: function(d){
      typeof callBack === 'function' && callBack(d);
    }
  });
};

const wakeupSetting = (callBack) => {
  wx.openSetting({
    success: function(d){
      console.log(d);
      typeof callBack === 'function' && callBack('Y');
    },
    fail: function (d) {
      console.log(d);
      typeof callBack === 'function' && callBack('N');
    }
  });
};

const isLogin = (callBack) => {
  wx.checkSession({
    success: function(){
      //登录状态还在
      wx.setStorage({
        key: 'isLogin',
        data: 'YES',     //YES已登录，NO没有登录
        success: function(){
          typeof callBack === 'function' && callBack('YES');
        }
      });
    },
    fail: function(){
      //登录状态失效
      wx.setStorage({
        key: 'isLogin',
        data: 'NO',     //YES已登录，NO没有登录
        success: function () {
          typeof callBack === 'function' && callBack('NO');
        }
      });
    }
  });
};

const login = (callBack) => {
  wx.login({
    success: function(loginMessage){
      if (loginMessage.code){
        wx.getUserInfo({
          withCredentials: true,
          success: function(infoMessage){
            infoMessage.userInfo.code = loginMessage.code;
            wx.request({
              url: 'https://www.zhanglitong.com/wechatapp/wechatapp_login.php',
              method: 'POST',
              data: infoMessage.userInfo,
              success: function(data){
                wx.setStorage({key: 'isLogin', data: 'YES'});
                wx.setStorage({key: '3r_session', data: data.data});
                wx.setStorage({key: 'userInfo', data: {avatarUrl: infoMessage.userInfo.avatarUrl, nickName: infoMessage.userInfo.nickName, code: infoMessage.userInfo.code}});
                typeof callBack === 'function' && callBack('YES');
              },
              fail: function(){
                typeof callBack === 'function' && callBack('NO');
              }
            });
          }
        });
      }
    },
    fail: function(){
      typeof callBack === 'function' && callBack('NO');
    }
  });
}

module.exports = {
  getLocation: getLocation,
  wakeupSetting: wakeupSetting,
  isLogin: isLogin,
  login: login
};