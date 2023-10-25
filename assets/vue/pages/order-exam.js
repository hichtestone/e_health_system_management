import Vue from "vue";
import axios from 'axios';
import OrderExamTable from "../Component/order-exam/OrderExamTable";
import draggable from "vuedraggable";

Vue.component('draggable', draggable)

document.addEventListener('DOMContentLoaded', () => {

	let el = document.getElementById('OrderExamTable');

	if (null != el) {
		Vue.prototype.$http = axios
		new Vue({
			el: '#OrderExamTable',
			components: {OrderExamTable},
		})
	}
})
