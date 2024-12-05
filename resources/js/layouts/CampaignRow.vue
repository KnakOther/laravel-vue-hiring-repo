<template>
    <td>
        <img class="recipient-avatar" :src="recipient.avatar_url" />
    </td>
    <td>
        {{ recipient.first_name }}
        {{ recipient.last_name }}
    </td>
    <td>{{ recipient.email }}</td>
    <td>{{ recipient.department }}</td>
    <td class="text-center">
        <v-icon
            :icon="getBooleanMaterialIcon(recipient.pivot.delivered_at)"
            :color="recipient.pivot.delivered_at ? 'green-darken-2' : 'red-darken-2'"
        ></v-icon>
    </td>
    <td class="text-center">
        <v-icon
            :icon="getBooleanMaterialIcon(recipient.pivot.opened_at)"
            :color="recipient.pivot.opened_at ? 'green-darken-2' : 'red-darken-2'"
        ></v-icon>
    </td>
    <td class="text-center">
        <v-icon
            :icon="getBooleanMaterialIcon(recipient.pivot.clicked_at)"
            :color="recipient.pivot.clicked_at ? 'green-darken-2' : 'red-darken-2'"
        ></v-icon>
    </td>
    <td>
        <v-btn @click="previewShown = true">Preview</v-btn>
    </td>
    <v-dialog :max-width="1200" v-model="previewShown">
        <v-card :title="`Preview for ${recipient.first_name} ${recipient.last_name}`">
            <div v-html="recipient.pivot.html"></div>
        </v-card>
    </v-dialog>
</template>

<script setup lang="ts">
import { defineProps, ref } from "vue";

const props = defineProps({
    recipient: Object,
});

const previewShown = ref(false);

const recipient = props.recipient;

function getBooleanMaterialIcon(value: boolean) {
    return value ? "mdi-check-circle" : "mdi-close-circle";
}
</script>

<style scoped lang="scss">

.recipient-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
}

</style>
