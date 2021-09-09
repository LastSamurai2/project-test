<style scoped>
  .close {
    float: none;
    margin-left: 0.4rem;
    font-size: 0.95rem;
  }
</style>
<template>
  <div
    class="row"
    v-show="hasFiltersEnabled"
  >
    <div class="col col-12">
      <b-card class="mb-4">
        <div class="d-flex flex-row">
          <div v-for="(search, key) in enabledFilters" :key="key">
            <label :for="search.key">{{ search.label }}</label>
            <button @click.prevent="onRemove(search.key)" type="button" class="close" aria-label="Close">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-dash-circle" viewBox="0 0 16 16">
                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                <path d="M4 8a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7A.5.5 0 0 1 4 8z"/>
              </svg>
              <span class="sr-only">{{ $t('table.remove-search') }}</span>
            </button>
            <input
                class="form-control"
                :ref="search.key"
                :value="search.value"
                type="text"
                @input="onChange(search.key, $event.target.value)"
              />
          </div>
        </div>
      </b-card>
    </div>
  </div>
</template>

<script>
import { Components } from '@protonemedia/inertiajs-tables-laravel-query-builder';
import TableSearchRows from './Components/TableSearchRows';

export default {
  mixins: [TableSearchRows],
};
</script>
