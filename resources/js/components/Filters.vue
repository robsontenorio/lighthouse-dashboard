<template>
  <v-card flat>
    <v-card-title>
      <h3>Filters</h3>
      <v-spacer />
      <v-btn icon @click="$emit('close')">
        <v-icon>mdi-close</v-icon>
      </v-btn>
    </v-card-title>
    <v-card-text class="mt-5">
      <div class="font-weight-black text-caption">STARTING FROM</div>
      <v-radio-group v-model="filters.form.start_date" @change="filter()">
        <v-radio
          v-for="option in options"
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
          <v-icon small class="mb-1" v-if="dateRangeText" left
            >mdi-selection-drag</v-icon
          >
          {{ dateRangeText }}
        </div>
      </div>

      <div class="font-weight-black text-caption mt-5 mb-5">CLIENTS</div>
      <v-btn x-small outlined @click="uncheckAll()">uncheck all</v-btn>
      <v-checkbox
        v-for="client in filters.options.clients"
        v-model="filters.form.clients"
        :label="client.username"
        :value="client.id"
        :key="client.id"
        @change="filter()"
        hide-details
        multiple
      />
    </v-card-text>
  </v-card>
</template>

<script>
export default {
  props: ["filters"],
  data() {
    return {
      options: [
        { value: "today", label: "Today" },
        { value: "yesterday", label: "Yesterday" },
        { value: "last week", label: "Last week" },
        { value: "last month", label: "Last Month" },
        { value: "in custom range", label: "In custom range" },
      ],
    };
  },
  computed: {
    dateRangeText() {
      return this.filters.form.range.join(" ~ ");
    },
    isCustomRange() {
      return this.filters.form.start_date === "in custom range";
    },
  },
  methods: {
    filter() {
      if (this.isCustomRange && this.filters.form.range.length < 2) {
        return;
      }

      if (!this.isCustomRange) {
        this.filters.form.range = [];
      }

      this.$emit("filter");
    },
    uncheckAll() {
      if (this.filters.form.clients.length) {
        this.filters.form.clients = [];
      }

      this.filter();
    },
  },
};
</script>
