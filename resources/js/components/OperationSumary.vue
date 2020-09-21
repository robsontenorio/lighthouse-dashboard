<template>
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
      <v-divider class="my-5" />
    </v-card-subtitle>
    <v-card-text>
      <h3 class="mb-5 black--text">Clients & Operations</h3>

      <div v-if="loading" class="text-center mt-8">
        <v-progress-circular indeterminate />
      </div>

      <v-data-table
        v-if="sumary.length && !loading"
        :headers="table.headers"
        :items="sumary"
        :loading="loading"
        hide-default-footer
      >
        <template #item.total_requests="{item}">{{ item.total_requests | numeral(0.0) }}</template>
      </v-data-table>

      <div v-if="!sumary.length && !loading">
        <v-alert icon="mdi-alert" text dense>No operations on selected range.</v-alert>
      </div>
    </v-card-text>
  </v-card>
</template>

<script>
import axios from "axios";
import Field from "../components/Field";

export default {
  props: ["operation", "filters"],
  components: { Field },
  data() {
    return {
      loading: false,
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
      this.loading = true;

      const response = await axios.get(
        `/lighthouse-dashboard/operations/${this.operation.id}/sumary`,
        {
          params: this.filters.form,
        }
      );

      this.loading = false;

      this.sumary = response.data;
    },
    close() {
      this.$emit("close");
    },
  },
};
</script>

<style>
</style>