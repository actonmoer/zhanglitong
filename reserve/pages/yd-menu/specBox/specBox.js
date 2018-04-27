let index         = 0,
    priceArr      = [],
    idArr         = [],
    classArr      = [],
    id1           = 1, 
    id2           = 1, 
    id3           = 1,
    selectSpecArr = [],
    specAttrOld   = [],
    money,
    name,
    pindex;

Component({
  properties: {
    goodsItem: {
      type: Object,
      value: '',
    },
    specAttr: {
      type: Array,
      value: ''
    },
    specTitle: {
      type: String,
      value: ''
    },
    specItemId: {
      type: String,
      value: ''
    },
    rid: {
      type: Number,
      value: ''
    }
  },
  data: {
    isSpec: false,
    specClass: '',
    specPrice: '',
    cartDatas: [],
    active: [[1], [1], [0]],
    selectSpec: [1, 1],
    itemid: 1,
    specItemArr: [],
  },
  methods: {
    // 选择规格
    specItem: function (event) {
      let that = this;
      pindex = event.currentTarget.dataset.parentindex;
      index = event.currentTarget.dataset.id - 1;

      if (pindex == 0) {
        id1 = event.currentTarget.dataset.id;
      } else if (pindex == 1) {
        id2 = event.currentTarget.dataset.id;
      } else if (pindex == 2) {
        id3 = event.currentTarget.dataset.id;
      }
      that.setData({
        active: [[id1], [id2], [id3]]
      });
    },

    specBox: function (event) {
      let that = this,
          totalPriceArr = 0;

      var specindex = event.currentTarget.dataset.specindex;
      for (let i = 0; i < that.data.specAttr.length; i++) {
        priceArr[specindex] = that.data.specAttr[specindex].cont[index].money;
        idArr[specindex] = that.data.specAttr[specindex].cont[index].id;
      }

      // 累加数组的金额
      for (let i = 0; i < priceArr.length; i++) {
        totalPriceArr += +priceArr[i];
      }
      that.setData({
        specPrice: totalPriceArr
      })

      //存多规格数组
      if (pindex == 0) {
        selectSpecArr[0] = index;
        specAttrOld[0] = index + 1;
      } else if (pindex == 1) {
        selectSpecArr[1] = index;
        specAttrOld[1] = index + 1;
      }
    },

    //点击多规格
    switchSpec: function (event) {
      let that = this,
          totalPrice = 0;
      that.setData({
        isSpec: true
      });
     
      if (that.data.goodsItem.spec_attr.length == 1) {
        selectSpecArr = [0];
        specAttrOld = [1];
      } else if (that.data.goodsItem.spec_attr.length == 2) {
        selectSpecArr = [0,0];
        specAttrOld = [1,1];
      }

      //清空多规格数组里的价钱
      priceArr = [];
      classArr = [];
      idArr = [];
      for (let i = 0; i < that.data.specAttr.length; i++) {
        totalPrice += +that.data.specAttr[i].cont[0].money;
        priceArr[i] = that.data.specAttr[i].cont[0].money;
        idArr[i] = that.data.specAttr[i].cont[0].id;
        classArr[i] = 0;
      }
      that.setData({
        specPrice: totalPrice
      })
    },

    //多属性加入购物车
    specAddCart: function (event) {
      let that = this,
          selectSpecAttr = [];
      that.setData({
        isSpec: false,
        active: [[1], [1], [0]]
      })
         
      //遍历所选择的多属性
      for (let i = 0; i < selectSpecArr.length;i++){
        for (let j = 0; j < that.data.goodsItem.spec_attr.length; j++) {
          selectSpecAttr[i] = {
            cont: [that.data.goodsItem.spec_attr[i].cont[selectSpecArr[i]] ],
            title: that.data.goodsItem.spec_attr[i].title
          }
        }
      }

      //.........跟单个商品加入购物车相同代码开始.........
      //获取storage
      that.setData({
        cartDatas: wx.getStorageSync('cartData'),
      });

      let goods         = that.data.goodsItem,
          checkItemid   = false,
          cacheCartData = that.data.cartDatas[1],
          rid = that.data.rid,
          isEqual;

      //判断storage是否为数组，如果不是就赋值成数组
      function isArray(o) {
        return Object.prototype.toString.call(o) == '[object Array]';
      }
      if (!isArray(cacheCartData)) {
        cacheCartData = [];
      }

      if (cacheCartData.length == 0) {
        cacheCartData.push({
          is_spec: 1,
          itemid: goods.itemid,
          mycatid: goods.mycatid,
          num: 1,
          price: that.data.specPrice,
          spec_attr: selectSpecAttr,
          spec_attr_old: specAttrOld,
          title: goods.title,
          total_price: that.data.specPrice
        })
      } else {
        for (let i = 0; i < cacheCartData.length; i++) {
          //判断storage的spec_attr_old跟新添加的spec_attr_old是否相同
          if (cacheCartData[i].is_spec == 1) {
            isEqual = (cacheCartData[i].spec_attr_old.toString() == specAttrOld.toString());
          }

          if (goods.itemid != cacheCartData[i].itemid || !isEqual) {
            checkItemid = true;
          } else {
            checkItemid = false;
            cacheCartData[i].num++;
            cacheCartData[i].total_price = (+cacheCartData[i].total_price) + (+that.data.specPrice);
            break;
          }
        };
        if (checkItemid) {
          cacheCartData.push({
            is_spec: 1,
            itemid: goods.itemid,
            mycatid: goods.mycatid,
            num: 1,
            price: that.data.specPrice,
            spec_attr: selectSpecAttr,
            spec_attr_old: specAttrOld,
            title: goods.title,
            total_price: that.data.specPrice
          })
          checkItemid = false;
        }
      }
      //设置storage
      wx.setStorageSync('cartData', [rid, cacheCartData]);

      //触发页面定义的函数
      that.triggerEvent('cartstorage');
      //.........跟单个商品加入购物车相同代码结束.........

    },

    //关闭多属性选择框
    specClose: function (event) {
      let that = this;
      that.setData({
        isSpec: false,
        active: [[1], [1], [0]]
      })
    },
  }
})