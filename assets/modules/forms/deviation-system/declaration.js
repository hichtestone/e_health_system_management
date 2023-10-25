require('./declaration.scss');
let swal 		 = require('sweetalert2')
const TailSelect = require('tail.select')
let Deviation 	 = require('./../../Utils/Deviation')

$(document).ready(function() {

	let deviationSystemID 			= $('#deviation-system-id').val()
	let url             			= Routing.generate('deviation.system.declaration.brouillon.save', { deviationSystemID: deviationSystemID})
	let deviationStatus 			= $('#deviation-system-status').val()
	let editMode        			= $('#edit-mode').val()
	let editQA        				= $('#edit-qa').val()
	let saveOnTheFly    			= editMode === 'edit' && deviationStatus === '0'

	// identification Block ----------------------------------------------------------------------------
	let identificationBlock			= $('#identification')
	let observedAt					= $('#deviation_system_declaration_observedAt')
	let declaredBy					= $('#deviation_system_declaration_declaredBy')
	let process						= $('#deviation_system_declaration_process')
	// identification QA Block -------------------------------------------------------------------------
	let identificationQABlock		= $('#identification-qa')
	let activity					= $('#deviation_system_declaration_activity')
	let referentQA					= $('#deviation_system_declaration_referentQA')
	let refISO9001					= $('#deviation_system_declaration_refISO9001')
	let documentQA					= $('#deviation_system_declaration_document')
	// description Block -------------------------------------------------------------------------------
	let descriptionBlock			= $('#description')
	let resume 						= $('#deviation_system_declaration_resume')
	let description					= $('#deviation_system_declaration_description')
	// causes Block ------------------------------------------------------------------------------------
	let causesBlock					= $('#causes')
	let causality					= $('#deviation_system_declaration_causality')
	let causalityDescription		= $('#deviation_system_declaration_causalityDescription')
	let grade 						= $('#deviation_system_declaration_grade')
	let potentialImpact 			= $('#deviation_system_declaration_potentialImpact')
	let potentialImpactDescription 	= $('#deviation_system_declaration_potentialImpactDescription')
	// corrections Block --------------------------------------------------------------------------------
	let correctionBlock				= $('#corrections')
	// quality assurance Block --------------------------------------------------------------------------
	let qualityAssuranceBlock		= $('#quality-assurance')
	let visaPilotProcessChiefQA		= $('#deviation_system_declaration_visaPilotProcessChiefQA')
	let visaAt						= $('#deviation_system_declaration_visaAt')
	let officialQA					= $('#deviation_system_declaration_officialQA')
	// plan action Block --------------------------------------------------------------------------------
	let planActionBlock				= $('#plan-action-system')
	let efficiencyMeasure			= $('#deviation_system_declaration_efficiencyMeasure')
	let efficiencyJustify			= $('#deviation_system_declaration_efficiencyJustify')
	let noEfficiencyMeasureReason	= $('#deviation_system_declaration_notEfficiencyMeasureReason')
	// others ------------------------------------------------------------------------------------
	let deleteDraftButton			= $('#delete-draft-popup')
	let isQaNcSystemGranted			= $('#is-qa-nc-system-granted').val()


	let deviationSystemCloseButton	= $('#deviation-system-close')
	let deviationSystemCloseMultiButton	= $('#deviation-system-close-multi')

	// tailSelect pour process field
	if (process.length) {
		process.closest('div').find('label').css('display', 'block')
		TailSelect(process.get(0), {
			hideDisabled: true,
			placeholder: "Process",
			search: false,
			multiple: true
		})
	}

	Deviation.renderTailSelectRequired(process)

	process.on('change', function () {
		Deviation.renderTailSelectRequired(process)
	})

	efficiencyMeasure.on('change', function () {
		Deviation.efficiencyMeasure(efficiencyMeasure, efficiencyJustify, noEfficiencyMeasureReason, editMode)
	})
	Deviation.efficiencyMeasure(efficiencyMeasure, efficiencyJustify, noEfficiencyMeasureReason, editMode)


	// manage Edit et QA mode -----------------------------------------------------------------------

	if (editMode !== 'edit') {

		// identification ----------------------
		observedAt.attr('disabled', 'disabled')
		declaredBy.attr('disabled', 'disabled')
		// identificationQABlock --------------------
		activity.attr('disabled', 'disabled')
		referentQA.attr('disabled', 'disabled')
		refISO9001.attr('disabled', 'disabled')
		documentQA.attr('disabled', 'disabled')
		// description -------------------------
		resume.attr('disabled', 'disabled')
		description.attr('disabled', 'disabled')
		// causesBlock ------------------------------
		causality.attr('disabled', 'disabled')
		causality.find('input').attr('disabled', 'disabled')
		causalityDescription.attr('disabled', 'disabled')
		grade.attr('disabled', 'disabled')
		potentialImpact.attr('disabled', 'disabled')
		potentialImpactDescription.attr('disabled', 'disabled')
		// quality assurance -------------------
		visaPilotProcessChiefQA.attr('disabled', 'disabled')
		visaAt.attr('disabled', 'disabled')
		officialQA.attr('disabled', 'disabled')
		// plan action -------------------------
		planActionBlock.find('*').attr('disabled', 'disabled')
		planActionBlock.css('background-color', 'white')

	} else if (editMode === 'edit' && editQA !== 'qa') {

		causesBlock.find('*').attr('disabled', 'disabled')
		causesBlock.css('background-color', 'lightgray')

		identificationQABlock.find('*').attr('disabled', 'disabled')
		identificationQABlock.css('background-color', 'lightgray')

		qualityAssuranceBlock.find('*').attr('disabled', 'disabled')
		qualityAssuranceBlock.css('background-color', 'lightgray')

	} else if (editMode === 'edit' && editQA === 'qa') {

		identificationBlock.find('*').attr('disabled', 'disabled')
		identificationBlock.css('background-color', 'lightgray')

		descriptionBlock.find('*').attr('disabled', 'disabled')
		descriptionBlock.css('background-color', 'lightgray')

		correctionBlock.find('*').attr('disabled', 'disabled')
		correctionBlock.css('background-color', 'lightgray')
	}

	// manage Grade / efficiency ---------------------------------------------------------------------

	$(document).on('changeGrade', function () {

		let gradeValue = null
		if (editMode === 'edit' && editQA !== 'qa') {
			gradeValue = $('#deviation_system_declaration_grade option:selected').val()
		} else {
			gradeValue = grade.val()
		}

		if (gradeValue === null || gradeValue === '' || gradeValue === '1' || gradeValue === 1) {

			planActionBlock.find('*').attr('disabled', 'disabled')
			planActionBlock.css('background-color', 'lightgray')

		} else {

			planActionBlock.find('*').removeAttr('disabled')
			planActionBlock.css('background-color', 'white')

			Deviation.efficiencyMeasure(efficiencyMeasure, efficiencyJustify, noEfficiencyMeasureReason, editMode)
		}
	})

	if (editMode === 'edit') {
		$(document).trigger('changeGrade')
	}

	grade.on('change', function () {
		$(document).trigger('changeGrade')
	})

	// render tailSelect Processus(s) -----------------------------------------------------------

	Deviation.renderTailSelect(process, 'Processus')

	// // Manage Potential Impact detail --------------------------------------------------------

	if (potentialImpact.val() === '' || potentialImpact.val() !== '99') {
		potentialImpactDescription.attr('disabled', 'disabled')
	} else {
		// just for 'others' options
		potentialImpactDescription.removeAttr('disabled')
	}

	$(document).on('potentialImpactDetail', function() {

		if (potentialImpact.val() === '' || potentialImpact.val() !== '99') {
			potentialImpactDescription.attr('disabled', 'disabled')
		} else if (potentialImpact.val() === '99') {
			// just for 'others' options
			potentialImpactDescription.removeAttr('disabled')
		}
	})

	potentialImpact.on('change', function () {
		$(document).trigger('potentialImpactDetail')
	})

	// manage auto save draft mode field -----------------------------------------------------

	$(':input').on('change', function () {

		if (saveOnTheFly === true) {

			let InputFilled  = $(this)
			let idName 		 = $(this).attr('id')
			let idNameLength = $(this).attr('id').length

			let fieldNAmeTmp = idName.substr(29, idNameLength)
			let fieldName 	 = fieldNAmeTmp.charAt(0).toUpperCase() + fieldNAmeTmp.slice(1)
			let fieldValue 	 = $(this).val();

			if (fieldName === 'PotentialImpact') {
				$(document).trigger('potentialImpactDetail');
			}

			if (fieldName === 'Causality_0' || fieldName === 'Causality_1' || fieldName === 'Causality_2') {
				fieldValue = $(this).prop('checked')
			}

			// send value
			if (fieldName === 'Resume' || fieldName === 'Description' || fieldName === 'PotentialImpactDescription' || fieldName === 'CausalityDescription') {
				$(this).one('blur', function () {
					Deviation.ajaxSendPersist(url, fieldName, fieldValue, InputFilled, 'system')
				})
			} else {
				Deviation.ajaxSendPersist(url, fieldName, fieldValue, InputFilled, 'system')
			}
		}
	})

	// manage delete draft --------------------------------------------------------------------

	Deviation.deleteDraft(
		deleteDraftButton,
		Routing.generate('deviation.system.declaration.brouillon.delete', { deviationSystemID: deviationSystemID }),
		'Suppression d\'un brouillon de déviation système',
		'/no-conformity/deviation-system/'
	)

	// manage cloture deviation ---------------------------------------------------------------

	$('#deviation-closure').on('click', function () {

		$.ajax({
			url: Routing.generate('deviation.system.close', { deviationSystemID: deviationSystemID }),
			type: "POST",
			dataType:'json',
			data: {confirm: 0},
			success: function(response) {

				if (response.messageStatus === 'OK') {

					swal.fire({
						title: 'Cloture de déviation',
						showCloseButton: true,
						showConfirmButton: true,
						showCancelButton: true,
						confirmButtonText: 'Confirmer',
						cancelButtonText:'Annuler',
						showLoaderOnConfirm: true,
						html: response.html,
						allowOutsideClick: () => !swal.isLoading(),
						preConfirm: function() {

							$.ajax({
								url: Routing.generate('deviation.system.close', { deviationSystemID: deviationSystemID }),
								type: "POST",
								dataType:'json',
								data: {confirm: 1},
								success: function (response) {

									if (response.messageStatus === 'OK') {

										swal.fire({
											html: `<div class="text-success">la deviation a été cloturée avec success.</div>`,
											timer: 2000,
											timerProgressBar: true,
										})

										$('.swal2-confirm').remove()

										setTimeout(function (){
											window.location.href =  '/no-conformity/deviation-system/'
										}, 2000)

									} else {

										let html = '<div>' + response.messageError + '<br></div><br>'

										response.isClosableMessages.forEach(function (current) {
											html += current + '<br>'
										})

										$('#swal2-content .admin-block').after(html)
										$('.swal2-confirm').remove()
										$('.swal2-cancel').remove()
									}
								}
							})

							return false
						},
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
	});


	// manage close -------------------------------------------------------------------------------------------------------------------

	if (deviationSystemCloseButton.length > 0) {

		deviationSystemCloseButton.on('click', function () {

			let url = Routing.generate('deviation.system.close', {deviationSystemID: deviationSystemID})
			let title = 'Clôture de la déviation NC Système'
			let successMessageHtml = '<div class="text-success">la déviation NC système a été clôturée avec succès.</div>'
			let redirection = Routing.generate('deviation.system.declaration', {deviationSystemID: deviationSystemID})

			Deviation.close(url, title, successMessageHtml, redirection)
		});
	}


	// manage close multi -------------------------------------------------------------------------------------------------------------------

	if (deviationSystemCloseMultiButton.length > 0) {

		deviationSystemCloseMultiButton.on('click', function () {

			let url = Routing.generate('deviation.system.multiple')
			let title = 'Clôture de la déviation NC Système'
			let redirection = Routing.generate('deviation.system.list')
			let successMessageHtml = '<div class="text-success">la déviation NC système a été clôturée avec succès.</div>'
			let deviationsSampleListTable = $('#deviation-system-list-table')

			Deviation.closeMulti(url, title, successMessageHtml, redirection, deviationsSampleListTable, false, true,  false);
		});
	}
});
