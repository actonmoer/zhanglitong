const url = 'https://www.zhanglitong.com';
const api = {
  classify: url + '/mobile/reserve_category.php', //餐馆菜式分类
  classifyType: url + '/mobile/ajax_reserve.php?for_ajax=category&catid=3927'
};
module.exports = api;