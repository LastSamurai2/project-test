<template>
    <jet-action-section>
        <template #title>
          {{ $t('user.delete.account-title') }}
        </template>

        <template #description>
            {{ $t('user.delete.info') }}
        </template>

        <template #content>
            <div class="max-w-xl text-sm text-gray-600">
              {{ $t('user.delete.message.warning') }}
            </div>

            <div class="mt-5">
                <jet-danger-button @click.native="confirmUserDeletion">
                  {{ $t('user.delete.account') }}
                </jet-danger-button>
            </div>

            <!-- Delete Account Confirmation Modal -->
            <jet-dialog-modal :show="confirmingUserDeletion" @close="confirmingUserDeletion = false">
                <template #title>
                  {{ $t('user.delete.account') }}
                </template>

                <template #content>
                  {{ $t('user.delete.message.confirmation')}}

                  <b-form-group label-cols="2" horizontal :label="$t('forms.password')" class="mt-4">
                    <b-form-input type="password" :placeholder="$t('forms.password')"
                                  ref="password"
                                  v-model="form.password"
                                  @keyup.enter.native="deleteUser" />

                    <jet-input-error :message="error('password', 'deleteUser')" class="mt-2" />
                  </b-form-group>

                </template>

                <template #footer>
                    <jet-secondary-button @click.native="confirmingUserDeletion = false">
                      {{ $t('button.cancel') }}
                    </jet-secondary-button>

                    <jet-danger-button class="ml-2" @click.native="deleteUser" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                      {{ $t('user.delete.account') }}
                    </jet-danger-button>
                </template>
            </jet-dialog-modal>
        </template>
    </jet-action-section>
</template>

<script>
    import JetActionSection from '@/Jetstream/ActionSection'
    import JetDialogModal from '@/Jetstream/DialogModal'
    import JetDangerButton from '@/Jetstream/DangerButton'
    import JetInput from '@/Jetstream/Input'
    import JetInputError from '@/Jetstream/InputError'
    import JetSecondaryButton from '@/Jetstream/SecondaryButton'

    export default {
        components: {
            JetActionSection,
            JetDangerButton,
            JetDialogModal,
            JetInput,
            JetInputError,
            JetSecondaryButton,
        },

        data() {
            return {
                confirmingUserDeletion: false,

                form: this.$inertia.form({
                    '_method': 'DELETE',
                    password: '',
                }, {
                    bag: 'deleteUser'
                })
            }
        },

        methods: {
            confirmUserDeletion() {
                this.form.password = '';

                this.confirmingUserDeletion = true;

                setTimeout(() => {
                    this.$refs.password.focus()
                }, 250)
            },

            deleteUser() {
                this.form.post(route('current-user.destroy'), {
                    preserveScroll: true
                }).then(response => {
                    if (! this.form.hasErrors()) {
                        this.confirmingUserDeletion = false;
                    }
                })
            },
        },
    }
</script>
