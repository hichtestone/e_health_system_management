require('./query-builder.scss');
const Swal = require('sweetalert2');

function show_hide(showField, hideField) {

	if (null == document.getElementById(showField) || null == document.getElementById(hideField)) {
		return;
	}

	// Show div contains show field
	document.getElementById(showField).classList.remove('d-none');

	// empty value field of hidefield
	let hideFieldSelect = document.getElementById(hideField).querySelector('select');
	hideFieldSelect.selectedIndex = 0;
}

document.addEventListener('DOMContentLoaded', function () {

	let selectorBuilder = $('#builder-basic');

	if (0 === selectorBuilder.length) {
		return;
	}

	// Donnees venant du server
	let filterData = ''  !== selectorBuilder.data('filter') ? JSON.parse(decodeURIComponent(selectorBuilder.data('filter'))) : null;
	let valueData  = ''  !== selectorBuilder.data('value') ? JSON.parse(decodeURIComponent(selectorBuilder.data('value'))) : null;
	let isReadOnly = '1' === selectorBuilder.data('readonly');


	// Click radio button "phase" or "visit"
	let $radioButtonsPhaseVisit = Array.prototype.slice.call(document.querySelectorAll('input[name="schema_condition[phaseVisit]"]'), 0);
	if ($radioButtonsPhaseVisit.length > 0) {

		$radioButtonsPhaseVisit.forEach($el => {
			$el.addEventListener('click', (evt) => {
				document.getElementById('field_phase').classList.add('d-none');
				document.getElementById('field_visit').classList.add('d-none');

				// Afficher bloc phase
				if ('phase' === $el.value) {
					show_hide('field_phase', 'field_visit');
				}

				// Afficher bloc visit
				if ('visit' === $el.value) {
					show_hide('field_visit', 'field_phase');
				}
			});
		});
	}

	// Instance de query builder
	// node_modules/jQuery-QueryBuilder/dist/js/query-builder.js
	selectorBuilder.queryBuilder({
		filters: filterData,
		rules: valueData,
		default_group_flags: {
			no_add_group: true
		},
		icons: {
			add_rule: 'fa fa-plus',
			remove_rule: 'fa fa-trash'
		},
	});

	// Conversion des champs en format
	selectorBuilder.on('afterUpdateRuleValue.queryBuilder', function (e, rule) {

		// Si champ date
		if ('undefined' !== typeof rule.filter.vartype && 'date' === rule.filter.vartype) {
			rule.$el.find('.rule-value-container input').attr('type', 'date');
		}

		// Si champ numeric
		if ('undefined' !== typeof rule.filter.vartype && 'numeric' === rule.filter.vartype) {
			rule.$el.find('.rule-value-container input').attr('type', 'number');
		}

	});

	setTimeout(() => {
		if (isReadOnly) {
			selectorBuilder.find('button').remove();
			selectorBuilder.find('input, select').attr('disabled', 'disabled');
		}
	}, 100);

	selectorBuilder.closest('form').on('submit', function () {

		let variables = [];

		let result = selectorBuilder.queryBuilder('getRules');
		//let result_sql = selectorBuilder.queryBuilder('getSQL')

		let variableFilter = Array.prototype.slice.call(document.querySelectorAll('.rule-filter-container'), 0);

		if (variableFilter.length > 0) {
			variableFilter.forEach($el => {
				// Find select > option[selected]
				let option_selected_value = $el.querySelector('select').options[$el.querySelector('select').selectedIndex].value;

				// remove suffixes "_ct" or "_var"
				option_selected_value = option_selected_value.replace(/_ct/g, '');
				option_selected_value = option_selected_value.replace(/_var/g, '');

				// store inside array
				variables.push(option_selected_value);
			});
		}

		// remove duplicated items inside array
		variables = [...new Set(variables)];

		// fill field schema_condition_variable
		if (null != document.getElementById('schema_condition_variable')) {
			document.getElementById('schema_condition_variable').value = variables.join(',');
		}

		if (!$.isEmptyObject(result) && null !== document.querySelector('input[name="schema_condition[condition]"]')) {
            document.querySelector('input[name="schema_condition[condition]"]').value = JSON.stringify(result, null, 2);
		}

		// Validate visit/phase
		let fieldVisitSelect = document.getElementById('schema_condition_visit');
		let fieldPhaseSelect = document.getElementById('schema_condition_phase');
		let fieldVisitCheckbox = document.getElementById('schema_condition_phaseVisit_0');
		let fieldPhaseCheckbox = document.getElementById('schema_condition_phaseVisit_1');

		if (
			(fieldVisitCheckbox.checked && '' === fieldVisitSelect.value) ||
			(fieldPhaseCheckbox.checked && '' === fieldPhaseSelect.value)
		) {
			event.preventDefault();
			Swal.fire('Un des champs visite/phase est obligatoire');
		}
	});
});


