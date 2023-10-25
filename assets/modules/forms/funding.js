
document.addEventListener('DOMContentLoaded', function(){

	let fundingPublicFundingSelect = document.getElementById("funding_publicFunding");
	let callProjectList 		   = document.getElementById('funding_callProject');
	let callProjectLabel 		   = $('label[for="funding_callProject"]');
	let fundingDemandedAtInput 	   = document.getElementById('funding_demandedAt');
	let fundingDemandedAtLabel 	   = $('label[for="funding_demandedAt"]');

	if (fundingPublicFundingSelect) {

		if(!fundingPublicFundingSelect.checked) {
			$(callProjectList).prop('disabled', true)
			$(callProjectLabel).prop('disabled', true)
			$(fundingDemandedAtInput).prop('disabled', true)
			$(fundingDemandedAtLabel).prop('disabled', true)
		}
		else {
			$(callProjectList).prop('disabled', false)
			$(callProjectLabel).prop('disabled', false)
			$(fundingDemandedAtInput).prop('disabled', false)
			$(fundingDemandedAtLabel).prop('disabled', false)
		}

		$('#new-funding input').on('change', function() {

			if(!fundingPublicFundingSelect.checked) {
				$(callProjectList).prop('disabled', true)
				$(callProjectLabel).prop('disabled', true)
				$(fundingDemandedAtInput).prop('disabled', true)
				$(fundingDemandedAtLabel).prop('disabled', true)
			}

			else {
				$(callProjectList).prop('disabled', false)
				$(callProjectLabel).prop('disabled', false)
				$(fundingDemandedAtInput).prop('disabled', false)
				$(fundingDemandedAtLabel).prop('disabled', false)
			}
		});

	}

});
