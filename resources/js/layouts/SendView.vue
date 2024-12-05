<template>
    <div class="header">
        <h1>Send Email</h1>
    </div>
    <v-card class="mb-3 pa-2">
        <v-list-item>
            <template v-slot:prepend>
                <v-card-title>Send</v-card-title>
            </template>
        </v-list-item>
        <v-list-item>
            <template v-slot:prepend>
                <v-card-subtitle class="form-label">Send to</v-card-subtitle>
            </template>
            <template v-slot:title>
                <v-btn-toggle
                    v-model="sendBy"
                    variant="outlined"
                    divided
                    class="mb-3"
                    mandatory
                    @update:modelValue="calculateRecipients()"
                >
                    <v-btn value="department">Department</v-btn>
                    <v-btn value="location">Location</v-btn>
                    <v-btn value="user">User</v-btn>
                </v-btn-toggle>
                <v-select
                    v-if="sendBy === 'department'"
                    item-title="text"
                    item-value="value"
                    :items="departments"
                    v-model="selectedDepartment"
                    @update:modelValue="calculateRecipients()"
                    multiple
                ></v-select>
                <v-select
                    v-if="sendBy === 'location'"
                    item-title="text"
                    item-value="value"
                    :items="locations"
                    v-model="selectedLocation"
                    @update:modelValue="calculateRecipients()"
                    multiple
                ></v-select>
                <v-autocomplete
                    v-if="sendBy === 'user'"
                    item-title="text"
                    item-value="value"
                    :items="userOptions"
                    v-model="selectedUser"
                    @update:modelValue="calculateRecipients()"
                    multiple
                    clear-on-select
                ></v-autocomplete>
                <p class="recipient-count" v-if="recipients.length == 1">
                    {{ recipients.length }} Recipient
                </p>
                <p class="recipient-count" v-if="recipients.length > 1">
                    {{ recipients.length }} Recipients
                </p>
            </template>
        </v-list-item>
        <v-list-item>
            <template v-slot:prepend>
                <v-card-subtitle class="form-label">Knak Email</v-card-subtitle>
            </template>
            <template v-slot:title>
                <div v-if="knakAsset" class="knak-email-container">
                    <div style="margin-right: 15px">{{ knakAsset.name }}</div>
                    <v-btn @click="selectAsset()" variant="tonal">Edit</v-btn>
                </div>
                <div v-else class="knak-email-container">
                    <v-btn @click="selectAsset()" variant="tonal">Select</v-btn>
                </div>
            </template>
        </v-list-item>
        <v-list-item>
            <template v-slot:prepend>
                <v-card-subtitle class="form-label">Schedule</v-card-subtitle>
            </template>
            <template v-slot:title>
                <v-btn-toggle
                    v-model="schedule"
                    variant="outlined"
                    divided
                    class="mb-3"
                    mandatory
                >
                    <v-btn value="now">Now</v-btn>
                    <v-btn value="later">Later</v-btn>
                </v-btn-toggle>
                <div v-show="schedule === 'later'">
                    <input type="datetime-local" :value="scheduleDate" />
                </div>
            </template>
        </v-list-item>
        <v-list-item>
            <v-btn
                class="ml-3 mt-5"
                color="indigo-darken-3"
                :prepend-icon="
                    schedule === 'now' ? 'mdi-send' : 'mdi-send-clock-outline'
                "
                @click="sendConfirmation = true"
                :disabled="!knakAsset || !recipients.length"
            >
                Send
            </v-btn>
        </v-list-item>
    </v-card>
    <v-dialog :max-width="600" :model-value="assetSelectionOpen">
        <div class="text-center" v-if="loading">
        <v-progress-circular indeterminate :size="39" ></v-progress-circular>
        </div>
        <v-card title="Knak Emails" v-else>
            <v-container>
                <v-row>
                    <v-text-field v-model="searchTerm" @input="debounceSearch"  dense label="Email Search" clearable outlined></v-text-field>
                </v-row>

                <v-row v-for="(item, index) in searchResults" :key="index" align="center"
                       justify="center" grid-list-md >
                    <v-col cols="12" sm="12" md="12" align="center"
                           justify="center">
                        <v-card variant="tonal">
                            <div class="email-top-container">
                                <v-img

                                    :max-width="200"
                                    :width="200"
                                    :src="item.preview_path + '?w=400&h=400&crop=top&fit=crop'"
                                ></v-img>
                                <div class="email-inner-container">
                                    <v-card-title>{{ item.name }}</v-card-title>
                                    <v-btn
                                        variant="outlined"
                                        text="Select"
                                        @click="retrieveKnakEmail(item.id)"
                                        class="ma-5"
                                    ></v-btn>
                                </div>
                            </div>
                        </v-card>
                    </v-col>
                </v-row>
            </v-container>

            <v-card-actions>
                <v-btn text="Close" @click="assetSelectionOpen = false"></v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
    <v-dialog v-model="sendConfirmation" max-width="500">
        <v-card>
            <v-card-title>Confirmation</v-card-title>
            <v-card-text>
                <p v-if="schedule=='now'">Are you sure you want to send this email?</p>
                <p v-else>Are you sure you want to schedule this email?</p>
            </v-card-text>
            <v-card-item>
                <v-text-field
                    label="# of Recipients"
                    v-model="recipients.length"
                    readonly
                ></v-text-field>
                <v-text-field
                    label="Send Date"
                    :model-value="schedule === 'now' ? 'Now' : scheduleDate"
                    readonly
                ></v-text-field>
            </v-card-item>
            <v-card-actions>
                <v-btn text @click="sendConfirmation = false">Cancel</v-btn>
                <v-spacer></v-spacer>
                <v-btn
                    variant="outlined"
                    prepend-icon="mdi-send"
                    @click="sendEmail()"
                    :disabled="!knakAsset || !recipients.length"
                    :loading="sendingLoading"
                >
                    Send
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
    <div class="preview" v-if="knakAsset && recipients.length">
        <v-card>
            <v-list>
                <v-list-item>
                    <template v-slot:prepend>
                        <v-card-title>Preview</v-card-title>
                    </template>
                </v-list-item>
                <v-list-item v-if="viewBy === 'user'">
                    <template v-slot:prepend>
                        <v-card-subtitle>User</v-card-subtitle>
                    </template>
                    <template v-slot:title>
                        <v-select
                            item-title="text"
                            item-value="value"
                            :items="recipientOptions"
                            v-model="selectedRecipientId"
                        ></v-select>
                    </template>
                </v-list-item>
            </v-list>
        </v-card>
        <v-card v-if="!!showPreview || previewLoading">
            <v-skeleton-loader
                v-if="previewLoading"
                class="mx-auto"
                elevation="12"
                max-width="800"
                type="table-heading, list-item-two-line, list-item-two-line, list-item-two-line, list-item-two-line, image, table-tfoot"
            ></v-skeleton-loader>
            <asset-preview
                v-if="!previewLoading && knakId"
                :knak-asset="knakAsset"
                :recipient-id="selectedRecipientId"
            />
        </v-card>
    </div>
