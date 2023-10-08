import './bootstrap';

import Vue from 'vue';

import player from './components/playerComponent'

Vue.component('player', player)

const app = new Vue({
    el: '#app',
    components: {player}
})


window.app = app;