
function Textarray(root) {

	let id = root.attr('data-ta-id');
	let name = root.attr('data-ta-name');
	let i = root.find('.fa-minus-circle').length-1;
	let elPlusButton = root.find('.fa-plus-circle').closest('a');

	function addNewItem(){

		// add new
		let elParent = elPlusButton.closest('div');
		let newEl = $(generateItem());
		newEl.append(elPlusButton);
		root.append(newEl);

		// add moins
		let elMinus = $('<a href="javascript:void(0)" class="btn btn-primary"><i class="fa fa-minus-circle"></i></a>');
		elMinus.click(function(){
			elMinus.closest('div').remove();
			return false;
		})
		elParent.append(elMinus);

	}

	elPlusButton.click(function(){
		addNewItem();
		return false;
	});

	root.find('.fa-minus-circle').closest('a').click(function(){
		$(this).closest('div').remove();
		return false;
	});

	function generateItem(){
		i++;
		let html = '<div data-ta-item="">';
			html += '<input type="text" id="' + id + '_' + i + '" name="' + name + '[]' + '" value="" /> ';
		html += '</div>';
		return html;
	}

}

$(function() {
	let els = $(".form-group[data-textarray]");
	for(let el of els){
		Textarray($(el));
	}
});

module.exports = Textarray;
