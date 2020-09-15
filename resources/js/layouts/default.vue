<template>
  <v-app>
    <v-navigation-drawer app dark>
      <v-list dense>
        <v-list-item
          two-line
          @click="$inertia.visit('/lighthouse-dashboard')"
          style="cursor:pointer"
        >
          <v-list-item-avatar color="teal darken-1" rounded>
            <v-icon>mdi-graphql</v-icon>
          </v-list-item-avatar>
          <v-list-item-content>
            <v-list-item-title class="font-weight-bold">Laravel Lighthouse</v-list-item-title>
            <v-list-item-subtitle>Dashboard</v-list-item-subtitle>
          </v-list-item-content>
        </v-list-item>
      </v-list>
      <v-divider></v-divider>

      <v-list>
        <v-list-item @click="$inertia.visit('/lighthouse-dashboard')">
          <v-list-item-icon>
            <v-icon>mdi-sitemap</v-icon>
          </v-list-item-icon>
          <v-list-item-title>Schema</v-list-item-title>
        </v-list-item>
        <v-list-item @click="$inertia.visit('/lighthouse-dashboard/operations')">
          <v-list-item-icon>
            <v-icon>mdi-pulse</v-icon>
          </v-list-item-icon>
          <v-list-item-title>Operations</v-list-item-title>
        </v-list-item>
        <v-list-item @click="$inertia.visit('/lighthouse-dashboard/types')">
          <v-list-item-icon>
            <v-icon>mdi-package-variant-closed</v-icon>
          </v-list-item-icon>
          <v-list-item-title>Types</v-list-item-title>
        </v-list-item>
      </v-list>
    </v-navigation-drawer>

    <v-main class="grey lighten-4">
      <v-container scroll-region class="pa-10">
        <slot />
      </v-container>
    </v-main>
  </v-app>
</template>
<script>
export default {
  data() {
    return {
      drawer: true,
      activeClass: "v-list-item--active",
      networkDialog: false,
      // menus: [
      //   { name: "Welcome", path: "/", icon: "mdi-home-outline" },
      //   { name: "Home", path: "/home", icon: "mdi-account-outline" },
      //   {
      //     name: "My Tournaments",
      //     path: "/my-tournaments",
      //     icon: "mdi-list"
      //   }
      // ]
    };
  },
  methods: {
    currentURL() {
      return window.location.pathname.substr(1);
    },
    isActivePath(path) {
      path = path.substr(1);

      return path === ""
        ? path === this.currentURL()
        : this.currentURL().startsWith(path);
    },
  },
};
</script>