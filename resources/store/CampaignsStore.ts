import { defineStore } from 'pinia';
import {ref} from "vue";
import axios from "axios";
import {Campaign} from "../types/Campaign";

export const useCampaignsStore = defineStore('campaigns', () => {
    const campaigns = ref<Campaign[]>([]);
    const loading = ref(false);

    const fetchCampaigns = async () => {
        loading.value = true;
        const response = await axios.get('/campaigns');
        campaigns.value = response.data.reverse();
        loading.value = false;
    };

    const fetchCampaignRecipients = async (campaignId: number) => {
        loading.value = true;
        const response = await axios.get(`/campaigns/${campaignId}/recipients`);
        loading.value = false;
        const campaignToUpdate = campaigns.value.find(c => c.id === campaignId);
        campaignToUpdate.recipients = response.data;
    }

    return {
        campaigns,
        loading,
        fetchCampaigns,
        fetchCampaignRecipients,
    };
});
