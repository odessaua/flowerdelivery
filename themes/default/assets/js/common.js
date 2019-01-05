/**
 * Common functions
 */

$(document).ready(function() {
    // Hide flash messages block
    $(".flash_messages .close").click(function(){
        $(".flash_messages").fadeOut();
    });

});


/**
 * Update cart data after product added.
 */
function reloadSmallCart()
{
    $("#cart").load(urlLang+'/cart/renderSmallCart');
}

function reloadPopupCart()
{
    $("#popup-cart").load(urlLang+'/cart/renderPopupCart');
}

/**
 * Add product to cart from list
 * @param data
 * @param textStatus
 * @param jqXHR
 * @param redirect
 */
function processCartResponseFromList(data, textStatus, jqXHR, redirect)
{
    var productErrors = $('#productErrors');
    if(data.errors)
    {
    	$('#notavailable-modal').arcticmodal({
            overlay: {
                css: {
                    backgroundColor: '#000',
                    opacity: .5
                }
            }
        });
    	//$.jGrowl(data.errors);
        /*window.location = redirect*/
    }else{
        reloadSmallCart();
        reloadPopupCart();
        $.jGrowl(jgrowlCheckout, {position:"bottom-right"});
        
        $('#cart-modal').arcticmodal({
            overlay: {
                css: {
                    backgroundColor: '#000',
                    opacity: .5
                }
            }
        });
        
    }
}

function processCartResponseFromCart(data, textStatus, jqXHR, redirect)
{
    var productErrors = $('#productErrors');
    if(data.errors)
    {
    	$('#notavailable-modal').arcticmodal({
            overlay: {
                css: {
                    backgroundColor: '#000',
                    opacity: .5
                }
            }
        });
    }
    else{
        $.jGrowl(jgrowlCheckout, {position:"bottom-right"});
        if(redirect.length > 0){
            location.reload();
        }
        else{
            reloadSmallCart();
            reloadPopupCart();
        }
    }
}
function applyInPage(el){
    console.log($(el).val());
    window.location = $(el).val();
}

function applyCategorySorter(el)
{
    window.location = $(el).val();
}

function getCitiesList(region_id, no_redirect, lang_id, lang_code) {
    $.post(
        '/site/cities/',
        {
            region_id: region_id,
            no_redirect: no_redirect,
            language_id: lang_id,
            language_code: lang_code
        },
        function (data) {
            $('.pr-regions').css('display', 'none');
            $('.pr-cities').css('display', 'block');
            if(data.length > 0){
                $('.hrc-content').html(data);
            }
        }
    );
}

function showRegions() {
    $('.hrc-content').html('');
    $('.pr-cities').css('display', 'none');
    $('.pr-regions').css('display', 'block');
}

// сортировка товаров по умолчанию|цене в категории
function sortCategorybyType(prefix) {
    var selected = $('#'+prefix+'_type_list :selected').val();
    if(typeParams[selected]['url'] !== undefined){
        window.location.href = typeParams[selected]['url'];
    }
}

// количество товаров на странице в категории
function setPerPage(prefix) {
    var selected = $('#'+prefix+'_per_page :selected').val();
    if(perPageParams[selected]['url'] !== undefined){
        window.location.href = perPageParams[selected]['url'];
    }
}

// копируем постраничную навигацию в начало списка товаров в категории
function copyPager(prefix) {
    var pager = $('ul.yiiPager').html();
    var output = '<ul class="yiiPager" id="'+prefix+'_fake_ul">';
    if(pager !== undefined && pager.length > 0){
        $('#'+prefix+'_fake_pager').html(output+pager+'</ul>');
    }
    else {
        $('.cat-sort-perpage').css('top', '-10px');
    }
}