<template>
  <b-card :title="title">
    <vuetable
      ref="vuetable"
      api-url="/api/LastImportsWidget"
      data-path="data"
      :fields="imports.fields"
      pagination-path
      @vuetable:pagination-data="onPaginationData"
    ></vuetable>
    <vuetable-pagination-bootstrap
      ref="pagination"
      @vuetable-pagination:change-page="onChangePage"
    />
  </b-card>
</template>
<script>
import Vuetable from "vuetable-2/src/components/Vuetable";
import VuetablePaginationBootstrap from "../../components/Common/VuetablePaginationBootstrap";
import { apiUrl } from "../../constants/config";

export default {
  props: ["title"],
  components: {
    vuetable: Vuetable,
    "vuetable-pagination-bootstrap": VuetablePaginationBootstrap
  },
  data() {
    return {
      imports: {
        fields: [
          {
            name: "result",
            sortField: "result",
            title: this.$t('common.result'),
            titleClass: "",
            dataClass: "list-item-heading"
          },
          {
            name: "executed_at",
            sortField: "executed_at",
            title: this.$t('flow.executed_at'),
            titleClass: "",
            dataClass: "text-muted"
          },
          {
            name: "finished_at",
            sortField: "finished_at",
            title: this.$t('flow.finished_at'),
            titleClass: "",
            dataClass: "text-muted"
          },
          {
            name: "status",
            sortField: "status",
            title: this.$t('common.status'),
            titleClass: "",
            dataClass: "text-muted"
          }
        ]
      }
    };
  },
  methods: {
    onPaginationData(paginationData) {
      this.$refs.pagination.setPaginationData(paginationData);
    },
    onChangePage(page) {
      this.$refs.vuetable.changePage(page);
    }
  }
};
</script>
