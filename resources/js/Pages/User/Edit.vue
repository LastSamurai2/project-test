<template>
  <app-layout>

    <template #header>
      <h1 v-if="editeduser.id">
        {{ $t('user.edit-user-title') }}
      </h1>
        <h1 v-if="!editeduser.id">
          {{ $t('user.new-user-title') }}
        </h1>
    </template>

    <template #buttons>
      <TopButtons :meta="buttons">
        <template #moreButtons>
            <div class="button-link">
              <button :class="'btn btn-primary'" @click.prevent="submitForm" >{{ $t('button.save') }}</button>
            </div>
            <div class="button-link" v-if="editeduser.id">
                <button :class="'btn btn-danger'" @click.prevent="deleteUser" >{{ $t('button.delete') }}</button>
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
                      <b-form-group label-cols="2" horizontal :label="$t('forms.email')">
                          <b-form-input type="email" v-model="form.email"></b-form-input>
                        <jet-input-error :message="error('email')" class="mt-2" />
                      </b-form-group>
                      <b-form-group label-cols="2" horizontal :label="$t('forms.password')" v-if="!editeduser.id">
                          <b-form-input type="password" v-model="form.password"></b-form-input>
                        <jet-input-error :message="error('password')" class="mt-2" />
                      </b-form-group>
                      <b-form-group label-cols="2" horizontal :label="$t('forms.password-confirmation')" v-if="!editeduser.id">
                          <b-form-input type="password" v-model="form.password_confirmation"></b-form-input>
                        <jet-input-error :message="error('password_confirmation')" class="mt-2" />
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

export default {
  props: {
    editeduser: Object,
    buttons: Array
  },
  components: {
    AppLayout,
    TopButtons,
    JetInputError
  },
  data() {
    return {
        form: this.$inertia.form({
            _method: 'post',
            email: this.editeduser.email,
            name: this.editeduser.name,
            password: this.editeduser.password,
            password_confirmation: this.editeduser.password_confirmation,
        }),
    };
  },
  methods: {
    submitForm() {
        if (this.editeduser.id) {
            this.form.post(this.route('user.update', this.editeduser.id), {})
        } else {
            this.form.post(this.route('user.create'), {})
        }
    },
    deleteUser() {
        if (confirm('Are you sure you want to delete this user?')) {
            this.$inertia.get(this.route('user.destroy', this.editeduser.id))
        }
    }
  }
};
</script>
