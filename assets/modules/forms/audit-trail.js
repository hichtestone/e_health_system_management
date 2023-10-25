
const ListFilter = require('@mp3000mp/list-filter');

document.addEventListener('DOMContentLoaded', function() {
	// active current menu audit trail
	let setDefaultActive = function() {
		let path = window.location.pathname;
		let element = $(".auditTrail a[href='" + path + "']");
		element.addClass("active");
	}

	setDefaultActive();

	let elTrigger = document.getElementById('at-search');
	let elList = document.getElementById('at-list');
	if(elTrigger !== null && elList !== null){
		new ListFilter(elTrigger, elList);
	}

})

