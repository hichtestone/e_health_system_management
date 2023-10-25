import '@symfony/ux-dropzone/src/style.css'
require ('@symfony/ux-dropzone/src/controller')
let Etmf = require('./../../Utils/Etmf')
const TailSelect = require('tail.select')
$(document).ready(function () {

	let tags 			= $('#document_version_tags')
	let zone 			= $('#document_version_zone')
	let section 		= $('#document_version_section')
	let artefact 		= $('#document_version_artefact')
	let documentLevels  = $('#document_version_documentLevels')
	let sponsor 		= $('#document_version_sponsor')
	let project 		= $('#document_version_project')
	let countries 		= $('#document_version_countries')
	let centers 		= $('#document_version_centers')
	let author 			= $('#document_version_author')
	let authorQa 		= $('#document_version_validatedQaBy')

	Etmf.renderTailSelect(tags, 'Tags')
	//Etmf.renderTailSelect(countries, 'Pays')
	//Etmf.renderTailSelect(centers, 'Centres')


	author.attr("disabled", "disabled");
	authorQa.attr("disabled", "disabled");

	zone.on('change', function () {

		let zoneID = $(this).val();

		if (zoneID) {
			$('#document_version_section > option').removeAttr("selected");
			section.removeAttr("disabled");
			getSections(zoneID);

			// artefact
			let html = '';
			html += "<option value=" + '' + ">" + "\<\< Sélectionnez une section \>\>" + "</option>";
			artefact.html(html);
			artefact.attr("disabled", "disabled");

			// artefact
			let html2 = '';
			html2 += "<option value=" + '' + ">" + "\<\< Sélectionnez un artefact \>\>" + "</option>";
			documentLevels.html(html2);
			documentLevels.attr("disabled", "disabled");

		} else {
			let html = '';
			html += "<option value=" + '' + ">" + "\<\< Sélectionnez une zone \>\>" + "</option>";
			section.html(html);
			section.attr("disabled", "disabled");

		}

	})

	section.on('change', function () {

		let sectionID = $(this).val();

		if (sectionID) {
			$('#document_version_artefact > option').removeAttr("selected");
			artefact.removeAttr("disabled");
			getArtefacts(sectionID);
		} else {
			let html = '';
			html += "<option value=" + '' + ">" + "\<\< Sélectionnez un section \>\>" + "</option>";
			artefact.html(html);
			artefact.attr("disabled", "disabled");

			let html2 = '';
			html2 += "<option value=" + '' + ">" + "\<\< Sélectionnez un artefact \>\>" + "</option>";
			documentLevels.html(html2);
			documentLevels.attr("disabled", "disabled");

			project.val("");

			let htmlAuthor = '';
			htmlAuthor += "<option value=" + '' + ">" + "\<\< Sélectionnez un projet \>\>" + "</option>";
			author.html(htmlAuthor);
			author.attr("disabled", "disabled");

			let htmlAuthorQa = '';
			htmlAuthorQa += "<option value=" + '' + ">" + "\<\< Sélectionnez un projet \>\>" + "</option>";
			authorQa.html(htmlAuthorQa);
			authorQa.attr("disabled", "disabled");

			centers.removeAttr("disabled")
			$("#document_version_centers option[value]").remove();
			countries.removeAttr("disabled")
			$("#document_version_countries option[value]").remove();
		}

	})

	artefact.on('change', function () {
		let artefactID = $(this).val();

		if (artefactID) {
			$('#document_version_documentLevels > option').removeAttr("selected");
			documentLevels.removeAttr("disabled");
			getLevels(artefactID);

			project.val("");

			let htmlAuthor = '';
			htmlAuthor += "<option value=" + '' + ">" + "\<\< Sélectionnez un projet \>\>" + "</option>";
			author.html(htmlAuthor);
			author.attr("disabled", "disabled");

			let htmlAuthorQa = '';
			htmlAuthorQa += "<option value=" + '' + ">" + "\<\< Sélectionnez un projet \>\>" + "</option>";
			authorQa.html(htmlAuthorQa);
			authorQa.attr("disabled", "disabled");

			centers.removeAttr("disabled")
			$("#document_version_centers option[value]").remove();
			countries.removeAttr("disabled")
			$("#document_version_countries option[value]").remove();

		} else {
			let html = '';
			html += "<option value=" + '' + ">" + "\<\< Sélectionnez un artefact \>\>" + "</option>";
			documentLevels.html(html);
			documentLevels.attr("disabled", "disabled");

			project.val("");

			let htmlAuthor = '';
			htmlAuthor += "<option value=" + '' + ">" + "\<\< Sélectionnez un projet \>\>" + "</option>";
			author.html(htmlAuthor);
			author.attr("disabled", "disabled");

			let htmlAuthorQa = '';
			htmlAuthorQa += "<option value=" + '' + ">" + "\<\< Sélectionnez un projet \>\>" + "</option>";
			authorQa.html(htmlAuthorQa);
			authorQa.attr("disabled", "disabled");

			centers.removeAttr("disabled")
			$("#document_version_centers option[value]").remove();
			countries.removeAttr("disabled")
			$("#document_version_countries option[value]").remove();

		}

	})

	documentLevels.on('change', function () {
		let documentLevelsID = $(this).val();
		if (documentLevelsID) {
			// center
			if (documentLevelsID === '2') {
				console.log('LEVEL CENTRE DONC PAYS DISABLED')
				countries.attr("disabled", "disabled")
				$("#document_version_countries option[value]").remove();

			} else {
				// pays
				console.log('LEVEL PAYS DONC CENTRE DISABLED')
				centers.attr("disabled", "disabled")
				$("#document_version_centres option[value]").remove();
			}
		} else {
			project.val("");

			let htmlAuthor = '';
			htmlAuthor += "<option value=" + '' + ">" + "\<\< Sélectionnez un projet \>\>" + "</option>";
			author.html(htmlAuthor);
			author.attr("disabled", "disabled");

			let htmlAuthorQa = '';
			htmlAuthorQa += "<option value=" + '' + ">" + "\<\< Sélectionnez un projet \>\>" + "</option>";
			authorQa.html(htmlAuthorQa);
			authorQa.attr("disabled", "disabled");

			centers.removeAttr("disabled")
			$("#document_version_centers option[value]").remove();

			countries.removeAttr("disabled")
			$("#document_version_countries option[value]").remove();

		}
	});

	sponsor.on('change', function () {
		let sponsorID = $(this).val();

		if (sponsorID) {
			$('#document_version_sponsor > option').removeAttr("selected")
			getProjects(sponsorID)
			project.removeAttr('disabled')
		} else {
			let html = ''
			html += "<option value=" + '' + ">" + "\<\< Sélectionnez un promoteur \>\>" + "</option>"
			project.html(html)
			project.attr("disabled", "disabled")

			let html2 = ''
			html2 += "<option value=" + '' + ">" + "\<\< Sélectionnez un projet \>\>" + "</option>"
			countries.html(html2)
			countries.attr("disabled", "disabled")
			centers.html(html2)
			centers.attr("disabled", "disabled")

			let htmlAuthor = '';
			htmlAuthor += "<option value=" + '' + ">" + "\<\< Sélectionnez un projet \>\>" + "</option>";
			author.html(htmlAuthor);
			author.attr("disabled", "disabled");

			let htmlAuthorQa = '';
			htmlAuthorQa += "<option value=" + '' + ">" + "\<\< Sélectionnez un projet \>\>" + "</option>";
			authorQa.html(htmlAuthorQa);
			authorQa.attr("disabled", "disabled");
		}

	})


	project.on('change', function () {
		let projectID = $(this).val();
		let documentLevelsID = $(documentLevels).val();

		if (projectID) {
			if (documentLevelsID) {
				// center
				if (documentLevelsID === '2') {
					getCenters(projectID)
					centers.removeAttr('disabled')
				} else {
					// pays
					getCountries(projectID);
					countries.removeAttr('disabled')
				}
			} else {
				getCenters(projectID)
				getCountries(projectID);
			}

			// Auteur
			getAuthors(projectID)
			author.removeAttr('disabled')
			// AQ réalisé par
			getAuthorQa(projectID)
			authorQa.removeAttr('disabled')
		}  else {
			let html = '';
			html += "<option value=" + '' + ">" + "\<\< Sélectionnez un projet \>\>" + "</option>";
			countries.html(html);
			centers.html(html);
			countries.attr("disabled", "disabled")
			centers.attr("disabled", "disabled")

			let htmlAuthor = '';
			htmlAuthor += "<option value=" + '' + ">" + "\<\< Sélectionnez un projet \>\>" + "</option>";
			author.html(htmlAuthor);
			author.attr("disabled", "disabled");

			let htmlAuthorQa = '';
			htmlAuthorQa += "<option value=" + '' + ">" + "\<\< Sélectionnez un projet \>\>" + "</option>";
			authorQa.html(htmlAuthorQa);
			authorQa.attr("disabled", "disabled");



		}
	})


	function getSections(zoneID) {

		$.ajax({
			url: Routing.generate('document.get.section.xhr', {zoneID: zoneID}),
			type: "GET",
			dataType: 'json',
			success: function (response) {

				let data = JSON.parse(response.sections);

				let html = '';
				html += "<option value=" + '' + ">" + "<< Sélectionnez une section >>" + "</option>";

				for (var key in data) {
					let sectionObj = data[key];
					html += "<option value=" + sectionObj.id + ">" + sectionObj.name + "</option>";
				}

				section.find('option').remove();
				section.html(html);

				$('#document_version_section > option').removeAttr("selected");
			},
			error: function (response) {
				console.log('error');
				console.log(response.statusLabel + ' :' + response.errorMessage);
			}
		});
	}

	function getArtefacts(sectionID) {

		$.ajax({
			url: Routing.generate('document.get.artefact.xhr', {sectionID: sectionID}),
			type: "GET",
			dataType: 'json',
			success: function (response) {

				let data = JSON.parse(response.artefacts);

				let html = '';
				html += "<option value=" + '' + ">" + "Sélectionnez un Artefact" + "</option>";

				for (let key in data) {
					let artefactObj = data[key];
					html += "<option value=" + artefactObj.id + ">" + artefactObj.name + "</option>";
				}

				artefact.find('option').remove();
				artefact.html(html);

				$('#document_version_artefact > option').removeAttr("selected");
			},
			error: function (response) {
				console.log('error');
				console.log(response.statusLabel + ' :' + response.errorMessage);
			}
		});
	}

	function getLevels(artefactID) {

		$.ajax({
			url: Routing.generate('document.get.level.xhr', {artefactID: artefactID}),
			type: "GET",
			dataType: 'json',
			success: function (response) {

				let data = JSON.parse(response.levels);

				let html = '';
				html += "<option value=" + '' + ">" + "<< Sélectionnez un niveau >>" + "</option>";

				for (let key in data) {
					let levelObj = data[key];
					html += "<option value=" + levelObj.id + ">" + levelObj.code + "</option>";
				}

				documentLevels.find('option').remove();
				documentLevels.html(html);

				$('#document_version_documentLevels > option').removeAttr("selected");
			},
			error: function (response) {
				console.log('error');
				console.log(response.statusLabel + ' :' + response.errorMessage);
			}
		});
	}

	function getProjects(sponsorID) {

		$.ajax({
			url: Routing.generate('document.get.project.xhr', {sponsorID: sponsorID}),
			type: "GET",
			dataType: 'json',
			success: function (response) {

				let data = JSON.parse(response.projects);
				console.log('Project change ', data);

				let html = '';
				html += "<option value=" + '' + ">" + "<< Sélectionnez un projet >>" + "</option>";

				for (let key in data) {
					let levelObj = data[key];
					html += "<option value=" + levelObj.id + ">" + levelObj.acronyme + "</option>";
				}

				project.find('option').remove();
				project.html(html);

				$('#document_version_project > option').removeAttr("selected");
			},
			error: function (response) {
				console.log('error');
				console.log(response.statusLabel + ' :' + response.errorMessage);
			}
		});
	}

	function getCountries(projectID) {

		$.ajax({
			url: Routing.generate('document.get.country.xhr', {projectID: projectID}),
			type: "GET",
			dataType: 'json',
			success: function (response) {

				let data = JSON.parse(response.countries);
				console.log(data)

				let html = '';

				for (var key in data) {
					let countryObj = data[key];
					html += "<option value=" + countryObj.id + ">" + countryObj.name + "</option>";
				}

				countries.find('option').remove();
				countries.html(html);

				/*countries.closest('div').find('label').css('display', 'block');
				let countriesTs = TailSelect(countries.get(0), {
					hideDisabled: true,
					placeholder: "<< Pays >>",
					search: true,
					multiple: true
				})

				countriesTs.enable()*/

				$('#document_version_countries > option').removeAttr("selected");
			},
			error: function (response) {
				console.log('error');
				console.log(response.statusLabel + ' :' + response.errorMessage);
			}
		});

	}

	function getCenters(projectID) {

		$.ajax({
			url: Routing.generate('document.get.center.xhr', {projectID: projectID}),
			type: "GET",
			dataType: 'json',
			success: function (response) {

				let data = JSON.parse(response.centers);

				let html = '';

				for (var key in data) {
					let centerObj = data[key];
					html += "<option value=" + centerObj.id + ">" + centerObj.name + "</option>";
				}

				centers.find('option').remove();
				centers.html(html);

				/*centers.closest('div').find('label').css('display', 'block');
				let centersTs = TailSelect(centers.get(0), {
					hideDisabled: true,
					placeholder: "<< Centres >>",
					search: true,
					multiple: true
				})

				centersTs.enable()*/


				$('#document_version_centers > option').removeAttr("selected");
			},
			error: function (response) {
				console.log('error');
				console.log(response.statusLabel + ' :' + response.errorMessage);
			}
		});
	}

	function getAuthors(projectID) {

		$.ajax({
			url: Routing.generate('document.get.author.xhr', {projectID: projectID}),
			type: "GET",
			dataType: 'json',
			success: function (response) {

				let data = JSON.parse(response.authors);
				console.log(data)

				let html = '';

				for (let key in data) {
					let authorObj = data[key];
					console.log(authorObj.user)
					html += "<option value=" + authorObj.id + ">" + authorObj.user.lastName + " " +authorObj.user.firstName + "</option>";
				}

				author.find('option').remove();
				author.html(html);

				$('#document_version_author > option').removeAttr("selected");
			},
			error: function (response) {
				console.log('error');
				console.log(response.statusLabel + ' :' + response.errorMessage);
			}
		});

	}

	function getAuthorQa(projectID) {
		$.ajax({
			url: Routing.generate('document.get.authorQa.xhr', {projectID: projectID}),
			type: "GET",
			dataType: 'json',
			success: function (response) {

				let data = JSON.parse(response.authorQa);

				let html = '';

				for (let key in data) {
					let authorObj = data[key];
					console.log(authorObj.user)
					html += "<option value=" + authorObj.id + ">" + authorObj.user.lastName + " " +authorObj.user.firstName + "</option>";
				}

				authorQa.find('option').remove();
				authorQa.html(html);

				$('#document_version_author > option').removeAttr("selected");
			},
			error: function (response) {
				console.log('error');
				console.log(response.statusLabel + ' :' + response.errorMessage);
			}
		});
	}

})
