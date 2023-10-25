"use strict";

let Axios 			= require('axios')
let CFloading 		= require('../CFloading/CFloading')
let Swal 			= require('sweetalert2')
const TailSelect 	= require('tail.select')

function Main() {

	this.confirmAction = function (target) {
		target.on('click', function (e) {
			CFloading.start();
			Axios.get(this.href)
				.then(response => {
					Swal.fire({
						showCloseButton: true,
						showConfirmButton: false,
						html: response.data.html,
						onOpen(popup) {
							console.log('OK');
						},
					});
				})
				.catch(res => {
					console.log(res);
				})
				.finally(() => {
					CFloading.stop();
				});

			return false;

		});
	};

	this.reasonDescription = function (origin, target, text) {

		let selectedElement = $(origin).find('option:selected').text()
		if (selectedElement.toLowerCase() === text) {
			$(target).removeAttr('disabled')
			$(target).css('border', '1px solid red')
		} else {
			$(target).attr("disabled", "disabled").attr("required", "false").val(null)
			$(target).css('border', '')
		}
	}

	this.renderTailSelectRequired = function renderTailSelectRequired(target) {

		if (target.length) {
			if (target.val() === null || target.val().length === 0) {
				setTimeout(()=> {
					target.closest('div').find('.tail-select').css('border', '1px solid red')
					target.attr('required', true)
				}, 500)
			} else {
				setTimeout(()=> {
					target.closest('div').find('.tail-select').css('border', 'unset')
					target.attr('required', false)
				}, 500)
			}
		}
	}

	this.renderTailSelect = function renderTailSelect(target, placeholder, multiple = true) {

		let tailSelectList = [];
		if (target.length) {
			target.closest('div').find('label').css('display', 'block');
			tailSelectList =  TailSelect(target.get(0), {
				hideDisabled: true,
				placeholder: placeholder,
				search: true,
				multiple: multiple,
			});
		}

		return tailSelectList;
	};
}

module.exports = new Main();
