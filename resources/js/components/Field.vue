<template>
  <div>
    <text-highlight :queries="[highlight]" class="font-weight-bold">{{ field.name }}</text-highlight>
    <span v-if="field.args">
      <span v-if="full">
        (
        <div v-html="field.args" class="ml-5" />)
      </span>
      <span v-else>(...)</span>
    </span>
    <span v-if="field.type_def" class="blue--text">: {{ field.type_def }}</span>
    <div class="text-caption grey--text pt-1" v-html="field.description" />
  </div>
</template>

<script>
import TextHighlight from "vue-text-highlight";

export default {
  props: {
    full: {
      type: Boolean,
      default: false,
    },
    field: {
      type: Object,
    },
    highlight: {
      type: String,
      default: "",
    },
  },
  components: { TextHighlight },
};
</script>
<style scoped>
span >>> .arg-name {
  color: indigo;
}

span >>> .arg-type {
  color: orange;
}
</style>