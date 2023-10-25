// Tail select
const TailSelect = require('tail.select');
let Main = require('./../Utils/main')

$(document).ready(function () {

	let contactObject = $('#contact_object')
	let contactObjectReason = $('#contact_objectReason')

	// Liste des interlocuteurs
	let elSelectInterlocutor = document.getElementById('contact_interlocutors');
	// Liste des intervenants
	let elSelectIntervenant = document.getElementById('contact_intervenants');

	let recipientID = document.getElementById('recipient');
	let recipientVal = $(recipientID).text();


	if($(elSelectInterlocutor).length || $(elSelectIntervenant).length) {
		// Tail select interlocuteurs
		$(elSelectInterlocutor).closest('div').find('label').css('display', 'block');
		let elSelectInterlocutorTs = TailSelect($(elSelectInterlocutor).get(0), {
			hideDisabled: true,
			placeholder: recipientVal,
			search: true,
			multiple: true
		});

		// Tail select intervenants
		$(elSelectIntervenant).closest('div').find('label').css('display', 'block');
		let elSelectIntervenantTs = TailSelect($(elSelectIntervenant).get(0), {
			hideDisabled: true,
			placeholder: recipientVal,
			search: true,
			multiple: true
		});

		// gestion Destinataire(s) Interlocuteur / Intervenants
		let typeRecipient = $("input[name='contact[typeRecipient]']");

		function update()
		{
			if(typeof typeRecipient.filter(':checked').val() === 'undefined') {
				elSelectInterlocutorTs.disable();
				$("label[for=contact_interlocutors]").removeClass('required');

				elSelectIntervenantTs.disable();
				$("label[for=contact_intervenants]").removeClass('required');

			} else if(typeRecipient.filter(':checked').val() === '2'){
				elSelectInterlocutorTs.disable();
				$("label[for=contact_interlocutors]").removeClass('required');

				elSelectIntervenantTs.enable();
				$("label[for=contact_intervenants]").addClass('required');
				if($(elSelectIntervenant).val().length === 0) {
					$(elSelectIntervenantTs.select).css('border', '1px solid red');
				}else{
					$(elSelectIntervenantTs.select).css('border', '');
				}

			} else if(typeRecipient.filter(':checked').val() === '1'){
				elSelectIntervenantTs.disable();
				$("label[for=contact_intervenants]").removeClass('required');

				elSelectInterlocutorTs.enable();
				$("label[for=contact_interlocutors]").addClass('required');
				if($(elSelectInterlocutor).val().length === 0) {
					$(elSelectInterlocutorTs.select).css('border', '1px solid red');
				}else{
					$(elSelectInterlocutorTs.select).css('border', '');
				}
			}
		}
		typeRecipient.change(function(){
			update();
		});

		update();

		elSelectInterlocutorTs.on('change', function(item, state){
			if($(elSelectInterlocutor).val().length > 0){
				$(elSelectInterlocutorTs.select).css('border', '');
			}else{
				$(elSelectInterlocutorTs.select).css('border', '1px solid red');
			}
		});

		elSelectIntervenantTs.on('change', function(item, state){
			if($(elSelectIntervenant).val().length > 0){
				$(elSelectIntervenantTs.select).css('border', '');
			}else{
				$(elSelectIntervenantTs.select).css('border', '1px solid red');
			}
		});

	}

	Main.reasonDescription(contactObject, contactObjectReason, 'autre');

	contactObject.on('change', function () {
		Main.reasonDescription(contactObject, contactObjectReason, 'autre');
	})

})
