<template>
    <app-layout>
        <template #header>
            <h1>
                {{ $t('user.users-title') }}
            </h1>
        </template>

        <Table
            :filters="queryBuilderProps.filters"
            :search="queryBuilderProps.search"
            :columns="queryBuilderProps.columns"
            :on-update="setQueryBuilder"
            :meta="users"
        >

            <template #topaction>
                <TopButtons :meta="buttons"></TopButtons>
            </template>

            <template #head>
                <tr>
                    <th class="">Id</th>
                    <th class="">{{ $t('user.email') }}</th>
                    <th class="">{{ $t('user.username') }}</th>
                    <th class="">{{ $t('user.created_at') }}</th>
                    <th>{{ $t('table.actions')}}</th>
                </tr>
            </template>

            <template #body>
                <tr v-for="user in users.data" :key="user.id">
                    <td class="">{{ user.id }}</td>
                    <td class="">{{ user.email }}</td>
                    <td class="">{{ user.name }}</td>
                    <td class="">{{ user.created_at }}</td>
                    <td><inertia-link :href="route('user.edit', user.id)">{{ $t('table.edit') }}</inertia-link></td>
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
        users: Object,
        buttons: Array
    },
    methods: {
        delete() {

        }
    }
}
</script>
