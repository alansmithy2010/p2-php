/*

	skOuterClick - A simple event-binder-plugin to handle click events of outside elements.
	Copyright (c) 2014 SUKOBUTO All rights reserved.
	Licensed under the MIT license.
	https://github.com/sukobuto/jquery.skOuterClick/blob/master/LICENSE

*/

// touch‘Î‰ž‰ü‘¢Ï‚Ý
// http://tmpla.info/javascript/jquery/jquery%25e3%2581%25a7%25e6%258c%2587%25e5%25ae%259a%25e8%25a6%2581%25e7%25b4%25a0%25e4%25bb%25a5%25e5%25a4%2596%25e3%2581%25ae%25e3%2581%25a8%25e3%2581%2593%25e3%2582%258d%25e3%2582%2592%25e3%2582%25bf%25e3%2583%2583%25e3%2583%2597%25e3%2581%2595%25e3%2582%258c%25e3%2581%259f%25e3%2582%25a4%25e3%2583%2599%25e3%2583%25b3/

(function($){

	$.fn.skOuterClick = function(method) {
		var methods = {
			init : function (handler) {
				var inners = new Array();
				if (arguments.length > 1) for (i = 1; i < arguments.length; i++) {
					inners.push(arguments[i]);
				}
				return this.each(function() {
					var self = $(this);
					var _this = this;
					var isInner = false;
					// Bind click event to suppress
					function onInnerClick(e){
						isInner = true;
					};
					self.on('click touchend', onInnerClick);
					for (var i = 0; i < inners.length; i++) {
						inners[i].on('click touchend',onInnerClick);
					}
					// Bind click elsewhere
					$(document).on('click touchend', function(e){
						if (!isInner) handler.call(_this, e);
						else isInner = false;
					});
				});
			}
		};
		if (methods[method]) {
			return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
		} else if (typeof method === 'function') {
			return methods.init.apply(this, arguments);
		} else {
			$.error('Method "' + method + '" does not exist in skOuterClick plugin!');
		}
	};
})(jQuery);
