import axios from 'axios'
import {API} from '@/api/api'

export const getNav = (id) => {

	return new Promise((resolve, reject) => {
		axios({
			method : "POST",
			url : API.getNav + "&catid=" + (id || 3927),
			data : {},
			headers: {
				"Content-Type" : "application/x-www-form-urlencoded; charset=UTF-8"
			}
		}).
		then((res) => {
			resolve(res.data.data);
		})
	})
};


export const getList = (data) => {
	return new Promise((resolve, reject) => {
		axios({
			method : "POST",
			url : API.getList,
			data : data,
			headers: {
				"Content-Type" : "application/x-www-form-urlencoded; charset=UTF-8"
			}
		}).
		then((res) => {
			resolve(res.data.data);
		})
	})
};


export const getMenuClasses = (id) => {
	return new Promise((resolve, reject) => {
		axios({
			method : "GET",
			url : API.getMenuClasses + "&rid=" + id,
			data : {},
			headers: {
				"Content-Type" : "application/x-www-form-urlencoded; charset=UTF-8"
			}
		}).
		then((res) => {
			resolve(res.data.data);
		})
	})
};

export const getMenuList = (data) => {
	return new Promise((resolve, reject) => {
		axios({
			method : "POST",
			url : API.getMenuList,
			data : data,
			headers: {
				"Content-Type" : "application/x-www-form-urlencoded; charset=UTF-8"
			}
		}).
		then((res) => {
			resolve(res.data.data);
		})
	})
};

export const getHotelDetail = (rid, cid) => {
	return new Promise((resolve, reject) => {
		axios({
			method : "POST",
			url : API.getHotelDetail,
			data : {
	          rid : rid
	        },
			headers: {
				"Content-Type" : "application/x-www-form-urlencoded; charset=UTF-8"
			}
		}).
		then((res) => {
			resolve(res.data.data);
		})
	})
};


export const getHotelRooms = (rid, t) => {
	return new Promise((resolve, reject) => {
		axios({
			method : "POST",
			url : API.getHotelRooms,
			data : {
						rid : rid,
						reserve_time: t
	        },
			headers: {
				"Content-Type" : "application/x-www-form-urlencoded; charset=UTF-8"
			}
		}).
		then((res) => {
			resolve(res.data.data);
		})
	})
};

export const getRoomDetail = (rid, cid, reserve_time) => {
	return new Promise((resolve, reject) => {
		axios({
			method : "POST",
			url : API.getRoomDetail,
			data : {
	          rid : rid,
						roomid : cid,
						reserve_time
	        },
			headers: {
				"Content-Type" : "application/x-www-form-urlencoded; charset=UTF-8"
			}
		}).
		then((res) => {
			resolve(res.data.data);
		})
	})
};

export const getDeskDetail = (rid, cid, reserve_time) => {
	return new Promise((resolve, reject) => {
		axios({
			method : "POST",
			url : API.getDeskDetail,
			data : {
	          rid : rid,
						deskid : cid,
						reserve_time
	        },
			headers: {
				"Content-Type" : "application/x-www-form-urlencoded; charset=UTF-8"
			}
		}).
		then((res) => {
			resolve(res.data.data);
		})
	})
};


export const joinCar = (data) => {
	return new Promise((resolve, reject) => {
		axios({
			method : "POST",
			url : API.joinCar,
			data : data,
			headers: {
				"Content-Type" : "application/x-www-form-urlencoded; charset=UTF-8"
			}
		}).
		then((res) => {
			if(res.data.code == 200){
				resolve(res.data.data);
			}else if(res.data.code == 100){
				window.location.href = "http://www.zhanglitong.com/mobile/login.php?callback=" + encodeURIComponent(window.location.href);
			}

		})
	})
};


export const getCar = (data) => {
	return new Promise((resolve, reject) => {
		axios({
			method : "POST",
			url : API.getCar,
			data : data,
			headers: {
				"Content-Type" : "application/x-www-form-urlencoded; charset=UTF-8"
			}
		}).
		then((res) => {
      resolve(res.data.data);
			// if(res.data.code == 200){
      // 	resolve(res.data.data);
      // }else if(res.data.code == 100){
      // 	window.location.href = "http://www.zhanglitong.com/mobile/login.php?callback=" + encodeURIComponent(window.location.href);
      // }
		})
	})
};

export const delCar = (data) => {
	return new Promise((resolve, reject) => {
		axios({
			method : "POST",
			url : API.delCar,
			data : data,
			headers: {
				"Content-Type" : "application/x-www-form-urlencoded; charset=UTF-8"
			}
		}).
		then((res) => {
			if(res.data.code == 200){
				resolve(res.data.data);
			}else if(res.data.code == 100){
				window.location.href = "http://www.zhanglitong.com/mobile/login.php?callback=" + encodeURIComponent(window.location.href);
			}
		})
	})
};



