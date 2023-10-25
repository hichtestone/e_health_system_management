"use strict";

require('./sw-link.scss');
const Swal = require('sweetalert2');
const Axios = require('axios');
const CFloading = require('../CFloading/CFloading');

/**
 * applique un event on click sur les  a[data-sw-popup] Expected:
 *   data-sw-title: popup title
 *   href: id html container
 *   data-sw-type: popup type
 * @constructor
 */
window.addEventListener('DOMContentLoaded', () => {
	// gestion des popups
	$(document).on('click', 'a[data-sw-link]',  e => {
		e.preventDefault();
		CFloading.start();
		Axios.get(e.target.href)
			.then(function (res) {
				Swal.fire({
					html: res.data,
					title: e.target.getAttribute('data-sw-title'),
					type: e.target.getAttribute('data-sw-type')
				});
			})
			.catch(function (err) {
				console.log(err);
			})
			.finally(function () {
				CFloading.stop();
			});
	});
});
