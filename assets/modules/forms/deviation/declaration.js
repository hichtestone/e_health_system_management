require('./declaration.scss');
let Deviation = require('./../../Utils/Deviation');

$(document).ready(function () {

	let projectID 		= $('#project-id').val();
	let deviationID 	= $('#deviation-id').val();
	let url 			= Routing.generate('deviation.declaration.brouillon.save', {projectID: projectID, deviationID: deviationID});
	let deviationStatus = $('#deviation-status').val();
	let editMode 		= $('#edit-mode').val();
	let saveOnTheFly 	= editMode === 'edit' && deviationStatus === '0';

	let grade 								= $('#deviation_declaration_grade');
	let observedAt 							= $('#deviation_declaration_observedAt');
	let occurenceAt 						= $('#deviation_declaration_occurenceAt');
	let declaredBy 							= $('#deviation_declaration_declaredBy');
	let type 								= $('#deviation_declaration_type');
	let typeCode 							= $('#deviation_declaration_typeCode');
	let subType 							= $('#deviation_declaration_subType');
	let subTypeCode 						= $('#deviation_declaration_subTypeCode');
	let center 								= $('#deviation_declaration_center');
	let institution 						= $('#deviation_declaration_institution');
	let patient 							= $('#deviation_declaration_patient');
	let resume 								= $('#deviation_declaration_resume');
	let description 						= $('#deviation_declaration_description');
	let potentialImpact 					= $('#deviation_declaration_potentialImpact');
	let potentialImpactDescription 			= $('#deviation_declaration_potentialImpactDescription');
	let causality 							= $('#deviation_declaration_causality');
	let causalityDescription 				= $('#deviation_declaration_causalityDescription');
	let planAction 							= $('#plan-action');
	let efficiencyMeasure 					= $('#deviation_declaration_efficiencyMeasure');
	let efficiencyJustify 					= $('#deviation_declaration_efficiencyJustify');
	let noEfficiencyMeasureReason 			= $('#deviation_declaration_notEfficiencyMeasureReason');
	let linkCreateAction 					= $('#link-create-action');
	let btn 								= $('#deviation_and_sample_deviationSample');
	let associateDeviationSample 			= $('#associateDeviationSample');
	let deviationProtocolCloseMultiButton 	= $('#deviation-protocol-multi-close');

	efficiencyMeasure.on('change', function () {
		Deviation.efficiencyMeasure(efficiencyMeasure, efficiencyJustify, noEfficiencyMeasureReason, editMode);
	});
	Deviation.efficiencyMeasure(efficiencyMeasure, efficiencyJustify, noEfficiencyMeasureReason, editMode);

	// manage EditMode -----------------------------------------------------------------------

	if (editMode !== 'edit') {
		observedAt.attr('disabled', 'disabled');
		occurenceAt.attr('disabled', 'disabled');
		declaredBy.attr('disabled', 'disabled');
		type.attr('disabled', 'disabled');
		typeCode.attr('disabled', 'disabled');
		subType.attr('disabled', 'disabled');
		subTypeCode.attr('disabled', 'disabled');
		center.attr('disabled', 'disabled');
		institution.attr('disabled', 'disabled');
		patient.attr('disabled', 'disabled');
		resume.attr('disabled', 'disabled');
		description.attr('disabled', 'disabled');
		potentialImpact.attr('disabled', 'disabled');
		potentialImpactDescription.attr('disabled', 'disabled');
		causality.attr('disabled', 'disabled');
		causality.find('input').attr('disabled', 'disabled');
		causalityDescription.attr('disabled', 'disabled');
		grade.attr('disabled', 'disabled');
		planAction.find('*').attr('disabled', 'disabled');
		planAction.css('background-color', 'white');
	}

	// manage Grade / efficiency ---------------------------------------------------------------------
	$(document).on('changeGrade', function () {

		if (grade.val() === null || grade.val() === '' || grade.val() === '1' || grade.val() === 1) {

			planAction.find('*').attr('disabled', 'disabled');
			planAction.css('background-color', 'lightgray');

		} else {

			planAction.find('*').removeAttr('disabled');
			planAction.css('background-color', 'white');

			Deviation.efficiencyMeasure(efficiencyMeasure, efficiencyJustify, noEfficiencyMeasureReason, editMode);
		}
	});

	if (editMode === 'edit') {

		$(document).trigger('changeGrade');
		typeCode.attr('disabled', 'disabled');
		subTypeCode.attr('disabled', 'disabled');
	}

	grade.on('change', function () {
		$(document).trigger('changeGrade');
	});

	// Manage user job -----------------------------------------------------------------------
	$(document).on('changeJob', function () {

		let declarantID = declaredBy.val();
		getJob(declarantID);

	});

	// Manage Potential Impact detail --------------------------------------------------------
	if (potentialImpact.val() === '' || potentialImpact.val() !== '99') {
		potentialImpactDescription.attr('disabled', 'disabled');
	} else {
		// just for 'others' options
		potentialImpactDescription.removeAttr('disabled');
	}

	$(document).on('potentialImpactDetail', function () {

		if (potentialImpact.val() === '' || potentialImpact.val() !== '99') {
			potentialImpactDescription.attr('disabled', 'disabled');
		} else if (potentialImpact.val() === '99') {
			// just for 'others' options
			potentialImpactDescription.removeAttr('disabled');
		}
	});

	potentialImpact.on('change', function () {
		$(document).trigger('potentialImpactDetail');
	});

	// Manage Types ------------------------------------------------------------------------
	let typeValue = type.val();
	let subTypeValue = subType.val();

	if (typeValue !== '') {
		$('#deviation_declaration_typeCode > option').removeAttr("selected");
		typeCode.find('option[value=' + typeValue + ']').attr("selected", "selected");
	}

	if (subTypeValue !== '') {
		$('#deviation_declaration_subTypeCode > option').removeAttr("selected");
		subTypeCode.find('option[value=' + subTypeValue + ']').attr("selected", "selected");
	}

	if (typeValue !== undefined && subTypeValue === undefined) {
		getSubTypes(typeValue);
	}

	type.on('change', function () {

		let typeID = $(this).val();

		if (typeID) {

			$('#deviation_declaration_typeCode > option').removeAttr("selected");
			typeCode.find('option[value=' + typeID + ']').attr("selected", "selected");

			getSubTypes(typeID);

		} else {

			let html = '';
			html += "<option value=" + '' + ">" + "\<\< Sous-Type \>\>" + "</option>";
			subType.html(html);

			$('#deviation_declaration_typeCode > option').removeAttr("selected");
			$('#deviation_declaration_subTypeCode > option').removeAttr("selected");
			subType.attr("disabled", "disabled");
		}

	});

	subType.on('change', function () {

		let subTypeID = $(this).val();

		if (subTypeID) {

			$('#deviation_declaration_subTypeCode > option').removeAttr("selected");
			subTypeCode.find('option[value=' + subTypeID + ']').attr("selected", "selected");

		} else {

			$('#deviation_declaration_subTypeCode > option').removeAttr("selected");
		}

	});

	// manage center, institution, patient ---------------------------------------------------
	center.on('change', function () {

		let centerID = $(this).val();

		if (centerID) {

			$('#deviation_declaration_center > option').removeAttr("selected");
			center.find('option[value=' + centerID + ']').attr("selected", "selected");

			getInstitutions(centerID);
			getPatients(centerID);

		} else {

			let html = '';
			html += "<option value=" + '' + ">" + "\<\< Institutions \>\>" + "</option>";
			institution.html(html);

			$('#deviation_declaration_center > option').removeAttr("selected");
			$('#deviation_declaration_institution > option').removeAttr("selected");
			institution.attr("disabled", "disabled");

			html = '';
			html += "<option value=" + '' + ">" + "\<\< Patients \>\>" + "</option>";
			patient.html(html);

			$('#deviation_declaration_patient > option').removeAttr("selected");
			patient.attr("disabled", "disabled");
		}
	});

	// manage Associate deviation sample ------------------------------------------------

	$(document).on('change', ('#deviation_and_sample_deviationSample'), function () {
		let $field = $(this);
		let $form = $field.closest('form');
		let target = '#' + $field.attr('id').replace('deviation_and_sample_deviationSample', 'deviation_and_sample_resume');

		// Les données à envoyer en Ajax
		let data = {};
		data[$field.attr('name')] = $field.val();
		// On soumet les données
		$.post($form.attr('action'), data).then(function (data) {
			// On récupère le nouveau <input>
			let $input = $(data).find(target);
			// On remplace notre <input> actuel
			$(target).replaceWith($input);
		});
	});

	Deviation.activeButton(btn, associateDeviationSample);
	btn.on('change', function () {
		Deviation.activeButton(btn, associateDeviationSample);
	});

	// manage auto save draft mode field -----------------------------------------------------
	$(':input').on('change', function () {

		if (saveOnTheFly === true) {

			let InputFilled = $(this);
			let idName = $(this).attr('id');
			let idNameLength = $(this).attr('id').length;

			let fieldNAmeTmp = idName.substr(22, idNameLength);
			let fieldName = fieldNAmeTmp.charAt(0).toUpperCase() + fieldNAmeTmp.slice(1);
			let fieldValue = $(this).val();

			// special behaviour by field
			if (fieldName === 'DeclaredBy') {
				let target = $('#declaredByJob');
				let declarantID = declaredBy.val();
				let url = Routing.generate('user.get.job.xhr', {userID: declarantID});
				Deviation.getJob(target, url);
			}

			if (fieldName === 'PotentialImpact') {
				$(document).trigger('potentialImpactDetail');
			}

			if (fieldName === 'Causality_0' || fieldName === 'Causality_1' || fieldName === 'Causality_2') {
				fieldValue = $(this).prop('checked');
			}

			// send value
			if (fieldName === 'Resume' || fieldName === 'Description' || fieldName === 'PotentialImpactDescription' || fieldName === 'CausalityDescription') {
				$(this).one('blur', function () {
					ajaxSendPersist(fieldName, fieldValue, InputFilled);
				});
			} else {
				ajaxSendPersist(fieldName, fieldValue, InputFilled);

				if (fieldName === 'Type') {
					ajaxSendPersist('SubType', null, subType);
				}

				if (fieldName === 'Center') {
					ajaxSendPersist('Institution', null, institution);
					ajaxSendPersist('Patient', null, patient);
				}
			}
		}
	});


	// manage delete draft -------------------------------------------------------------------------------

	let target = $('#delete-draft-popup');
	let deleteDraft = Routing.generate('deviation.declaration.brouillon.delete', {
		projectID: projectID,
		deviationID: deviationID
	});
	let title = 'Suppression d\'un brouillon de déviation';
	let redirection = '/projects/' + projectID + '/deviation';

	Deviation.deleteDraft(target, deleteDraft, title, redirection);


	// manage close ----------------------------------------------------------------------

	if ($('#deviation-closure').on('click', function () {

		url = Routing.generate('deviation.close', {projectID: projectID, deviationID: deviationID});
		title = 'Clôture de la déviation protocolaire';
		let successMessageHtml = '<div class="text-success">la déviation protocolaire été clôturée avec succès.</div>';
		redirection = '/projects/' + projectID + '/deviation';

		Deviation.close(url, title, successMessageHtml, redirection);
	}))

		// manage close multi -------------------------------------------------------------------------------------------------------------------

		if (deviationProtocolCloseMultiButton.length > 0) {

			deviationProtocolCloseMultiButton.on('click', function () {
				let url = Routing.generate('deviation.close.multiple')
				let title = 'Clôture de la déviation protocolaire'
				let redirection = Routing.generate('deviation.list', {'projectID' : projectID})
				let successMessageHtml = '<div class="text-success">la déviation protocolaire a été clôturée avec succès.</div>'
				let deviationsSampleListTable = $('#deviation-list-table')

				Deviation.closeMulti(url, title, successMessageHtml, redirection, deviationsSampleListTable, true, false, false)
			});
		}


	// tools functions ------------------------------------------------------------------------

	function ajaxSendPersist(fieldName, fieldValue, InputFilled) {

		$.ajax({
			url: url,
			type: "POST",
			dataType: 'json',
			data: JSON.stringify({'fieldName': fieldName, 'fieldValue': fieldValue}),
			success: function (response) {

				if (response.messageStatus === 'KO') {
					console.log('KO');
				} else {
					let inputID = InputFilled.attr('id');
					let checkNode = '';

					if (inputID !== 'deviation_declaration_causality_0' && inputID !== 'deviation_declaration_causality_1' && inputID !== 'deviation_declaration_causality_2') {
						checkNode = '<span id="checkNode" style="margin-left: 10px;"><i class="fa fa-check"></i></span>';
						InputFilled.after(checkNode);
					} else {
						checkNode = '<span id="checkNode" style="margin-left: 10px; position: relative; top: 23px; left: 25px;"><i class="fa fa-check"></i></span>';
						causality.append(checkNode);
					}

					$('#checkNode').fadeIn(200).fadeOut(200).fadeIn(200).fadeOut(200).fadeIn(200);
					setTimeout(function () {
						$('#checkNode').fadeOut(500);
					}, 1500);
					setTimeout(function () {
						$('#checkNode').remove();
						InputFilled.find('#checkNode').remove();
					}, 2000);
				}

			},
			error: function (response) {
				console.log('error');
			}
		});

	}

	function getSubTypes(typeID) {

		$.ajax({
			url: Routing.generate('deviation.get.subType.xhr', {typeID: typeID}),
			type: "GET",
			dataType: 'json',
			success: function (response) {
				let data = JSON.parse(response.subTypes);

				let html = '';
				html += "<option value=" + '' + ">" + "\<\< Sous-Type \>\>" + "</option>";

				for (var key in data) {
					let subType = data[key];
					html += "<option value=" + subType.id + ">" + subType.type + "</option>";
				}

				subType.find('option').remove();
				subType.html(html);

				$('#deviation_declaration_subTypeCode > option').removeAttr("selected");

				if (editMode === 'edit') {
					subType.removeAttr("disabled");
				} else {
					subType.attr("disabled", "disabled");
				}
			},
			error: function (response) {
				console.log('error');
				console.log(response.statusLabel + ' :' + response.errorMessage);
			}
		});
	}

	function getInstitutions(centerID) {

		$.ajax({
			url: Routing.generate('institution.get.xhr', {centerID: centerID}),
			type: "GET",
			dataType: 'json',
			success: function (response) {

				let data = JSON.parse(response.institutions);

				let html = '';
				html += "<option value=" + '' + ">" + "\<\< Institutions \>\>" + "</option>";

				for (var key in data) {
					let institution = data[key];
					html += "<option value=" + institution.id + ">" + institution.name + "</option>";
				}

				institution.find('option').remove();
				institution.html(html);

				$('#deviation_declaration_institution > option').removeAttr("selected");
				institution.removeAttr('disabled');
			},
			error: function (response) {

				console.log('error');
				console.log(response.statusLabel + ' :' + response.errorMessage);
			}
		});
	}

	function getPatients(centerID) {

		$.ajax({
			url: Routing.generate('patient.get.xhr', {centerID: centerID}),
			type: "GET",
			dataType: 'json',
			success: function (response) {
				let data = JSON.parse(response.patients);

				let html = '';
				html += "<option value=" + '' + ">" + "\<\< Patients \>\>" + "</option>";

				for (var key in data) {
					let patient = data[key];
					html += "<option value=" + patient.id + ">" + patient.number + "</option>";
				}

				patient.find('option').remove();
				patient.html(html);

				$('#deviation_declaration_patient > option').removeAttr("selected");
				patient.removeAttr('disabled');
			},
			error: function (response) {
				console.log('error');
				console.log(response.statusLabel + ' :' + response.errorMessage);
			}
		});
	}
});
