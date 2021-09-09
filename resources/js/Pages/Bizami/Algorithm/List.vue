<template>
    <app-layout>
        <template #header>
            <h1>
                Bizami / Algorithms
            </h1>
        </template>

        <Table
            :filters="queryBuilderProps.filters"
            :search="queryBuilderProps.search"
            :columns="queryBuilderProps.columns"
            :on-update="setQueryBuilder"
            :meta="algorithms"
        >
            <template #topaction>
                <TopButtons :meta="buttons"></TopButtons>
            </template>

            <template #head>
                <tr>
                    <th class="sortable" @click.prevent="sortBy('name')">{{ $t('forms.name') }}</th>
                    <th class="sortable" @click.prevent="sortBy('description')">{{ $t('forms.description') }}</th>
                    <th class="sortable" @click.prevent="sortBy('type')">{{ $t('forms.type') }}</th>
                    <th class="sortable" @click.prevent="sortBy('min_cycle')">Cykl Min</th>
                    <th class="sortable" @click.prevent="sortBy('cycle_a')">Cykl (A)</th>
                    <th class="sortable" @click.prevent="sortBy('cycle_b')">Cykl (B)</th>
                    <th class="sortable" @click.prevent="sortBy('cycle_c')">Cykl (C)</th>
                    <th class="sortable" @click.prevent="sortBy('cycle_d')">Cykl (D)</th>
                    <th class="sortable" @click.prevent="sortBy('r2_a_b')">R2 (A/B)</th>
                    <th class="sortable" @click.prevent="sortBy('r2_b_c')">R2 (B/C)</th>
                    <th class="sortable" @click.prevent="sortBy('r2_c_d')">R2 (C/D)</th>
                    <th class="sortable" @click.prevent="sortBy('mediana_multiplier_for_picks')">Mediana Multiplier For Picks</th>
                    <th class="sortable" @click.prevent="sortBy('mediana_multiplier_for_zmm')">ZMM Mediana Multiplier</th>
                    <th class="sortable" @click.prevent="sortBy('seasonability_multiplier')">Seasonability Multplier</th>
                    <th class="sortable" @click.prevent="sortBy('picks_threshold')">Picks Threshold</th>
                    <th class="sortable" @click.prevent="sortBy('wd1_additional_minimum_condition')">WD1 Additional Minimum Condtition %</th>
                    <th class="sortable" @click.prevent="sortBy('wd2_cycle_extended')">WD2 Cycle Extended Value</th>
                    <th class="sortable" @click.prevent="sortBy('wd2_reaction_value_threshold')">WD2 Reaction Value Threshold</th>
                    <th>{{$t('common.columns.actions')}}</th>
                </tr>
            </template>

            <template #body>
                <tr v-for="algorithm in algorithms.data" :key="algorithm.id">
                    <td class="">{{ algorithm.name }}</td>
                    <td class="">{{ algorithm.description }}</td>
                    <td class="">{{ getLabelOrValue(algorithm.type, 'type') }}</td>
                    <td class="" @click.prevent="(event) => {editInPlace(event, algorithm.id, 'min_cycle')}">{{ algorithm.min_cycle }}</td>
                    <td class="" @click.prevent="(event) => {editInPlace(event, algorithm.id, 'cycle_a')}">{{ algorithm.cycle_a }}</td>
                    <td class="" @click.prevent="(event) => {editInPlace(event, algorithm.id, 'cycle_b')}">{{ algorithm.cycle_b }}</td>
                    <td class="" @click.prevent="(event) => {editInPlace(event, algorithm.id, 'cycle_c')}">{{ algorithm.cycle_c }}</td>
                    <td class="" @click.prevent="(event) => {editInPlace(event, algorithm.id, 'cycle_d')}">{{ algorithm.cycle_d }}</td>
                    <td class="" @click.prevent="(event) => {editInPlace(event, algorithm.id, 'r2_a_b')}">{{ algorithm.r2_a_b }}</td>
                    <td class="" @click.prevent="(event) => {editInPlace(event, algorithm.id, 'r2_b_c')}">{{ algorithm.r2_b_c }}</td>
                    <td class="" @click.prevent="(event) => {editInPlace(event, algorithm.id, 'r2_c_d')}">{{ algorithm.r2_c_d }}</td>
                    <td class="" @click.prevent="(event) => {editInPlace(event, algorithm.id, 'mediana_multiplier_for_picks')}">{{ algorithm.mediana_multiplier_for_picks }}</td>
                    <td class="" @click.prevent="(event) => {editInPlace(event, algorithm.id, 'mediana_multiplier_for_zmm')}">{{ algorithm.mediana_multiplier_for_zmm }}</td>
                    <td class="" @click.prevent="(event) => {editInPlace(event, algorithm.id, 'seasonability_multiplier')}">{{ algorithm.seasonability_multiplier }}</td>
                    <td class="" @click.prevent="(event) => {editInPlace(event, algorithm.id, 'picks_threshold')}">{{ algorithm.picks_threshold }}</td>
                    <td class="" @click.prevent="(event) => {editInPlace(event, algorithm.id, 'wd1_additional_minimum_condition')}">{{ algorithm.wd1_additional_minimum_condition }}</td>
                    <td class="" @click.prevent="(event) => {editInPlace(event, algorithm.id, 'wd2_cycle_extended')}">{{ algorithm.wd2_cycle_extended }}</td>
                    <td class="" @click.prevent="(event) => {editInPlace(event, algorithm.id, 'wd2_reaction_value_threshold')}">{{ algorithm.wd2_reaction_value_threshold }}</td>
                    <td><inertia-link :href="route('bizami.algorithm.edit', algorithm.id)">{{$t('button.edit')}}</inertia-link></td>
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
import Swal from "sweetalert2";

