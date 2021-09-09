<template>
    <div class="md:grid md:grid-cols-3 md:gap-6">

      <b-row>
        <b-colxx xxs="12">
          <b-card class="mb-4">

            <jet-section-title>
                <template #title><slot name="title"></slot></template>
                <template #description><slot name="description"></slot></template>
            </jet-section-title>


            <slot name="content"></slot>
          </b-card>
        </b-colxx>
      </b-row>
    </div>
</template>

<script>
    import JetSectionTitle from './SectionTitle'

    export default {
        components: {
            JetSectionTitle,
        }
    }
</script>
