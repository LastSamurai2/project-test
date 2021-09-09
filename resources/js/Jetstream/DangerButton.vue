<template>
  <b-button :type="type" variant="danger">
    <slot></slot>
  </b-button>
</template>

<script>
    export default {
        props: {
            type: {
                type: String,
                default: 'button',
            },
        }
    }
</script>
