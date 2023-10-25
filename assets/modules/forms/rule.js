
document.addEventListener('DOMContentLoaded', function () {

	let ruleStudyTranfer = $('#rule_studyTransfer');
	let ruleOutTransfer = $('#rule_outStudyTransfer');
	let mappingCB = $('#rule_mapping');


	if(ruleStudyTranfer){

		let studyTransferTerritory = $('#studyTransferTerritory');
		let outTransferTerritory = $('#outTransferTerritory');
		let ruleMapping = $('#ruleMapping');

		function refreshStudyTranfer(){
			if(ruleStudyTranfer.prop('checked')){
				studyTransferTerritory.css('display', '');
				studyTransferTerritory.find('select, input').prop('required', true);
				studyTransferTerritory.find('label').addClass('required');
			}else{
				studyTransferTerritory.css('display', 'none');
				studyTransferTerritory.find('select, input').prop('required', false);
				studyTransferTerritory.find('label').removeClass('required');
			}
		}
		refreshStudyTranfer();
		ruleStudyTranfer.change(function(){
			refreshStudyTranfer();
		});

		function refreshOut(){
			if(ruleOutTransfer.prop('checked')){
				outTransferTerritory.css('display', '');
				outTransferTerritory.find('select, input:not(:checkbox)').prop('required', true);
				outTransferTerritory.find('label').addClass('required');
			}else{
				outTransferTerritory.css('display', 'none');
				outTransferTerritory.find('select, input:not(:checkbox)').prop('required', false);
				outTransferTerritory.find('label').removeClass('required');
			}
		}
		refreshOut();
		ruleOutTransfer.change(function(){
			refreshOut();
		});


		function mapping(){
			if(mappingCB.prop('checked')){
				ruleMapping.css('display', '');
				ruleMapping.find('select, input:not(:checkbox)').prop('required', true);
				ruleMapping.find('label').addClass('required');
			}else{
				ruleMapping.css('display', 'none');
				ruleMapping.find('select, input:not(:checkbox)').prop('required', false);
				ruleMapping.find('label').removeClass('required');
			}
		}
		mapping();
		mappingCB.change(function(){
			mapping();
		});


	}

});
