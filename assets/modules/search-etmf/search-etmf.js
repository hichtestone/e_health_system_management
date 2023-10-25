let Etmf = require('./../Utils/Etmf')
const TailSelect = require('tail.select')

let $ = require('jquery')
let dt = require('datatables.net')
let dtStyle = require('datatables.net-dt/css/jquery.dataTables.css')

$(document).ready(function () {

	if (typeof jQuery.fn.DataTable != "undefined") {
		$('#dtDocument').DataTable({
			"language": {
				"emptyTable": "Aucune donnée disponible dans le tableau",
				"lengthMenu": "Afficher _MENU_ éléments",
				"loadingRecords": "Chargement...",
				"processing": "Traitement...",
				"zeroRecords": "Aucun élément correspondant trouvé",
				"paginate": {
					"first": "Premier",
					"last": "Dernier",
					"previous": "Précédent",
					"next": "Suivant"
				},
				"info": "Affichage de _START_ à _END_ sur _TOTAL_ éléments",
				"infoEmpty": "Affichage de 0 à 0 sur 0 éléments",
				"infoThousands": ",",
				"search": "Rechercher:",
				"thousands": ",",
				"infoFiltered": "(filtrés depuis un total de _MAX_ éléments)",
			}
		});
	}

	let sponsor	 = $('#search_sponsor')
	let project	 = $('#search_project')
	let zone	 = $('#search_zone')
	let section	 = $('#search_section')
	let artefact = $('#search_artefact')
	let country	 = $('#search_country')
	let center	 = $('#search_center')
	let tag	     = $('#search_tag')
	let status	 = $('#search_status')
	let reset	 = $('#search_reset')


	Etmf.renderTailSelect(sponsor, 'Promoteur')
	Etmf.renderTailSelect(project, 'Étude')
	Etmf.renderTailSelect(zone, 'Zone')
	Etmf.renderTailSelect(section, 'Section')
	Etmf.renderTailSelect(artefact, 'Artefact')
	Etmf.renderTailSelect(country, 'pays')
	Etmf.renderTailSelect(center, 'Centre')
	Etmf.renderTailSelect(tag, 'Tag')
	Etmf.renderTailSelect(status, 'Statut')


	reset.on('click', function () {
		sponsor.empty()
		project.empty()
		zone.empty()
		section.empty()
		artefact.empty()
		country.empty()
		center.empty()
		tag.empty()
		status.empty()

		console.log('ici2')
	})




	/*if (sponsor.length) {
		sponsor.closest('div').find('label').css('display', 'block')
		//let sponsorTs = TailSelect(sponsor.get(0), {yarn a
		TailSelect(sponsor.get(0), {
			hideDisabled: true,
			placeholder: "Sponsor",
			search: true,
			multiple: true
		})

		sponsorTs.on('close', function() {

			let data=[];
			let $el=$("#search_sponsor");
			$el.find('option:selected').each(function(){
				data.push({ value:$(this).val(), text:$(this).text() });
				getProjects($(this).val())
			});
			console.log(data)
		});
	}*/


	function getProjects(sponsorID) {

		$.ajax({
			url: Routing.generate('document.get.project.xhr', {sponsorID: sponsorID}),
			type: "GET",
			dataType: 'json',
			success: function (response) {

				let data = JSON.parse(response.projects);
				console.log('Project changed ', data);

				let html = '';
				for (let key in data) {
					let levelObj = data[key];
					html += "<option value=" + levelObj.id + ">" + levelObj.acronyme + "</option>";
				}

				project.find('option').remove();
				project.html(html);

				project.closest('div').find('label').css('display', 'block');
				let projectTs = TailSelect(project.get(0), {
					hideDisabled: true,
					placeholder: "<< Projetsss >>",
					search: true,
					multiple: true
				})

				projectTs.enable();

				$('#search_project > option').removeAttr("selected");
			},
			error: function (response) {
				console.log('error');
				console.log(response.statusLabel + ' :' + response.errorMessage);
			}
		});
	}

	/*sponsor.on('change', function () {
		let selected = $(this).val() || [];
		console.log("You have selected the country - " + selected.join(', '));
	})*/


	/*sponsor.on('change', function () {
		alert('ICI')
		let $field = $(this)
		let $form = $field.closest('form')
		let target = '#' + $field.attr('id').replace('search_sponsor', 'search_project')

		// Les données à envoyer en Ajax
		let data = {}

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
			Etmf.renderTailSelect(project, 'Projets')
		})

		Etmf.renderTailSelectRequired(sponsor)
	})*/

})

