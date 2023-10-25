
let axios = require('axios');
let swal = require('sweetalert2');
let CFloading = require('../CFloading/CFloading');

document.addEventListener('DOMContentLoaded', function () {

	let elCountryLD = $('#institution_country')

	if(elCountryLD.length){

		let typeIdNoFiness = JSON.parse($('#typeIdNoFiness').html())
		let elInstitutionType = $('#institution_institutionType')

		function refreshCountry(){
			if (elCountryLD.val() == '1') {
				$('#institutionDepartment').css('display', '')
				$('#institutionDepartment').find('select').prop('required', true)
				$('#institutionDepartment').find('label').addClass('required')
				if (typeIdNoFiness.indexOf(parseInt(elInstitutionType.val())) === -1) {
					$('#bloc-finess').show()
					$('#bloc-finess').find('input').prop('required', true)
					$('#bloc-finess').find('label').addClass('required')


					$('#bloc-siret').show();
					$('#bloc-siret').find('input').prop('required', false)
					$('#bloc-siret').find('label').removeClass('required')

				} else {
					$('#bloc-finess').show()
					$('#bloc-finess').find('input').prop('required', true)
					$('#bloc-finess').find('label').addClass('required')


					$('#bloc-siret').show();
					$('#bloc-siret').find('input').prop('required', false)
					$('#bloc-siret').find('label').removeClass('required')

				}
			} else {

				$('#institutionDepartment').css('display', 'none')
				$('#institutionDepartment').find('select').prop('required', false)
				$('#institutionDepartment').find('label').removeClass('required')


				$('#bloc-finess').hide()
				$('#bloc-finess').find('input').prop('required', false)
				$('#bloc-finess').find('label').removeClass('required')

				$('#bloc-siret').hide()
				$('#bloc-siret').find('input').prop('required', false)
				$('#bloc-siret').find('label').removeClass('required')

			}
		}
		refreshCountry();
		elCountryLD.change(function(){
			refreshCountry()
		});
		elInstitutionType.change(function(){
			refreshCountry()
		});
	}

	// services
	let institutionId = $('#institution-services').attr('data-institution-id')
	if (institutionId) {
		$('#service-new, .service-edit').click(function(){
			CFloading.start();
			axios.get(this.href)
				.then(response => {
					swal.fire({
						showCloseButton: true,
						showConfirmButton: false,
						html: response.data.html,
						onOpen(popup) {
							let elCB = $('#service_addressInherited');
							let elAddress = $('#service-address');
							function updateAddressInherited(){
								if(elCB.prop('checked')){
									elAddress.css('display', 'none');
									elAddress.find('.required label').removeClass('required');
									elAddress.find('.required input').prop('required', false);
									elAddress.find('.required select').prop('required', false);
								}else{
									elAddress.css('display', '');
									elAddress.find('.required label').addClass('required');
									elAddress.find('.required input').prop('required', true);
									elAddress.find('.required select').prop('required', false);
								}
							}
							elCB.change(function(){
								updateAddressInherited();
							})
							updateAddressInherited();
						},
					});
				})
				.catch(res => {
					console.log(res);
				})
				.finally(() => {
					CFloading.stop();
				});

			return false;
		})
	}

});
