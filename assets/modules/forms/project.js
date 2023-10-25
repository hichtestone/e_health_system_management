
const TailSelect = require('tail.select')
const axios = require('axios');
require('./_project.scss')
const swal = require('sweetalert2')
let Main = require('./../Utils/main')

document.addEventListener('DOMContentLoaded', function(){

	let respDelegEl = document.getElementById('resp_deleg');

	if(respDelegEl){

		let dateToday = new Date();
		// gestion champs pays/étude internationale ou pas
		let countryList = document.getElementById('project_countries');

		//Promoter
		let sponsorList = document.getElementById('project_sponsor');


		let countryListTS = TailSelect(countryList, {
			hideDisabled: true,
			placeholder: "Pays",
			search: true
		});


		// gestion internationalisation
		let territorySelect = $("input[name='project[territory]']");
		function updateCountriesDisplay()
		{
			if(typeof territorySelect.filter(':checked').val() === 'undefined') {
				countryListTS.options.all('unselect');
				countryListTS.config('multiple', false);
				$(countryList).val('');
				countryListTS.disable();
			}else if(territorySelect.filter(':checked').val() === '1'){
				countryListTS.config('multiple', false);
				countryListTS.options.all('unselect');
				countryListTS.options.handle('select', '1', '#');
				$(countryList).val(1);
				countryListTS.disable();
			}else{
				countryListTS.config('multiple', true);
				countryListTS.enable();
				$("label[for=project_countries]").addClass('required');
				if($(countryList).val().length === 0) {
					$(countryListTS.select).css('border', '1px solid red');
				}
			}
		}
		territorySelect.change(function(){
			updateCountriesDisplay();
		});
		updateCountriesDisplay();
		countryListTS.on('change', function(item, state){
			if($(countryList).val().length > 0){
				$(countryListTS.select).css('border', '');
			}else{
				$(countryListTS.select).css('border', '1px solid red');
			}
		});


		// gestion type de traitement
		let drugDiv = $('#drugDiv');
		let typeTraitementSelect = $('#project_trailTreatments');
		let typeTraitementDrugMandatory = ['3','4','5','6'];
		let typeTraitementNAId = '6';
		let previouslySelected = [];

		function updateDrugMandatory(){

			let currentlySelected = typeTraitementSelect.val();

			// check si drug mandatory
			if(currentlySelected.filter(function(item) {
				return typeTraitementDrugMandatory.indexOf(item) !== -1;
			}).length > 0){
				drugDiv.find('legend').addClass('required');
				drugDiv.find('input').first().prop('required', true);
			}else{
				drugDiv.find('legend').removeClass('required');
				drugDiv.find('input').first().prop('required', false);
			}

			if ('' != currentlySelected) {

				$('#project_drugs').html('')
				let api_url = $('#drugDiv').attr('data-api-url')

				axios.get(`${api_url}?treatment=${currentlySelected}`)
					.then(response => {
						$('#project_drugs').html(response.data.html)
					})
			}
		}

		typeTraitementSelect.change(function(){
			let currentlySelected = typeTraitementSelect.val();
			updateDrugMandatory();

			// gère sélection de NA qui déselectionne le reste
			let newSelections = currentlySelected.filter(function (element) {
				return previouslySelected.indexOf(element) === -1;
			});
			previouslySelected = currentlySelected;
			if(newSelections.length > 0){
				if(newSelections[0] === typeTraitementNAId){
					previouslySelected = [typeTraitementNAId];
					typeTraitementSelect.val(previouslySelected);
				}else{
					let index = currentlySelected.indexOf(typeTraitementNAId);
					if(index > -1){
						currentlySelected.splice(index, 1);
						typeTraitementSelect.val(currentlySelected);
					}
				}
			}
		});

		if(0 < typeTraitementSelect.length) {
			updateDrugMandatory();
		}




		// gestion délégation quand on choisi Unicancer comme promoteur
		function updateResponsablilityDisplay() {
			if(sponsorList.value === '14'){
				respDelegEl.style.display = 'none';
			}else{
				respDelegEl.style.display = '';
				//$(typePharmacovigilance).css('display', 'none');
			}
		}
		sponsorList.addEventListener('change', function (e) {
			updateResponsablilityDisplay();
		});
		updateResponsablilityDisplay();



		// gestion check pharmacovigilance
		let typePharmacovigilance = $('#pharmaType');
		let inputPharmaCoDeleg = $("input[name='project[delegation][pharmacovigilance]']");
		function updatePharmacoDisplay() {
			let value = inputPharmaCoDeleg.filter(':checked').val();
			if (value) {
				$(typePharmacovigilance).css('display', '');
			} else {
				$(typePharmacovigilance).css('display', 'none');
			}
		}
		inputPharmaCoDeleg.change(function() {
			updatePharmacoDisplay();
		});
		updatePharmacoDisplay();




		// gestion de Logo
		$('.custom-file-input').on('change', function (event) {
			let inputFile = event.currentTarget;
			$(inputFile).parent().find('.custom-file-label').html(inputFile.files[0].name);
		})

		//Nom médicament expérimental, auxiliaire ou concomittant(DCI)
		$('#addDrug').bind("click", function (event) {
			event.preventDefault();
			let newDiv = $(' <div class="row mb-1">\n' +
				'            <div class="col-4">\n' +
				'            </div>\n' +
				'            <div class="col-4">\n' +
				'            <input type="text" name="drug[]" required="required" placeholder="nom médicament" class="form-control">' +
				'            </div>\n' +
				'            <div class="col-3">\n' +
				'                <button class="btn btn-primary removeDrug"><i class="fa fa-minus-circle"></i></button>\n' +
				'            </div>\n' +
				'        </div>' +
				'');

			$('#newDrugField').append(newDiv);

			$("body").on("click", ".removeDrug", function () {
				$(this).closest(".row").remove();
			});
		});

		$("body").on("click", ".removeDrug", function (e) {
			e.preventDefault();
			let idDrug = $(this).children('span').first().text();
			if (idDrug != '') {
				$(this).closest(".drug-"+idDrug).remove();

			} else {
				$(this).closest(".row").remove();
			}
		});

	}


	if ($('[data-drug-project]').length) {

		// On change project "Type de traitement dans l'étude"
		$(document).delegate('select[data-drug-project]', 'change', function () {

			let thisClosest = $(this).closest('tr')
			if (0 == thisClosest.length && 0 < $(this).closest('.admin-block').length) {
				thisClosest = $(this).closest('.admin-block')
			}

			let block = $(this).data('show')+$(this).val()



			// Hide other
			thisClosest.find('*[data-hide="'+$(this).data('show')+'"]').addClass('d-none')

			if ('treatment_' === $(this).data('show')) {
				thisClosest.find('[data-hide="drug_rcp_bi_"]').addClass('d-none')
			}

			// show next block
			thisClosest.find('[data-block="'+block+'"]').removeClass('d-none')
		})

		// On click add btn row
		$(document).delegate('a[data-add-btn]','click', function () {

			let warning_add_project = $('[data-dialog-warning_add_project]').attr('data-dialog-warning_add_project')
			let warning_add_rows = $('[data-dialog-warning_add_project]').attr('data-dialog-warning_add_rows')
			let max_rows = parseInt($('[data-dialog-warning_add_project]').attr('data-max_rows'))


			let thisRow = $(this).closest( 'tr' )[0];
			let row_count = $(this).closest( 'table' ).find('tr').length

			// filled field count
			let field_count = 0
			$(thisRow).find('select').each(function() {
				if ('' !== $(this).val()) {
					++field_count
				}
			})

			// If more than 5 rows
			if (max_rows < row_count) {
				swal.fire(warning_add_rows)
				return
			}

			// If fields are empty
			if (0 == field_count) {
				swal.fire(warning_add_project)
				return
			}

			// Clone row and add action
			$(thisRow).clone().insertAfter(thisRow)
				// Hide DIV
				.find('div[data-block]').addClass('d-none')
				// Reset field
				.find('select').val('')

			$(this).addClass('d-none')
			$(thisRow).find('a[data-remove-btn]').removeClass('d-none')
		})

		// On click remove btn row
		$(document).delegate('a[data-remove-btn]','click', function () {
			let thisRow = $(this).closest( 'tr' )[0];
			$(thisRow).remove()
		})
	}


	// Population étudiée

	let projectStudyPopulation = $('#project_studyPopulation')

	Main.renderTailSelectRequired(projectStudyPopulation)
	Main.renderTailSelect(projectStudyPopulation, 'Population étudiée', true)

	projectStudyPopulation.on('change', function () {
		Main.renderTailSelectRequired(projectStudyPopulation)
	})
});
