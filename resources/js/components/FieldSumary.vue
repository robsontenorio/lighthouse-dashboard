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
      <field :field="field" :full="true" />
      <v-divider class="my-5" />
    </v-card-subtitle>
    <v-card-text>
      <h3 class="black--text">Clients & Fields</h3>
      <p class="mt-3 mb-8">Where that field is used?</p>

      <div v-if="loading" class="text-center">
        <v-progress-circular indeterminate />
      </div>

      <v-expansion-panels v-if="!loading && sumary.length">
        <v-expansion-panel v-for="client in sumary" :key="client.id">
          <v-expansion-panel-header class="grey lighten-5">
            <v-row no-gutters>
              <v-col>{{ client.username }}</v-col>
              <v-col class="text-right">
                <v-chip x-small>{{ totalRequestByClient(client) }}</v-chip>
              </v-col>
            </v-row>
          </v-expansion-panel-header>
          <v-expansion-panel-content>
            <v-data-table
              :headers="table.headers"
              :items="client.metrics"
              :loading="loading"
              hide-default-footer
            >
              <template #item.total_requests="{ item }">
                {{ item.total_requests | numeral(0.0) }}
              </template>
            </v-data-table>
          </v-expansion-panel-content>
        </v-expansion-panel>
      </v-expansion-panels>

      <div v-if="!sumary.length && !loading">
        <v-alert icon="mdi-alert" text dense>
          No operations on selected range.
        </v-alert>
      </div>
    </v-card-text>
  </v-card>
</template>

<script>
import axios from "axios";
import _ from "lodash";
import Field from "../components/Field";

export default {
  props: ["field", "filters"],
  components: { Field },
  data() {
    return {
      loading: false,
      sumary: [],
      table: {
        headers: [
          { text: "Operation", value: "field.name", sortable: false },
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
    field() {
      this.load();
    },
  },
  methods: {
    totalRequestByClient(client) {
      return _(client.metrics).sumBy("total_requests");
    },
    async load() {
      this.loading = true;

      const response = await axios.get(
        `/lighthouse-dashboard/fields/${this.field.id}/sumary`,
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
.more {
  cursor: pointer;
  color: black;
  text-align: right;
  margin-top: 10px;
}
</style>