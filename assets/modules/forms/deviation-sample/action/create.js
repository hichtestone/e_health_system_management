// Tail select
const TailSelect = require('tail.select')
let Deviation = require('../../../Utils/Deviation');

$(document).ready(function () {
	// Liste des interlocuteurs
	let elSelectInterlocutor = $('#deviation_sample_action_interlocutor')
	// Liste des intervenants
	let elSelectIntervenant = $('#deviation_sample_action_user')

	if (elSelectInterlocutor.length || elSelectIntervenant.length) {
		elSelectInterlocutor.closest('div').find('label').css('display', 'block')
		let elSelectInterlocutorTs = TailSelect(elSelectInterlocutor.get(0), {
			hideDisabled: true,
			placeholder: "Interlocuteur(s)",
			search: true,
			deselect: true,
		})

		elSelectIntervenant.closest('div').find('label').css('display', 'block')
		let elSelectIntervenantTS = TailSelect(elSelectIntervenant.get(0), {
			hideDisabled: true,
			placeholder: "Intervenant(s)",
			search: true,
			deselect: true,
		})

		// Tail select interlocuteurs
		//let elSelectInterlocutorTs = Deviation.renderTailSelect(elSelectInterlocutor, 'Interlocuteur', false)

		// Tail select intervenants
		//let elSelectIntervenantTs = Deviation.renderTailSelect(elSelectIntervenant, 'Interlocuteur', false)

		// gestion de responsable (s) Interlocuteur / Intervenants
		let typeManger = $("input[name='deviation_sample_action[typeManager]']")

		function update() {
			if (typeof typeManger.filter(':checked').val() === 'undefined') {
				if (elSelectInterlocutorTs.length) {
					elSelectInterlocutorTs.disable()
				}
				$("label[for=deviation_sample_action_interlocutor]").removeClass('required')
				if (elSelectIntervenantTS.length) {
					elSelectIntervenantTS.disable()
				}
				$("label[for=deviation_sample_action_user]").removeClass('required')

			} else if (typeManger.filter(':checked').val() === '1') {
				elSelectInterlocutorTs.disable()
				$("label[for=deviation_sample_action_interlocutor]").removeClass('required')

				elSelectIntervenantTS.enable()
				$("label[for=deviation_sample_action_user]").addClass('required')
				Deviation.renderTailSelectRequired(elSelectIntervenant);

			} else if (typeManger.filter(':checked').val() === '2') {
				elSelectIntervenantTS.disable()
				$("label[for=deviation_sample_action_user]").removeClass('required')

				elSelectInterlocutorTs.enable()
				$("label[for=deviation_sample_action_interlocutor]").addClass('required')
				Deviation.renderTailSelectRequired(elSelectInterlocutor)
			}
		}

		typeManger.change(function () {
			update()
		});

		update()


		elSelectInterlocutorTs.on('change', function() {

			if ($(elSelectInterlocutor).val().length > 0) {
				$(elSelectInterlocutorTs.select).css('border', '')
			} else {
				$(elSelectInterlocutorTs.select).css('border', '1px solid red')
			}
		});

		elSelectIntervenantTS.on('change', function() {

			if ($(elSelectIntervenant).val().length > 0) {
				$(elSelectIntervenantTS.select).css('border', '')
			} else {
				$(elSelectIntervenantTS.select).css('border', '1px solid red')
			}
		});
	}

});
