(function ($) {

	//Assigning proxy function
	var org_relationship_update_results = acf.fields.relationship.update_results;

	acf.fields.relationship.update_results = function (div) {

		var type = div.attr('data-post_type');

		//if it's our widget field, use our function
		if (type == 'widget_relationship_field') {

			// add loading class, stops scroll loading
			div.addClass('loading');

			// vars
			var left = div.find('.relationship_left .relationship_list'),
				right = div.find('.relationship_right .relationship_list'),
				paged = parseInt(div.attr('data-paged')),
				args = div.attr('data-args');

			// get results
			$.ajax({
				url     : ajaxurl,
				type    : 'post',
				dataType: 'html',
				data    : {
					'action'    : 'acf_Widget/get_widget_list',
					'paged'     : paged,
					'args'      : args,
					'post_type' : type,
					'field_name': div.parent().attr('data-field_name'),
					'field_key' : div.parent().attr('data-field_key'),
					'nonce'     : acf.nonce
				},
				success : function (html) {

					div.removeClass('no-results').removeClass('loading');

					// new search?
					if (paged == 1) {
						left.find('li:not(.load-more)').remove();
					}


					// no results?
					if (!html) {
						div.addClass('no-results');
						return;
					}


					// append new results
					left.find('.load-more').before(html);


					// less than 10 results?
					var ul = $('<ul>' + html + '</ul>');
					if (ul.find('li').length < 10) {
						div.addClass('no-results');
					}


					// hide values
					acf.fields.relationship.hide_results(div);
				}
			});
		}

		//if not, use the default function
		else {
			org_relationship_update_results(div);
		}

	};

})(jQuery);