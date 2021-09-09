<template>
  <app-layout>
    <template #header>
      <h1>
        Bizami / {{ $t('bizami.warehouse-s')}}
      </h1>
    </template>

      <Table
          :filters="queryBuilderProps.filters"
          :search="queryBuilderProps.search"
          :columns="queryBuilderProps.columns"
          :on-update="setQueryBuilder"
          :meta="warehouses"
      >
          <template #head>
              <tr>
                  <th class="sortable" @click.prevent="sortBy('warehouse_id')">{{$t('forms.warehouse_id')}}</th>
                  <th class="">{{ $t('forms.type') }}</th>
                  <th>{{$t('common.columns.actions')}}</th>
              </tr>
          </template>

          <template #body>
              <tr v-for="warehouse in warehouses.data" :key="warehouse.id">
                  <td class="">{{ warehouse.warehouse_id }}</td>
                  <td class="">{{ getLabelOrValue(warehouse.type, 'type') }}</td>
                  <td><inertia-link :href="route('bizami.warehouse.edit', {id: warehouse.id})">{{$t('button.edit')}}</inertia-link></td>
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
      warehouses: Object,
      columns: Object
  },
  methods: {
      getLabelOrValue(value, name) {
          if (this.columns[name] && this.columns[name].options && this.columns[name].options.length > 0) {
              const option = this.columns[name].options.find(opt => opt.code === value);
              if (option) {
                  return option.label;
              }
          }
          return value;
      }
  }
}
</script>
