const PopupConfirmation = require('../../popups/popups-confirmation')

$(document).ready(function() {

	let reportModelListTable = $('#reportModelVersion-list-table')

	$('#reportMonitoring-publication').on('click', function() {

		let reportModelVersionIDs 	= PopupConfirmation.getSelectedItemsIDs(reportModelListTable)
		let url_process 			= Routing.generate('admin.report.model.publication', {reportModelVersionIDs: reportModelVersionIDs})
		let url_callback 			= Routing.generate('admin.report.model.index')

		if (reportModelVersionIDs > 0) {

			PopupConfirmation.createPopup(
				"Publication",
				"Attention, la publication de ces modèles rendra leur version précédente obsolète. La nouvelle version du modèle devra être configurée et activée au niveau de chaque projet (onglet Paramétrage > Rapport de monitoring) pour être utilisable",
				url_process,
				url_callback
			)
		}

	})

	$('#reportMonitoring-obsolete').on('click', function() {

		let reportModelVersionIDs 	= PopupConfirmation.getSelectedItemsIDs(reportModelListTable)
		let url_process 			= Routing.generate('admin.report.model.outdated', {reportModelVersionIDs: reportModelVersionIDs})
		let url_callback 			= Routing.generate('admin.report.model.index')

		console.log(url_process)

		if (reportModelVersionIDs > 0) {

			PopupConfirmation.createPopup (
				"Rendre obsolète des versions",
				"Les versions de modèle suivantes vont être déclarées obsolètes. Il ne sera plus possible de créer de rapport sur ces versions de modèle ni de les paramétrer. Cette action est irréversible.",
				url_process,
				url_callback
			)
		}
	})
});
