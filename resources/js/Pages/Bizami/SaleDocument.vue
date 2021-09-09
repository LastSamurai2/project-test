<template>
  <app-layout>
    <template #header>
      <h1>
        Bizami / {{ $t('bizami.sale.title') }}
      </h1>
    </template>

      <Table
          :filters="queryBuilderProps.filters"
          :search="queryBuilderProps.search"
          :columns="queryBuilderProps.columns"
          :on-update="setQueryBuilder"
          :meta="sale_documents"
      >
          <template #head>
              <tr>
                  <th>{{ $t('bizami.sale.doc_id') }}</th>
                  <th class="sortable" @click.prevent="sortBy('date')">{{ $t('common.date') }}</th>
                  <th>{{ $t('common.type') }}</th>
                  <th>{{ $t('bizami.product_id') }}</th>
                  <th class="sortable" @click.prevent="sortBy('qty')">{{ $t('common.qty') }}</th>
                  <th class="sortable" @click.prevent="sortBy('value')">{{ $t('common.value') }}</th>
                  <th>{{ $t('bizami.sale.warehouse_id') }}</th>
                  <th>{{ $t('bizami.sale.is_investment') }}</th>
              </tr>
          </template>

          <template #body>
              <tr v-for="document in sale_documents.data" :key="document.id">
                  <td class="">{{ document.document_id }}</td>
                  <td class="">{{ document.date }}</td>
                  <td class="">{{ document.type }}</td>
                  <td class="">{{ document.product_id }}</td>
                  <td class="">{{ document.qty }}</td>
                  <td class="">{{ document.value }}</td>
                  <td class="">{{ document.warehouse_id }}</td>
                  <td class="">{{ document.is_investment }}</td>
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
      sale_documents: Object,
  }
}
</script>
