document.addEventListener('DOMContentLoaded', function() {
	// submissionDetails
	let typeSubmissionList = document.getElementById('submission_typeSubmission');

	if(typeSubmissionList) {

		let submissionAmendmentNumber = $('#submissionAmendmentNumber');
		let submissionDeclarationType = $('#submissionDeclarationType');

		function refreshSubType(){
			if($(typeSubmissionList).val() === '2'){
				console.log($(typeSubmissionList).val());
				submissionAmendmentNumber.css('display', '');
				submissionAmendmentNumber.find('select, input, textarea').prop('required', true);
				submissionAmendmentNumber.find('label').addClass('required');
			} else{
				submissionAmendmentNumber.css('display', 'none');
				submissionAmendmentNumber.find('select, input, textarea').prop('required', false);
				submissionAmendmentNumber.find('label').removeClass('required');
			}
			if($(typeSubmissionList).val() === '3'){
				console.log($(typeSubmissionList).val());
				submissionDeclarationType.css('display', '');
				submissionDeclarationType.find('select, input').prop('required', true);
				submissionDeclarationType.find('label').addClass('required');
			} else{
				submissionDeclarationType.css('display', 'none');
				submissionDeclarationType.find('select, input').prop('required', false);
				submissionDeclarationType.find('label').removeClass('required');
			}
		}
		refreshSubType();
		$(typeSubmissionList).change(function(){
			refreshSubType();
		});


		function setEvent(){
			$('#submission_nameSubmissionRegulatory').change(function(){
				if($(this).val() != ''){
					$(this).removeClass('is-invalid');
				}
			})
		}
		setEvent();

		$(document).on('change', '#submission_country, #submission_typeSubmissionRegulatory', function () {

			let $field = $(this)
			let $submissionCountry = $('#submission_country')
			let $form = $field.closest('form')
			let target = '#' + $field.attr('id')
				.replace('submission_typeSubmissionRegulatory', 'submission_nameSubmissionRegulatory')
				.replace('submission_country', 'submission_typeSubmissionRegulatory')

			// Les données à envoyer en Ajax
			let data = {}
			data[$submissionCountry.attr('name')] = $submissionCountry.val()
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
	}
})
