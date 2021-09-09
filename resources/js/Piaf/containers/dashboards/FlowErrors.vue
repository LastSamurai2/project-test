<template>
  <b-card :title="title">
    <vuetable
      ref="vuetable"
      api-url="/api/FlowErrorsWidget"
      data-path="data"
      :fields="errors.fields"
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

export default {
  props: ["title"],
  components: {
    vuetable: Vuetable,
    "vuetable-pagination-bootstrap": VuetablePaginationBootstrap
  },
  data() {
    return {
      errors: {
        fields: [
          {
            name: "report_id",
            sortField: "report_id",
            title: this.$t('common.name'),
            titleClass: "",
            dataClass: "list-item-heading"
          },
          {
            name: "message",
            sortField: "message",
            title: this.$t('flow.message'),
            titleClass: "",
            dataClass: "text-muted"
          },
          {
            name: "level",
            sortField: "level",
            title: this.$t('flow.level'),
            titleClass: "",
            dataClass: "text-muted"
          },
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
