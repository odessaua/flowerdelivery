
/**
 * Function executed after product added to cart
 */
function processCartResponse(data, textStatus, jqXHR)
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
        // Display errors
        //productErrors.html(data.errors.join('<br/>')).show();
    }else{
        // Display "Successful message"
        productErrors.hide();
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

$(document).ready(function(){
	$("#selectCurrencyProduct").change(function(){
		
		var id = $(this).val();
		
		$.ajax({
			url: '/store/ajax/activateCurrency/'+id,
			type: 'GET',
			data: {id: id},
			success: function(){
				window.location.reload(true);
			}
		});
	})
});
