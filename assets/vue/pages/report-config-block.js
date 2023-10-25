import moment from "moment";
import Vue from "vue";
import axios from 'axios';
import ReportConfigBlock from "../Component/report-config/ReportConfigBlock";


document.addEventListener('DOMContentLoaded', () => {
	let el = document.getElementById('ReportConfigBlock');

	if (null != el) {
		Vue.prototype.$http = axios;
		Vue.filter('formatDate', function (value) {
			if (value) {
				return moment(String(value), 'YYYY-MM-DD').format('DD/MM/YYYY')

			}
		});
		new Vue({
			el: '#ReportConfigBlock',
			components: {ReportConfigBlock},
		});
	}
});