export default {
    mixins: [InteractsWithQueryBuilder],
    components: {
        AppLayout,
        Table,
        TopButtons
    },
    props: {
        algorithms: Object,
        buttons: Array,
        columns: Object
    },
    methods: {
        editInPlace(event, id, column) {

            const td = event.target;
            if(td.nodeName !== "TD") {
                return;
            }

            const _self = this;
            const input = document.createElement('input');
            const initValue = event.target.innerHTML;

            const handler = function (event) {

                if(event.target.value !== initValue) {
                    const thisRow = _self.algorithms.data.find(row => row.id === id)
                    thisRow[column] = event.target.value;
                    axios.put('/api/algorithmInline/' + id,  thisRow)
                        .then((res) => {
                        })
                        .catch((err) => {
                            Swal.fire({
                                icon: 'error',
                                title: 'There was an error during saving the algorithm.'
                            })
                        });
                }
                input.remove();
            };

            input.value = td.innerHTML;
            input.style.position = 'absolute';
            input.style.top = 0;
            input.style.left = 0;
            input.style.width = '100%';
            input.style.height = '100%';
            input.style.zIndex = 10;
            input.style.paddingTop = '1rem';
            input.style.paddingBottom = '1rem';
            input.style.paddingLeft = '2px';
            input.style.paddingRight = '2px';
            input.style.textAlign = 'center';

            td.style.position = 'relative';

            td.appendChild(input);
            input.focus();
            input.select();

            input.addEventListener('blur', handler)
            input.addEventListener('keyup', event => {
                event.preventDefault();
                if (event.keyCode === 13 || event.keyCode === 9) {
                    jQuery(input).trigger('blur')
                }

                else if( event.keyCode === 27 ) {
                    input.value = initValue;
                    jQuery(input).trigger('blur')
                }
            })
        },
        getLabelOrValue(value, name) {
            if (this.columns[name] && this.columns[name].options && this.columns[name].options.length > 0) {
                const option = this.columns[name].options.find(opt => opt.code === value);
                if (option) {
                    return option.label;
                }
            }
            return value;
        }
    },
}
</script>
