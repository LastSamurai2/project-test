<template>
  <app-layout>

    <template #header>
      <h1 v-if="algorithm.id">
        Bizami / {{ $t('bizami.algorithm-edit')}}
      </h1>
      <h1 v-if="!algorithm.id">
        Bizami / Nowy algorytm
      </h1>


    </template>

    <template #buttons>
      <TopButtons :meta="buttons">
        <template #moreButtons>
          <div class="button-link">
            <button :class="'btn btn-primary'" @click.prevent="submitForm" >{{ $t('button.save') }}</button>
          </div>
          <div class="button-link" v-if="algorithm.id">
            <button :class="'btn btn-danger'" @click.prevent="deleteEntry" >{{ $t('button.delete') }}</button>
          </div>
        </template>
      </TopButtons>

    </template>

      <b-row>
          <b-colxx xxs="12">
              <b-card class="mb-4" :title="$t('section.general')">
                  <b-form ref="sampleForm">
                      <b-form-group label-cols="2" horizontal :label="$t('forms.name')">
                          <b-form-input v-model="form.name"></b-form-input>
                          <jet-input-error :message="error('name')" class="mt-2" />
                      </b-form-group>
                      <b-form-group label-cols="2" horizontal :label="$t('forms.description')">
                          <b-form-input v-model="form.description"></b-form-input>
                      </b-form-group>
                      <component is="biz-select"
                                 :value="algorithm.type"
                                 :label="$t('forms.type')"
                                 :name="'type'"
                                 :readonly="false"
                                 :required="true"
                                 :options="fields['type'].options"
                                 :updateField="updateField">
                      </component>
                  </b-form>
              </b-card>
          </b-colxx>
      </b-row>

      <b-row>
          <b-colxx xxs="12">
              <b-card class="mb-4" :title="'Cykle'">
                  <b-form ref="sampleForm">
                      <b-form-group label-cols="2" horizontal :label="'Cykl MIN'">
                          <b-form-input v-model="form.min_cycle"></b-form-input>
                      </b-form-group>
                      <b-form-group label-cols="2" horizontal :label="'Cykl (A)'">
                          <b-form-input v-model="form.cycle_a"></b-form-input>
                      </b-form-group>
                      <b-form-group label-cols="2" horizontal :label="'Cykl (B)'">
                          <b-form-input v-model="form.cycle_b"></b-form-input>
                      </b-form-group>
                      <b-form-group label-cols="2" horizontal :label="'Cykl (C)'">
                          <b-form-input v-model="form.cycle_c"></b-form-input>
                      </b-form-group>
                      <b-form-group label-cols="2" horizontal :label="'Cykl (D)'">
                          <b-form-input v-model="form.cycle_d"></b-form-input>
                      </b-form-group>
                      <b-form-group label-cols="2" horizontal :label="'R2 (A/B)'">
                          <b-form-input v-model="form.r2_a_b"></b-form-input>
                      </b-form-group>
                      <b-form-group label-cols="2" horizontal :label="'R2 (B/C)'">
                          <b-form-input v-model="form.r2_b_c"></b-form-input>
                      </b-form-group>
                      <b-form-group label-cols="2" horizontal :label="'R2 (C/D)'">
                          <b-form-input v-model="form.r2_c_d"></b-form-input>
                      </b-form-group>
                  </b-form>
              </b-card>
          </b-colxx>
      </b-row>


      <b-row>
          <b-colxx xxs="12">
            <b-card class="mb-4" :title="$t('section.parameters')">
              <b-form ref="sampleForm">
                  <b-form-group label-cols="2" horizontal :label="'Mediana Multiplier For Picks'">
                      <b-form-input v-model="form.mediana_multiplier_for_picks"></b-form-input>
                  </b-form-group>
                  <b-form-group label-cols="2" horizontal :label="'Seasonability Multiplier'">
                      <b-form-input v-model="form.seasonability_multiplier"></b-form-input>
                  </b-form-group>
                  <b-form-group label-cols="2" horizontal :label="'ZMM Mediana Multiplier'">
                      <b-form-input v-model="form.mediana_multiplier_for_zmm"></b-form-input>
                  </b-form-group>
                  <b-form-group label-cols="2" horizontal :label="'Picks Threshold'">
                      <b-form-input v-model="form.picks_threshold"></b-form-input>
                  </b-form-group>
              </b-form>
            </b-card>
          </b-colxx>
        </b-row>

      <b-row>
          <b-colxx xxs="12">
              <b-card class="mb-4" :title="$t('section.additional-condition-parameters')">
                  <b-form ref="sampleForm">
                      <b-form-group label-cols="2" horizontal :label="'WD1 Additional Minimum Condtition %'">
                          <b-form-input v-model="form.wd1_additional_minimum_condition"></b-form-input>
                      </b-form-group>
                      <b-form-group label-cols="2" horizontal :label="'WD2 Cycle Extended Value'">
                          <b-form-input v-model="form.wd2_cycle_extended"></b-form-input>
                      </b-form-group>
                      <b-form-group label-cols="2" horizontal :label="'WD2 Reaction Value Threshold'">
                          <b-form-input v-model="form.wd2_reaction_value_threshold"></b-form-input>
                      </b-form-group>
                  </b-form>
              </b-card>
          </b-colxx>
      </b-row>

  </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout';
import TopButtons from '@/Protonemedia/TopButtons';
import JetInputError from '@/Jetstream/InputError';
import Select from "@/components/form/select";

export default {
  props: {
    algorithm: Object,
    buttons: Array,
    fields: Object,
  },
  components: {
    AppLayout,
    TopButtons,
    JetInputError,
    'biz-select': Select
  },

  data() {
    return {
      form: []
    };
  },
  methods: {
    getData() {
      this.form = this.$inertia.form(Object.assign({}, {_method: 'post'}, this.algorithm));
    },
    submitForm() {
      if (this.algorithm.id) {
        this.form.post(this.route('bizami.algorithm.update', this.algorithm.id), {})
      } else {
        this.form.post(this.route('bizami.algorithm.create'), {})
      }
    },
    deleteEntry() {
      if (confirm('Are you sure you want to delete this algorithm?')) {
        this.$inertia.get(this.route('bizami.algorithm.destroy', this.algorithm.id))
      }
    },
    updateField(value, name) {
       this.form[name] = value
    }
  },
  mounted() {
    this.getData();
  }
};
</script>
