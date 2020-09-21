<template>
  <div>
    <v-app-bar app color="white" elevation="1" class="pt-2">
      <v-row align="center" class="mb-5">
        <v-col>
          <h2>
            <v-icon color="black" class="mb-1" left>mdi-graphql</v-icon>Schema
          </h2>
        </v-col>
        <v-col>
          <v-col cols="auto" class="text-right primary--text">
            <v-icon class="mb-1 primary--text">mdi-clock-outline</v-icon>
            {{ filters.form.start_date }}
            <v-btn
              color="primary"
              fab
              x-small
              depressed
              dark
              @click="displayFilters()"
              class="ml-3"
            >
              <v-icon>mdi-filter-variant</v-icon>
            </v-btn>
          </v-col>
        </v-col>
      </v-row>
    </v-app-bar>

    <v-card v-if="requests_series.length">
      <v-card-title>Requests</v-card-title>
      <v-card-subtitle>{{ total_requests | numeral(0.0) }} requests in selected period.</v-card-subtitle>
      <v-card-text>
        <overview-chart :series="requests_series" />
      </v-card-text>
    </v-card>

    <v-card v-if="requests_series.length" class="mt-8">
      <v-card-title>Clients</v-card-title>
      <v-card-subtitle>{{ total_clients | numeral(0.0) }} clients in selected period.</v-card-subtitle>

      <v-card-text>
        <clients-chart :series="client_series" />
      </v-card-text>
    </v-card>

    <div v-if="requests_series.length === 0" class="text-center grey--text">
      <v-icon color="grey" x-large>mdi-weather-windy</v-icon>
      <h3 class="mt-3">Oops! Nothing here.</h3>
      <p class="text-caption mt-3">Make your first request to this Schema.</p>
    </div>

    <v-navigation-drawer v-model="display.filters" app stateless right width="380" class="pa-5">
      <filters :filters="filters" @filter="filter()" @close="hideFilters()" />
    </v-navigation-drawer>
    <v-overlay :value="loading">
      <v-progress-circular indeterminate />
    </v-overlay>
  </div>
</template>

<script>
import OverviewChart from "../components/charts/OverviewChart";
import ClientsChart from "../components/charts/ClientsChart";
import Filters from "../components/Filters";
import _ from "lodash";

export default {
  props: [
    "schema",
    "requests_series",
    "client_series",
    "clients",
    "start_date",
    "range",
    "selectedClients",
  ],
  components: { OverviewChart, ClientsChart, Filters },
  data() {
    return {
      loading: false,
      display: {
        filters: false,
        sumary: false,
      },
      filters: {
        form: {
          start_date: this.start_date || "today",
          range: this.range || [],
          clients: this.selectedClients || [],
        },
        options: {
          clients: this.clients || [],
        },
      },
    };
  },
  computed: {
    total_requests() {
      return _.sumBy(this.requests_series, "y");
    },
    total_clients() {
      return this.client_series.length;
    },
  },
  methods: {
    displayFilters() {
      this.display.filters = true;
    },
    hideFilters() {
      this.display.filters = false;
    },
    async filter() {
      this.loading = true;

      await this.$inertia.replace(`/lighthouse-dashboard`, {
        data: this.filters.form,
        replace: true,
        preserveScroll: true,
      });

      this.loading = false;
    },
  },
};
</script>