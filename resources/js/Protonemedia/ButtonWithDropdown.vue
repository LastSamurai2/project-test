<template>
  <OnClickOutside :do="hide">
    <div class="relative">
      <button
          @click.prevent="toggle"
          type="button"
          :disabled="disabled"
          class="btn dropdown-toggle btn-secondary"
          style="border-radius: 5px; min-width: 100px"
          aria-haspopup="true"
          ref="button"
      >
        <slot name="button" />
      </button>

      <div ref="tooltip" class="absolute z-10" v-show="opened">
        <div class="mt-2 w-64 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5">
          <slot />
        </div>
      </div>
    </div>
  </OnClickOutside>
</template>

<script>

import { Components } from '@protonemedia/inertiajs-tables-laravel-query-builder';

export default {
  mixins: [Components.ButtonWithDropdown],

  components: {
    OnClickOutside: Components.OnClickOutside,
  },
};
</script>
