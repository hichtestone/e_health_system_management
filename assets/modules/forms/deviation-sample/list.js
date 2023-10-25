let swal 		= require('sweetalert2')
let Deviation 	= require('./../../Utils/Deviation')

$(document).ready(function() {

	let deviationClosuresButton	= $('#deviation-sample-close-multi')

	deviationClosuresButton.on('click', function () {

		let title 						= 'Clôture des déviations échantillon biologique'
		let url 						= Routing.generate('no_conformity.deviation.sample.declaration.multiple')
		let redirection 				= Routing.generate('no_conformity.deviation.sample.index')
		let successMessageHtml 			= '<div class="text-success">la déviation échantillon biologique a été cloturée avec succès.</div>'
		let deviationsSampleListTable 	= $('#deviation-sample-list-table')

		Deviation.closeMulti(deviationClosuresButton, url, title, successMessageHtml, redirection, deviationsSampleListTable)
	})
})