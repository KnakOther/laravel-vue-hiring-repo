import "./bootstrap";
import { createApp } from "vue";
import app from "./layouts/App.vue";
import vuetify from "./vuetify";
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'

import { createRouter, createWebHistory } from "vue-router";

import HomeView from "./layouts/HomeView.vue";
import SendView from "./layouts/SendView.vue";
import CampaignsView from "./layouts/CampaignsView.vue";
import RecipientView from "./layouts/RecipientView.vue";
import CampaignView from "@/layouts/CampaignView.vue";
import {createPinia} from "pinia";

const routes = [
    { path: '/', component: HomeView },
    { path: '/send-page', component: SendView },
    { path: '/campaigns-page', component: CampaignsView },
    { path: '/recipients-page', component: RecipientView },
    {
        path: "/campaign-page/:id",
        component: CampaignView,
        props:  (route) => ({
            campaignId: parseInt(route.params.id),
        })
    },
];


const pinia = createPinia();

const router = createRouter({
    history: createWebHistory(),
    routes,
});

createApp(app).component('VueDatePicker', VueDatePicker).use(router).use(vuetify).use(pinia).mount("#app");
