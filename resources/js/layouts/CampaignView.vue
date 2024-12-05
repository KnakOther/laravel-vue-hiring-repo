<template>
    <div>
        <RouterLink to="/campaigns-page"
            ><v-btn>← Back To Campaigns</v-btn></RouterLink
        >
        <div v-if="campaign">
            <div class="page-header">
                <h1>{{ campaign.email_name }} Report</h1>
            </div>
            <div class="report-grid" v-if="campaign.recipients.length">
                <v-card>
                    <div class="card-wrapper">
                        <h3>Send Data</h3>
                        <p>
                            Send Date:
                            <span class="big-nice-data">{{
                                getHumanReadableDate(campaign.send_date)
                            }}</span>
                        </p>
                        <p>
                            Send Time:
                            <span class="big-nice-data">{{
                                getHumanReadableTime(campaign.send_date)
                            }}</span>
                        </p>
                        <p>
                            Recipients:
                            <span class="big-nice-data">{{
                                campaign.recipients.length
                            }}</span>
                        </p>
                        <div class="stats">
                            <div class="stat">
                                Delivery Rate:
                                <v-progress-circular
                                    :model-value="
                                        campaign.stats.delivered_percentage
                                    "
                                    :rotate="360"
                                    :size="100"
                                    :width="15"
                                >
                                    <template v-slot:default>
                                        {{
                                            campaign.stats.delivered_percentage
                                        }}
                                        %
                                    </template>
                                </v-progress-circular>
                            </div>
                            <div class="stat">
                                Open Rate:
                                <v-progress-circular
                                    :model-value="
                                        campaign.stats.opened_percentage
                                    "
                                    :rotate="360"
                                    :size="100"
                                    :width="15"
                                >
                                    <template v-slot:default>
                                        {{ campaign.stats.opened_percentage }} %
                                    </template>
                                </v-progress-circular>
                            </div>
                        </div>

                        <v-btn
                            target="_blank"
                            :href="
                                'https://testing.enterprise.knak.io/builder/' +
                                campaign.knak_email_id
                            "
                            variant="tonal"
                            append-icon="mdi-open-in-new"
                        >
                            View in Knak
                        </v-btn>
                    </div>
                </v-card>
                <v-card>
                    <div class="card-wrapper">
                        <h3>Recipients</h3>
                        <v-table>
                            <thead>
                                <tr>
                                    <th></th>
                                    <th class="text-left">Name</th>
                                    <th class="text-left">Email</th>
                                    <th class="text-left">Department</th>
                                    <th class="text-left">Delivered</th>
                                    <th class="text-left">Opened</th>
                                    <th class="text-left">Clicked</th>
                                    <th class="text-left">Sent HTML</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="recipient in campaign.recipients"
                                    :key="recipient.id"
                                    class="recipient-row"
                                >
                                    <campaign-row :recipient="recipient" />
                                </tr>
                            </tbody>
                        </v-table>
                    </div>
                </v-card>
            </div>
            <div v-else>No recipients found</div>
        </div>
        <div v-if="error">Campaign not found</div>
        <v-skeleton-loader
            v-if="loading"
            class="mx-auto"
            elevation="12"
            type="table-heading, list-item-two-line, list-item-two-line, list-item-two-line, list-item-two-line, image, table-tfoot"
        ></v-skeleton-loader>
    </div>
</template>
<script setup lang="ts">
import { onMounted, ref } from "vue";
import { Campaign } from "../../types/Campaign";
import { useCampaignsStore } from "../../store/CampaignsStore";
import CampaignRow from "@/layouts/CampaignRow.vue";
const store = useCampaignsStore();

const { campaignId } = defineProps<{
    campaignId: number;
}>();

const campaign = ref<Campaign | null>(null);
const error = ref(false);
const loading = ref(false);

onMounted(async () => {
    loading.value = true;
    await fetchCampaigns();
    await findCampaignById();
    await fetchCampaignRecipients();
    loading.value = false;
});

async function fetchCampaigns() {
    if (store.campaigns.length) return;
    try {
        await store.fetchCampaigns();
    } catch {
        error.value = true;
        console.error("Failed to fetch campaigns");
    }
}

async function findCampaignById() {
    campaign.value = store.campaigns.find((c) => c.id === campaignId);
}

async function fetchCampaignRecipients() {
    try {
        await store.fetchCampaignRecipients(campaignId);
    } catch {
        console.error("Failed to fetch campaign recipients");
    }
}

function getHumanReadableDate(date: string) {
    return new Date(date).toLocaleDateString();
}

function getHumanReadableTime(date: string) {
    return new Date(date).toLocaleTimeString();
}
</script>

<style lang="scss" scoped>
.report-grid {
    display: grid;
    grid-template-columns: 1fr 3fr;
    gap: 20px;
}

.card-wrapper {
    padding: 20px;
}

.recipient-row {
    margin: 10px 0;
    height: 80px;
}

.recipient-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
}

.big-nice-data {
    font-size: 1.5rem;
    font-weight: 800;
}

.page-header {
    margin-top: 20px;
    margin-bottom: 20px;
}
.stats {
    display: grid;
    grid-template-columns: 1fr 1fr;
}
.stat {
    display: flex;
    align-items: center;
    margin-top: 20px;
    margin-bottom: 20px;
    justify-content: space-between;
    padding-right: 40px;
}
</style>
