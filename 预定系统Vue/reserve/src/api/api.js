let local = "https://www.zhanglitong.com/";
// let local = "http://119.23.234.49:81/";

export const API = {

	getNav : local + "mobile/ajax_reserve.php?for_ajax=category",

	getList : local + "mobile/ajax_reserve.php?for_ajax=malllist",

	getMenuClasses : local + "mobile/ajax_reserve.php?for_ajax=cuisinecate",
	getMenuList : local + "mobile/ajax_reserve.php?for_ajax=cuisine",


	getHotelDetail : local + "mobile/ajax_reserve.php?for_ajax=restaurantinfo",
	getHotelRooms : local + "mobile/ajax_reserve.php?for_ajax=room-desk",
	getRoomDetail : local + "mobile/ajax_reserve.php?for_ajax=roominfo",
	getDeskDetail : local + "mobile/ajax_reserve.php?for_ajax=deskinfo",

	joinCar : local + "mobile/ajax_reserve.php?for_ajax=add_shoppingcart",
	getCar : local + "mobile/ajax_reserve.php?for_ajax=get_shoppingcart",
	delCar : local + "mobile/ajax_reserve.php?for_ajax=del_shoppingcart_cuisine",

	gopay : local + "mobile/ajax_reserve.php?for_ajax=add_order",
	addCuisines : local + "mobile/ajax_reserve.php?for_ajax=add_cuisines",

	getOrder : local + "mobile/ajax_reserve.php?for_ajax=get_orders",
	getOrderDetal : local + "mobile/ajax_reserve.php?for_ajax=get_orders_info",

	getUserInfo : local + "mobile/ajax_reserve.php?for_ajax=get_userinfo",

	getArea : local + "mobile/ajax_reserve.php?for_ajax=area",

	refunds : local + "mobile/ajax_reserve.php?for_ajax=refunds",

	invite : local + "mobile/ajax_reserve.php?for_ajax=get_invitation",
	getSlot : local + "mobile/ajax_reserve.php?for_ajax=get_limit_time_slot",

  callWaiter : local + "mobile/call_small_two.php",

  giveFood : local + "/mobile/ajax_reserve.php?for_ajax=present"

}
