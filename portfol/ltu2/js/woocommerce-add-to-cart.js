window.jQuery(document).ready(function(d){d("body").on("adding_to_cart",function(a,o,t){o&&o.hasClass("vc_gitem-link")&&o.addClass("vc-gitem-add-to-cart-loading-btn").parents(".vc_grid-item-mini").addClass("vc-woocommerce-add-to-cart-loading").append(d('<div class="vc_wc-load-add-to-loader-wrapper"><div class="vc_wc-load-add-to-loader"></div></div>'))}).on("added_to_cart",function(a,o,t,c){void 0===c&&(c=d(".vc-gitem-add-to-cart-loading-btn")),c&&c.hasClass("vc_gitem-link")&&c.removeClass("vc-gitem-add-to-cart-loading-btn").parents(".vc_grid-item-mini").removeClass("vc-woocommerce-add-to-cart-loading").find(".vc_wc-load-add-to-loader-wrapper").remove()})});' ) && $button
			.removeClass( 'vc-gitem-add-to-cart-loading-btn' )
			.parents( '.vc_grid-item-mini' )
			.removeClass( 'vc-woocommerce-add-to-cart-loading' )
			.find( '.vc_wc-load-add-to-loader-wrapper' ).remove();
	} );
} );
