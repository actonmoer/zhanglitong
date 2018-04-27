Component({
  properties: {
    goodsItem: {
      type: Object,
      value: '',
    },
    specIndex: {
      type: Number,
      value: ''
    },
    cartDataStorage: { 
      type: Array,
      value: ''
    },
    rid:{
      type:String,
      value:''
    }
  },
  data: {
    cartDatas: []
  },
  methods: {
    // 增加商品
    addCart: function (event) {
      // 获取storage
      this.setData({
        cartDatas: wx.getStorageSync('cartData'),
      });

      let that          = this,
          goods         = that.data.goodsItem,
          checkItemid   = false,
          cacheCartData = that.data.cartDatas[1],
          rid           = that.data.rid,
          isEqual;

      //判断storage是否为数组
      function isArray(o) {
        return Object.prototype.toString.call(o) == '[object Array]';
      }
      if (!isArray(cacheCartData)) {
        cacheCartData = [];
      }

      if (cacheCartData.length == 0) {
        cacheCartData.push({
          is_spec: 0,
          itemid: goods.itemid,
          mycatid: goods.mycatid,
          num: 1,
          price: goods.price,
          title: goods.title,
          total_price: goods.price
        })
      } else {
        for (let i = 0; i < cacheCartData.length; i++) {
          if (goods.itemid != cacheCartData[i].itemid) {
            checkItemid = true;
          } else {
            if (cacheCartData[i].is_spec == 0) {
              checkItemid = false;
              cacheCartData[i].num++;
              cacheCartData[i].total_price = (+cacheCartData[i].total_price) + (+goods.price);
              break;
            } else {
              if (goods.itemid == cacheCartData[i].itemid) {
                if (goods.spec_attr_old.toString() == cacheCartData[i].spec_attr_old.toString()) {
                  checkItemid = false;
                  cacheCartData[i].num++;
                  cacheCartData[i].total_price = (+cacheCartData[i].total_price) + (+goods.price);
                  break;
                }
              }
            }
          }
        };

        if (checkItemid) {
          cacheCartData.push({
            is_spec: 0,
            itemid: goods.itemid,
            mycatid: goods.mycatid,
            num: 1,
            price: goods.price,
            title: goods.title,
            total_price: goods.price
          })
          checkItemid = false;
        }
      }
      //设置storage
      wx.setStorageSync('cartData', [ rid , cacheCartData]);
      //触发页面定义的函数
      that.triggerEvent('cartstorage');
    },

    // 减少商品
    reduceCart: function (event) {

      //获取storage
      this.setData({
        cartDatas: wx.getStorageSync('cartData')
      });

      let that          = this,
          goods         = that.data.goodsItem,
          cacheCartData = that.data.cartDatas[1],
          rid           = that.data.rid,
          isEqual;

      for (let i = 0; i < cacheCartData.length; i++) {
        if (cacheCartData[i].is_spec == 0) {
          if (goods.itemid == cacheCartData[i].itemid && cacheCartData[i].num != 0) {
            cacheCartData[i].num--;
            cacheCartData[i].total_price = (+cacheCartData[i].total_price) - (+goods.price);
            // 删除storage中num为0的数据
            if (cacheCartData[i].num == 0) {
              cacheCartData.splice(i, 1);
            }
            break;
          }
        } else {
          if (goods.itemid == cacheCartData[i].itemid && cacheCartData[i].num != 0) {
            if (goods.spec_attr_old.toString() == cacheCartData[i].spec_attr_old.toString()) {
              cacheCartData[i].num--;
              cacheCartData[i].total_price = (+cacheCartData[i].total_price) - (+goods.price);
              // 删除storage中num为0的数据
              if (cacheCartData[i].num == 0) {
                cacheCartData.splice(i, 1);
              }
              break;
            }
          }
        }
      };

      //设置storage
      wx.setStorageSync('cartData', [rid, cacheCartData]);

      //触发页面定义的函数
      that.triggerEvent('cartstorage');
    }
  }
})