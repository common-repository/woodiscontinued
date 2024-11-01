var discontinuePub = {
    simpleProductDiscontinued : function() {
        $elem = jQuery('p.stock.in-stock');
            if($elem && $elem.text().toUpperCase() === 'DISCONTINUED') { 
                $elem.removeClass('in-stock'); jQuery('form.cart').hide() 
            }
    },
    observed : function() { return jQuery('table.variations').find('select').get(0) },
    observer : function(theObserved) {
        return new MutationObserver(function() {
            if(theObserved.value.length > 0){
                $elem = jQuery('p.stock.in-stock');
                if($elem.text().toUpperCase() === 'DISCONTINUED') {
                    $elem.removeClass('in-stock');
                    jQuery('.single_add_to_cart_button').hide();
                    jQuery('.input-text.qty.text').hide();
                }                    
            }
        })
    }
};