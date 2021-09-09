<template>
    <jet-form-section @submitted="updatePassword">
        <template #title>
            {{ $t('user.update_password')}}
        </template>

        <template #description>
            {{ $t('user.update_password-message') }}
        </template>

        <template #form>
          <b-form-group label-cols="2" horizontal :label="$t('forms.password-current')">
            <b-form-input
                v-model="form.current_password"
                ref="current_password"
                autocomplete="current-password"
                type="password"
                :placeholder="$t('forms.password')"
            />
            <jet-input-error :message="error('current_password', 'updatePassword')" class="mt-2" />
          </b-form-group>

          <b-form-group label-cols="2" horizontal :label="$t('forms.password-new')">
            <b-form-input
                v-model="form.password"
                autocomplete="new-password"
                type="password"
                :placeholder="$t('forms.password')"
            />
            <jet-input-error :message="error('password','updatePassword')" class="mt-2" />
          </b-form-group>

          <b-form-group label-cols="2" horizontal :label="$t('forms.password-confirmation')">
            <b-form-input
                v-model="form.password_confirmation"
                autocomplete="new-password"
                type="password"
                :placeholder="$t('forms.password')"
            />
            <jet-input-error :message="error('password_confirmation','updatePassword')" class="mt-2" />
          </b-form-group>
        </template>

        <template #actions>
            <jet-button :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                {{ $t('button.save')}}
            </jet-button>
            <jet-action-message :on="form.recentlySuccessful" class="m-3">
              {{  $t('forms.saved') }}.
            </jet-action-message>
        </template>
    </jet-form-section>
</template>

<script>
    import JetActionMessage from '@/Jetstream/ActionMessage'
    import JetButton from '@/Jetstream/Button'
    import JetFormSection from '@/Jetstream/FormSection'
    import JetInput from '@/Jetstream/Input'
    import JetInputError from '@/Jetstream/InputError'
    import JetLabel from '@/Jetstream/Label'

    export default {
        components: {
            JetActionMessage,
            JetButton,
            JetFormSection,
            JetInput,
            JetInputError,
            JetLabel,
        },

        data() {
            return {
                form: this.$inertia.form({
                    current_password: '',
                    password: '',
                    password_confirmation: '',
                }, {
                    bag: 'updatePassword',
                }),
            }
        },

        methods: {
            updatePassword() {
                this.form.put(route('user-password.update'), {
                    preserveScroll: true
                }).then(() => {
                    this.$refs.current_password.focus()
                })
            },
        },
    }
</script>
