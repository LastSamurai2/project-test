<template>
  <app-layout>
    <template #header>
      <h1>
        Bizami / {{ $t('bizami.product-s') }}
      </h1>
    </template>

      <Table
          :filters="queryBuilderProps.filters"
          :search="queryBuilderProps.search"
          :columns="queryBuilderProps.columns"
          :on-update="setQueryBuilder"
          :meta="products"
      >
          <template #head>
              <tr>
                  <th class="sortable" v-show="showColumn('sku')" @click.prevent="sortBy('sku')">Sku</th>
                  <th class="sortable" v-show="showColumn('name')" @click.prevent="sortBy('name')">{{ $t('common.name') }}</th>
                  <th class="">{{ $t('bizami.catalog_number') }}</th>
                  <th v-show="showColumn('provider')">{{ $t('bizami.provider') }}</th>
                  <th v-show="showColumn('producent')">{{ $t('bizami.producent') }}</th>
                  <th v-show="showColumn('catalog_group')">{{ $t('bizami.catalog_group') }}</th>
                  <th v-show="showColumn('gross_weight')">{{ $t('bizami.gross.weight') }}</th>
                  <th v-show="showColumn('gross_volume')">{{ $t('bizami.gross.volume') }}</th>
                  <th v-show="showColumn('category')">{{ $t('bizami.category') }}</th>
                  <th v-show="showColumn('is_active')">{{ $t('common.status') }}</th>
                  <th class="">{{ $t('common.value') }}</th>
              </tr>
          </template>

          <template #body>
              <tr v-for="product in products.data" :key="product.id">
                  <td v-show="showColumn('sku')" class="">{{ product.sku }}</td>
                  <td v-show="showColumn('name')" class="text-wrap" style="font-size: 12px; min-width: 350px">{{ product.name }}</td>
                  <td class="">{{ product.catalog_number }}</td>
                  <td v-show="showColumn('provider')">{{ product.provider }}</td>
                  <td v-show="showColumn('producent')">{{ product.producent }}</td>
                  <td v-show="showColumn('catalog_group')" style="font-size: 12px;">{{ product.catalog_group }}</td>
                  <td v-show="showColumn('gross_weight')" class="">{{ product.gross_weight }}</td>
                  <td v-show="showColumn('gross_volume')" class="">{{ product.gross_volume }}</td>
                  <td v-show="showColumn('category')">{{ product.category }}</td>
                  <td v-show="showColumn('is_active')">{{ product.is_active }}</td>
                  <td class="">{{ product.value }}</td>
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
      products: Object,
  }
}
</script>
