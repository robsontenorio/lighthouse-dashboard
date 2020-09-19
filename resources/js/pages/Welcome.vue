<template>
  <div>
    <v-app-bar app color="white" elevation="1">
      <h2>
        <v-icon color="black" class="mb-1" left>mdi-graphql</v-icon>Schema
      </h2>
      <v-chip label class="mx-5 font-weight-bold">{{ schema.name }}</v-chip>
      <div class="text-caption grey--text">Last updated on {{ schema.updated_at }}</div>
    </v-app-bar>
    <v-card>
      <v-card-title class="bordered">Requests</v-card-title>
      <v-card-text>
        <overview-chart :series="requests_series" />
      </v-card-text>
    </v-card>

    <v-card class="mt-12">
      <v-card-title class="bordered">Clients</v-card-title>
      <v-card-text>
        <clients-chart :series="client_series" />
      </v-card-text>
    </v-card>
  </div>
</template>

<script>
import OverviewChart from "../components/charts/OverviewChart";
import ClientsChart from "../components/charts/ClientsChart";

export default {
  props: ["schema", "requests_series", "client_series"],
  components: { OverviewChart, ClientsChart },
};
</script>