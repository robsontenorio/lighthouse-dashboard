<template>
  <v-card outlined class="text-truncate my-5 scroll" max-height="400">
    <v-card-subtitle class="font-weight-bold">TRACING</v-card-subtitle>
    <v-card-text>
      <div v-for="(resolver, index) in execution.resolvers" :key="index">
        <v-row no-gutters>
          <v-col>{{ resolver.path.join('.') }}</v-col>
          <v-col class="text-right">{{ resolver.duration | milliseconds }}</v-col>
        </v-row>
      </div>
    </v-card-text>
  </v-card>
</template>

<script>
export default {
  props: ["execution"],
  data() {
    return {
      height: 50,
    };
  },
};
</script>
<style scoped>
.scroll {
  overflow-y: auto !important;
}
</style>