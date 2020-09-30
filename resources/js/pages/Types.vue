  <template>
  <div>
    <v-app-bar app color="white" elevation="1" class="pt-2">
      <v-row align="center" class="mb-5">
        <v-col>
          <h2><v-icon left color="black">mdi-shape-outline</v-icon>Types</h2>
        </v-col>
        <v-col cols="3" class="text-right">
          <v-text-field
            v-model="search"
            outlined
            prepend-inner-icon="mdi-magnify"
            label="Type or Field ..."
            background-color="white"
            autocomplete="off"
            dense
            hide-details
          />
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
    <v-data-table
      v-for="type in filteredTypes"
      :key="type.id"
      :headers="table.headers"
      :items="type.fields"
      @click:row="selectField"
      hide-default-footer
      class="elevation-1 row-pointer mb-8"
    >
      <template #top>
        <div class="pa-3">
          <text-highlight :queries="[search]" class="title">
            {{ type.name }}
          </text-highlight>
          <div class="text-caption grey--text">{{ type.description }}</div>
        </div>
      </template>
      <template #item.name="{ item }">
        <field :field="item" :highlight="search" class="py-4" />
      </template>
      <template #item.total_requests="{ item }">
        {{ item.total_requests | numeral(0.0) }}
      </template>
    </v-data-table>
    <div v-if="filteredTypes.length === 0" class="text-center grey--text">
      <v-icon color="grey" x-large>mdi-weather-windy</v-icon>
      <h3 class="mt-3">Oops! Nothing here.</h3>
      <p class="text-caption mt-3" v-if="types.length === 0">
        Make your first request to this Schema.
      </p>
      <p class="text-caption mt-3" v-else>
        It searchs only on
        <strong>Types</strong> and <strong>Fields</strong>.
      </p>
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

      <field-sumary
        v-show="display.component === 'sumary'"
        :field="selectedField"
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
import _ from "lodash";
import { normalizeText } from "normalize-text";
import TextHighlight from "vue-text-highlight";
import Filters from "../components/Filters";
import Field from "../components/Field";
import FieldSumary from "../components/FieldSumary";

export default {
  props: ["types", "start_date", "range", "clients", "selectedClients"],
  components: { TextHighlight, FieldSumary, Field, Filters },
  data() {
    return {
      loading: false,
      search: "",
      selectedField: {},
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
      table: {
        headers: [
          {
            text: "Field",
            value: "name",
            sortable: false,
          },
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
  computed: {
    filteredTypes() {
      if (this.search === "") return this.types;

      return _(this.types)
        .filter((item) => this.containsText(item, this.search))
        .value();
    },
  },
  methods: {
    async filter() {
      this.loading = true;

      await this.$inertia.replace(`/lighthouse-dashboard/types`, {
        data: this.filters.form,
        replace: true,
        preserveScroll: true,
      });

      this.loading = false;
    },
    selectField(field) {
      this.selectedField = field;
      this.setNavigationComponent("sumary");
      this.displayNavigation();
    },
    containsText(item, text) {
      const fields = _(item.fields).map("name").join(" ");

      return (
        normalizeText(item.name).includes(normalizeText(text)) ||
        normalizeText(fields).includes(normalizeText(text))
      );
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