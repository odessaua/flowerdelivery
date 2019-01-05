
/**
 * Show dialog
 * @param order_id
 */
function openAddProductDialog(order_id)
{
    $( "#dialog-modal").dialog({
        width: '90%',
        modal: true
    });
}

/**
 * Add product to order
 * @param el TR
 */
function addProductToOrder(el, order_id, token)
{
    var product_id = $(el).children('a').attr('href');
    var quantity   = $('#count_' + product_id).val();
    var price      = $('#price_' + product_id).val();
    var variants   = $('#variants_'+ product_id+' option:selected').text();

    $.ajax({
        url: "/admin/orders/orders/addProduct",
        type: "POST",
        data: {
            YII_CSRF_TOKEN: token,
            order_id: order_id,
            product_id: product_id,
            quantity: quantity,
            price: price,
            variants: variants
        },
        dataType: "html",
        success: function(){
            reloadOrderedProducts(order_id);
            $.jGrowl(productSuccessAddedToOrder,{position:"bottom-right"});
        }
    });

    return false;
}

/**
 * Delete ordered product
 * @param product_id
 * @param order_id
 * @param token
 */
function deleteOrderedProduct(id, order_id, token)
{
    if(confirm(deleteQuestion))
    {
        $.ajax({
            url: "/admin/orders/orders/deleteProduct",
            type: "POST",
            data: {
                YII_CSRF_TOKEN: token,
                id: id,
                order_id: order_id
            },
            dataType: "html",
            success: function(){
                reloadOrderedProducts(order_id);
            }
        });
    }
}

/**
 * Update products list
 */
function reloadOrderedProducts(order_id)
{
    $('#orderedProducts').load('/admin/orders/orders/renderOrderedProducts/order_id/' + order_id);
}

/**
 * Recount total price on change delivery method
 * @param el
 */
function recountOrderTotalPrice(el)
{
    var deliveryMethod = searchDeliveryMethodById($(el).val());

    if(!deliveryMethod)
    {
        return;
    }

    var total          = parseFloat(orderTotalPrice);
    var delivery_price = parseFloat(deliveryMethod.price);
    var free_from      = parseFloat(deliveryMethod.free_from);

    if(delivery_price > 0)
    {
        if(free_from > 0 && total > free_from)
        {
            $("#orderDeliveryPrice").html('0.00');
        }else{
            total = total + delivery_price;
            $("#orderDeliveryPrice").html(delivery_price.toFixed(2));
        }
    }else{
        $("#orderDeliveryPrice").html('0.00');
    }

    $('#orderSummary').html( total.toFixed(2) );
}

/**
 * @param id
 */
function searchDeliveryMethodById(id)
{
    var result = false;
    $(deliveryMethods).each(function(){
        if(parseInt(this.id) == parseInt(id))
        {
            result = this;
        }
    });

    return result;
}

/**
 * Change Order status
 * @param status
 * @param token
 */
function setOrderStatus(status, token)
{
    var ids = $.fn.yiiGridView.getSelection('ordersListGrid');
    $.post(
        'https://www.7roses.com/admin/orders/orders/setStatus/',
        {
            YII_CSRF_TOKEN: token,
            ids: ids,
            status: status
        },
        function (data) {
            $.fn.yiiGridView.update('ordersListGrid');
            $.jGrowl(data);
        },
        'text'
    );
}

function removeOrderPhoto(id, token) {
    if(confirm('Вы действительно хотите удалить это фото?')) {
        $.post(
            '/admin/orders/orders/removeDeliveryPhoto/',
            {
                YII_CSRF_TOKEN: token,
                id: id
            },
            function (data) {
                if (data * 1 > 0) {
                    $('#photo_' + id).remove();
                }
            },
            'text'
        );
    }
}
