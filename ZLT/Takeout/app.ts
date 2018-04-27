function takeoutstr(data){
    let str = '',
    obj = data || '',
    url = new RegExp('http://www.zhanglitong.com');
    if(obj){
        obj.forEach(function(v, i, arr){
            if(url.test(v.thumb)){
                v.thumb = 'https:'+v.thumb.substring(5);
            }
            str += `<div class="seller-item">
                <div class="seller-img">
                    <img src="${v.thumb}" alt="">
                </div>
                <div class="seller-detail">
                    <div class="seller-title">
                        <span class="title">${v.take_out_shop_name}</span>
                    </div>
                    <div class="sale">
                        <span>总售${v.month_sell_count}</span>
                        <span class="distance">1000km</span>
                    </div>
                    <div class="time">
                        <span>${v.delivery_time}分钟送达</span>
                    </div>
                    <div class="seller-price">
                        <span class="start-price">起送价¥${v.start_price}</span>
                        <span class="distribution-price">配送价¥${v.distribution_price}</span>
                    </div>
                </div>
            </div>`;
        });
    }
    return str;
}