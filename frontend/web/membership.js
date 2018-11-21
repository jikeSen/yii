"use strict";
(function(){
    // 获取用户会员信息
    var pageInit = pageInit || {},
        isWeiXin = navigator.userAgent.toLowerCase().indexOf('micromessenger') != -1,
        showDialog = function(msg){
            $(document).dialog({content: msg});
        };

    pageInit.user = {uid: null};
    pageInit.user.vipLevels = {1:'周会员', 2:'月度会员', 3: '年度会员',4:'日会员',};
    var uid = $(" input[ name='uid' ] ").val();
    // 获取用户会员信息
    $(document).ready(function(){
        pageInit.user.uid = uid;
        $.getJSON('/v1/membership_show', {uid: uid}, function(ret){
            var res = ret.data;
            var res2 = res.membership;
            if(ret.code == 200 && !$.isEmptyObject(res.membership)){
                var myVip = pageInit.user.vipLevels[res2.grade];
                $('.bottom').find('.icon').attr('src', '/images/pub_icon'+ res2.grade + '.png');
                $('.bottom').find('.item_title').html('您是<span>VIP (' + myVip + ')</span>');
                $('.bottom').find('.item_con').html('将于'+ res2.expire +'过期');
                $('.surepay').text('续费');
            }
        })
    });

    var membershipLists = {};
    $.getJSON('/v1/membership_items?uid='+uid,function(ret){
        if(ret.code == 200){
            membershipLists = ret.data;
            var goodsElements = [];
            for(var itemId in membershipLists){
                var element = [
                    '<li class="type items" data-money="', membershipLists[itemId]['price'], '" data-goods_id="', membershipLists[itemId]['id'], '">',
                    '<img src="'+ membershipLists[itemId]['img'] +'" class="icon">',
                    '<p class="item_title"><span class="item_type">VIP (',membershipLists[itemId]['title'],')</span>' + (itemId == 3 ? '<span class="tj">推荐</span>' : '') + '</p>',
                    '<p class="item_con">可享',membershipLists[itemId]['valid_date'],'天会员特权</p>',
                    '<span class="price">',membershipLists[itemId]['price'],'元</span>',
                    '</li>'
                ];
                goodsElements.push(element.join(''));
            }
            $("#goodsList").html(goodsElements.join(''));
        }
    });

    pageInit.orderFields = { payType: 'weixin'};//点击的时候的方法
    pageInit.initOrderFields = function(eleObj){
        var goodsId = +eleObj.data('goods_id');
        var money = +eleObj.data('money');
        if(!goodsId || !money){
            return false;
        }
        pageInit.orderFields.goodsId = goodsId;
        pageInit.orderFields.money = money;
    };

    $('.close').on('click',function(){
        $('.pay').removeClass('show');
        $('.mask').addClass('hide');
    });

    $('.mask').on('click',function(){
        $('.pay').removeClass('show');
        $('.mask').addClass('hide');
    });

    //支付宝微信切换
    $('.paylist').on('click',function(){
        $('.paylist').removeClass('active');
        $(this).addClass('active');
    });

    // 动态创建订单form并提交 修改为ajax 提交
    pageInit.submitOrderForm = function(){
        var formFields = pageInit.orderFields;
        formFields.uid = pageInit.user.uid;

        var params = {
            'category':1,
            'title':'微信支付购买会员',
            'frm' :3,
            'uid':formFields.uid,
            'cash':formFields.money,
            'productId':formFields.goodsId,
            'comment':'微信支付购买会员'
        };

        $.ajax({
            url:'/v1/buymembership',
            type:'post',
            dataType:'json',
            data: params,
            success:function(rs){
                if(rs.code === 200){
                    location.href ='weixin://';
                }else {
                    //失败
                    alert(rs.message);
                }

            },
            complete: function(XMLHttpRequest, textStatus) {
                //alert(XMLHttpRequest);
            }
        });

        /*var formHtml = [];
        for(var field in formFields){
            formHtml = formHtml.concat(['<input type="text" name="', field, '" value="', formFields[field], '" />']);
        }
        formHtml.push('<input type="text" name="returnurl" value="'+encodeURIComponent("http://test.api.qifanfan.cn/membership")+'" />');
        $("#orderForm").html(formHtml.join(''));*/

    };

    // 准备订单数据
    $("#goodsList").on("click", ".items", function(){
        pageInit.initOrderFields($(this));
    });

    // 选择支付方式
    $(".paylist").on("click", function(){
        $("#submitOrder").data('type', $(this).data('type'));
    })
    // 根据选择的支付方式提交订单
    $("#submitOrder").on("click", function () {
        var payType = $(this).data('type');
        /*if(payType == 'weixin' && isWeiXin){
            showDialog('暂不支持在微信客户端内支付');
            return false;
        }*/
        pageInit.orderFields.payType = payType;
        pageInit.submitOrderForm();
    });

    //选择购买种类
    $("#goodsList").on('click', '.type', function(e){
        $('.type').removeClass('active');
        $(e.currentTarget).addClass('active');

        var icon = $(e.currentTarget).find('.icon').attr('src');
        var title = $(e.currentTarget).find('.item_type').text();
        var pirce = $(e.currentTarget).find('.price').html();
        $('.bottom').find('.icon').attr('src',icon);
        $('.bottom').find('.item_title').html(title);
        $('.bottom').find('.item_con').html('总计 <span>'+pirce+'</span>');

        $('.surepay').show();
    });

    //弹出支付选择框  aaa
    $('.surepay').on('click',function(){
        if(!pageInit.user.uid){
            showDialog('获取用户信息失败，请尝试重新打开页面');
            return false;
        }
        var goodsId = $('.type.items.active').data('goods_id');
        let obj = '';
        Object.keys(membershipLists).forEach(function(key){
            //console.log(membershipLists[key].id);
            if(membershipLists[key].id == goodsId){
                obj = membershipLists[key];
                return;
            }
            // forEach  return只是停止 并不会返回值  而且array的return 只会返回array || boolean 数组或者布尔值
        });

        $(".needpay").text(obj.price);
        $(".buymsg").text(obj.title + "(" + obj.valid_date + ")天");
        $('.pay').addClass('show');
        $('.mask').removeClass('hide');
    });

    function overPay(){
        if(typeof window.closeWebview == 'function'){
            window.closeWebview();
            return true;
        }
        if(typeof window.appInterface == 'object'){
            if(typeof window.appInterface.closeWebview == 'function'){
                window.appInterface.closeWebview();
            }
        }
    }

    var locationHash = location.hash;
    if(locationHash){
        if(locationHash.indexOf('?') != -1){
            locationHash = locationHash.substring(0, locationHash.indexOf('?'));
        }
        if(locationHash == '#finished'){
            setTimeout(overPay, 800);
        }
    }

})();