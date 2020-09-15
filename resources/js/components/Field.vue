<template>
  <div>
    <text-highlight :queries="[highlight]" class="font-weight-bold">{{ field.name }}</text-highlight>
    <span v-if="field.type_def" class="blue--text">: {{ field.type_def }}</span>
    <div class="text-caption grey--text pt-1" v-html="field.description" />
  </div>
</template>

<script>
import TextHighlight from "vue-text-highlight";

export default {
  props: {
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