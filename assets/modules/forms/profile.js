'use strict';

/**
 * AUDIT TRAIL FILTER PROFILE
 *
 *
 */
//Filter Profiles
$('select#sort-profiles').change(function() {
	let filter = $(this).val();
	filterList(filter);
});

// Profiles filter function
function filterList(value) {
	let list = $(".profiles .profiles-info");
	$(list).hide();
	// *=" means that if a data-custom type contains multiple values, it will find them
	$(".profiles").find("article[data-custom-type*=" + value + "]").each(function (i) {
		$(this).show();
	});
}
