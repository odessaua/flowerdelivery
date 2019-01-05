$(window).load(function (){
    var topBar = $('#navigation');
    var start  = topBar.offset().top;
    var fixed;
    $('.update').css('display','none');
    $(window).scroll(function () {
        if(!fixed && (topBar.offset().top - $(window).scrollTop() < 0)){
            $("#hd").css('margin-bottom','38px');
            topBar.css('top', 0);
            topBar.css('position', 'fixed');
            topBar.css('width', $('#doc3').css('width'));
            topBar.css('opacity', '0.92');
            fixed = true;
        }else if(fixed && $(window).scrollTop() <= start){
            topBar.css('position', '');
            topBar.css('width', '');
            topBar.css('opacity', '1');
            $("#hd").css('margin-bottom','');
            fixed = false;
        }
    });

    jQuery(function($) {
        // активное меню таблицы Товаров
        function fixDiv() {
        var $cache = $('#productsListGridActions');
        if ($(window).scrollTop())
          $cache.css({
            'position': 'fixed',
            'top': '35px'
          });
        else
          $cache.css({
            'position': 'relative',
          });
        }
        $(window).scroll(fixDiv);
        fixDiv();

        // активное меню таблицы Заказов
        function fixDivOrders() {
            var $cache = $('#ordersListGridActions');
            if ($(window).scrollTop())
                $cache.css({
                    'position': 'fixed',
                    'top': '35px'
                });
            else
                $cache.css({
                    'position': 'relative',
                });
        }
        $(window).scroll(fixDivOrders);
        fixDivOrders();
    });
});