export const gopay = (data) => {
	return new Promise((resolve, reject) => {
		axios({
			method : "POST",
			url : API.gopay,
			data : data,
			headers: {
				"Content-Type" : "application/x-www-form-urlencoded; charset=UTF-8"
			}
		}).
		then((res) => {
			if(res.data.code == 100){
				window.location.href = "http://www.zhanglitong.com/mobile/login.php?callback=" + encodeURIComponent(window.location.href);
			}else{
				resolve(res.data);
			}
		})
	})
};


export const getOrder = (data) => {
	return new Promise((resolve, reject) => {
		axios({
			method : "POST",
			url : API.getOrder,
			data : data,
			headers: {
				"Content-Type" : "application/x-www-form-urlencoded; charset=UTF-8"
			}
		}).
		then((res) => {
			if(res.data.code == 200){
				resolve(res.data);
			}else if(res.data.code == 100){
				window.location.href = "http://www.zhanglitong.com/mobile/login.php?callback=" + encodeURIComponent(window.location.href);
			}
		})
	})
};

export const getOrderDetal = (data) => {
	return new Promise((resolve, reject) => {
		axios({
			method : "POST",
			url : API.getOrderDetal,
			data : data,
			headers: {
				"Content-Type" : "application/x-www-form-urlencoded; charset=UTF-8"
			}
		}).
		then((res) => {
			if(res.data.code == 200){
				resolve(res.data);
			}else if(res.data.code == 100){
				window.location.href = "http://www.zhanglitong.com/mobile/login.php?callback=" + encodeURIComponent(window.location.href);
			}
		})
	})
};

export const getUserInfo = (data) => {
	return new Promise((resolve, reject) => {
		axios({
			method : "POST",
			url : API.getUserInfo,
			data : data,
			headers: {
				"Content-Type" : "application/x-www-form-urlencoded; charset=UTF-8"
			}
		}).
		then((res) => {
			if(res.data.code == 200){
				resolve(res.data.data);
			}else if(res.data.code == 100){
				window.location.href = "http://www.zhanglitong.com/mobile/login.php?callback=" + encodeURIComponent(window.location.href);
			}
		})
	})
};


export const getArea = (areaid) => {
	return new Promise((resolve, reject) => {
		axios({
			method : "GET",
			url : API.getArea + "&areaid=" + areaid,
			data : {},
			headers: {
				"Content-Type" : "application/x-www-form-urlencoded; charset=UTF-8"
			}
		}).
		then((res) => {
			resolve(res.data.data);
		})
	})
};

export const refunds = (data) => {
	return new Promise((resolve, reject) => {
		axios({
			method : "POST",
			url : API.refunds,
			data : data,
			headers: {
				"Content-Type" : "application/x-www-form-urlencoded; charset=UTF-8"
			}
		}).
		then((res) => {
			resolve(res.data);
		})
	})
};


export const addCuisines = (data) => {
	return new Promise((resolve, reject) => {
		axios({
			method : "POST",
			url : API.addCuisines,
			data : data,
			headers: {
				"Content-Type" : "application/x-www-form-urlencoded; charset=UTF-8"
			}
		}).
		then((res) => {
			resolve(res.data);
		})
	})
};


export const invite = (data) => {
	return new Promise((resolve, reject) => {
		axios({
			method : "POST",
			url : API.invite,
			data : data,
			headers: {
				"Content-Type" : "application/x-www-form-urlencoded; charset=UTF-8"
			}
		}).
		then((res) => {
			resolve(res.data);
		})
	})
};


export const getSlot = (data) => {
	return new Promise((resolve, reject) => {
		axios({
			method : "POST",
			url : API.getSlot,
			data : data,
			headers: {
				"Content-Type" : "application/x-www-form-urlencoded; charset=UTF-8"
			}
		}).
		then((res) => {
			resolve(res.data);
		})
	})
};

export const callWaiter = (data) => {
  return new Promise((resolve, reject) => {
    axios({
      method : "GET",
      url : API.callWaiter+"?rid="+data.rid+"&commonid="+data.commonid+"&reserve_type="+data.reserve_type,
      data : {},
      headers: {
        "Content-Type" : "application/x-www-form-urlencoded; charset=UTF-8"
      }
    }).then((res) => {
      if(res.data.code == 200){
        alert("成功呼叫服务员,请稍后！");
      }else if(res.data.code == -202){
        alert("服务员不存在");
      }else{
        alert(res.data.data);
      }
    })
  })
};

//获取赠送菜
// export const giveFood = (data) => {
//   return new Promise((resolve, reject) => {
//     axios({
//       method : "GET",
//       url : API.giveFood+"?rid="+data.rid,
//       data : {},
//       headers: {
//         "Content-Type" : "application/x-www-form-urlencoded; charset=UTF-8"
//       }
//     }).then((res) => {
//       console.log(res);
//     })
//   })
// };














