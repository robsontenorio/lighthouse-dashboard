<template>
  <div>
    <v-app-bar app color="white" elevation="1">
      <h2>Schema</h2>
      <v-chip label class="mx-5 font-weight-bold">{{ schema.name }}</v-chip>
      <div class="text-caption grey--text">Last updated on {{ schema.updated_at }}</div>
    </v-app-bar>TODO
  </div>
</template>

<script>
export default {
  props: ["schema"],
};
</script>