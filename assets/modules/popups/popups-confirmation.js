let swal = require('sweetalert2');

function PopupConfirmation() {

	this.createPopup = function popupConfirmation (title, message, url_process, url_callback) {

		swal.fire({
			title: title,
			showCloseButton: true,
			showConfirmButton: true,
			showCancelButton: true,
			text: message,

		}).then((result) => {

			if (result.isConfirmed) {

				$.ajax({
					url: url_process,
					type: "POST",
					dataType: 'json',
					success: function (response) {

						if (response.messageStatus === 'OK') {

							swal.fire({
								title: title,
								showCloseButton: false,
								showConfirmButton: false,
								showCancelButton: false,
								timer: 2000,
								timerProgressBar: true,
								text: 'action effectuée avec succès',
							}).then(() => {
								window.location = url_callback
							})

						} else {

							swal.fire({
								title: title,
								showCloseButton: false,
								showConfirmButton: false,
								showCancelButton: false,
								text: response.messageErrors,
							})
						}
					},
					error: function (response) {

						swal.fire({
							title: title,
							showCloseButton: false,
							showConfirmButton: false,
							showCancelButton: false,
							text: response.messageErrors,
						})
					}
				})
			}
		})
	}

	this.getSelectedItemsIDs = function getSelectedItemsIDs (listTable) {

			selectedIDs = []
			listTable.find('input:checkbox:checked').each(function () {
				selectedIDs.push($(this).closest('tr').attr('data-id'));
			})

			return selectedIDs
		}
}

module.exports = new PopupConfirmation();
