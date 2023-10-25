// Tail select
const TailSelect = require('tail.select');

document.addEventListener('DOMContentLoaded', function() {

	// Liste des interlocuteurs
	let elSelectInterlocutor = document.getElementById('deviation_review_action_interlocutor');
	// Liste des intervenants
	let elSelectIntervenant = document.getElementById('deviation_review_action_user');

	if($(elSelectInterlocutor).length || $(elSelectIntervenant).length) {
		// Tail select interlocuteurs
		$(elSelectInterlocutor).closest('div').find('label').css('display', 'block');
		let elSelectInterlocutorTs = TailSelect($(elSelectInterlocutor).get(0), {
			hideDisabled: true,
			placeholder: "Interlocuteur",
			search: true,
			multiple: false
		});

		// Tail select intervenants
		$(elSelectIntervenant).closest('div').find('label').css('display', 'block');
		let elSelectIntervenantTs = TailSelect($(elSelectIntervenant).get(0), {
			hideDisabled: true,
			placeholder: "Intervenant",
			search: true,
			multiple: false
		});

		// gestion de responsable (s) Interlocuteur / Intervenant
		let typeManger = $("input[name='deviation_review_action[typeManager]']");

		function update()
		{
			if(typeof typeManger.filter(':checked').val() === 'undefined') {
				elSelectInterlocutorTs.disable();
				$("label[for=deviation_review_action_interlocutor]").removeClass('required');

				elSelectIntervenantTs.disable();
				$("label[for=deviation_review_action_user]").removeClass('required');

			} else if(typeManger.filter(':checked').val() === '1'){
				elSelectInterlocutorTs.disable();
				$("label[for=deviation_review_action_interlocutor]").removeClass('required');

				elSelectIntervenantTs.enable();
				$("label[for=deviation_review_action_user]").addClass('required');
				if($(elSelectIntervenant).val().length === 0) {
					$(elSelectIntervenantTs.select).css('border', '1px solid red');
				}else{
					$(elSelectIntervenantTs.select).css('border', '');
				}

			} else if(typeManger.filter(':checked').val() === '2'){
				elSelectIntervenantTs.disable();
				$("label[for=deviation_review_action_user]").removeClass('required');

				elSelectInterlocutorTs.enable();
				$("label[for=deviation_review_action_interlocutor]").addClass('required');
				if($(elSelectInterlocutor).val().length === 0) {
					$(elSelectInterlocutorTs.select).css('border', '1px solid red');
				}else{
					$(elSelectInterlocutorTs.select).css('border', '');
				}
			}
		}
		typeManger.change(function(){
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
})
