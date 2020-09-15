  <template>
  <div>
    <v-app-bar app color="white" elevation="1" class="pt-2">
      <v-row align="center" class="mb-5">
        <v-col>
          <h2>Types</h2>
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
          <v-btn color="primary" fab x-small depressed dark @click="displayFilters()" class="ml-3">
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
          <text-highlight :queries="[search]" class="title">{{ type.name }}</text-highlight>
          <div class="text-caption grey--text">{{ type.description }}</div>
        </div>
      </template>
      <template #header.statistics_count>Requests {{ filters.form.start_date }}</template>
      <template #item.name="{item}">
        <div class="py-4">
          <field :field="item" :highlight="search" />
        </div>
      </template>
      <!-- <template #item.statistics_count="{item}">
          <div class="text-right">{{ item.statistics_count }}</div>
      </template>-->
    </v-data-table>
    <div v-if="filteredTypes.length === 0" class="text-center grey--text">
      <h3>Oops! Nothing here.</h3>
      <p class="text-caption mt-3">It searchs only on Type name or Field name.</p>
    </div>

    <v-navigation-drawer
      v-model="display.filters"
      right
      :app="display.filters"
      width="380"
      class="pa-5"
    >
      <v-card flat>
        <v-card-title>
          <h3>Filters</h3>
          <v-btn text small outlined color="primary" @click="reset()" class="ml-3">reset</v-btn>
          <v-spacer />
          <v-btn icon @click="hideFilters()">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-card-text class="mt-5">
          <div class="font-weight-black text-caption">STARTING FROM</div>
          <v-radio-group v-model="filters.form.start_date" @change="filter()">
            <v-radio
              v-for="option in filters.options"
              :label="option.label"
              :value="option.value"
              :key="option.value"
            />
          </v-radio-group>

          <div v-if="isCustomRange">
            <v-date-picker
              v-model="filters.form.range"
              :max="new Date().toISOString()"
              :show-current="false"
              @change="filter()"
              no-title
              range
              class="elevation-2"
            />
            <div class="py-3 font-weight-bold">
              <v-icon small class="mb-1" v-if="dateRangeText" left>mdi-selection-drag</v-icon>
              {{ dateRangeText }}
            </div>
          </div>
        </v-card-text>
      </v-card>
    </v-navigation-drawer>
    <v-navigation-drawer
      v-model="display.sumary"
      right
      :app="display.sumary"
      width="380"
      class="pa-5"
    >
      <field-sumary :field="selectedField" :filters="filters" @close="hideSumary()" />
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
import Field from "../components/Field";
import FieldSumary from "../components/FieldSumary";

export default {
  props: ["types", "start_date", "range"],
  components: { TextHighlight, FieldSumary, Field },
  data() {
    return {
      loading: false,
      search: "",
      selectedField: {},
      display: {
        sumary: false,
        filters: false,
      },
      filters: {
        form: {
          start_date: this.start_date || "today",
          range: this.range || [],
        },
        options: [
          { value: "today", label: "Today" },
          { value: "yesterday", label: "Yesterday" },
          { value: "last week", label: "Last week" },
          { value: "last month", label: "Last Month" },
          { value: "in custom range", label: "In custom range" },
        ],
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
            value: "statistics_count",
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
    dateRangeText() {
      return this.filters.form.range.join(" ~ ");
    },
    isCustomRange() {
      return this.filters.form.start_date === "in custom range";
    },
  },
  methods: {
    async filter() {
      if (this.isCustomRange && this.filters.form.range.length < 2) {
        return;
      }

      if (!this.isCustomRange) {
        this.filters.form.range = [];
      }

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
      this.displaySumary();
    },
    containsText(item, text) {
      const fields = _(item.fields).map("name").join(" ");

      return (
        normalizeText(item.name).includes(normalizeText(text)) ||
        normalizeText(fields).includes(normalizeText(text))
      );
    },
    async displaySumary() {
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
    reset() {
      this.filters.form.start_date = "today";
      this.hideFilters();
      this.filter();
    },
  },
};
</script>
<style lang="css" scoped>
.row-pointer >>> tbody tr :hover {
  cursor: pointer;
}
</style>