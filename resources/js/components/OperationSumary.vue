<template>
  <div>
    <v-card flat>
      <v-card-title>
        <h2>Sumary</h2>
        <v-spacer />
        <v-btn icon @click="close()">
          <v-icon>mdi-close</v-icon>
        </v-btn>
      </v-card-title>
      <v-card-subtitle class="pt-5">
        <field :field="operation.field" :full="true" />
        <v-divider class="mt-5" />
      </v-card-subtitle>
      <v-card-text>
        <v-btn small outlined @click="seeTracings()" class="mb-8">
          See latest tracings
          <v-icon small>mdi-arrow-right</v-icon>
        </v-btn>

        <h3 class="black--text">Clients & Operations</h3>
        <p class="mt-3 mb-8">Who is using that operation?</p>

        <div v-if="loading.sumary" class="text-center">
          <v-progress-circular indeterminate />
        </div>

        <v-data-table
          v-if="sumary.length && !loading.sumary"
          :headers="table.headers"
          :items="sumary"
          :loading="loading.sumary"
          hide-default-footer
        >
          <template #item.total_requests="{ item }">{{
            item.total_requests | numeral(0.0)
          }}</template>
        </v-data-table>

        <div v-if="!sumary.length && !loading.sumary">
          <v-alert icon="mdi-alert" text dense
            >No operations on selected range.</v-alert
          >
        </div>
      </v-card-text>
    </v-card>
    <v-overlay :value="loading.tracing" class="text-center">
      <v-progress-circular indeterminate />
      <p class="my-5">Loading tracings ...</p>
    </v-overlay>
  </div>
</template>

<script>
import axios from "axios";
import Field from "../components/Field";

export default {
  props: ["operation", "filters"],
  components: { Field },
  data() {
    return {
      loading: {
        sumary: false,
        tracing: false,
      },
      sumary: [],
      table: {
        headers: [
          { text: "Client", value: "username", sortable: false },
          {
            text: "Requests",
            value: "total_requests",
            sortable: false,
            align: "end",
          },
        ],
      },
    };
  },
  watch: {
    operation() {
      this.load();
    },
  },
  methods: {
    async load() {
      this.loading.sumary = true;

      const response = await axios.get(
        `/lighthouse-dashboard/operations/${this.operation.id}/sumary`,
        {
          params: this.filters.form,
        }
      );

      this.loading.sumary = false;

      this.sumary = response.data;
    },
    close() {
      this.$emit("close");
    },
    seeTracings() {
      this.loading.tracing = true;

      this.$inertia.visit(
        `/lighthouse-dashboard/operations/${this.operation.id}`
      );
    },
  },
};
</script>