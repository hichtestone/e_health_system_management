import Vue from "vue";
import axios from 'axios';
import OrderReportBlock from "../Component/report-block/OrderReportBlock";
import draggable from "vuedraggable";

Vue.component('draggable', draggable)

document.addEventListener('DOMContentLoaded', () => {

	let el = document.getElementById('OrderReportBlock');

	if (null != el) {
		Vue.prototype.$http = axios
		new Vue({
			el: '#OrderReportBlock',
			components: {OrderReportBlock},
		})
	}
})
