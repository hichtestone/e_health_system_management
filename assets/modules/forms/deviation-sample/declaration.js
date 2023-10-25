require('./deviation.scss');
import moment 		from "moment"
const TailSelect 	= require('tail.select')
let Deviation 		= require('./../../Utils/Deviation')

$(document).ready(function () {

	let deviationSampleID 		= $('#deviation-sample-id').val()
	let deviationSampleStatus 	= $('#deviation-sample-status').val()
	let url             		= Routing.generate('no_conformity.deviation.sample.declaration.brouillon.save', { deviationSampleID: deviationSampleID })
	let urlCreateAction 		= Routing.generate('no_conformity.deviation.sample.declaration.action.new', { deviationSampleID: deviationSampleID })
	let editMode        		= $('#edit-sample-mode').val()
	let isTailSelect    		= false
	let saveOnTheFly    		= editMode === 'edit' && deviationSampleStatus === '0'

	let observedAt					= $('#deviation_sample_declaration_observedAt')
	let occurrenceAt 				= $('#deviation_sample_declaration_occurrenceAt')
	let declaredBy 					= $('#deviation_sample_declaration_declaredBy')
	let projects 					= $('#deviation_sample_declaration_projects')
	let institutions 				= $('#deviation_sample_declaration_institutions')
	let detectionContext 			= $('#deviation_sample_declaration_detectionContext')
	let detectionContextReason 		= $('#deviation_sample_declaration_detectionContextReason')
	let detectionCenter 			= $('#deviation_sample_declaration_detectionCenter')
	let detectionCenterReason 		= $('#deviation_sample_declaration_detectionCenterReason')
	let processInvolves 			= $('#deviation_sample_declaration_processInvolves')
	let processInvolvesReason 		= $('#deviation_sample_declaration_processInvolvedReason')
	let natureType 					= $('#deviation_sample_declaration_natureType')
	let natureTypeReason 			= $('#deviation_sample_declaration_natureTypeReason')
	let resume						= $('#deviation_sample_declaration_resume')
	let causality 					= $('#deviation_sample_declaration_causality')
	let causalityReason 			= $('#deviation_sample_declaration_causalityReason')
	let causalityDescription 		= $('#deviation_sample_declaration_causalityDescription')
	let potentialImpactSample 		= $('#deviation_sample_declaration_potentialImpactSample')
	let potentialImpactSampleReason = $('#deviation_sample_declaration_potentialImpactSampleReason')
	let description 				= $('#deviation_sample_declaration_description')
	let decisionTaken 				= $('#deviation_sample_declaration_decisionTaken')
	let decisionFileVichFile 		= $('#deviation_sample_declaration_decisionFileVich_file')
	let decisionFileVichFileDelete 	= $('#deviation_sample_declaration_decisionFileVich_delete')
	let planAction                  = $('#plan-action')
	let efficiencyMeasure 			= $('#deviation_sample_declaration_efficiencyMeasure')
	let efficiencyJustify 			= $('#deviation_sample_declaration_efficiencyJustify')
	let notEfficiencyMeasureReason 	= $('#deviation_sample_declaration_notEfficiencyMeasureReason')
	let grade 						= $('#deviation_sample_declaration_grade')
	let btn  						= $('#deviation_and_sample_deviation')
	let associateDeviation 			= $('#associateDeviation')
	let linkCreateDeviationSample	= $('#link-create-action-deviation-sample')
	let deviationSampleCloseButton	= $('#deviation-sample-close')
	let deviationSampleCloseMultiButton	= $('#deviation-sample-close-multi')

	// manage editMode ----------------------------------------------------------------------------------------------------------

	if (editMode !== 'edit') {
		observedAt.attr('disabled', 'disabled')
		occurrenceAt.attr('disabled', 'disabled')
		declaredBy.attr('disabled', 'disabled')
		projects.attr('disabled', 'disabled')
		institutions.attr('disabled', 'disabled')
		detectionContext.attr('disabled', 'disabled')
		detectionContextReason.attr('disabled', 'disabled')
		detectionCenter.attr('disabled', 'disabled')
		detectionCenterReason.attr('disabled', 'disabled')
		processInvolves.attr('disabled', 'disabled')
		processInvolvesReason.attr('disabled', 'disabled')
		natureType.attr('disabled', 'disabled')
		natureTypeReason.attr('disabled', 'disabled')
		resume.attr('disabled', 'disabled')
		causality.find('input').attr('disabled', 'disabled')
		causalityReason.attr('disabled', 'disabled')
		causalityDescription.attr('disabled', 'disabled')
		potentialImpactSample.attr('disabled', 'disabled')
		description.attr('disabled', 'disabled')
		decisionTaken.attr('disabled', 'disabled')
		decisionFileVichFile.attr('disabled', 'disabled')
		decisionFileVichFileDelete.attr('disabled', 'disabled')
		efficiencyMeasure.attr('disabled', 'disabled')
		efficiencyJustify.attr('disabled', 'disabled')
		notEfficiencyMeasureReason.attr('disabled', 'disabled')
		grade.attr('disabled', 'disabled')
		planAction.find('*').attr('disabled', 'disabled')
		planAction.css('background-color', 'white')
	}

	if (editMode === 'edit') {
		$(document).trigger('changeGrade')
	}

	// manage grade change ---------------------------------------------------------------------------------------------------------------------

	$(document).on('changeGrade', function () {

		if (grade.val() === null || grade.val() === '' || grade.val() === '1' || grade.val() === 1) {

			efficiencyMeasure.attr('disabled', 'disabled').attr("required", "false").val(null)
			efficiencyJustify.attr('disabled', 'disabled').attr("required", "false").val(null)
			notEfficiencyMeasureReason.attr('disabled', 'disabled').attr("required", "false").val(null)

			$('#deviation-sample-plan-action a').remove()
			$('a.deviation-sample-action-edit').prop('disabled', true).css('pointer-events', 'none')
			$('a.deviation-sample-action-delete').prop('disabled', true).css('pointer-events', 'none')

		} else {

			efficiencyMeasure.removeAttr('disabled').attr("required", "false")
			if ($('#link-create-action-deviation-sample').length < 1) {
				$('a.deviation-sample-action-edit').prop('disabled', false).css('cursor', 'pointer')
				$('a.deviation-sample-action-delete').prop('disabled', false).css('cursor', 'pointer')
			}
		}
	})

	grade.on('change', function () {
		$(document).trigger('changeGrade')
	})

	// render tailSelect Projet(s) concerné(s), Etablissements, Processus impliqué(s) -------------------------------------------------------
	Deviation.renderTailSelectRequired(projects)
	Deviation.renderTailSelect(projects, 'Projet(s)')
	Deviation.renderTailSelect(institutions, 'Etablisement(s)')
	Deviation.renderTailSelectRequired(processInvolves)
	Deviation.renderTailSelect(processInvolves, 'Processus impliqué(s) ')

	// manage etablisement(s) --------------------------------------------------------------------------------------------------------------

	projects.on('change', function () {
		let $field = $(this)
		let $form = $field.closest('form')
		let target = '#' + $field.attr('id').replace('deviation_sample_declaration_projects', 'deviation_sample_declaration_institutions')

		// Les données à envoyer en Ajax
		let data = {}

		data[declaredBy.attr('name')] = declaredBy.val()
		//data[$causality.attr('name')] = $causality.val()
		data[grade.attr('name')] = grade.val()
		data[projects.attr('name')] = projects.val()
		data[efficiencyMeasure.attr('name')] = $field.val()
		data[$field.attr('name')] = $field.val()
		// On soumet les données
		$.post($form.attr('action'), data).then(function (data) {
			// On récupère le nouveau <tailSelect>
			let $input = $(data).find(target)
			// On remplace notre <tailSelect> actuel
			$(target).replaceWith($input)
			// hide old tailSelect
			$(target).next('div').css('display', 'none')
			$(target).closest('div').find('label').css('display', 'block');
			TailSelect($(target).get(0), {
				hideDisabled: true,
				placeholder: "Établissements(s)",
				search: true,
				multiple: true
			})
		})

		Deviation.renderTailSelectRequired(projects)
	})

	// manage Associate deviation protocolaire -----------------------------------------------------------------------------------

	$(document).on('change', ('#deviation_and_sample_deviation'), function () {
		let $field = $(this)
		let $form = $field.closest('form')
		let target = '#' + $field.attr('id').replace('deviation_and_sample_deviation', 'deviation_and_sample_resume')

		// Les données à envoyer en Ajax
		let data = {}
		data[$field.attr('name')] = $field.val()
		// On soumet les données
		$.post($form.attr('action'), data).then(function (data) {
			// On récupère le nouveau <input>
			let $input = $(data).find(target);
			// On remplace notre <input> actuel
			$(target).replaceWith($input);
		})
	})

	Deviation.activeButton(btn, associateDeviation)
	btn.on('change', function () {
		Deviation.activeButton(btn, associateDeviation)
	})

	// manage auto save draft mode field -----------------------------------------------------------------------------------------

	$(':input').on('change', function () {

		if (saveOnTheFly === true) {
			let InputFilled = $(this)
			let idName = $(this).attr('id')
			let idNameLength = $(this).attr('id').length

			let fieldNAmeTmp = idName.substr(29, idNameLength)
			let fieldName = fieldNAmeTmp.charAt(0).toUpperCase() + fieldNAmeTmp.slice(1)
			let fieldValue = $(this).val()

			if (fieldName === 'Causality_0' || fieldName === 'Causality_1' || fieldName === 'Causality_2') {
				fieldValue = $(this).prop('checked')
			}

			if (fieldName === 'DeclaredBy') {
				target = $('#declaredByJobDeviationSample')
				let declarantID = $('#deviation_sample_declaration_declaredBy').val()
				url = Routing.generate('user.get.job.xhr', { userID: declarantID })
				Deviation.getJob(target, url)
			} else {
				let tailSelects = ['deviation_sample_declaration_projects', 'deviation_sample_declaration_institutions', 'deviation_sample_declaration_processInvolves']
				Deviation.ajaxSendPersist(url, fieldName, fieldValue, InputFilled, 'sample', tailSelects)
			}
		}
	})

	// manage delete draft ------------------------------------------------------------------------------------------------------

	let target 			= $('#delete-deviation-sample-draft-popup')
	let deleteDraftUrl 	= Routing.generate('no_conformity.deviation.sample.declaration.brouillon.delete', { deviationSampleID: deviationSampleID })
	let title 			= 'Suppression d\'un brouillon de déviation échantillon biologique'
	let redirection 	= '/no-conformity/deviation-sample/'

	Deviation.deleteDraft(target, deleteDraftUrl, title, redirection)

	// manage efficiencyMeasure ----------------------------------------------------------------------------------------------------

	Deviation.efficiencyMeasure(efficiencyMeasure, efficiencyJustify, notEfficiencyMeasureReason, editMode);

	$(efficiencyMeasure).on('change', function () {
		Deviation.efficiencyMeasure(efficiencyMeasure, efficiencyJustify, notEfficiencyMeasureReason, editMode);
	});

	// manage champs "préciser" ----------------------------------------------------------------------------------------------------

	Deviation.reasonDescription(detectionContext, detectionContextReason, editMode, isTailSelect);
	Deviation.reasonDescription(detectionCenter, detectionCenterReason, editMode, isTailSelect);
	Deviation.reasonDescription(natureType, natureTypeReason, editMode, isTailSelect);
	Deviation.reasonDescription(processInvolves, processInvolvesReason, editMode, true);
	Deviation.reasonDescription(potentialImpactSample, potentialImpactSampleReason, editMode, isTailSelect);

	detectionContext.on('change', function () {
		Deviation.reasonDescription(detectionContext, detectionContextReason, editMode, isTailSelect);
	})

	detectionCenter.on('change', function () {
		Deviation.reasonDescription(detectionCenter, detectionCenterReason, editMode, isTailSelect);
	})

	natureType.on('change', function () {
		Deviation.reasonDescription(natureType, natureTypeReason, editMode, isTailSelect);
	})

	processInvolves.on('change', function () {
		Deviation.renderTailSelectRequired(processInvolves)
		Deviation.reasonDescription(processInvolves, processInvolvesReason, editMode, true);
	})

	potentialImpactSample.on('change', function () {
		Deviation.reasonDescription(potentialImpactSample, potentialImpactSampleReason, editMode, isTailSelect);
	})

	// manage causality -----------------------------------------------------------------------------------------------------------

	let causalities = $('input[type="checkbox"][name="deviation_sample_declaration[causality][]"]:checked').map(function() { return this.value; }).get();
	let others = ['0', '1']
	let disableDescription = false
	let count = 0;

	causalities.map((item) => {
		if (others.includes(item)) {
			count++
		}
	})

	if (count === others.length) {
		disableDescription = true
	}

	if (causalities.length === 0 || disableDescription) {
		causalityReason.attr("disabled", "disabled").attr("required", "false").val(null)
	}

	causality.on('change',(event) => {
		if (causalities.indexOf(event.target.value) < 0) {
			causalities.push(event.target.value)
		} else {
			let index = causalities.indexOf(event.target.value)
			causalities.splice(index, 1)
		}
		let other = '2'

		if(causalities.includes(other)) {
			causalityReason.removeAttr('disabled')
		} else {
			causalityReason.attr("disabled", "disabled").attr("required", "false").val(null)
		}
	});

	// manage decisionTaken ------------------------------------------------------------------------------------------------------------------

	decisionTaken.on('change', function (event) {
		if (event.target.value > 0) {
			$('.currentDate').text(moment(Date.now()).format('DD/MM/YYYY'))
		} else {
			$('.currentDate').text('')
		}
	})

	// manage tooltip décision --------------------------------------------------------------------------------------------------------

	let labelDecisionTaken = $('label[for="deviation_sample_declaration_decisionTaken"]')
	$(labelDecisionTaken).after('&nbsp;&nbsp;'+Deviation.tooltip("Réqualification des échantillons : désétiquetage de l'échantillon et retrait du lien existant avec l'étude clinique."))

	let labelDecisionFile = $('#deviation-sample-decision-file')
	$(labelDecisionFile).append(Deviation.tooltip("Joindre un fichier avec : Code CRB, N°patient, identifiant du centre et type d'échantillons.'"))

	// manage close -------------------------------------------------------------------------------------------------------------------

	if (deviationSampleCloseButton.length > 0) {

		deviationSampleCloseButton.on('click', function () {

			let url = Routing.generate('no_conformity.deviation.sample.declaration.close', {deviationSampleID: deviationSampleID});
			let title = 'Clôture de la déviation échantillon biologique';
			let successMessageHtml = '<div class="text-success">la déviation échantillon biologique a été cloturée avec succès.</div>';
			let redirection = Routing.generate('no_conformity.deviation.sample.declaration', {deviationSampleID: deviationSampleID});

			Deviation.close(url, title, successMessageHtml, redirection);
		});
	}

	// manage close multi -------------------------------------------------------------------------------------------------------------------

	if (deviationSampleCloseMultiButton.length > 0) {

		deviationSampleCloseMultiButton.on('click', function () {

			let url = Routing.generate('no_conformity.deviation.sample.declaration.multiple');
			let title = 'Clôture de la déviation échantillon biologique';
			let redirection = Routing.generate('no_conformity.deviation.sample.index');
			let successMessageHtml = '<div class="text-success">la déviation échantillon biologique a été cloturée avec succès.</div>';
			let deviationsSampleListTable = $('#deviation-sample-list-table');

			Deviation.closeMulti(url, title, successMessageHtml, redirection, deviationsSampleListTable, false, false, true);
		});
	}
})
