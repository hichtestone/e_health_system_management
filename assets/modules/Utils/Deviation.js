"use strict";

let Axios 			= require('axios');
let CFloading 		= require('../CFloading/CFloading');
let Swal 			= require('sweetalert2');
const TailSelect 	= require('tail.select');

function Deviation() {

	this.getJob = function (target, url) {

		$.ajax({
			url: url,
			type: "GET",
			dataType: 'json',
			success: function (response) {
				target.html(response.jobLabel);
			},
			error: function (response) {
				console.log(response.statusLabel + ' :' + response.errorMessage);
			}
		});
	};

	this.reasonDescription = function (origin, target, editMode, isTailSelect) {

		let selectedElement = $(origin).find('option:selected').text();

		if (editMode === 'edit') {
			if (isTailSelect && selectedElement.indexOf("autre") >= 0) {
				$(target).removeAttr('disabled');
			} else if (!isTailSelect && selectedElement.toLowerCase() === 'autre') {
				$(target).removeAttr('disabled');
			} else {
				$(target).attr("disabled", "disabled").attr("required", "false").val(null);
			}
		} else {
			$(target).attr("disabled", "disabled").attr("required", "false");
		}
	};

	this.tooltip = function (title) {

		return '<i class="fa fa-question-circle" data-toggle="tooltip" data-placement="right" title="' + title + '">';
	};

	this.activeButton = function (origin, target) {

		(origin.val()) === '' ? target.attr("disabled", "disabled") : target.removeAttr("disabled");
	};

	this.deleteDraft = function (target, url, title, redirection) {

		target.on('click', function (e) {
			$.ajax({
				url: url,
				type: "POST",
				dataType: 'json',
				data: {confirm: 0},
				success: function (response) {

					if (response.messageStatus === 'OK') {
						Swal.fire({
							title: title,
							showCloseButton: true,
							showConfirmButton: true,
							showCancelButton: true,
							confirmButtonText: 'Confirmer',
							cancelButtonText: 'Annuler',
							html: response.html,
						}).then((result) => {
							if (result.isConfirmed) {
								$.ajax({
									url: url,
									type: "POST",
									dataType: 'json',
									data: {confirm: 1},
									success: function (response) {
										if (response.messageStatus) {
											window.location.href = redirection;
										} else {
											console.log('error del draft');
										}
									}
								});
							}

							if (result.isDismissed) {
								Swal.close();
							}
						});

					} else {
						console.log('err');
						console.log(response);
					}
				},
				error: function (response) {
					console.log('error');
					console.log(response.statusLabel + ' :' + response.errorMessage);
				}
			});

			e.preventDefault();
			e.stopPropagation();
			return false;

		});
	};

	this.deleteAction = function (target) {

		target.on('click', function () {
			CFloading.start();
			Axios.get(this.href)
				.then(response => {
					Swal.fire({
						showCloseButton: true,
						showConfirmButton: false,
						html: response.data.html,
						onOpen(popup) {
							console.log('OK');
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
	};

	this.efficiencyMeasure = function efficiencyMeasure(measure, justify, reason, editMode) {

		if (editMode === 'edit') {

			if (measure.val() === '0' || measure.val() === '1' || measure.val() === '2') {

				justify.removeAttr('disabled')

				if (measure.val() === '0' || measure.val() === '1') {
					reason.attr("disabled", "disabled").attr("required", "false").val(null)
				}

				if (measure.val() === '2') {
					reason.removeAttr('disabled')
					justify.attr("disabled", "disabled").attr("required", "false").val(null)
				}

			} else {
				justify.attr("disabled", "disabled").attr("required", "false").val(null)
				reason.attr("disabled", "disabled").attr("required", "false").val(null)
			}
		}
	};

	this.efficiencyMeasureCorrection = function efficiencyMeasureCorrection(origin, target, editMode) {

		let efficiencyMeasure = origin.val();
		if (editMode === 'edit') {
			if (efficiencyMeasure === '1' || efficiencyMeasure === '2') {
				target.removeAttr('disabled');
				target.css('border', '1px solid red')
			} else {
				target.attr("disabled", "disabled").attr("required", "false").val(null);
				target.css('border', 'unset')
			}
		} else {
			target.attr("disabled", "disabled").attr("required", "false");
		}
	};

	this.renderTailSelect = function renderTailSelect(target, placeholder, multiple = true) {

		let tailSelectList = [];
		if (target.length) {
			target.closest('div').find('label').css('display', 'block');
			tailSelectList =  TailSelect(target.get(0), {
				hideDisabled: true,
				placeholder: placeholder,
				search: true,
				multiple: multiple,
			});
		}

		return tailSelectList;
	};

	this.renderTailSelectRequired = function renderTailSelectRequired(target) {

		if (target.length) {
			if (target.val() === null || target.val().length === 0) {
				setTimeout(()=> {
					target.closest('div').find('.tail-select').css('border', '1px solid red')
					target.attr('required', true)
				}, 500)
			} else {
				setTimeout(()=> {
					target.closest('div').find('.tail-select').css('border', 'unset')
					target.attr('required', false)
				}, 500)
			}
		}
	}

	this.ajaxSendPersist = function ajaxSendPersist(url, fieldName, fieldValue, InputFilled, typeDeviation, tailSelects = []) {

		let causalities = [
			'deviation_' + typeDeviation  + '_declaration_causality_0',
			'deviation_' + typeDeviation  + '_declaration_causality_1',
			'deviation_' + typeDeviation  + '_declaration_causality_2'
		]

		let causalityID = 'deviation_' + typeDeviation  + '_declaration_causality'

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
					if (!causalities.includes(inputID) || !tailSelects.includes(inputID)) {
						checkNode = '<span id="checkNode" style="margin-left: 10px;"><i class="fa fa-check"></i></span>';
						if (tailSelects.includes(inputID)) {
							InputFilled.next('div').after(checkNode);
						} else {
							InputFilled.after(checkNode);
						}
					} else {
						checkNode = '<span id="checkNode" style="margin-left: 10px; position: relative; top: 23px; left: 25px;"><i class="fa fa-check"></i></span>';
						$('#' + causalityID).append(checkNode);
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
				console.log(response);
			}
		});
	};

	this.close = function close(url, title, successMessageHtml, redirection) {

		$.ajax({
				url: url,
				type: "POST",
				dataType: 'json',
				data: {confirm: 0},
				success: function (response) {
					if (response.messageStatus === 'OK') {
						Swal.fire({
							title: title,
							showCloseButton: true,
							showConfirmButton: true,
							showCancelButton: true,
							confirmButtonText: 'Confirmer',
							cancelButtonText: 'Annuler',
							showLoaderOnConfirm: true,
							html: response.html,
							allowOutsideClick: () => !Swal.isLoading(),
							preConfirm: function () {
								$.ajax({
									url: url,
									type: "POST",
									dataType: 'json',
									data: {confirm: 1},
									success: function (response) {
										if (response.messageStatus === 'OK') {
											Swal.fire({
												html: successMessageHtml,
												timer: 2000,
												timerProgressBar: true,
											});
											$('.swal2-confirm').remove();
											setTimeout(function () {
												window.location.href = redirection;
											}, 2000);
										} else {
											let html = '<div>' + response.messageError + '<br></div><br>';
											response.isClosableMessages.forEach(function (current) {
												html += current + '<br>';
											});
											$('#swal2-content .admin-block').after(html);
											$('.swal2-confirm').remove();
											$('.swal2-cancel').remove();
										}
									}
								});

								return false;
							},
						});
					} else {
						console.log('err');
					}
				},
				error: function (response) {
					console.log('error');
				}
			});
	};

	this.closeMulti = function closeMulti(url, title, successMessageHtml, redirection, deviationSampleListTable, isProtocol = false, isSystem = false, isSample = false) {

		let deviationSampleIDs = [];
		deviationSampleListTable.find('input:checkbox:checked').each(function (item) {
			deviationSampleIDs.push($(this).closest('tr').attr('data-id'));
		});

		if (deviationSampleIDs.length > 0) {
			$.ajax({
				url: url,
				type: "POST",
				dataType: 'json',
				data: {
					confirm: 0,
					items: deviationSampleIDs
				},
				success: function (response) {

					if (response.messageStatus === 'OK') {
						Swal.fire({
							title: title,
							showCloseButton: true,
							showConfirmButton: true,
							showCancelButton: true,
							confirmButtonText: 'Confirmer',
							cancelButtonText: 'Annuler',
							html: response.html,
							preConfirm: function () {

								$.ajax({
									url: url,
									type: "POST",
									dataType: 'json',
									data: {
										confirm: 1,
										items: deviationSampleIDs
									},
									success: function (response) {
										if (response.messageStatus === 'OK') {
											Swal.fire({
												html: successMessageHtml,
												timer: 2000,
												timerProgressBar: true,
											});

											$('.swal2-confirm').remove();

											setTimeout(function () {
												window.location.href = redirection;
											}, 2000);

										} else {
											let html = '<div>' + "Les déviations suivantes n\' pas pû être clôturées:" + '<br></div><br>';
											if (isSample) {
												response.deviationSamplesInfo.forEach((item) => {
													html += '<p>' + item.code + '</p>';
													html += '<ul>';

													item.isClosableMessages.forEach((message) => {
														html += '<li>' + message + '</li>';
													});

													html += '</ul>';
												});
											} else if (isSystem) {
												response.deviationSystemsInfo.forEach((item) => {
													html += '<p>' + item.code + '</p>';
													html += '<ul>';

													item.isClosableMessages.forEach((message) => {
														html += '<li>' + message + '</li>';
													});

													html += '</ul>';
												});
											} else {
												response.deviationsInfo.forEach((item) => {
													html += '<p>' + item.code + '</p>';
													html += '<ul>';

													item.isClosableMessages.forEach((message) => {
														html += '<li>' + message + '</li>';
													});

													html += '</ul>';
												});
											}


											$('#swal2-content .admin-block').after(html);
											$('.swal2-confirm').remove();
											$('.swal2-cancel').remove();
										}
									}
								});

								return false;
							}
						});
					} else {
						console.log('err');
					}
				},
				error: function (response) {
					console.log('error');
					console.log(response);
					console.log(response.statusLabel + ' :' + response.errorMessage);
				}
			});
		}
	};
}

module.exports = new Deviation();