</template>

<script lang="ts" setup>
import { debounce } from 'lodash';
import { onMounted, Ref, ref, computed, watch } from "vue";
import axios from "axios";
import { Recipient } from "@/layouts/Recipient";
import { Asset } from "@/layouts/Asset";
import AssetPreview from "@/layouts/asset-preview/AssetPreview.vue";
import {useRouter} from "vue-router";
import {useCampaignsStore} from "../../store/CampaignsStore";

const store = useCampaignsStore();

const previewLoading = ref(false);
const searchResults = ref([]);
const sendConfirmation = ref(false);
const sendingLoading = ref(false);

const viewBy = ref("user") as Ref<string>;
const sendBy = ref("department") as Ref<string>;
const schedule = ref("now") as Ref<string>;
const scheduleDate = ref(new Date().toISOString().slice(0, 16));

const departments = ref([]);
const locations = ref([]);
const users = ref([] as Recipient[]);
const userOptions = ref([]);

const selectedDepartment = ref([]);
const selectedLocation = ref([]);
const selectedUser = ref([]);

const knakId = ref(null as string | null);
const knakAsset = ref(null as Asset | null);
const showPreview = ref(false);
const selectedRecipientId = ref(null as number | null);
const currentlyPreviewedRecipientId = ref(null as Number | null);
const searchTerm = ref("");

const recipients = ref([]);
const loading = ref(false);


const router = useRouter()

onMounted(() => {
    getRecipients();
    console.log("SendView mounted");
});

