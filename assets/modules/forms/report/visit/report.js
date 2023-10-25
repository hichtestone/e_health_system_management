document.addEventListener('DOMContentLoaded', function() {

	function setEvent(){
		if($('#report_visit_reportConfigVersion > option').length === 1){
			$('#report_visit_reportConfigVersion').addClass('is-invalid');
		}
	}

	setEvent();

	$(document).on('change', '#report_visit_reportType, #report_visit_reportConfigVersion', function () {
		let $field = $(this)
		let $reportType = $('#report_visit_reportType')
		let $form = $field.closest('form')
		let target = '#' + $field.attr('id').replace('report_visit_reportType', 'report_visit_reportConfigVersion')

		// Les données à envoyer en Ajax
		let data = {}
		data[$reportType.attr('name')] = $reportType.val()
		data[$field.attr('name')] = $field.val()
		// On soumet les données
		$.post($form.attr('action'), data).then(function (data) {
			// On récupère le nouveau <select>
			let $input = $(data).find(target);
			// On remplace notre <select> actuel
			$(target).replaceWith($input);
			setEvent();
		})
	})
})
