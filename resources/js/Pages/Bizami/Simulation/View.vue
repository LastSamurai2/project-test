<template>
  <app-layout>
    <loading :active.sync="isLoading"
             :is-full-page="true"/>

    <template #header>
      <div class="d-flex justify-content-between mb-2">
        <h2 class="align-middle m-0">
          Bizami Simulation
        </h2>
      </div>
    </template>

    <template #buttons>
      <TopButtons :meta="buttons">
        <template #moreButtons>
          <div class="button-link">
              <inertia-link :class="'btn btn-secondary'" :href="route('bizami.simulations')">{{ $t('button.back') }}</inertia-link>
          </div>
          <div class="button-link">
            <jet-responsive-nav-link :href="route('bizami.simulation.view', { id: simulation.id, view: 'analytics'})" v-if="viewType !== 'analytics'">
              {{ $t('bizami.button.analytics-view') }}
            </jet-responsive-nav-link>
            <jet-responsive-nav-link :href="route('bizami.simulation.view', { id: simulation.id})" v-if="viewType === 'analytics'">
              {{ $t('bizami.button.base-view') }}
            </jet-responsive-nav-link>
          </div>
          <div class="button-link">
            <button :class="'btn btn-danger'" @click.prevent="submitData" >{{ $t('button.submit') }}</button>
          </div>
        </template>
      </TopButtons>
    </template>

    <hot-table :settings="hotSettings"
               ref="hotTableComponent"
               style="z-index: 1"
               licenseKey="non-commercial-and-evaluation">
    </hot-table>
  </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout'
import JetNavLink from '@/Jetstream/NavLink';
import JetResponsiveNavLink from '@/Jetstream/ResponsiveNavLink'
import {HotTable} from '@handsontable/vue';
import Handsontable from 'handsontable';
import Loading from 'vue-loading-overlay';
import TopButtons from '@/Protonemedia/TopButtons';
import 'vue-loading-overlay/dist/vue-loading.css';

export default {
  components: {
    HotTable,
    AppLayout,
    JetNavLink,
    JetResponsiveNavLink,
    Loading,
    TopButtons
  },
  props: {
     simulation: Object,
     viewType: String,
  },
  data() {
    return {
      totals: {},
      buttons: [],
      columnsDef: [],
      isLoading: true,
      hotSettings: {
        rowHeaders: true,
        columnSorting: true,
        stretchH: 'all',
        colHeaders: true,
      }
    }
  },
  methods: {
    submitData() {
      const rawHotData = this.$refs.hotTableComponent.hotInstance.getData();
      const mapedData = rawHotData.map(row => {
        let resultObj = {};
        row.forEach((column, index) => {
          resultObj[this.columnsDef[index].code] = column
        });
        return resultObj;
      })
      this.$inertia.post(this.route('bizami.simulation.update', { id: this.simulation.id, view: this.viewType }), mapedData);
    },
    renderTotals(vue) {
      const htInstance = vue.$refs.hotTableComponent.hotInstance
      const colsLength = Object.keys(this.columnsDef).length;
      const footer = document.getElementById('htTotals')
            footer.innerHTML = ""; // trzeba wyczyścic zawartośc przed kolejnym updatem

      let arrayPlaceholder = Array(colsLength).join(".").split(".");// create an empty array
      let last = 1;

      this.columnsDef.forEach((col, index) => {
        if (col.countable === true) {
          let td = document.createElement("td");
          td.innerHTML = htInstance.getDataAtProp(col.code).reduce((accu, curr) => (accu * 1) + (curr * 1))
          td.style.backgroundColor = '#bbbbbb';
          td.style.color = '#ffffff';
          td.style.fontWeight = 'bold';
          arrayPlaceholder.splice(index, 1, td);
        }
      })

      arrayPlaceholder.forEach((td, index) => {
        if (td !== "") {
          const span = document.createElement('td');
          span.colSpan = last;
          span.style.backgroundColor = '#bbbbbb';

          last > 0 && footer.appendChild(span);
          footer.appendChild(td);

          last = 0;
        } else {
          last += 1
          if (colsLength - 1 === index) {
            const span = document.createElement('td');
            span.colSpan = last;
            span.style.backgroundColor = '#bbbbbb';
            footer.appendChild(span);
          }
        }
      })
    },
    getData() {
      axios.get(route('bizami.simulation.tableData',  { id: this.simulation.id, view: this.viewType }))
          .then((res) => {
            // ------
            // after data recive
            // ------
            this.columnsDef = res.data.columns; // responseMock.columns //
            const sourceData = res.data.data; // responseMock.data

            function columnsDefinition(columns) {
              return columns.map(col => {
                return {
                  data: col.code,
                  readOnly: !col.editable ? true : false
                }
              });
            }

            const onlyColumnsCode = this.columnsDef.map(col => col.code);

            this.$refs.hotTableComponent.hotInstance.updateSettings({
              columns: columnsDefinition(this.columnsDef),
              colHeaders: this.columnsDef.map(col => col.label),
              cells: (row, col, prop) => {
                var cellProperties = {};
                if (onlyColumnsCode.indexOf(prop) !== -1) {
                  cellProperties.renderer = function (instance, td, row, col, prop, value, cellProperties) {
                    td.style.backgroundColor = '#fbfbfb';                                   // base color
                    // row colors before
                    if (sourceData.length > 0 && sourceData[row] && sourceData[row].color !== "") {
                      td.style.backgroundColor = sourceData[row].color;                     // base color
                    }
                    // columns colors after
                    // TODO can optimize ?
                    const colColor = this.columnsDef.find(col => col.code === prop).color;
                    if (colColor) {
                      td.style.backgroundColor = colColor;                     // base color
                    }
                    td.innerHTML = value
                  }.bind(this);
                }
                return cellProperties;
              }
            })

            Handsontable.hooks.once('afterLoadData', () => {
              this.renderTotals(this)
            });

            // to jedyny spsób na aktualizacje danych bez psucia labelek headera
            this.$refs.hotTableComponent.hotInstance.loadData(sourceData);
            this.isLoading = false;

     //},2000)
          })
          .catch((err) => {
            console.log(err)
          });
    },
    onFieldChange() {
      Handsontable.hooks.add('afterChange', (changes) => {
            if(changes) {
              this.renderTotals(this)
              //console.log('Dane zmienionego pola, możliwe do wysyłki Ajaxem: ', changes)
            }
          }
      );
    }
  },
  created() {
    Handsontable.hooks.once('afterInit', function () {
      // Create placeholder for Totals (tfoot)
      var table = document.querySelector('.ht_master .htCore');
      var tf = document.createElement("tfoot");
      var tr = document.createElement("tr");
      tr.id = 'htTotals';
      tf.appendChild(tr);
      table.appendChild(tf);
    });

    this.getData();
  },
  mounted() {
    this.onFieldChange()
  }
}
</script>
