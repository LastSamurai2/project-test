<template>
    <b-button variant="primary" :type="type">
        <slot></slot>
    </b-button>
</template>

<script>
    export default {
        props: {
            type: {
                type: String,
                default: 'submit',
            },
        }
    }
</script>
