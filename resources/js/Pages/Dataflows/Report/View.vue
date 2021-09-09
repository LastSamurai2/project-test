<template>
  <app-layout>
      <template #header>
         <h1>
           {{ $t('flow.dataflow-title') }} / View Report #{{ report.id }}
         </h1>
      </template>

      <Table>

          <template #topaction>
              <TopButtons :meta="buttons"></TopButtons>
          </template>

          <template #body>
              <tr>
                  <td>{{ $t('flow.schedule_name') }}</td>
                  <td>{{report.schedule_name}}</td>
              </tr>
              <tr>
                  <td>{{ $t('common.type') }}</td>
                  <td>{{report.type}}</td>
              </tr>
              <tr>
                  <td>{{ $t('common.status') }}</td>
                  <td>{{report.status_label}}</td>
              </tr>
              <tr>
                  <td>{{ $t('flow.executed_at') }}</td>
                  <td>{{report.executed_at}}</td>
              </tr>
              <tr>
                  <td>{{ $t('flow.finished_at') }}</td>
                  <td>{{report.finished_at}}</td>
              </tr>
              <tr>
                  <td>{{ $t('common.result') }}</td>
                  <td>{{report.result}}</td>
              </tr>
          </template>
      </Table>

      <Table
          :filters="queryBuilderProps.filters"
          :search="queryBuilderProps.search"
          :columns="queryBuilderProps.columns"
          :on-update="setQueryBuilder"
          :meta="logs"
      >

          <template #head>
              <tr>
                  <th class="vuetable-th-title">Id</th>
                  <th class="sortable" @click.prevent="sortBy('created_at')">{{ $t('flow.log_time') }}</th>
                  <th class="">{{ $t('flow.level') }}</th>
                  <th class="">{{ $t('flow.message') }}</th>
              </tr>
          </template>

          <template #body>
              <tr v-for="log in logs.data" :key="log.id">
                  <td class="">{{ log.id }}</td>
                  <td class="">{{ log.created_at }}</td>
                  <td class="">{{ log.level }}</td>
                  <td class="">{{ log.message }}</td>
              </tr>
          </template>
      </Table>

  </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout'
import { InteractsWithQueryBuilder } from '@protonemedia/inertiajs-tables-laravel-query-builder';
import Table from '@/Protonemedia/Table'
import TopButtons from "@/Protonemedia/TopButtons";

export default {
    mixins: [InteractsWithQueryBuilder],
        components: {
          AppLayout,
          Table,
          TopButtons
        },
        props: {
            report: Object,
            logs: Object,
            buttons: Array
        },
    }
</script>
