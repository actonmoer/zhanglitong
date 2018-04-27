import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex);

export default new Vuex.Store({
	state: {
		cache : {},
		cuisines: [],
		transitionName: true
	},

	mutations: {
		add() {
			state.cache['test'] = "abc";
		}
	}
});

