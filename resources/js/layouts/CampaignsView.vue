<template>
    <div>
        <div class="page-header">
            <h1>Campaigns</h1>
        </div>
        <v-table>
            <thead>
                <tr>
                    <th class="text-left">ID</th>
                    <th class="text-left">Name</th>
                    <th class="text-left">Send Date</th>
                    <th class="text-left">Recipients</th>
                    <th class="text-left">Delivery %</th>
                    <th class="text-left">Open Rate</th>
                    <th class="text-left">Click-thru Rate</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="campaign in sortedCampaigns" :key="campaign.id" @click="goToCampaign(campaign.id)" class="campaign-row">
                    <td>{{ campaign.id }}</td>
                    <td>{{ campaign.email_name }}</td>
                    <td>{{ humanReadableDate(campaign.send_date) }} <span v-if="campaign.scheduled">🗓️</span></td>
                    <td>{{ campaign.stats.total_recipients}}</td>
                    <td>{{ campaign.stats.delivered_percentage}}%</td>
                    <td>{{ campaign.stats.opened_percentage}}%</td>
                    <td>{{ campaign.stats.clicked_percentage}}%</td>
                </tr>
            </tbody>
        </v-table>
    </div>
</template>
<script lang="ts" setup>
import { useRouter } from "vue-router";
import { onMounted, computed } from "vue";
import { useCampaignsStore} from "../../store/CampaignsStore";

const store = useCampaignsStore();
const router = useRouter();

onMounted(() => {
    fetchCampaigns();
});

async function fetchCampaigns() {
    try {
        await store.fetchCampaigns();
    } catch {
        console.error("Failed to fetch campaigns");
    }
}

const sortedCampaigns = computed(() => {
    return store.campaigns.sort((a, b) => {
        return new Date(b.send_date).getTime() - new Date(a.send_date).getTime();
    });
});


function goToCampaign(campaignId) {
    router.push(`/campaign-page/${campaignId}`)
}

function humanReadableDate(date: string) {
    return new Date(date).toLocaleString();
}
</script>
<style lang="scss" scoped>
.page-header {
    margin-bottom: 1em;
}

.campaign-row {
    cursor: pointer;
    &:hover {
        background-color: rgba(#FFF, 0.1);
    }
}
</style>
