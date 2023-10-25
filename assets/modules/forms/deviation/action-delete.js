// Tail select
let axios = require('axios');
let swal = require('sweetalert2');
let CFloading = require('../../CFloading/CFloading');

document.addEventListener('DOMContentLoaded', function() {
	$('.deviation-action-delete').click(function(){
		CFloading.start();
		axios.get(this.href)
			.then(response => {
					swal.fire({
						showCloseButton: true,
						showConfirmButton: false,
						html: response.data.html,
						onOpen(popup) {
							function refreshForm(){
								console.log('Supprimer une action')
							}

							refreshForm()

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

	})

})
