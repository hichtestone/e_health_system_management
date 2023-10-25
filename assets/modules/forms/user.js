'use strict';

require('./user.scss');

const ListFilter = require('@mp3000mp/list-filter');
const axios = require('axios');
const TailSelect = require('tail.select');
const Swal = require('sweetalert2');

let Main = require('../Utils/main');

// Activer/Desactiver projet -------------------------------------------------------------------------------------------------------------------
let associate = $('.user-project-associate')
let dissociate = $('.user-project-dissociate')

Main.confirmAction(associate)
Main.confirmAction(dissociate)


document.addEventListener('DOMContentLoaded', function(){

	function ajaxResBgEffect(element, success) {
		let cMem = element.css('background-color');
		let c = (success == 1 ? 'green' : 'red');
		element.css('background-color', c);
		setTimeout(function () {
			element.css('background-color', cMem);
		}, 500);
	}

	let root = $('#user-projects');

	if(root.length){
		root.find('.fa-edit').click(function(){
			let td = $(this).closest('td');
			$(this).css('display', 'none');

			td.find('input').css('display', '');
			td.find('span').css('display', 'none');
			td.find('input').change(function(){

				CFloading.start();
				axios.post($(this).attr('data-url'), {rate: $(this).val()})
					.then(function(res){
						if(res.data.status == 1){
							td.find('.fa-edit').css('display', '');
							td.find('input').css('display', 'none');
							td.find('span').css('display', '');
							td.find('span').html(td.find('input').val()+'%');
							ajaxResBgEffect(td, true);
						}else{
							console.log(res);
						}
					})
					.catch(function(err){
						console.log(err);
					})
					.finally(function(){
						CFloading.stop();
					})

				;

			});


		});

	}

});



