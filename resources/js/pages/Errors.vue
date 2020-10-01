<template>
  <div>
    <v-app-bar app color="white" elevation="1" class="pt-2">
      <v-row align="center" class="mb-5">
        <v-col>
          <h2>
            <v-icon left color="black" class="mb-1">mdi-alert-rhombus</v-icon>
            Errors
          </h2>
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
            @click="displayNavigation()"
            class="ml-3"
          >
            <v-icon>mdi-filter-variant</v-icon>
          </v-btn>
        </v-col>
      </v-row>
    </v-app-bar>

    <div v-if="!errors.length" class="text-center grey--text">
      <v-icon color="grey" x-large>mdi-bug-check-outline</v-icon>
      <h3 class="mt-3">Good job!</h3>
      <p class="text-caption mt-3">No errors for selected filters.</p>
    </div>

    <div v-if="errors.length">
      <div class="title">Latest</div>
      <div class="text-caption grey--text mb-3">
        Listing latest 100 for selected filters.
      </div>
    </div>
    <v-data-table
      v-if="errors.length"
      :headers="table.headers"
      :items="errors"
      class="elevation-1"
      hide-default-footer
      show-expand
    >
      <template #item.message="{ item }">
        <div class="text-truncate message">
          {{ item.message }}
        </div>
      </template>
      <template #item.category="{ item }">
        <v-chip outlined small :color="categoryColor(item.category)">
          {{ item.category }}
        </v-chip>
      </template>
      <template #item.request.requested_at="{ item }">
        <div class="text-no-wrap">
          {{ item.request.requested_at }}
        </div>
      </template>
      <template v-slot:expanded-item="{ headers, item }">
        <td :colspan="headers.length" class="pa-5">
          <v-row>
            <v-col>
              <h4>Body</h4>
              <v-divider class="mt-3" />
              <div v-highlight>
                <pre
                  class="language-graphql"
                ><code class="break-spaces">{{ item.body }}</code></pre>
              </div>
            </v-col>
            <v-col>
              <h4>Original exception</h4>
              <v-divider class="mt-3" />
              <div v-highlight>
                <pre
                  class="language-graphql"
                ><code class="break-spaces">{{ item.original_exception }}</code></pre>
              </div>
            </v-col>
          </v-row>
        </td>
      </template>
    </v-data-table>

    <div class="mt-5" v-if="errors.length">
      <v-chip dark small color="purple">
        When client sends an invalid request
      </v-chip>
      <v-chip dark small color="red" class="ml-2">
        Internal error/exception on API
      </v-chip>
    </div>
    <v-navigation-drawer
      v-model="display.navigation"
      right
      app
      temporary
      width="380"
      class="pa-5"
    >
      <filters
        :filters="filters"
        @filter="filter()"
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

export default {
  props: ["errors", "clients", "start_date", "range", "selectedClients"],
  components: { Filters },
  data() {
    return {
      loading: false,
      display: {
        navigation: false,
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
      table: {
        headers: [
          { text: "Message", value: "message", sortable: false },
          {
            text: "Operation",
            value: "request.operation.field.name",
            sortable: false,
          },
          { text: "Category", value: "category", sortable: false },
          { text: "Client", value: "request.client.username", sortable: false },
          { text: "Date", value: "request.requested_at", sortable: false },
        ],
      },
    };
  },
  methods: {
    async filter() {
      this.loading = true;

      await this.$inertia.replace(`/lighthouse-dashboard/errors`, {
        data: this.filters.form,
        replace: true,
        preserveScroll: true,
      });

      this.loading = false;
    },
    categoryColor(category) {
      return category === "internal" ? "red" : "purple";
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
<style scoped>
.break-spaces {
  white-space: break-spaces !important;
}

.message {
  max-width: 400px;
}
</style>
