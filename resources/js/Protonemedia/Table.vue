<style scoped>
/*
TODO: Convert to @apply
*/

table >>> th {
  font-weight: 500;
  font-size: 0.75rem;
  line-height: 1rem;
  padding-top: 0.75rem;
  padding-bottom: 0.75rem;
  padding-left: 1.5rem;
  padding-right: 1.5rem;
  text-align: left;
  --tw-text-opacity: 1;
  color: rgba(107, 114, 128, var(--tw-text-opacity));
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

table >>> td {
  font-size: 0.875rem;
  line-height: 1.25rem;
  padding-top: 1rem;
  padding-bottom: 1rem;
  padding-left: 1.5rem;
  padding-right: 1.5rem;
  --tw-text-opacity: 1;
  color: rgba(107, 114, 128, var(--tw-text-opacity));
  white-space: nowrap;
}

table >>> tr:hover td {
  --tw-bg-opacity: 1;
  background-color: rgba(249, 250, 251, var(--tw-bg-opacity));
}
</style>

<template>
  <div>
    <slot name="topaction"></slot>
    <div class="container-fluid">

      <div class="row">
        <div class="col-12 vuetable mb-2 mt-2">

          <div class="toolbar d-flex mb-2">
            <slot name="tableFilter"
                :hasFilters="hasFilters"
                :filters="filters"
                :changeFilterValue="changeFilterValue">
              <TableFilter v-if="hasFilters" :filters="filters" :on-change="changeFilterValue" />
            </slot>

            <slot name="tableGlobalSearch"
                  :search="search"
                  :changeGlobalSearchValue="changeGlobalSearchValue">
              <TableGlobalSearch
                  v-if="search && search.global"
                  :value="search.global.value"
                  :on-change="changeGlobalSearchValue"
              />
            </slot>
            <!-- Filters aka Search Rows -->

            <slot name="tableAddSearchRow"
                  :hasSearchRows="hasSearchRows"
                  :search="search"
                  :newSearch="newSearch"
                  :enableSearch="enableSearch">
              <TableAddSearchRow
                  v-if="hasSearchRows"
                  :rows="search"
                  :new="newSearch"
                  :on-add="enableSearch"
              />
            </slot>

            <slot name="tableColumns"
                  :hasColumns="hasColumns"
                  :columns="columns"
                  :changeColumnStatus="changeColumnStatus">
                <TableColumns v-if="hasColumns" :columns="columns" :on-change="changeColumnStatus" />
              </slot>
          </div>
          <slot
              name="tableSearchRows"
              :hasSearchRows="hasSearchRows"
              :search="search"
              :newSearch="newSearch"
              :disableSearch="disableSearch"
              :changeSearchValue="changeSearchValue"
          >
            <TableSearchRows
                ref="rows"
                v-if="hasSearchRows"
                :rows="search"
                :new="newSearch"
                :on-remove="disableSearch"
                :on-change="changeSearchValue"
            />
          </slot>
          <slot name="tableWrapper" :meta="meta">
            <slot name="table">
              <table class="table-divided order-with-arrow vuetable ui blue selectable celled stackable attached table">
                <thead>
                <slot name="head" />
                </thead>
                <tbody>
                <slot name="body" />
                </tbody>
              </table>
            </slot>
            <slot name="pagination">
              <Pagination :meta="meta" />
            </slot>
          </slot>

       </div>
      </div>

    </div>
  </div>
</template>

<script>
import Pagination from "./Pagination";
import TableFilter from "./TableFilter";
import TableGlobalSearch from "./TableGlobalSearch";
import TableSearchRows from "./TableSearchRows";
import TableAddSearchRow from "./TableAddSearchRow";
import TableColumns from "./TableColumns"
import { Components } from '@protonemedia/inertiajs-tables-laravel-query-builder';

export default {
  mixins: [Components.Table],

  components: {
    Pagination,
    TableAddSearchRow,
    TableColumns,
    TableFilter,
    TableGlobalSearch,
    TableSearchRows,
  },
};
</script>