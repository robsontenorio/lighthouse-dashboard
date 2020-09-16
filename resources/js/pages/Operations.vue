<template>
  <div>
    <v-app-bar app color="white" elevation="1" class="pt-2">
      <v-row align="center" class="mb-5">
        <v-col>
          <h2>
            <v-icon left color="black">mdi-pulse</v-icon>Operations
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

    <v-data-table
      :headers="table_top_operations.headers"
      :items="topOperations"
      hide-default-footer
      class="elevation-1 mb-8"
    >
      <template #top>
        <div class="title pa-3">Top requested</div>
      </template>
    </v-data-table>

    <v-data-table
      :headers="table_slowlest_operations.headers"
      :items="slowlestOperations"
      hide-default-footer
      class="elevation-1"
    >
      <template #top>
        <div class="title pa-3">Slow response (ms)</div>
      </template>
    </v-data-table>

    <v-navigation-drawer
      v-model="display.filters"
      right
      :app="display.filters"
      width="380"
      class="pa-5"
    >
      <filters :filters="filters" @filter="filter()" @close="hideFilters()" />
    </v-navigation-drawer>
    <v-overlay :value="loading">
      <v-progress-circular indeterminate />
    </v-overlay>
  </div>
</template>

<script>
import Filters from "../components/Filters";

export default {
  props: [
    "schema",
    "operations",
    "topOperations",
    "slowlestOperations",
    "start_date",
    "range",
  ],
  components: { Filters },
  data() {
    return {
      loading: false,
      display: {
        filters: false,
      },
      filters: {
        form: {
          start_date: this.start_date || "today",
          range: this.range || [],
        },
      },
      table_top_operations: {
        headers: [
          { text: "Operation", value: "name", sortable: false },
          {
            text: "Requests",
            value: "tracings_count",
            sortable: false,
            align: "end",
          },
        ],
      },
      table_slowlest_operations: {
        headers: [
          { text: "Operation", value: "name", sortable: false },
          {
            text: "Average",
            value: "average_duration",
            sortable: false,
            align: "end",
          },
          {
            text: "Latest",
            value: "latest_duration",
            sortable: false,
            align: "end",
          },
        ],
      },
      series: [
        {
          name: "Requests",
          data: [],
        },
      ],
      options: {
        chart: {
          type: "line",
          width: "100%",
          toolbar: {
            show: false,
          },
        },
        dataLabels: {
          enabled: false,
        },
        stroke: {
          curve: "straight",
          width: 1,
        },
        xaxis: {
          categories: [],
        },
        yaxis: {
          opposite: true,
        },
        grid: {
          yaxis: {
            lines: {
              show: false,
            },
          },
        },
      },
    };
  },
  methods: {
    getSeries(operation) {
      console.log(operation.metrics.totals);
      return [
        {
          name: "Requests",
          data: operation.metrics.totals,
        },
      ];
    },
    getOptions(operation) {
      return {
        ...this.options,
        ...{
          xaxis: { categories: operation.metrics.dates },
        },
      };
      // Object.assign(this.options.xaxis.categories, operation.metrics.dates);
      console.log(operation.metrics.dates);

      return this.options;
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
    displayFilters() {
      this.display.filters = true;
    },
    hideFilters() {
      this.display.filters = false;
    },
  },
};
</script>
<style scoped>
.bordered {
  border-bottom: 1px solid #efefef;
}
</style>