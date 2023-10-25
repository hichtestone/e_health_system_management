let swal = require('sweetalert2');

$(document).ready(function() {

	let deviationClosuresButton = $('#deviation-multi-close')
	let deviationListTable 		= $('#deviation-list-table')
	let deviationIDs 			= []
	let projectID				= $('#project-id').val()

	$(deviationClosuresButton).on('click', function (event) {

		deviationIDs = []
		deviationListTable.find('input:checkbox:checked').each(function (item) {
			deviationIDs.push($(this).closest('tr').attr('data-id'));
		})

		if (deviationIDs.length > 0) {

			$.ajax({
				url: Routing.generate('deviation.close.multiple', { projectID: projectID }),
				type: "POST",
				dataType:'json',
				data: {
					confirm: 0,
					items: deviationIDs
				},
				success: function(response) {

					if (response.messageStatus === 'OK') {

						swal.fire({
							title: 'Clôtures de déviations',
							showCloseButton: true,
							showConfirmButton: true,
							showCancelButton: true,
							confirmButtonText: 'Confirmer',
							cancelButtonText:'Annuler',
							html: response.html,
							preConfirm: function() {

								$.ajax({
									url: Routing.generate('deviation.close.multiple', {projectID: projectID}),
									type: "POST",
									dataType: 'json',
									data: {
										confirm: 1,
										items: deviationIDs
									},
									success: function (response) {

										if (response.messageStatus === 'OK') {

											swal.fire({
												html: `<div class="text-success">la clôture a été effectuée avec success.</div>`,
											})

											$('.swal2-confirm').remove()

											setTimeout(function () {
												window.location.href = '/projects/' + projectID + '/deviation'
											}, 2000)

										} else {

											let html = '<div>' + "Les déviations suivantes non pas pû être clôturées:" + '<br></div><br>'

											response.deviationsInfo.forEach((item) => {
												html += '<p>' + item.code + '</p>'
												html += '<ul>'

												item.isClosableMessages.forEach((message) => {
													html += '<li>' + message + '</li>'
												})

												html += '</ul>'
											})

											$('#swal2-content .admin-block').after(html)
											$('.swal2-confirm').remove()
											$('.swal2-cancel').remove()
										}
									}
								})

								return false
							}
						})
					} else {
						console.log('err')
					}
				},
				error: function(response) {
					console.log('error')
					console.log(response)
					console.log(response.statusLabel + ' :' + response.errorMessage)
				}
			});

			event.preventDefault();
			event.stopPropagation();
			return false

		} else {

		}

		event.preventDefault();
		event.stopPropagation();
		return false
	})
})