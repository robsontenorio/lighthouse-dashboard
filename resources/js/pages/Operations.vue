<template>
  <div>
    <v-app-bar app color="white" elevation="1" class="pt-2">
      <v-row align="center" class="mb-5">
        <v-col>
          <h2><v-icon left color="black">mdi-pulse</v-icon>Operations</h2>
        </v-col>
        <v-col cols="auto" class="text-right primary--text">
          <v-icon class="mb-1 primary--text">mdi-clock-outline</v-icon>
          {{ filters.form.start_date }}
          <v-btn
            color="primary"
            fab
            x-small
            depressed
            dark
            @click="setNavigationComponent('filters')"
            class="ml-3"
          >
            <v-icon>mdi-filter-variant</v-icon>
          </v-btn>
        </v-col>
      </v-row>
    </v-app-bar>

    <div
      v-if="topOperations.length === 0 && slowlestOperations.length === 0"
      class="text-center grey--text"
    >
      <v-icon color="grey" x-large>mdi-weather-windy</v-icon>
      <h3 class="mt-3">Oops! Nothing here.</h3>
      <p class="text-caption mt-3">Make your first request to this Schema.</p>
    </div>

    <div v-if="topOperations.length">
      <div class="title">Top</div>
      <div class="text-caption grey--text mb-3">
        Most requested operations in selected period.
      </div>

      <v-data-table
        :headers="table_top_operations.headers"
        :items="topOperations"
        @click:row="selectOperation"
        hide-default-footer
        class="elevation-1 row-pointer mb-8"
      >
        <template #item.field="{ item }">
          <field :field="item.field" class="py-4" />
        </template>
        <template #item.total_requests="{ item }">
          {{ item.total_requests | numeral(0.0) }}
        </template>
        <template #item.total_errors="{ item }">
          <span class="red--text">{{ item.total_errors }}</span>
        </template>
      </v-data-table>
    </div>
    <div v-if="topOperations.length">
      <div class="title">Slow</div>
      <div class="text-caption grey--text mb-3">
        Most slowlest operations in selected period.
      </div>
      <v-data-table
        :headers="table_slowlest_operations.headers"
        :items="slowlestOperations"
        @click:row="selectOperation"
        hide-default-footer
        class="elevation-1 row-pointer"
      >
        <template #item.field="{ item }">
          <field :field="item.field" class="py-4" />
        </template>
        <template #item.average_duration="{ item }">
          {{ item.average_duration | milliseconds }}
        </template>
        <template #item.latest_duration="{ item }">
          {{ item.latest_duration | milliseconds }}
        </template>
      </v-data-table>
    </div>

    <v-navigation-drawer
      v-model="display.navigation"
      right
      app
      width="380"
      class="pa-5"
    >
      <filters
        v-show="display.component === 'filters'"
        :filters="filters"
        @filter="filter()"
        @close="hideNavigation()"
      />

      <operation-sumary
        v-show="display.component === 'sumary'"
        :operation="selectedOperation"
        :filters="filters"
        @close="hideNavigation()"
      />
    </v-navigation-drawer>
    <v-overlay :value="loading">
      <v-progress-circular indeterminate />
    </v-overlay>
  </div>
</template>

<script>
import Filters from "../components/Filters";
import Field from "../components/Field";
import OperationSumary from "../components/OperationSumary";

export default {
  props: [
    "topOperations",
    "slowlestOperations",
    "start_date",
    "range",
    "clients",
    "selectedClients",
  ],
  components: { Filters, Field, OperationSumary },
  data() {
    return {
      loading: false,
      selectedOperation: {
        field: {},
      },
      display: {
        navigation: false,
        component: "filters",
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
      table_top_operations: {
        headers: [
          { text: "Operation", value: "field", sortable: false },
          {
            text: "Requests",
            value: "total_requests",
            sortable: false,
            align: "end",
          },
          {
            text: "Errors",
            value: "total_errors",
            sortable: false,
            align: "end",
          },
        ],
      },
      table_slowlest_operations: {
        headers: [
          { text: "Operation", value: "field", sortable: false },
          {
            text: "Average (ms)",
            value: "average_duration",
            sortable: false,
            align: "end",
          },
          {
            text: "Latest (ms)",
            value: "latest_duration",
            sortable: false,
            align: "end",
          },
        ],
      },
    };
  },
  methods: {
    selectOperation(operation) {
      this.selectedOperation = operation;
      this.setNavigationComponent("sumary");
      this.displayNavigation();
    },
    async filter() {
      this.loading = true;

      await this.$inertia.replace(`/lighthouse-dashboard/operations`, {
        data: this.filters.form,
        replace: true,
        preserveScroll: true,
      });

      this.loading = false;
    },
    setNavigationComponent(name) {
      this.display.component = name;
      this.displayNavigation();
    },
    hideNavigation() {
      this.display.navigation = false;
    },
    displayNavigation() {
      this.display.navigation = true;
    },
  },
};
</script>