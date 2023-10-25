'use strict';

/**
 * Publication
 */

const TailSelect = require('tail.select');

// Gestion type de communication
document.addEventListener('DOMContentLoaded', function(){
	let isCommunicationTypeSelect = document.getElementById('publication_communicationType');

	if(isCommunicationTypeSelect){

		let isCongressList = document.getElementById('publication_isCongress');

		let journalList = document.getElementById('publication_journals');
		let labelJournalList = $('label[for="publication_journals"]');

		let congressList = document.getElementById('publication_congress');
		let labelCongressList = $('label[for="publication_congress"]');

		let labelComment = $('label[for="publication_comment"]');


		// date d/mY ou Y
		let typeComm = $('#publication_communicationType');
		let dateY = $('#dateY');
		let dateYmd = $('#dateYmd');
		function updateDateDisplay(){
			if(typeComm.val() == ''){
				dateY.css('display', 'none');
				dateYmd.css('display', 'none');
			}else if(typeComm.val() == 2){
				dateY.css('display', '');
				dateYmd.css('display', 'none');
			}else{
				dateY.css('display', 'none');
				dateYmd.css('display', '');
			}
		}
		typeComm.change(function(){
			updateDateDisplay();
		});
		updateDateDisplay();

		if(dateYmd.find('input').val() !== ''){
			dateY.find('input').val(dateYmd.find('input').val().split('/')[2]);
		}
		dateY.find('input').change(function(){
			dateYmd.find('input').val('01/01/'+$(this).val());
		});
		dateYmd.find('input').change(function(){
			dateY.find('input').val($(this).val().split('/')[2]);
		});


		// disabled
		$(isCongressList).prop('disabled', true);
		// hidden journals
		$(journalList).hide();
		$(labelJournalList).css('display', 'none');

		// hidden congress
		$(congressList).hide();
		$(labelCongressList).css('display', 'none');

		//$(labelComment).append('mon text');
		$(labelComment).after('&nbsp;&nbsp;<i class="fa fa-question-circle" ' +
			'data-toggle="tooltip" ' +
			'data-placement="right" ' +
			'title="Merci de renseigner le journal ou le lieu du congrès, son titre et ses auteurs.' +
			'">');

		$('[data-toggle="tooltip"]').tooltip();

		if(isCommunicationTypeSelect !== null) {

			function updateCountriesDisplay() {
				if(isCommunicationTypeSelect.value === ''){
					$(isCongressList).prop("selected", false)
					$(isCongressList).prop('disabled', true);

					$(labelJournalList).css('display', 'none');
					$(journalList).hide();

					$(labelCongressList).css('display', 'none');
					$(congressList).hide();
				}else if(isCommunicationTypeSelect.value === '1'){
					$(isCongressList).prop("selected", false)
					$(isCongressList).prop('disabled', true);

					$(labelCongressList).css('display', 'none');
					$(congressList).hide();

					$(labelJournalList).css('display', 'inline-block');
					$(journalList).show();
				}else{
					$(isCongressList).prop('disabled', false);
					$(labelCongressList).css('display', 'inline-block');
					$(congressList).show();

					$(labelJournalList).css('display', 'none');
					$(journalList).hide();
				}
			}

			updateCountriesDisplay();
			isCommunicationTypeSelect.addEventListener('change', function (e) {
				updateCountriesDisplay();
			});
		}
	}

});

