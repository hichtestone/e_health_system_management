// Tail select
let axios = require('axios');
let swal = require('sweetalert2');
let CFloading = require('../../CFloading/CFloading');

document.addEventListener('DOMContentLoaded', function() {

	// Supprimer la revue
	$('.deleteReviewActionBtn, .deleteReviewCrexActionBtn').click(function(){
		CFloading.start();
		axios.get(this.href)
			.then(response => {
				swal.fire({
					showCloseButton: true,
					showConfirmButton: false,
					html: response.data.html,
					onOpen(popup) {
						function refreshForm(){
							console.log('Annuler une action supplémentaire')
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
	});

})
