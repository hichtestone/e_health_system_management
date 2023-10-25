"use strict";

require('./CFSwitchableForm.scss');

function CFSwitchableForm () {

	const CFS = this;

	//show-form-admin

	// instanciation de l'objet
	CFS.init = function(container, options){
		options = options || {};

		let me = this;
		me.elContainer = container;
		me.container = {
			read: container.find('.sf-read'),
			write: container.find('.sf-write'),
		};
		me.onSwitch = options.onSwitch || false;

		// event on button
		me.elContainer.find('.sf-button').click(function(){
			me.switch();
		});

		// propriété public
		me.state = options.state || 'read';

		// méthode publique
		me.switch = function(){
			me.container.write.toggleClass('d-none');
			me.container.read.toggleClass('d-none');
			if(me.onSwitch != false){
				me.onSwitch();
			}
		};

		return me;

	};

	// auto instanciation
	let els = $('.switchable-form-wrap');
	let arrSF = [];
	els.each(function(){
		arrSF.push(new CFS.init($(this)));
	});

}

window.addEventListener('DOMContentLoaded', () => {
	new CFSwitchableForm();
});

module.exports = new CFSwitchableForm();
