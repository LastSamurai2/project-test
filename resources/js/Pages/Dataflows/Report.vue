<template>
  <app-layout>
    <template #header>
      <h1>
        {{ $t('flow.dataflow-title') }}
      </h1>
    </template>

    <Table
        :filters="queryBuilderProps.filters"
        :search="queryBuilderProps.search"
        :columns="queryBuilderProps.columns"
        :on-update="setQueryBuilder"
        :meta="reports"
    >

      <template #topaction>
        <TopButtons :meta="buttons"></TopButtons>
      </template>

      <template #head>
        <tr>
          <th class="vuetable-th-title">Id</th>
            <th v-show="showColumn('schedule_id')">{{ $t('flow.title') }}</th>
            <th v-show="showColumn('type')">{{ $t('common.type') }}</th>
            <th v-show="showColumn('executed_at')" class="sortable" @click.prevent="sortBy('executed_at')">{{ $t('flow.executed_at') }}</th>
            <th v-show="showColumn('finished_at')" class="sortable" @click.prevent="sortBy('finished_at')">{{ $t('flow.finished_at') }}</th>
            <th v-show="showColumn('status')">{{ $t('common.status') }}</th>
            <th v-show="showColumn('result')" class="">{{ $t('common.result') }}</th>
            <th>{{ $t('table.actions') }}</th>
        </tr>
      </template>
<!--
      'report_id' => 'ID',
      'schedule_id' => 'Schedule',
      'type' => "Type",
      'executed_at' => "Executed At",
      'finished_at' => "Finished At",
      'result' => "Result",-->

      <template #body>
        <tr v-for="report in reports.data" :key="report.id">
            <td class="">{{ report.id }}</td>
            <td v-show="showColumn('schedule_id')">{{ report.schedule_id }}</td>
            <td v-show="showColumn('type')">{{ report.type }}</td>
            <td v-show="showColumn('executed_at')">{{ report.executed_at }}</td>
            <td v-show="showColumn('finished_at')">{{ report.finished_at }}</td>
            <td v-show="showColumn('status')">{{ report.status }}</td>
            <td v-show="showColumn('result')" >{{ report.result }}</td>
            <td><inertia-link :href="route('dataflows.report.view', report.id)">{{ $t('table.view')}}</inertia-link></td>
        </tr>
      </template>
    </Table>
  </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout'
import { InteractsWithQueryBuilder } from '@protonemedia/inertiajs-tables-laravel-query-builder';
import Table from '@/Protonemedia/Table'
import TopButtons from '@/Protonemedia/TopButtons'

export default {
  mixins: [InteractsWithQueryBuilder],
  components: {
    AppLayout,
    Table,
    TopButtons
  },
  props: {
    reports: Object,
    buttons: Array
  },
  methods: {
    delete() {

    }
  }
}
</script>
