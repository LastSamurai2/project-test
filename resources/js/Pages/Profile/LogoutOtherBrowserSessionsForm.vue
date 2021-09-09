<template>
    <jet-action-section>
        <template #title>
          {{ $t('profile.session.title') }}
        </template>

        <template #description>
          {{ $t('profile.session.message.description') }}
        </template>

        <template #content>
            <div class="max-w-xl text-sm text-gray-600">
              {{ $t('profile.session.message.content') }}
            </div>

            <!-- Other Browser Sessions -->

                  <b-row v-if="sessions.length > 0" class="mt-3">
                    <b-colxx xxs="3"   v-for="(session, i) in sessions" :key="i">

                  <b-card
                      :title="session.agent.platform + ' - ' + session.agent.browser"
                      tag="session"
                      style="max-width: 14rem;"
                      class="mb-2"
                  >

                    <template #header>
                      <div>
                        <svg fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor" class="w-8 h-8 text-gray-500" v-if="session.agent.is_desktop">
                          <path d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>

                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round" class="w-8 h-8 text-gray-500" v-else>
                          <path d="M0 0h24v24H0z" stroke="none"></path><rect x="7" y="4" width="10" height="16" rx="1"></rect><path d="M11 5h2M12 17v.01"></path>
                        </svg>
                      </div>
                    </template>
                    <b-card-text>
                        <div class="text-xs text-gray-500">
                          {{ session.ip_address }},
                          <span class="text-green-500 font-semibold" v-if="session.is_current_device">{{ $t('profile.session.this-device') }}</span>
                          <span v-else>{{ $t('profile.session.last-active') }} {{ session.last_active }}</span>
                        </div>
                    </b-card-text>
                  </b-card>


                </b-colxx>
              </b-row>


            <div class="flex items-center mt-5">
                <jet-button @click.native="confirmLogout">
                  {{ $t('profile.session.button') }}
                </jet-button>

                <jet-action-message :on="form.recentlySuccessful" class="ml-3">
                    Done.
                </jet-action-message>
            </div>

            <!-- Logout Other Devices Confirmation Modal -->
            <jet-dialog-modal :show="confirmingLogout" @close="confirmingLogout = false">
                <template #title>
                  {{ $t('profile.session.button') }}
                </template>

                <template #content>
                  {{ $t('profile.session.modal.content') }}

                  <b-form-group label-cols="2" horizontal :label="$t('forms.password')" class="mt-4">
                    <b-form-input type="password" :placeholder="$t('forms.password')"
                                    ref="password"
                                    v-model="form.password"
                                    @keyup.enter.native="logoutOtherBrowserSessions" />

                        <jet-input-error :message="error('password', 'logoutOtherBrowserSessions')" class="mt-2" />
                  </b-form-group>
                </template>

                <template #footer>
                    <jet-secondary-button @click.native="confirmingLogout = false">
                      {{ $t('button.cancel') }}
                    </jet-secondary-button>

                    <jet-button class="ml-2" @click.native="logoutOtherBrowserSessions" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                      {{ $t('profile.session.button') }}
                    </jet-button>
                </template>
            </jet-dialog-modal>
        </template>
    </jet-action-section>
</template>

<script>
    import JetActionMessage from '@/Jetstream/ActionMessage'
    import JetActionSection from '@/Jetstream/ActionSection'
    import JetButton from '@/Jetstream/Button'
    import JetDialogModal from '@/Jetstream/DialogModal'
    import JetInput from '@/Jetstream/Input'
    import JetInputError from '@/Jetstream/InputError'
    import JetSecondaryButton from '@/Jetstream/SecondaryButton'

    export default {
        props: ['sessions'],

        components: {
            JetActionMessage,
            JetActionSection,
            JetButton,
            JetDialogModal,
            JetInput,
            JetInputError,
            JetSecondaryButton,
        },

        data() {
            return {
                confirmingLogout: false,

                form: this.$inertia.form({
                    '_method': 'DELETE',
                    password: '',
                }, {
                    bag: 'logoutOtherBrowserSessions'
                })
            }
        },

        methods: {
            confirmLogout() {
                this.form.password = ''

                this.confirmingLogout = true

                setTimeout(() => {
                    this.$refs.password.focus()
                }, 250)
            },

            logoutOtherBrowserSessions() {
                this.form.post(route('other-browser-sessions.destroy'), {
                    preserveScroll: true
                }).then(response => {
                    if (! this.form.hasErrors()) {
                        this.confirmingLogout = false
                    }
                })
            },
        },
    }
</script>
