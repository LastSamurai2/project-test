<template>
  <app-layout>

    <template #header>
      <h1>
        {{ $t('flow.dataflow-title') }} / Edit Schedule
      </h1>
    </template>

    <template #buttons>
      <TopButtons :meta="buttons">
        <template #moreButtons>
          <div class="button-link">
            <inertia-link :class="'btn btn-primary'" :href="route()" @click.prevent="submitForm" >{{ $t('button.save') }}</inertia-link>
          </div>
          <div class="button-link">
            <inertia-link :class="'btn btn-primary'" :href="route()" @click.prevent="addToQueue" >{{ $t('flow.add-queue-button') }}</inertia-link>
          </div>
        </template>
      </TopButtons>
    </template>

    <b-row>
      <b-colxx xxs="12">
        <b-card class="mb-4" :title="'General'">
          <b-form ref="sampleForm">
            <b-form-group label-cols="2" horizontal :label="'Code'" :disabled=true>
                <b-form-input v-model="form.code"></b-form-input>
            </b-form-group>

            <b-form-group label-cols="2" horizontal :label="'Profile Class'" :disabled=true>
                <b-form-input v-model="form.profile_class"></b-form-input>
            </b-form-group>

            <b-form-group label-cols="2" horizontal :label="'Name'">
              <b-form-input v-model="form.name"></b-form-input>
              <jet-input-error :message="error('name')" class="mt-2" />
            </b-form-group>

            <component is="biz-select"
                       :value="schedule.status"
                       :label="'Status'"
                       :name="'status'"
                       :readonly="false"
                       :required="true"
                       :options="fields['status'].options"
                       :updateField="updateField">

            </component>

            <b-form-group label-cols="2" horizontal :label="'Schedule'">
                <b-form-input v-model="form.schedule"></b-form-input>
                <jet-input-error :message="error('schedule')" class="mt-2" />
            </b-form-group>

            <b-form-group label-cols="2" horizontal :label="'Priority'">
                <b-form-input v-model="form.priority"></b-form-input>
            </b-form-group>

          </b-form>
        </b-card>

      </b-colxx>
    </b-row>
    <b-row>
      <b-colxx xxs="12">
          <b-card class="mb-4" :title="'Parameters'">
              <b-form ref="sampleForm">
                  <div v-for="(config, code) in form.parameters_config">
                      <b-form-group label-cols="2" horizontal :label="config.label">
                          <b-form-input v-model="form.parameters[code]"></b-form-input>
                      </b-form-group>
                  </div>
              </b-form>
          </b-card>
      </b-colxx>
    </b-row>

  </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout';
import TopButtons from '@/Protonemedia/TopButtons';
import Select from '../../../components/form/select';
import JetInputError from '@/Jetstream/InputError';

export default {
  props: {
    schedule: Object,
    fields: Object,
    buttons: Array
  },
  components: {
    AppLayout,
    TopButtons,
    'biz-select': Select,
    JetInputError
  },
  data() {
    return {
      form: []
    };
  },
  mounted() {
    this.getData();
  },
  methods: {
    updateField(value, name) {
      this.form[name] = value
    },
    submitForm() {
        if (this.schedule.id) {
            this.form.post(this.route('dataflows.schedule.update', this.schedule.id), {})
        }
    },
    addToQueue() {
        if (this.schedule.id) {
            this.form.post(this.route('dataflows.schedule.addToQueue', this.schedule.id));
        }
    },
    getData() {
      this.form = this.$inertia.form(Object.assign({}, {_method: 'post'}, this.schedule));
    },
  }
};
</script>
