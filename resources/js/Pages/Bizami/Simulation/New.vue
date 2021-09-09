<template>
  <app-layout>

    <template #header>
      <h1>
        Bizami / {{ $t('menu.bizami-simulations') }} / {{ $t('common.new_f') }}
      </h1>
    </template>

    <div v-show="isLoading">Calculating, please wait {{progress}}% ...</div>

    <template #buttons>

      <TopButtons :meta="buttons">
        <template #moreButtons>
          <div class="button-link">
            <button :class="'btn btn-primary'" @click.prevent="submitForm" >{{ $t('bizami.button.create-simulation') }}</button>
          </div>
        </template>
      </TopButtons>
    </template>
      <b-row>
          <b-colxx xxs="12">
            <b-card class="mb-4" :title="$t('common.parameters')">
              <b-form ref="sampleForm">
                  <component is="biz-select"
                             :value="form.algorithm"
                             :label="$t('bizami.algorithm')"
                             :name="'algorithm'"
                             :readonly="false"
                             :required="true"
                             :options="fields['algorithm'].options"
                             :updateField="updateField">
                  </component>
                  <jet-input-error :message="apiError('algorithm', errors)" class="mt-2" />
                  <component is="biz-select"
                             :value="form.provider"
                             :label="$t('bizami.provider')"
                             :name="'provider'"
                             :readonly="false"
                             :required="true"
                             :options="fields['provider'].options"
                             :updateField="updateField">

                  </component>
                  <jet-input-error :message="apiError('provider', errors)" class="mt-2" />
                  <component is="biz-select"
                             :value="form.supplier"
                             :label="$t('bizami.warehouse')"
                             :name="'warehouse'"
                             :readonly="false"
                             :required="true"
                             :options="fields['warehouse'].options"
                             :updateField="updateField">
                  </component>
                  <jet-input-error :message="apiError('warehouse', errors)" class="mt-2" />
              </b-form>
            </b-card>
          </b-colxx>
        </b-row>

  </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout';
import TopButtons from '@/Protonemedia/TopButtons';
import Select from '../../../components/form/select'
import JetInputError from "@/Jetstream/InputError";

export default {
  props: {
    buttons: Array,
    fields: Object,
    isLoading: false,
    progress: String,
    errors: {
      type: Object,
      default() {
        return {}
      }
    }
  },
  components: {
    AppLayout,
    TopButtons,
    JetInputError,
    'biz-select': Select
  },
  data() {
      return {
          form: this.$inertia.form({
              _method: 'post',
              algorithm: '',
              warhouse: '',
              provider: '',
          }),
      };
  },
  methods: {
    updateField(value, name) {
        this.form[name] = value;
    },
    submitForm() {
        this.progress = '0';
        this.isLoading = true;
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute('content');
        this.processSimulation();
    },
    processSimulation(simulationId)
    {
        axios.post(route('bizami.simulation.new.create', {id: simulationId}),  this.form)
           .then((res) => {
                simulationId = res.data.id;
                this.progress = res.data.progress;
                if (!res.data.is_last_iteration) {
                    return this.processSimulation(simulationId);
                }
                this.$inertia.visit(route('bizami.simulation.view', {id: simulationId}));
           }).catch((err) => {
               this.errors = err.response.data.errors
               this.isLoading = false;
           });
      }
  }
};
</script>
