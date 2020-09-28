<template>
  <div>
    <v-app-bar app color="white" elevation="1" class="pt-2">
      <h2><v-icon left color="black">mdi-pulse</v-icon>Operations</h2>
    </v-app-bar>

    <v-dialog :value="true" persistent>
      <v-card tile class="px-5">
        <v-card-title>
          <h3>
            <v-icon left color="black">mdi-pulse</v-icon>
            {{ operation.field.name }}
          </h3>
          <v-spacer />
          <v-btn icon @click="back()">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-card-subtitle class="pt-3 pl-15">
          Listing latest 50 tracings.
        </v-card-subtitle>
        <v-card-text>
          <v-data-table
            :headers="table.headers"
            :items="operation.tracings"
            :items-per-page="50"
            hide-default-footer
            show-expand
            class="elevation-3"
          >
            <template #item.arguments="{ item }">
              <div v-highlight>
                <pre
                  class="language-graphql ml-n5"
                ><code>{{ payloadPreview(item.payload) }}</code></pre>
              </div>
            </template>
            <template #item.duration="{ item }">{{
              item.duration | milliseconds
            }}</template>
            <template v-slot:expanded-item="{ headers, item }">
              <td :colspan="headers.length">
                <v-row>
                  <v-col>
                    <div v-highlight>
                      <pre
                        class="language-graphql"
                      ><code>{{ payloadPretty(item.payload) }}</code></pre>
                    </div>
                  </v-col>
                  <v-col>
                    <tracing-execution :execution="item.execution.execution" />
                  </v-col>
                </v-row>
              </td>
            </template>
          </v-data-table>
        </v-card-text>
      </v-card>
    </v-dialog>
  </div>
</template>

<script>
import { component as VueCodeHighlight } from "vue-code-highlight";
import TracingExecution from "../components/tracing/TracingExecution";
import * as prettier from "prettier/standalone";
import * as graphql from "prettier/parser-graphql";

export default {
  props: ["operation"],
  components: { VueCodeHighlight, TracingExecution },
  data() {
    return {
      table: {
        headers: [
          { text: "Arguments", value: "arguments", sortable: false },
          { text: "Client", value: "request.client.username", sortable: false },
          { text: "Requested at", value: "start_time", sortable: false },
          { text: "Duration", value: "duration", sortable: false },
        ],
      },
    };
  },
  methods: {
    payloadPreview(request) {
      const maxLength = 50;
      const regex = /\(([^)]+)\)/;
      const matches = regex.exec(request);

      if (!matches) {
        return "-";
      }

      const preview = "(" + matches[1] + ")";

      return preview.length < maxLength
        ? preview
        : preview.substring(0, maxLength) + "...";
    },
    payloadPretty(payload) {
      return prettier.format(payload, {
        parser: "graphql",
        plugins: [graphql],
      });
    },
    back() {
      window.history.back();
    },
  },
};
</script>
