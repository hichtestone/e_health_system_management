// Tail select
const TailSelect = require('tail.select');
let axios = require('axios');
let swal = require('sweetalert2');
let CFloading = require('../CFloading/CFloading');

let Main = require('../Utils/main');


// Activer/Desactiver interlocuteur -------------------------------------------------------------------------------------------------------------------
let enable = $('.center-user-enable')
let disable = $('.center-user-disable')

Main.confirmAction(enable)
Main.confirmAction(disable)


document.addEventListener('DOMContentLoaded', function() {

	let centerCenterStatus = $('#center_centerStatus');
	$(centerCenterStatus).change(function () {
		if($(this).val() === '3') {
			let html = "<div class=\"alert alert-primary mt-2\" role=\"alert\" id=\"removeAlert\">\n" +
				"  <i class=\"fas fa-exclamation-triangle\"></i> Cette action est irréversible" +
				"</div>"
			;
			$(this).parent().append(html);
		} else {
			$('#removeAlert').css('display', 'none');
		}
	})


	let centerInstitutionList = $('#selection_institutions, #center_institutions');

	let elCenterInstitutionListTs = TailSelect(centerInstitutionList.get(0), {
		placeholder: "Choissiez au moins un établissement",
		multiple: true,
		search: true
	});

	$(location).attr('pathname').split("/").pop() === "new"
		? $(elCenterInstitutionListTs.select).css('border', '1px solid red')
		: $(elCenterInstitutionListTs.select).css('border', '');

	if(centerInstitutionList.length){
		$(centerInstitutionList).closest('div').find('label').css('display', 'block');

		elCenterInstitutionListTs.on('change', function(item, state){
			if($(centerInstitutionList).val().length > 0){
				$(elCenterInstitutionListTs.select).css('border', '');
			}else{
				$(elCenterInstitutionListTs.select).css('border', '1px solid red');
			}
		});

	}

	let institutionId = $('#center-interlocutors').attr('data-center-id');
	if(institutionId){
		$('#interlocutor-new, .interlocutor-edit').click(function(){
			CFloading.start();
			axios.get(this.href)
				.then(response => {
					swal.fire({
						showCloseButton: true,
						showConfirmButton: false,
						html: response.data.html,
						onOpen(popup) {
							// Gestion datepicker
							$(".js-datepicker").datepicker({
								dateFormat: 'dd/mm/yy',
								autoclose: true,
								closeText: 'Fermer',
								prevText: '&#x3c;Préc',
								nextText: 'Suiv&#x3e;',
								currentText: 'Aujourd\'hui',
								monthNames: ['Janvier','Fevrier','Mars','Avril','Mai','Juin',
									'Juillet','Aout','Septembre','Octobre','Novembre','Decembre'],
								monthNamesShort: ['Jan','Fev','Mar','Avr','Mai','Jun',
									'Jul','Aou','Sep','Oct','Nov','Dec'],
								dayNames: ['Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi'],
								dayNamesShort: ['Dim','Lun','Mar','Mer','Jeu','Ven','Sam'],
								dayNamesMin: ['Di','Lu','Ma','Me','Je','Ve','Sa'],
							});
							let helperUrl = $('#formHelperUrl');
							let elAfterService = $('#afterService');
							let elIsPrincpalInv = $('#isPrincipalInvestigator');
							let elService = $('#interlocutor_center_service');
							let elInterlocutor = $('#interlocutor_center_interlocutor');
							function refreshForm(){
								if(elService.val() == ''){
									elAfterService.css('display', 'none');
									elIsPrincpalInv.css('display', 'none');
									elService.val('');
								}else{
									axios.get(helperUrl.html(), {params: {service: elService.val(), interlocutor: elInterlocutor.val()}})
										.then(response => {
											elAfterService.css('display', '');
											let val = elInterlocutor.val();
											elInterlocutor.empty();
											elInterlocutor.append($('<option></option>').attr('value', '').text('<<Interlocutor>>'));
											for(let interlocutor of response.data.interlocutors){
												elInterlocutor.append($('<option></option>').attr('value', interlocutor.value).text(interlocutor.text))
											}
											elInterlocutor.val(val);
											if(response.data.canPI){
												elIsPrincpalInv.css('display', '');
											}else{
												elIsPrincpalInv.find('input').prop('checked', false);
												elIsPrincpalInv.css('display', 'none');
											}
										})
										.catch(res => {
											console.log(res);
										});
								}
							}
							if(helperUrl.length){
								elService.change(function(){
									refreshForm();
								});
								elInterlocutor.change(function(){
									refreshForm();
								});
								refreshForm();
							}
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
	}

	if($('#center-document-tracking-table').length){
		$('#document-new, .document-edit').click(function(){
			CFloading.start();
			axios.get(this.href)
				.then(response => {
					swal.fire({
						showCloseButton: true,
						showConfirmButton: false,
						html: response.data.html,
						onOpen(popup) {
							// Gestion datepicker
							$(".js-datepicker").datepicker({
								dateFormat: 'dd/mm/yy',
								autoclose: true,
								closeText: 'Fermer',
								prevText: '&#x3c;Préc',
								nextText: 'Suiv&#x3e;',
								currentText: 'Aujourd\'hui',
								monthNames: ['Janvier','Fevrier','Mars','Avril','Mai','Juin',
									'Juillet','Aout','Septembre','Octobre','Novembre','Decembre'],
								monthNamesShort: ['Jan','Fev','Mar','Avr','Mai','Jun',
									'Jul','Aou','Sep','Oct','Nov','Dec'],
								dayNames: ['Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi'],
								dayNamesShort: ['Dim','Lun','Mar','Mer','Jeu','Ven','Sam'],
								dayNamesMin: ['Di','Lu','Ma','Me','Je','Ve','Sa'],
							});

							let helperUrl = $('#formHelperUrl');
							let elSelect = $('#document_tracking_center_documentTracking');
							let elSentAt = $('#sentAt');
							let elReceivedAt = $('#receivedAt');
							function refreshForm(){
								if(elSelect.val() == ''){
									elSentAt.css('display', 'none');
									elReceivedAt.css('display', 'none');
								}else{
									axios.get(helperUrl.html(), {params: {documentTracking: elSelect.val()}})
										.then(response => {
											elSentAt.css('display', response.data.toBeSent ? '' : 'none');
											elReceivedAt.css('display', response.data.toBeReceived ? '' : 'none');
										})
										.catch(res => {
											console.log(res);
										});
								}
							}
							elSelect.change(function(){
								refreshForm();
							});
							refreshForm();
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
	}

})
