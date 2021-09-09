<template>
  <ButtonWithDropdown placement="bottom-start" :active="hasEnabledFilter">
    <template #button>
      {{ $t('table.filter')}}
    </template>
    <div class="list-group">
      <div v-for="(filter, key) in filters" :key="key" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between">
        <div>{{filter.label}}</div>
          <select
            :value="filter.value"
            @change="onChange(filter.key, $event.target.value)"
            class="ml-5"
          >
            <option v-for="(option, key) in filter.options" :value="key" :key="key">{{ option }}</option>
          </select>
      </div>
    </div>
  </ButtonWithDropdown>
</template>

<script>
import { Components } from '@protonemedia/inertiajs-tables-laravel-query-builder';
import ButtonWithDropdown from "./ButtonWithDropdown";

export default {
  mixins: [Components.TableFilter],

  components: {
    ButtonWithDropdown
  },
};
</script>
