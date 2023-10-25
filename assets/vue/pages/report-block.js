import Vue from "vue";
import axios from 'axios';
import ReportBlock from "../Component/report-block/ReportBlock";
import VueFormulate from '@braid/vue-formulate';

document.addEventListener('DOMContentLoaded', () => {

	let el = document.getElementById('ReportBlock');

	if (null != el) {
		Vue.prototype.$http = axios
		new Vue({
			el: '#ReportBlock',
			components: { ReportBlock },
		})
	}
})
