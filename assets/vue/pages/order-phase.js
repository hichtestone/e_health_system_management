import Vue from "vue";
import axios from 'axios';
import OrderPhaseTable from "../Component/order-phase/OrderPhaseTable";
import draggable from "vuedraggable";

Vue.component('draggable', draggable)

document.addEventListener('DOMContentLoaded', () => {

	let el = document.getElementById('OrderPhaseTable');

	if (null != el) {
		Vue.prototype.$http = axios
		new Vue({
			el: '#OrderPhaseTable',
			components: {OrderPhaseTable},
		})
	}
})
