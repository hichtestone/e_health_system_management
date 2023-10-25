let Deviation = require('./../../Utils/Deviation')

$(document).ready(function() {

	let deviationSystemID 		= $('#deviation-system-id').val()
	let deviationSystemReviewID = $('#deviation-system-review-id').val()

	// delete review --------------------------------------------------------------------
	let deleteButton	= $('#deleteReviewDraftBtn')
	let deleteUrl 		= Routing.generate('deviation.system.review.delete', { deviationSystemID: deviationSystemID, deviationSystemReviewID: deviationSystemReviewID })
	let titleDelete 	= 'Suppression de la revue CREX de la déviation NC Système'
	let redirectDelete 	= '/no-conformity/deviation-system/' + deviationSystemID + '/review/list'

	Deviation.deleteDraft(deleteButton, deleteUrl, titleDelete, redirectDelete)

	// close review --------------------------------------------------------------------
	let closeButton		= $('#closeReviewBtn')
	let closeUrl 		= Routing.generate('deviation.system.review.close', { deviationSystemID: deviationSystemID, deviationSystemReviewID: deviationSystemReviewID })
	let titleClose		= 'Clôture de la revue CREX de la déviation NC Système'
	let redirectClose 	= '/no-conformity/deviation-system/' + deviationSystemID + '/review/list'
	let successMessage	= 'La revue à bien été clôturée'

	closeButton.on('click', function () {
		Deviation.close(closeUrl, titleClose, successMessage, redirectClose)
	})
})