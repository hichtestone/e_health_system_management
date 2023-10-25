
let axios = require('axios');
let swal = require('sweetalert2');
let CFloading = require('../CFloading/CFloading');

document.addEventListener('DOMContentLoaded', function () {


	// services
	let datesId = $('#project-database-freeze').attr('data-project-date-id');
	$('#databasefreeze-new, .databasefreeze-edit').click(function(){
		CFloading.start();
		axios.get(this.href)
			.then(response => {
				swal.fire({
					showCloseButton: true,
					showConfirmButton: false,
					html: response.data.html,
					onOpen(popup) {

						// Gestion datepicker
						$(".js-datepicker").datepicker({
							dateFormat: 'dd/mm/yy',
							autoclose: true,
							closeText: 'Fermer',
							prevText: '&#x3c;Préc',
							nextText: 'Suiv&#x3e;',
							currentText: 'Aujourd\'hui',
							monthNames: ['Janvier','Fevrier','Mars','Avril','Mai','Juin',
								'Juillet','Aout','Septembre','Octobre','Novembre','Decembre'],
							monthNamesShort: ['Jan','Fev','Mar','Avr','Mai','Jun',
								'Jul','Aou','Sep','Oct','Nov','Dec'],
							dayNames: ['Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi'],
							dayNamesShort: ['Dim','Lun','Mar','Mer','Jeu','Ven','Sam'],
							dayNamesMin: ['Di','Lu','Ma','Me','Je','Ve','Sa'],
						});

						let elReason = $('#database_freeze_reason');
						let elOtherReason = $('#reason-other');
						function updateAddressInherited(){
							if(elReason.val() != '4'){
								elOtherReason.css('display', 'none');
								elOtherReason.find('label').removeClass('required');
								elOtherReason.find('input').prop('required', false);
							}else{
								elOtherReason.css('display', '');
								elOtherReason.find('label').addClass('required');
								elOtherReason.find('input').prop('required', true);
							}
						}
						elReason.change(function(){
							updateAddressInherited();
						})
						updateAddressInherited();
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


});
