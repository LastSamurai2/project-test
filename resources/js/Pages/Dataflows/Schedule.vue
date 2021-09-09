<template>
  <app-layout>
    <template #header>
      <h1>
        {{ $t('flow.dataflow-title') }} / {{ $t('flow.title-s')}}
      </h1>
    </template>

    <Table
        :filters="queryBuilderProps.filters"
        :search="queryBuilderProps.search"
        :columns="queryBuilderProps.columns"
        :on-update="setQueryBuilder"
        :meta="schedules"
    >
        <template #topaction>
            <TopButtons :meta="buttons"></TopButtons>
        </template>

          <template #head>
            <tr>
                <th class="vuetable-th-title">Id</th>
                <th class="sortable" v-show="showColumn('code')" @click.prevent="sortBy('code')">{{ $t('common.code') }}</th>
                <th class="sortable" v-show="showColumn('name')" @click.prevent="sortBy('name')">{{ $t('common.name') }}</th>
                <th class="">{{ $t('flow.is_forced') }}</th>
                <th class="" v-show="showColumn('schedule')">{{ $t('flow.schedule') }}</th>
                <th class="">{{ $t('common.status') }}</th>
                <th class="sortable" @click.prevent="sortBy('name')">{{ $t('common.priority') }}</th>
                <th>{{ $t('table.actions') }}</th>
            </tr>
          </template>

          <template #body>
            <tr v-for="schedule in schedules.data" :key="schedule.id">
                <td class="">{{ schedule.id }}</td>
                <td class="" v-show="showColumn('code')">{{ schedule.code }}</td>
                <td class="" v-show="showColumn('name')">{{ schedule.name }}</td>
                <td class="">{{ getLabelOrValue(schedule.is_forced, 'is_forced') }}</td>
                <td class="" v-show="showColumn('schedule')">{{ getLabelOrValue(schedule.schedule, 'schedule') }}</td>
                <td class="">{{ getLabelOrValue(schedule.status, 'status') }}</td>
                <td class="">{{ schedule.priority }}</td>
                <td><inertia-link :href="route('dataflows.schedule.edit', schedule.id)">{{ $t('table.edit') }}</inertia-link></td>
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
      schedules: Object,
      buttons: Array,
      columns: Object
  },
  methods: {
    getLabelOrValue(value, name) {
      // if in columns definition
      if(this.columns[name] && this.columns[name].options && this.columns[name].options.length > 0) {
        const option = this.columns[name].options.find(opt => opt.code === value)
        return option.label;
      } else {
        return value;
      }
    }
  }
}
</script>
