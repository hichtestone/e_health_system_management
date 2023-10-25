const TailSelect = require('tail.select')
let axios 		 = require('axios')
let swal 		 = require('sweetalert2')
let CFloading 	 = require('../CFloading/CFloading')

document.addEventListener('DOMContentLoaded', function () {

	let jobList = $('#interlocutor_job')

	if (jobList.length) {

		let cooperatorLD = document.getElementById('interlocutor_cooperators')
		let cooperatorTS = TailSelect(cooperatorLD, {
			placeholder: "Coopérateurs",
			search: true
		});

		let institutionLD = document.getElementById('interlocutor_institutions')
		let institutionTS = TailSelect(institutionLD, {
			placeholder: "Etablissements",
			search: true
		});

		function updInstitutionsList() {
			if ($('#interlocutor_institutions').val().length > 0) {
				$(institutionTS.select).css('border', '')
			} else {
				$(institutionTS.select).css('border', '1px solid red')
			}
		}

		institutionTS.on('change', function() {updInstitutionsList()});
		updInstitutionsList()

		let jobNoRpps 	= JSON.parse($('#jobNoRpps').html())
		let jobInv 		= JSON.parse($('#jobInv').html())

		function refreshJob() {

			if (jobNoRpps.indexOf(parseInt(jobList.val())) >= 0) {

				if (parseInt(jobList.val()) === 10) {

					$('#interlocutorRpps').css('display', '')
					$('#interlocutorRpps').find('input').prop('required', false)
					$('#interlocutorRpps').find('input').removeAttr('required')
					$('#interlocutor_rppsNumber').removeAttr('required')

				} else {

					$('#interlocutorRpps').css('display', '')
					$('#interlocutorRpps').find('input').prop('required', true)
					$('#interlocutorRpps').find('label').addClass('required')
				}

			} else {

				$('#interlocutorRpps').css('display', 'none')
				$('#interlocutorRpps').find('input').prop('required', false)
				$('#interlocutorRpps').find('label').removeClass('required')
			}

			if (jobInv.indexOf(parseInt(jobList.val())) >= 0) {

				$('#interlocutorSpecs').css('display', '')
				$('#interlocutorSpecs .required').find('select').prop('required', true)
				$('#interlocutorSpecs .required').find('label').addClass('required')

			} else {

				$('#interlocutorSpecs').css('display', 'none')
				$('#interlocutorSpecs .required').find('select').prop('required', false)
				$('#interlocutorSpecs .required').find('label').removeClass('required')
			}
		}

		refreshJob();
		jobList.change(function() {refreshJob()});
	}

	if ($('#interlocutor-document-tracking-table').length) {

		$('#document-new, .document-edit').click(function(){
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

							let helperUrl = $('#formHelperUrl')
							let elSelect = $('#document_tracking_interlocutor_documentTracking')
							let elSentAt = $('#sentAt')
							let elReceivedAt = $('#receivedAt')

							function refreshForm(){

								if(elSelect.val() === ''){
									elSentAt.css('display', 'none')
									elReceivedAt.css('display', 'none')
								}else{
									axios.get(helperUrl.html(), {params: {documentTracking: elSelect.val()}})
										.then(response => {
											elSentAt.css('display', response.data.toBeSent ? '' : 'none')
											elReceivedAt.css('display', response.data.toBeReceived ? '' : 'none')
										})
										.catch(res => {
											console.log(res)
										});
								}
							}
							elSelect.change(function() {
								refreshForm()
							});
							refreshForm()
						},
					});
				})
				.catch(res => {
					console.log(res)
				})
				.finally(() => {
					CFloading.stop()
				});

			return false
		});
	}
});
