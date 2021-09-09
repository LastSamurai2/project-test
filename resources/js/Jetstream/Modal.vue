<template>
    <portal to="modal">

            <div v-show="show" v-bind:style="overLayStyles">
              <div v-bind:style="modalStyles" class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:w-full">

                <div v-show="show && closeable" @click="close" class="float-right m-3">
                  <i :class="'iconsminds-close'" style="font-size: 20px; cursor: pointer"/>
                </div>

                <div v-show="show" :class="maxWidthClass">
                    <slot></slot>
                </div>
              </div>
            </div>

    </portal>
</template>

<script>
    export default {
        props: {
            show: {
                default: false
            },
            maxWidth: {
                default: '2xl'
            },
            closeable: {
                default: true
            },
        },

        data() {
          return {
            overLayStyles: {
              top: 0,
              left: 0,
              height: '100%',
              width: '100%',
              position: 'fixed',
              background: 'rgba(0,0,0,0.5)',
              'z-index': 1500
            },
            modalStyles: {
              position: 'fixed',
              width: '50%',
              top: '50%',
              left: '50%',
              transform: 'translate(-50%, -50%)'
            }
          }
        },

        methods: {
            close() {
                if (this.closeable) {
                    this.$emit('close')
                }
            }
        },

        watch: {
            show: {
                immediate: true,
                handler: (show) => {
                    if (show) {
                        document.body.style.overflow = 'hidden'
                    } else {
                        document.body.style.overflow = null
                    }
                }
            }
        },

        created() {
            const closeOnEscape = (e) => {
                if (e.key === 'Escape' && this.show) {
                    this.close()
                }
            }

            document.addEventListener('keydown', closeOnEscape)

            this.$once('hook:destroyed', () => {
                document.removeEventListener('keydown', closeOnEscape)
            })
        },

        computed: {
            maxWidthClass() {
                return {
                    'sm': 'sm:max-w-sm',
                    'md': 'sm:max-w-md',
                    'lg': 'sm:max-w-lg',
                    'xl': 'sm:max-w-xl',
                    '2xl': 'sm:max-w-2xl',
                }[this.maxWidth]
            }
        }
    }
</script>
