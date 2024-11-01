jQuery(document).ready(function($){
    $('._stock_status_field').not('.custom-stock-status').remove();

    var productIdUrlArgs = window.location.search.substring(1)
        .split('&')
        .filter(function(item) {
            if(item.indexOf('post') > -1) {
				return item
			}
        });
    
    var productIdArg = productIdUrlArgs && productIdUrlArgs[0] && productIdUrlArgs[0].split('=');  

    $('.variations_options.variations_tab.variations_tab.show_if_variable > a').click(function(){
        var mutationCallback = function(mutations, _) {
            for(var i = 0; i < mutations.length; i++) {
                if(mutations[i].removedNodes && 
                    mutations[i].removedNodes[0] && 
                    mutations[i].removedNodes[0].className === 'blockUI blockOverlay') {
                    
                    //adding option to main dropdown for variation
                    var addedOptions = {
                        variable_stock_status_discontinued : woodiscontinuedVars.variableDrodownDiscontinued,
                    };

                    $.each(addedOptions, function(val, text){
                        if($('#added-by-discontinued-plugin').length === 0) {
                            $('#field_to_edit optgroup[label="Inventory"]').append($('<option id="added-by-discontinued-plugin"></option>').val(val).html(text));
                        }
                    });
                    
                    $('p').not('.custom-stock-status-variable').filter(function(index, el) { return el.className.indexOf('variable_stock_status') > -1 }).remove();

                    $.get(
                        '/wp-json/woodiscontinued/v2/my_meta_query?parent_product_id=' + (productIdArg && productIdArg[1]) , 
                        function(restApiResults) {
                            $('p').not('.custom-stock-status-variable').filter(function(index, el) { return el.className.indexOf('variable_stock_status') > -1 }).remove();

                            var $variableProductsOnPage = $('.woocommerce_variation');
                            for(var i = 0; i < $variableProductsOnPage.length; i++){
                                var $variableProductOnPage = $($variableProductsOnPage[i]);
                                var productId = $variableProductOnPage.find('h3 > input').val();

                                var onPageStockOptions = $.map($variableProductOnPage.find("#variable_stock_status" + i).find("option"), function(val){ 
                                    return val.value;
                                })

                                var currentStockStatusFromRestApiObj = restApiResults.find(function(item){
                                    if(item != null && item[productId] != null) {
                                        return item[productId];
                                    }
                                });

                                if(currentStockStatusFromRestApiObj != null && onPageStockOptions.indexOf(currentStockStatusFromRestApiObj[productId]) > -1) {
                                    $($variableProductOnPage.find("#variable_stock_status" + i)).val(currentStockStatusFromRestApiObj[productId]);
                                }
                            }
                    });
                }
            } 
        }

        var observer = new MutationObserver(mutationCallback);
        observer.observe(
            document.getElementById('woocommerce-product-data'), 
            { childList: true, subtree: true });
    });
});

