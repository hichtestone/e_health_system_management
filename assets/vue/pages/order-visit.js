import Vue from "vue";
import axios from 'axios';
import OrderVisitTable from "../Component/order-visit/OrderVisitTable";
import draggable from "vuedraggable";

Vue.component('draggable', draggable)

document.addEventListener('DOMContentLoaded', () => {

	let el = document.getElementById('OrderVisitTable');

	if (null != el) {
		Vue.prototype.$http = axios
		new Vue({
			el: '#OrderVisitTable',
			components: {OrderVisitTable},
		})
	}
})
