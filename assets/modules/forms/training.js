// Tail select
const TailSelect = require('tail.select');

document.addEventListener('DOMContentLoaded', function() {
	// gestion de champs "support"
	$('.custom-file-input').on('change', function (event) {
		let inputFile = event.currentTarget;
		$(inputFile).parent().find('.custom-file-label').html(inputFile.files[0].name);
	});

	let elSelect= $('#training_users');
	if(elSelect.length){
		elSelect.closest('div').find('label').css('display', 'block');
		let selectTS = TailSelect(elSelect.get(0), {
			multiple: true,
			search: true
		});
		function updSelectRequired(){
			if(elSelect.val().length > 0){
				$(selectTS.select).css('border', '');
			}else{
				$(selectTS.select).css('border', '1px solid red');
			}
		}
		selectTS.on('change', function(item, state){
			updSelectRequired();
		});
		updSelectRequired();
	}

});
