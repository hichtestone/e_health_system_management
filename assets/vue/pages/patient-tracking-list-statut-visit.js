import Vue 				from "vue"
import axios 			from 'axios'
import ListVisitTable 	from "../Component/patient-tracking/ListVisitTable"
import Datepicker 		from 'vuejs-datepicker'
import moment 			from 'moment'
import JsonCSV 			from 'vue-json-csv'
import VueFormulate 	from '@braid/vue-formulate'
import SmartTable 		from 'vuejs-smart-table'

Vue.use(Datepicker)
Vue.use(SmartTable)
Vue.component('downloadCsv', JsonCSV)
Vue.use(VueFormulate)

document.addEventListener('DOMContentLoaded', () => {

	let el = document.getElementById('ListVisitTable');

	if (null != el) {
		Vue.prototype.$http = axios
		Vue.filter('formatDate', function(value) {
			if (value) {
				return moment(String(value), 'DD/MM/YYYY').format('DD/MM/YYYY')
			}
		})
		new Vue({
			el: '#ListVisitTable',
			components: {ListVisitTable},
		})
	}
})
