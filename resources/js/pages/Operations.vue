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

    <div
      v-if="topOperations.length === 0 && slowlestOperations.length === 0"
      class="text-center grey--text"
    >
      <v-icon color="grey" x-large>mdi-weather-windy</v-icon>
      <h3 class="mt-3">Oops! Nothing here.</h3>
      <p class="text-caption mt-3">Make your first request to this Schema.</p>
    </div>

    <v-data-table
      v-if="topOperations.length"
      :headers="table_top_operations.headers"
      :items="topOperations"
      @click:row="selectOperation"
      hide-default-footer
      class="elevation-1 row-pointer mb-8"
    >
      <template #top>
        <div class="pa-3">
          <div class="title">Top</div>
          <div class="text-caption grey--text">Most requested operations in selected period.</div>
        </div>
      </template>
      <template #item.field="{item}">
        <field :field="item.field" class="py-4" />
      </template>
    </v-data-table>

    <v-data-table
      v-if="slowlestOperations.length"
      :headers="table_slowlest_operations.headers"
      :items="slowlestOperations"
      @click:row="selectOperation"
      hide-default-footer
      class="elevation-1 row-pointer"
    >
      <template #top>
        <div class="pa-3">
          <div class="title">Slow</div>
          <div class="text-caption grey--text">Most slowlest operations in selected period.</div>
        </div>
      </template>
      <template #item.field="{item}">
        <field :field="item.field" class="py-4" />
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
    <v-navigation-drawer
      v-model="display.sumary"
      right
      :app="display.sumary"
      width="380"
      class="pa-5"
    >
      <operation-sumary :operation="selectedOperation" :filters="filters" @close="hideSumary()" />
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
  props: ["topOperations", "slowlestOperations", "start_date", "range"],
  components: { Filters, Field, OperationSumary },
  data() {
    return {
      loading: false,
      selectedOperation: {
        field: {},
      },
      display: {
        filters: false,
        sumary: false,
      },
      filters: {
        form: {
          start_date: this.start_date || "today",
          range: this.range || [],
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
    selectOperation(operation) {
      this.selectedOperation = operation;
      this.displaySumary();
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
    displaySumary() {
      this.hideFilters();
      this.display.sumary = true;
    },
    hideSumary() {
      this.display.sumary = false;
    },
    displayFilters() {
      this.hideSumary();
      this.display.filters = true;
    },
    hideFilters() {
      this.display.filters = false;
    },
  },
};
</script>