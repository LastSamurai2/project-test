<template>
  <app-layout>
    <template #header>
      <h1>
        Bizami / {{ $t('bizami.warehouse_state-s') }}
      </h1>
    </template>

      <Table
          :filters="queryBuilderProps.filters"
          :search="queryBuilderProps.search"
          :columns="queryBuilderProps.columns"
          :on-update="setQueryBuilder"
          :meta="warehouse_states"
      >
          <template #head>
              <tr>
                  <th>ID</th>
                  <th>{{ $t('bizami.product_id') }}</th>
                  <th class="sortable" @click.prevent="sortBy('qty')">{{ $t('common.qty') }}</th>
                  <th class="sortable" @click.prevent="sortBy('qty_reserved')">{{ $t('bizami.qty_reserved') }}</th>
                  <th class="sortable" @click.prevent="sortBy('qty_ordered')">{{ $t('bizami.qty_ordered') }}</th>
                  <th>{{ $t('common.status') }}</th>
                  <th class="sortable" @click.prevent="sortBy('norm')">{{ $t('common.norm') }}</th>
              </tr>
          </template>

          <template #body>
              <tr v-for="warehouse_state in warehouse_states.data" :key="warehouse_state.id">
                  <td class="">{{ warehouse_state.warehouse_id }}</td>
                  <td class="">{{ warehouse_state.product_id }}</td>
                  <td class="">{{ warehouse_state.qty }}</td>
                  <td class="">{{ warehouse_state.qty_reserved }}</td>
                  <td class="">{{ warehouse_state.qty_ordered }}</td>
                  <td class="">{{ warehouse_state.status }}</td>
                  <td class="">{{ warehouse_state.norm }}</td>
              </tr>
          </template>
      </Table>

  </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout'
import { InteractsWithQueryBuilder } from '@protonemedia/inertiajs-tables-laravel-query-builder';
import Table from '@/Protonemedia/Table'

export default {
  mixins: [InteractsWithQueryBuilder],
  components: {
    AppLayout,
    Table
  },
  props: {
      warehouse_states: Object,
  }
}
</script>
