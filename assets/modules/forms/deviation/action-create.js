// Tail select
const TailSelect = require('tail.select')

$(document).ready(function() {

	let elSelectInterlocutor = $('#deviation_action_interlocutor')
	let elSelectIntervenant = $('#deviation_action_intervener')


	$("label[for=deviation_action_interlocutor]").removeClass('required')
	$("label[for=deviation_action_intervener]").removeClass('required')

	if (elSelectInterlocutor.length || elSelectIntervenant.length) {

		elSelectInterlocutor.closest('div').find('label').css('display', 'block')
		let elSelectInterlocutorTs = TailSelect(elSelectInterlocutor.get(0), {
			hideDisabled: true,
			placeholder: "Interlocuteur(s)",
			search: true,
			deselect: true,
		})

		elSelectIntervenant.closest('div').find('label').css('display', 'block')
		let elSelectIntervenantTs = TailSelect(elSelectIntervenant.get(0), {
			hideDisabled: true,
			placeholder: "Intervenant(s)",
			search: true,
			deselect: true,
		})

		// gestion de responsable (s) Interlocuteur / Intervenants
		let TypeManager = $("input[name='deviation_action[typeManager]']")

		function update()
		{
			if (TypeManager.filter(':checked').val() === 'undefined') {

				elSelectInterlocutorTs.disable();
				$("label[for=deviation_action_interlocutor]").removeClass('required')

				elSelectIntervenantTs.disable();
				$("label[for=deviation_action_intervener]").removeClass('required')

				$('#deviation_action_interlocutor').parent().find('.select-label').css('background-color', 'lightgray');
				$('#deviation_action_intervener').parent().find('.select-label').css('background-color', 'lightgray');


			} else if (TypeManager.filter(':checked').val() === '1') {

				elSelectInterlocutorTs.disable()

				$("label[for=deviation_action_interlocutor]").removeClass('required')

				elSelectIntervenantTs.enable()
				$("label[for=deviation_action_intervener]").addClass('required')

				if (elSelectIntervenant.val() === null) {
					$(elSelectIntervenantTs.select).css('border', '1px solid red')
				} else if (elSelectIntervenant.val().length === 0) {
					$(elSelectIntervenantTs.select).css('border', '1px solid red')
				} else {
					$(elSelectIntervenantTs.select).css('border', '')
				}

				$('#deviation_action_interlocutor').parent().find('.select-label').css('background-color', 'lightgray');


			} else if (TypeManager.filter(':checked').val() === '2') {

				elSelectIntervenantTs.disable()
				$("label[for=deviation_action_intervener]").removeClass('required')

				elSelectInterlocutorTs.enable()
				$("label[for=deviation_action_interlocutor]").addClass('required')

				if ($(elSelectInterlocutor).val() === null) {
					$(elSelectInterlocutorTs.select).css('border', '1px solid red')
				} else if ($(elSelectInterlocutor).val().length === 0) {
					$(elSelectInterlocutorTs.select).css('border', '1px solid red')
				} else {
					$(elSelectInterlocutorTs.select).css('border', '')
				}

				$('#deviation_action_intervener').parent().find('.select-label').css('background-color', 'lightgray');
			}
		}

		TypeManager.change(function(){
			update();
		})

		update()

		elSelectInterlocutorTs.on('change', function() {

			if ($(elSelectInterlocutor).val().length > 0) {
				$(elSelectInterlocutorTs.select).css('border', '')
			} else {
				$(elSelectInterlocutorTs.select).css('border', '1px solid red')
			}
		});

		elSelectIntervenantTs.on('change', function() {

			if ($(elSelectIntervenant).val().length > 0) {
				$(elSelectIntervenantTs.select).css('border', '')
			} else {
				$(elSelectIntervenantTs.select).css('border', '1px solid red')
			}
		});
	}
})
