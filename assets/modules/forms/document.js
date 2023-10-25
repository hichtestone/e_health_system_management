// Tail select
const TailSelect = require('tail.select');
let axios = require('axios');
let swal = require('sweetalert2');
let CFloading = require('../CFloading/CFloading');


document.addEventListener('DOMContentLoaded', function() {

	// Validation du form doc transverse
	if( null !== (form_document_transverse = document.getElementsByName('document_transverse')[0])){
		$(form_document_transverse).submit(function(e){

			// Checkbox "Supprimer"
			let deleteChecked = true === $('#document_transverse_filenameVich_delete').prop('checked')

			// Fichier upload
			let fileUploadExists = '' != $('#document_transverse_filenameVich_file').val()

			// Fichier download
			let fileDownloadExists = 0 < $('[data-download]').length


			// En cas de suppression du fichier ou upload nouveau fichier
			if(fileDownloadExists && (deleteChecked || fileUploadExists)) {

				e.preventDefault()

				let text_dialog_confirm = $('[data-dialog-confirm]').attr('data-dialog-confirm')
				let text_dialog_btn_yes = $('[data-dialog-btn-yes]').attr('data-dialog-btn-yes')
				let text_dialog_btn_no = $('[data-dialog-btn-no]').attr('data-dialog-btn-no')

				swal.fire({
					title: text_dialog_confirm,
					showDenyButton: true,
					showCancelButton: true,
					confirmButtonText: text_dialog_btn_yes,
					cancelButtonText: text_dialog_btn_no
				}).then((result) => {

					if (result.isConfirmed) {
						e.currentTarget.submit();
					}
				})
			}

		});
	}

	if(document.getElementById('document-table') !== null){

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

							let helperUrl = $('#formHelperUrl');
							let elSelect = $('#document_tracking_center_documentTracking');
							if(!elSelect.length){
								elSelect = $('#document_tracking_interlocutor_documentTracking');
							}
							let elSentAt = $('#sentAt');
							let elReceivedAt = $('#receivedAt');
							function refreshForm(){
								if(elSelect.val() == ''){
									elSentAt.css('display', 'none');
									elReceivedAt.css('display', 'none');
								}else{
									axios.get(helperUrl.html(), {params: {documentTracking: elSelect.val()}})
										.then(response => {
											elSentAt.css('display', response.data.toBeSent ? '' : 'none');
											elReceivedAt.css('display', response.data.toBeReceived ? '' : 'none');
										})
										.catch(res => {
											console.log(res);
										});
								}
							}
							elSelect.change(function(){
								refreshForm();
							});
							refreshForm();
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

	}

});
