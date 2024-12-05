<template>
    <div>
        <div class="header">
            <h1>Recipients</h1>
            <v-btn
                variant="outlined"
                text="Import CSV"
                @click="isActive = true"
            >
            </v-btn>
        </div>
        <v-table>
            <thead>
                <tr>
                    <th></th>
                    <th class="text-left">ID</th>
                    <th class="text-left">First Name</th>
                    <th class="text-left">Last Name</th>
                    <th class="text-left">Email</th>
                    <th class="text-left">Department</th>
                    <th class="text-left">Position</th>
                    <th class="text-left">Location</th>
                </tr>
            </thead>
            <tbody>
                <tr
                    v-for="recipient in recipients"
                    :key="recipient.id"
                    class="recipient-row"
                >
                    <td>
                        <img
                            class="recipient-avatar"
                            :src="recipient.avatar_url"
                        />
                    </td>
                    <td>{{ recipient.id }}</td>
                    <td>{{ recipient.first_name }}</td>
                    <td>{{ recipient.last_name }}</td>
                    <td>{{ recipient.email }}</td>
                    <td>{{ recipient.department }}</td>
                    <td>{{ recipient.position }}</td>
                    <td>{{ recipient.location }}</td>
                </tr>
            </tbody>
        </v-table>
        <v-dialog max-width="500" v-model="isActive">
            <v-card title="Import CSV">
                <v-card-text>
                    <p>Attach a CSV file to import recipients</p>
                </v-card-text>

                <v-card-item>
                    <v-file-input
                        :v-bind="csvFile"
                        label="File input"
                        accept=".csv"
                    ></v-file-input>
                </v-card-item>

                <v-card-actions>
                    <v-btn text="Close" @click="isActive = false"></v-btn>
                    <v-spacer></v-spacer>
                    <v-btn
                        variant="outlined"
                        text="Import"
                        @click="uploadRecipients"
                    ></v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>

<script lang="ts" setup>
import { Recipient } from "../../types/Recipient";
import axios from "axios";
import { onMounted, ref } from "vue";

let isActive = ref(false);
let recipients: Recipient[] = ref([]);
let csvFile = ref({});

onMounted(() => {
    fetchRecipients();
});

async function fetchRecipients() {
    try {
        const response = await axios.get("/recipients");
        recipients.value = response.data;
    } catch {
        console.error("Failed to fetch recipients");
    }
}

async function uploadRecipients() {
    const formData = new FormData();
    formData.append('file', new Blob(['test payload'], {
        type: 'text/csv',
    }));
    formData.append("csv", csvFile.value);
    try {
        await axios.post("/recipients/bulk", formData, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        });
        await fetchRecipients();
        isActive.value = false;
    } catch {
        console.error("Failed to upload recipients");
    }
}
</script>

<style lang="scss" scoped>
.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.recipient-row {
    margin: 10px 0;
    height: 80px;
    &:hover {
        background-color: rgba(#FFF, 0.1);
    }
}

.recipient-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
}
</style>
