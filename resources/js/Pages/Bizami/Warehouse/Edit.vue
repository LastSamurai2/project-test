<template>
  <app-layout>

    <template #header>
      <h1>
        Bizami / {{ $t('bizami.warehouse') }} / {{ warehouse.warehouse_id }}
      </h1>

    </template>

    <template #buttons>
      <TopButtons :meta="buttons">
        <template #moreButtons>
          <div class="button-link">
            <button :class="'btn btn-primary'" @click.prevent="submitForm" >{{ $t('button.save') }}</button>
          </div>
        </template>
      </TopButtons>

    </template>

      <b-row>
          <b-colxx xxs="12">
              <b-card class="mb-4" :title="$t('section.general')">
                  <b-form ref="sampleForm">
                      <b-form-group label-cols="2" horizontal :label="$t('forms.warehouse_id')">
                          <b-form-input v-model="form.warehouse_id" :readonly="true"></b-form-input>
                      </b-form-group>
                      <component is="biz-select"
                                 :value="warehouse.type"
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
  </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout';
import TopButtons from '@/Protonemedia/TopButtons';
import JetInputError from '@/Jetstream/InputError';
import Select from '../../../components/form/select';

export default {
  props: {
    warehouse: Object,
    buttons: Array,
    fields: Object,
  },
  components: {
    AppLayout,
    TopButtons,
    JetInputError,
    'biz-select': Select,
  },

  data() {
    return {
      form: []
    };
  },
  methods: {
      updateField(value, name) {
          this.form[name] = value
      },
      getData() {
          this.form = this.$inertia.form(Object.assign({}, {_method: 'post'}, this.warehouse));
      },
      submitForm() {
          this.form.post(this.route('bizami.warehouse.update', this.warehouse.id), {})
      }
  },
  mounted() {
      this.getData();
  }
};
</script>
