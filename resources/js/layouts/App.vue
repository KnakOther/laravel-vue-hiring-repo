<template>
    <div class="body" id="app">
        <div class="top-bar">
            <div id="logo" alt="Knak logo" class="knak-logo"></div>
            <h1 class="title">SEND</h1>
        </div>
        <div>
            <v-layout>
                <v-navigation-drawer expand-on-hover rail>
                    <v-list density="compact" nav>
                        <v-list-item
                            prepend-icon="mdi-account-multiple"
                            title="Recipients"
                            to="/recipients-page"
                        ></v-list-item>
                        <v-list-item
                            prepend-icon="mdi-email-fast-outline"
                            title="Send"
                            to="/send-page"
                        >
                        </v-list-item>
                        <v-list-item
                            prepend-icon="mdi-bullhorn-variant-outline"
                            title="Campaigns"
                            to="/campaigns-page"
                        ></v-list-item>
                    </v-list>
                </v-navigation-drawer>
                <v-main class="content"><RouterView /></v-main>
            </v-layout>
        </div>
    </div>
</template>
<script setup>


</script>
<style scoped lang="scss">
.body {
    background: black;
    color: #fff;
    height: 100vh; /* Full viewport height */
    font-family: "Poppins", sans-serif;
}

.top-bar {
    grid-area: topbar;
    background-color: #30353c;
    width: 100%;
    height: 48px;
    color: #fff;
    display: flex;
    align-items: center;
    padding: 0 40px;
    .knak-logo {
        background-image: url(https://assets.knak.io/img/knak-logo-white.svg);
        background-size: contain;
        background-repeat: no-repeat;
        height: 28px;
        width: 100px;
        margin-left: 50px;
    }
    .title {
        color: #fff;
        font-size: 1rem;
    }
}

.sidebar {
    grid-area: sidebar;
    color: #fff;
    display: flex;
    flex-direction: column;
    background-color: #30353c;
    font-weight: 500;
    a {
        color: #fff;
        padding: 10 30px;
        text-decoration: none;
    }
    .navigation {
        display: flex;
        flex-direction: column;
    }
}

.content {
    grid-area: content;
    padding: 20px;
    overflow-y: scroll;
    margin-left: 80px;
    max-width: calc(100vw - 100px);
}
</style>
