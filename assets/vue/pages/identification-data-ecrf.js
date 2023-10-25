import Vue from "vue";
import axios from 'axios';
import VueFormulate from '@braid/vue-formulate';
import IdentificationDataTable from "../Component/identification-data/IdendificationDataTable";
import '../../modules/formulate/formulate.min.css'

Vue.use(VueFormulate)

document.addEventListener('DOMContentLoaded', () => {

	let el = document.getElementById('IdentificationDataTable');

	if (null != el) {
		Vue.prototype.$http = axios
		// Filter Capitalize
		Vue.filter('capitalize', function (value) {
			if (!value) return ''
			value = value.toString()
			return value.charAt(0).toUpperCase() + value.slice(1)
		})
		new Vue({
			el: '#IdentificationDataTable',
			components: {IdentificationDataTable},
		})
	}
})
