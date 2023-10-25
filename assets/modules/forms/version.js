
let axios = require('axios');
let swal = require('sweetalert2');
let CFloading = require('../CFloading/CFloading');

document.addEventListener('DOMContentLoaded', function () {

	let documentId = $('.document-version').attr('data-document-id');
	if(documentId){
		$('.show-version').click(function(){
			CFloading.start();
			axios.get(this.href)
				.then(response => {
					swal.fire({
						showCloseButton: true,
						showConfirmButton: false,
						html: response.data.html,
						onOpen(popup) {
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

	// Validation du form version
	if( null !== (form_version_document_transverse = document.getElementsByName('version_document_transverse')[0])){
		$(form_version_document_transverse).submit(function(e){

			// Checkbox "Supprimer"
			let deleteChecked = true === $('#version_document_transverse_filename1Vich_delete').prop('checked')

			// Fichier upload
			let fileUploadExists = '' != $('#version_document_transverse_filename1Vich_file').val()

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


});
