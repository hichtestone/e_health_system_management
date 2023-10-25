import Vue 				from "vue";
import axios 			from 'axios';
import ListPatientTable from "../Component/patient-tracking/ListPatientTable";
import Datepicker 		from 'vuejs-datepicker';
import multiselect 		from 'vue-multiselect';
import moment 			from 'moment';
import SmartTable 		from 'vuejs-smart-table';
import JsonCSV 			from 'vue-json-csv';
import ToggleButton     from 'vue-js-toggle-button';

require('vue-multiselect/dist/vue-multiselect.min.css');

Vue.component('multiselect', multiselect)
Vue.component('datepicker', Datepicker)
Vue.use(SmartTable)
Vue.component('downloadCsv', JsonCSV)
Vue.use(ToggleButton)

document.addEventListener('DOMContentLoaded', () => {

	let el = document.getElementById('ListPatientTable');

	if (null != el) {
		Vue.prototype.$http = axios
		Vue.filter('formatDate', function(value) {
			if (value) {
				return moment(String(value), 'YYYY-MM-DD').format('DD/MM/YYYY')
			}
		})
		new Vue({
			el: '#ListPatientTable',
			components: {ListPatientTable},
		})
	}
})
