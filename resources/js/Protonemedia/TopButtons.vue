<style scoped>
  .button-link {
    margin-left: 1rem
  }
</style>

<template>
  <div class="d-flex justify-content-end buttons-wrapper my-3">
    <slot name="moreButtons"></slot>

    <div v-for="button in meta" :class="'button-link'">
      <inertia-link :class="'btn btn-primary'" :href="button.url">{{ $t(button.label) }}</inertia-link>
    </div>

  </div>
</template>

<script>

export default {
  props: {
    meta: {
      type: Array,
      default: () => {
        return {};
      },
      required: false,
    }
  }
};
</script>
