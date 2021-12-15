<template>
  <div>
    <v-app-bar app color="white" elevation="1">
      <h2><v-icon left color="black">mdi-pulse</v-icon>Operations</h2>
    </v-app-bar>

    <v-bottom-sheet :value="true" persistent fullscreen scrollable>
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
        <v-card-subtitle class="pt-3 mb-5 bordered">
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
                  class="language-graphql ml-n5 text-truncate payload"
                ><code>{{ extractPayloadArgs(item.payload) }}</code></pre>
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
                      <pre class="language-graphql"><code>{{ payloadPretty(item.payload) }}</code></pre>
                    </div>
                  </v-col>               
                </v-row>
              </td>
            </template>
          </v-data-table>
        </v-card-text>
      </v-card>
    </v-bottom-sheet>
  </div>
</template>

<script>
import { component as VueCodeHighlight } from "vue-code-highlight";
import * as prettier from "prettier/standalone";
import * as graphql from "prettier/parser-graphql";

export default {
  props: ["operation"],
  components: { VueCodeHighlight },
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
    extractPayloadArgs(request) {
      const maxLength = 50;
      const regex = /\(([^)]+)\)/;
      const matches = regex.exec(request);

      if (!matches) {
        return "-";
      }

      return "(" + matches[1] + ")";
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
<style scoped>
.payload {
  max-width: 500px;
}
</style>