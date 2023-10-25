"use strict";

require('./CFloading.scss');
let spinner = require('spin.js');

function CFloading(){

	this.spinner = new spinner.Spinner({
		color: '#97C8E8',
		fadeColor: '#2C4D7A',
		length: 30,
		lines: 20,
		radius: 45,
		scale: 1,
		width: 5
	});
	this.nbActive = 0;

	// affiche le loader par dessus un voile
	this.start = function(nbActive = 1){
		if(this.nbActive == 0){
			$('body').prepend('<div class="CFloading"></div>');
			let wrapper = $('.CFloading');
			this.spinner.spin();
			wrapper.prepend(this.spinner.el);
		}
		this.nbActive += (nbActive || 1);
	};

	// supprime le loader
	this.stop = function(nbActive = 1){
		this.nbActive -= (nbActive || 1);
		if(this.nbActive == 0) {
			this.spinner.stop();
			$('.CFloading').remove();
		}
	};

	// return le nombre de CFLoading actif
	this.getNbActive = function(){
		return this.nbActive;
	};

	return this;

}
module.exports = new CFloading();
