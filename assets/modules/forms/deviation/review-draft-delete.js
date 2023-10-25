// Tail select
let axios = require('axios');
let swal = require('sweetalert2');
let CFloading = require('../../CFloading/CFloading');

document.addEventListener('DOMContentLoaded', function() {

	// Supprimer la revue
	$('#deleteReviewDraftBtn, #deleteReviewCrexDraftBtn').click(function(){
		CFloading.start();
		axios.get(this.href)
			.then(response => {
				swal.fire({
					showCloseButton: true,
					showConfirmButton: false,
					html: response.data.html,
					onOpen(popup) {
						function refreshForm(){
							console.log('Supprimer la revue')
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