const getRecipients = async () => {
    const response = await axios.get("/recipients");
    users.value = response.data;
    departments.value = users.value.reduce((acc, user) => {
        if (
            !acc.find((department: any) => department.value === user.department)
        ) {
            acc.push({
                text: user.department,
                value: user.department,
            });
        }
        return acc;
    }, []);
    locations.value = users.value.reduce((acc, user) => {
        if (!acc.find((location: any) => location.value === user.location)) {
            acc.push({
                text: user.location,
                value: user.location,
            });
        }
        return acc;
    }, []);
    userOptions.value = users.value.map((user) => ({
        text: user.first_name + " " + user.last_name + " <" + user.email + ">",
        value: user.id,
    }));
};

const recipientOptions = computed(() => {
    let recipientMap = recipients.value.map((user) => ({
        text: user.first_name + " " + user.last_name + " <" + user.email + ">",
        value: user.id,
    }));
    // if no selected recipient or if recipient not in list, default to first recipient
    if (
        !selectedRecipientId.value ||
        !recipientMap.find((r) => r.value === selectedRecipientId.value)
    ) {
        selectedRecipientId.value = recipientMap[0].value;
    }
    return recipientMap;
});

watch(selectedRecipientId, () => {
    viewPreview();
});

const assetSelectionOpen = ref(false);
const selectAsset = () => {
    assetSelectionOpen.value = true;
};

const retrieveKnakEmail = async (slectedKnakId) => {
    knakId.value = slectedKnakId;
    const params = {
        recipient_id: selectedRecipientId.value,
    };
    loading.value = true;
    const response = await axios.get("/assets/" + knakId.value, {
        params,
    });
    knakAsset.value = response.data as Asset;
    assetSelectionOpen.value = false;
    loading.value = false
};

const sendEmail = async () => {
    sendingLoading.value = true;
    try{
        const payload: any = { asset_id: knakId.value };
        if (sendBy.value === "user") {
            payload.filterType = "user";
            payload.filterValue = selectedUser.value;
        } else if (sendBy.value === "department") {
            payload.filterType = "department";
            payload.filterValue = selectedDepartment.value;
        } else if (sendBy.value === "location") {
            payload.filterType = "location";
            payload.filterValue = selectedLocation.value;
        }

        if (schedule.value === "later") {
            payload.send_date = scheduleDate.value;
            payload.scheduled = true;
        }

        const response = await axios.post("/campaigns", payload);
        // wait 5 seconds for email to send
        // await new Promise((resolve) => setTimeout(resolve, 5000));
        // await router.push(`/campaigns-page`)
        await store.fetchCampaigns();
        await router.push(`/campaign-page/${response.data.id}`)

        sendConfirmation.value = false;
    } catch {
        console.error("Failed to send email");
    } finally {
        sendingLoading.value = false;
    }
};

const fetchAssetPreview = async () => {
    try {
        previewLoading.value = true;
        const response = await axios.get("/assets/" + knakId.value, {
            params: {
                recipient_id: selectedRecipientId.value,
            },
        });
        knakAsset.value = response.data as Asset;
    } catch {
        console.error("Failed to retrieve Knak email");
    } finally {
        previewLoading.value = false;
    }
};

async function viewPreview() {
    await fetchAssetPreview();
    currentlyPreviewedRecipientId.value = selectedRecipientId.value;
    showPreview.value = true;
}

async function calculateRecipients() {
    let params = {
        filterType: sendBy.value,
        filterValue:
            sendBy.value === "user"
                ? selectedUser.value
                : sendBy.value === "department"
                  ? selectedDepartment.value
                  : selectedLocation.value,
    };
    if (params.filterValue.length === 0) {
        return (recipients.value = []);
    }

    try {
        const response = await axios.get("/recipients/filter", {
            params,
        });
        recipients.value = response.data.recipients;
    } catch {
        console.error("Failed to update recipient count");
    }
}
const debounceSearch = debounce(async () => {
    const response = await axios.get(`/assets/search/${searchTerm.value}`);
    searchResults.value= response.data.hits;
}, 500);
</script>

<style scoped>
.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.knak-email-container {
    display: flex;
    flex-direction: row;
    align-items: center;
}

.form-label {
    width: 120px;
}

.recipient-count {
    margin-top: -15px;
    font-size: 0.8em;
    font-style: italic;
}

.email-top-container {
    margin: 10px;
    display: flex;
    flex-direction: row;
    justify-content: flex-start;
    align-items: flex-start;
}

.email-inner-container {
    display: flex;
    margin-left: 10px;
    flex-direction: column;
    align-items: flex-start;
    justify-content: flex-start;
}
</style>
