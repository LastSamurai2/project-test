<template>
  <ButtonWithDropdown ref="dropdown" v-show="hasRows" :disabled="!rowsLeft" class="w-auto">
    <template #button>
      <span>{{ $t('table.add-a-filter')}}</span>
    </template>

    <div class="list-group">
      <button
          v-for="searchOption in rows"
          v-show="showRow(searchOption)"
          :key="searchOption.key"
          @click.prevent="enableSearch(searchOption.key)"
          style="display: flex"
          class="list-group-item list-group-item-action align-items-center justify-content-between"
      >
        <span>{{ searchOption.label }}</span>
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-circle ml-5" viewBox="0 0 16 16">
          <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
          <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
        </svg></button>
    </div>
  </ButtonWithDropdown>
</template>

<script>
import { Components } from '@protonemedia/inertiajs-tables-laravel-query-builder';
import TableAddSearchRow from './Components/TableAddSearchRow'
import ButtonWithDropdown from "./ButtonWithDropdown";

export default {
  mixins: [TableAddSearchRow],

  components: {
    OnClickOutside: Components.OnClickOutside,
    ButtonWithDropdown
  },
};
</script>
