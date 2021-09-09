<template>
    <app-layout>
        <template #header>
            <h1>
                Dashboard
            </h1>
        </template>

        <b-row>
            <b-colxx xl="6" lg="12">
                <b-row>
                    <b-colxx md="12" class="mb-4">
                        <sales-chart-card></sales-chart-card>
                    </b-colxx>
                </b-row>
              <b-row>
                <b-colxx md="12" class="mb-4">
                  <flow-errors :title="$t('dashboards.last-7-errors')"></flow-errors>
                </b-colxx>
              </b-row>
            </b-colxx>
          <b-colxx xl="6" lg="12" class="mb-4">
            <last-imports :title="$t('dashboards.last-imports')"></last-imports>
          </b-colxx>
        </b-row>


    </app-layout>
</template>

<script>
    import AppLayout from '@/Layouts/AppLayout'
    import LastImports from "@/Piaf/containers/dashboards/LastImports";
    import FlowErrors from "@/Piaf/containers/dashboards/FlowErrors";
    import RecentOrders from "@/Piaf/containers/dashboards/RecentOrders";
    import SalesChartCard from "@/Piaf/containers/dashboards/SalesChartCard";

    export default {
        components: {
            AppLayout,
            LastImports,
            FlowErrors,
            "recent-orders": RecentOrders,
            "sales-chart-card": SalesChartCard
        }
    }
</script>
