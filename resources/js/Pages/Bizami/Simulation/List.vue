<template>
  <app-layout>
    <template #header>
      <h1>
        Bizami / Simulations
      </h1>
    </template>

      <Table
          :filters="queryBuilderProps.filters"
          :search="queryBuilderProps.search"
          :columns="queryBuilderProps.columns"
          :on-update="setQueryBuilder"
          :meta="simulations"
      >
          <template #topaction>
              <TopButtons :meta="buttons"></TopButtons>
          </template>

          <template #head>
              <tr>
                  <th class="">{{ $t('bizami.columns.date') }}</th>
                  <th class="">{{ $t('bizami.columns.user') }}</th>
                  <th class="">{{ $t('bizami.columns.algorithm') }}</th>
                  <th class="">{{ $t('bizami.columns.supplier') }}</th>
                  <th>{{'Actions'}}</th>
              </tr>
          </template>

          <template #body>
              <tr v-for="simulation in simulations.data" :key="simulation.id">
                  <td class="">{{ simulation.created_at }}</td>
                  <td class="">{{ simulation.user_name }}</td>
                  <td class="">{{ simulation.algorithm_name }}</td>
                  <td class="">{{ simulation.supplier }}</td>
                  <td><inertia-link :href="route('bizami.simulation.view', {id: simulation.id})">{{'View'}}</inertia-link></td>
              </tr>
          </template>
      </Table>

  </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout'
import { InteractsWithQueryBuilder } from '@protonemedia/inertiajs-tables-laravel-query-builder';
import Table from '@/Protonemedia/Table';
import TopButtons from "@/Protonemedia/TopButtons";

export default {
  mixins: [InteractsWithQueryBuilder],
  components: {
    AppLayout,
    Table,
    TopButtons
  },
  props: {
      simulations: Object,
      buttons: Array
  }
}
</script>
