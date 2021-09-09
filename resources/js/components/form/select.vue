<template>
  <b-form-group label-cols="2" horizontal :label="label">
    <v-select v-model="selection" :options="options" @input="setSelected"/>
  </b-form-group>
</template>

<script>

import vSelect from "vue-select";
import "vue-select/dist/vue-select.css";

export default {
  props: {
    value: [String, Number],
    name: String,
    label: String,
    required: Boolean,
    readonly: Boolean,
    options: Array,
    updateField: Function
  },
  components: {
    "v-select": vSelect
  },
  data() {
    return {
      selection: null
    };
  },
  mounted() {
    this.selection = this.options.find(opt => opt.code === this.value);
  },
  methods: {
    setSelected(value) {
      this.selection = value;
      this.updateField(value.code, this.name)
    }
  },

};
</script>
