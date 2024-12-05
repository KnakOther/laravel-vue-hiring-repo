<template>
  <v-list>
    <v-list-item>
      <template v-slot:prepend>
        <v-card-subtitle>From</v-card-subtitle>
      </template>
      <template v-slot:title>{{ knakAsset?.from_name }}</template>
    </v-list-item>
    <v-list-item>
      <template v-slot:prepend>
        <v-card-subtitle>Reply to</v-card-subtitle>
      </template>
      <template v-slot:title>{{ knakAsset?.reply_email }}</template>
    </v-list-item>
    <v-list-item>
      <template v-slot:prepend>
        <v-card-subtitle>Subject line</v-card-subtitle>
      </template>
      <template v-slot:title>{{ knakAsset?.subject }}</template>
    </v-list-item>
    <div class="preview-toggles">
      <v-tabs
          dark
          next-icon="mdi-arrow-right-bold-box-outline"
          prev-icon="mdi-arrow-left-bold-box-outline"
          show-arrows
      >
        <v-tabs-slider color="blue"></v-tabs-slider>
        <v-tab @click="toggleMobileWidth(false)">Desktop</v-tab>
        <v-tab @click="toggleMobileWidth(true)"> Mobile </v-tab>
      </v-tabs>
    </div>
    <div class="email-wrapper">
      <div class="email-content" :class="{'email-content--mobile': isMobile}" v-html="knakAsset?.html"></div>
    </div>
  </v-list>
</template>
<script setup lang="ts">
import { ref } from "vue";
import {Asset} from "@/layouts/Asset";

const { knakAsset, recipientId } = defineProps<{
  knakAsset: Asset | null;
  recipientId?: number;
}>();

const isMobile = ref(false);

function toggleMobileWidth(value: boolean) {
  isMobile.value = value;
}
</script>

<style lang="scss" scoped>
.preview-toggles {
  display: flex;
  justify-content: center;
  margin-bottom: 20px;
  text-align: center;
  width: 100%;
}

.email-wrapper {
  display: flex;
  justify-content: center;
}

.email-content {
  width: 100%;
  margin: 0 auto;
  transition: width 0.5s;
}

.email-content--mobile {
  width: 580px;
}
</style>
