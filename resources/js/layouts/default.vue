<template>
  <v-app>
    <v-navigation-drawer app dark>
      <v-list dense>
        <v-list-item
          two-line
          @click="$inertia.visit('/lighthouse-dashboard')"
          style="cursor: pointer"
        >
          <v-list-item-avatar color="teal darken-1" rounded>
            <v-icon>mdi-graphql</v-icon>
          </v-list-item-avatar>
          <v-list-item-content>
            <v-list-item-title class="font-weight-bold">
              Lighthouse Dashboard
            </v-list-item-title>
            <v-list-item-subtitle>Analytics</v-list-item-subtitle>
          </v-list-item-content>
        </v-list-item>
      </v-list>
      <v-divider></v-divider>

      <v-list>
        <v-list-item @click="$inertia.visit('/lighthouse-dashboard')">
          <v-list-item-icon>
            <v-icon>mdi-graphql</v-icon>
          </v-list-item-icon>
          <v-list-item-title>Schema</v-list-item-title>
        </v-list-item>
        <v-list-item
          @click="$inertia.visit('/lighthouse-dashboard/operations')"
        >
          <v-list-item-icon>
            <v-icon>mdi-pulse</v-icon>
          </v-list-item-icon>
          <v-list-item-title>Operations</v-list-item-title>
        </v-list-item>
        <v-list-item @click="$inertia.visit('/lighthouse-dashboard/types')">
          <v-list-item-icon>
            <v-icon>mdi-shape-outline</v-icon>
          </v-list-item-icon>
          <v-list-item-title>Types</v-list-item-title>
        </v-list-item>
        <v-list-item @click="$inertia.visit('/lighthouse-dashboard/errors')">
          <v-list-item-icon>
            <v-icon>mdi-alert-rhombus</v-icon>
          </v-list-item-icon>
          <v-list-item-title>Errors</v-list-item-title>
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