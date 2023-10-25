"use strict";

const TailSelect = require('tail.select')

function Etmf() {

	this.renderTailSelect = function renderTailSelect(target, placeholder, multiple = true) {

		let tailSelectList = []
		if (target.length) {
			target.closest('div').find('label').css('display', 'block')
			tailSelectList =  TailSelect(target.get(0), {
				hideDisabled: true,
				placeholder: placeholder,
				search: true,
				multiple: multiple,
			})
		}

		return tailSelectList
	};

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

}

module.exports = new Etmf()
